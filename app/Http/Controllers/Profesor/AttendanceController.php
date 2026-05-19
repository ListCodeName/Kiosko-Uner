<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * GET /profesor/api/attendance?group_id=&week_start=YYYY-MM-DD
     *
     * Devuelve los alumnos del grupo y sus registros de asistencia
     * para la semana indicada (lunes–domingo).
     */
    public function index(Request $request)
    {
        $request->validate([
            'group_id'   => 'required|integer|exists:groups,id',
            'week_start' => 'required|date_format:Y-m-d',
        ]);

        $group = Group::where('id', $request->group_id)
            ->where('professor_id', Auth::id())
            ->with('students:id,name,username')
            ->firstOrFail();

        // Calcular los 7 días de la semana (lunes–domingo)
        $monday = Carbon::parse($request->week_start)->startOfWeek(Carbon::MONDAY);
        $sunday = $monday->copy()->addDays(6);

        // Registros existentes para esa semana
        $records = Attendance::where('group_id', $group->id)
            ->whereBetween('date', [$monday->format('Y-m-d'), $sunday->format('Y-m-d')])
            ->get()
            ->keyBy(fn($r) => $r->student_id . '_' . $r->date->format('Y-m-d'));

        // Mapa de asistencias indexado por student_id y fecha
        $attendance_map = [];
        foreach ($records as $key => $record) {
            $attendance_map[$record->student_id][$record->date->format('Y-m-d')] = $record->present;
        }

        return response()->json([
            'group'          => [
                'id'   => $group->id,
                'name' => $group->name,
            ],
            'students'       => $group->students,
            'week_start'     => $monday->format('Y-m-d'),
            'week_end'       => $sunday->format('Y-m-d'),
            'attendance_map' => $attendance_map,
        ]);
    }

    /**
     * POST /profesor/api/attendance
     *
     * Body: { group_id, student_id, date (YYYY-MM-DD), present (null|0|1) }
     * Crea o actualiza (upsert) el registro de asistencia.
     */
    public function upsert(Request $request)
    {
        $data = $request->validate([
            'group_id'   => 'required|integer|exists:groups,id',
            'student_id' => 'required|integer|exists:users,id',
            'date'       => 'required|date_format:Y-m-d',
            'present'    => 'nullable|integer|in:0,1',
        ]);

        // Verificar que el grupo pertenece al profesor autenticado
        $group = Group::where('id', $data['group_id'])
            ->where('professor_id', Auth::id())
            ->firstOrFail();

        // Verificar que el alumno pertenece al grupo
        $isMember = $group->students()->where('users.id', $data['student_id'])->exists();
        if (!$isMember) {
            return response()->json(['message' => 'El alumno no pertenece a este grupo'], 422);
        }

        $record = Attendance::updateOrCreate(
            [
                'group_id'   => $data['group_id'],
                'student_id' => $data['student_id'],
                'date'       => $data['date'],
            ],
            [
                'present' => $data['present'],
            ]
        );

        return response()->json([
            'message'    => 'Asistencia actualizada',
            'attendance' => $record,
        ]);
    }
}
