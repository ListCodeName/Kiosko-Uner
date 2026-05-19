<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\Group;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PerformanceController extends Controller
{
    /* ══════════════════════════════════════════════════════════
     * GET /profesor/api/performance/individual
     *
     * Devuelve TODOS los alumnos de TODOS los grupos del profesor
     * con sus métricas individuales (sin filtrar por grupo).
     * ══════════════════════════════════════════════════════════ */
    public function individual(Request $request)
    {
        $professorId = Auth::id();

        // Todos los grupos del profesor con sus alumnos
        $groups = Group::where('professor_id', $professorId)
            ->with('students:id,name,username')
            ->get();

        // Consolidar alumnos únicos (un alumno puede estar en varios grupos)
        $studentsMap = [];
        foreach ($groups as $group) {
            foreach ($group->students as $student) {
                if (!isset($studentsMap[$student->id])) {
                    $studentsMap[$student->id] = [
                        'student' => $student,
                        'groups'  => [],
                    ];
                }
                $studentsMap[$student->id]['groups'][] = $group->name;
            }
        }

        if (empty($studentsMap)) {
            return response()->json(['students' => []]);
        }

        // IDs de grupos del profesor para buscar asistencias
        $groupIds = $groups->pluck('id');

        $students = collect(array_values($studentsMap))->map(function ($entry) use ($groupIds) {
            $student = $entry['student'];
            $groupNames = $entry['groups'];

            /* ── Asistencia (sobre todos los grupos del profesor) ── */
            $attAll = Attendance::whereIn('group_id', $groupIds)
                ->where('student_id', $student->id)
                ->whereNotNull('present')
                ->count();

            $attPresent = Attendance::whereIn('group_id', $groupIds)
                ->where('student_id', $student->id)
                ->where('present', 1)
                ->count();

            $attendancePct = $attAll > 0
                ? round(($attPresent / $attAll) * 100)
                : null;

            /* ── Actividad ─────────────────────────────────────── */
            $activityCounts = ActivityLog::where('user_id', $student->id)
                ->select('action', DB::raw('COUNT(*) as total'))
                ->groupBy('action')
                ->pluck('total', 'action')
                ->toArray();

            $activityTotal = array_sum($activityCounts);

            return [
                'id'               => $student->id,
                'name'             => $student->name,
                'username'         => $student->username,
                'groups'           => $groupNames,
                'attendance_pct'   => $attendancePct,
                'att_present'      => $attPresent,
                'att_total'        => $attAll,
                'activity_total'   => $activityTotal,
                'activity_by_type' => $activityCounts,
            ];
        });

        return response()->json(['students' => $students]);
    }


    /* ══════════════════════════════════════════════════════════
     * GET /profesor/api/performance/attendance-detail
     *     ?student_id=&month=YYYY-MM
     *
     * Devuelve el mapa de asistencia día a día de un mes para
     * un alumno, considerando TODOS los grupos del profesor.
     * ══════════════════════════════════════════════════════════ */
    public function attendanceDetail(Request $request)
    {
        $request->validate([
            'student_id' => 'required|integer|exists:users,id',
            'month'      => 'required|date_format:Y-m',
        ]);

        $professorId = Auth::id();
        $studentId   = $request->student_id;

        // IDs de los grupos del profesor que contienen al alumno
        $groupIds = Group::where('professor_id', $professorId)
            ->whereHas('students', fn($q) => $q->where('users.id', $studentId))
            ->pluck('id');

        // Rango del mes
        $monthStart = Carbon::createFromFormat('Y-m', $request->month)->startOfMonth();
        $monthEnd   = $monthStart->copy()->endOfMonth();

        // Registros del mes
        $records = Attendance::whereIn('group_id', $groupIds)
            ->where('student_id', $studentId)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->get(['date', 'present']);

        // Mapa fecha → estado
        $dayMap = [];
        foreach ($records as $r) {
            $dayMap[$r->date->format('Y-m-d')] = $r->present; // 1, 0, null
        }

        // Contadores del mes
        $present  = collect($dayMap)->filter(fn($v) => $v === 1)->count();
        $absent   = collect($dayMap)->filter(fn($v) => $v === 0)->count();
        $marked   = $present + $absent;

        // Nombre del alumno
        $student = User::select('id', 'name', 'username')->findOrFail($studentId);

        return response()->json([
            'student'    => $student,
            'month'      => $monthStart->format('Y-m'),
            'month_label'=> ucfirst($monthStart->locale('es')->isoFormat('MMMM YYYY')),
            'day_map'    => $dayMap,
            'stats'      => [
                'present'  => $present,
                'absent'   => $absent,
                'marked'   => $marked,
                'pct'      => $marked > 0 ? round(($present / $marked) * 100) : null,
            ],
        ]);
    }

    /* ══════════════════════════════════════════════════════════
     * GET /profesor/api/performance/activity-detail?student_id=
     *
     * Devuelve el resumen de actividad de un alumno:
     *  - total por tipo de acción
     *  - últimas 30 entradas del log
     * ══════════════════════════════════════════════════════════ */
    public function activityDetail(Request $request)
    {
        $request->validate([
            'student_id' => 'required|integer|exists:users,id',
        ]);

        $studentId = $request->student_id;

        // Verificar que el alumno pertenece a algún grupo del profesor
        $professorId = Auth::id();
        $isMember = Group::where('professor_id', $professorId)
            ->whereHas('students', fn($q) => $q->where('users.id', $studentId))
            ->exists();

        if (!$isMember) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $student = User::select('id', 'name', 'username')->findOrFail($studentId);

        // Totales por tipo (se mantiene agrupado por acción, o se podría hacer por módulo en el futuro)
        $byType = ActivityLog::where('user_id', $studentId)
            ->select('action', DB::raw('COUNT(*) as total'))
            ->groupBy('action')
            ->pluck('total', 'action')
            ->toArray();

        // Últimas 30 entradas
        $recent = ActivityLog::where('user_id', $studentId)
            ->latest('created_at')
            ->limit(30)
            ->get(['action', 'module', 'description', 'created_at'])
            ->map(fn($l) => [
                'action'      => $l->action,
                'module'      => $l->module,
                'description' => $l->description,
                'created_at'  => $l->created_at->toIso8601String(),
            ]);

        $total = array_sum($byType);

        return response()->json([
            'student' => $student,
            'total'   => $total,
            'by_type' => $byType,
            'recent'  => $recent,
        ]);
    }
}
