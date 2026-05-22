<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\Group;
use App\Models\User;
use App\Models\GroupShift;
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
        $allStudentIds = array_keys($studentsMap);

        // Asistencia por alumno (sobre todos los grupos del profesor) en una sola consulta
        $attAll = Attendance::whereIn('group_id', $groupIds)
            ->whereIn('student_id', $allStudentIds)
            ->whereNotNull('present')
            ->select('student_id', DB::raw('COUNT(*) as total'), DB::raw('SUM(present) as present_count'))
            ->groupBy('student_id')
            ->get()
            ->keyBy('student_id');

        // Actividad por alumno (Solo Contribuciones Cualificadas) en una sola consulta
        $actAll = ActivityLog::whereIn('user_id', $allStudentIds)
            ->contributions()
            ->select('user_id', 'action', DB::raw('COUNT(*) as total'))
            ->groupBy('user_id', 'action')
            ->get()
            ->groupBy('user_id');

        $students = collect(array_values($studentsMap))->map(function ($entry) use ($attAll, $actAll) {
            $student = $entry['student'];
            $groupNames = $entry['groups'];

            /* ── Asistencia ── */
            $att        = $attAll->get($student->id);
            $attTotal   = $att ? (int) $att->total : 0;
            $attPresent = $att ? (int) $att->present_count : 0;
            $attendancePct = $attTotal > 0 ? round(($attPresent / $attTotal) * 100) : null;

            /* ── Actividad ── */
            $studentLogs = $actAll->get($student->id, collect());
            $activityCounts = $studentLogs->pluck('total', 'action')->toArray();
            $activityTotal = array_sum($activityCounts);

            return [
                'id'               => $student->id,
                'name'             => $student->name,
                'username'         => $student->username,
                'groups'           => $groupNames,
                'attendance_pct'   => $attendancePct,
                'att_present'      => $attPresent,
                'att_total'        => $attTotal,
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

    /* ══════════════════════════════════════════════════════════
     * GET /profesor/api/performance/grupal
     *
     * Devuelve TODOS los grupos del profesor con:
     *  - Promedio grupal de asistencia y actividad
     *  - Lista de miembros con sus métricas individuales
     *  - Estadística global (media de todos los alumnos)
     *  - Pódio: más destacado en asistencia y contribución
     *  - Alertas: alumnos por debajo de la media global
     * ══════════════════════════════════════════════════════════ */
    public function grupal(Request $request)
    {
        $professorId = Auth::id();

        $groups = Group::where('professor_id', $professorId)
            ->with('students:id,name,username')
            ->get();

        if ($groups->isEmpty()) {
            return response()->json([
                'groups'       => [],
                'global_stats' => null,
                'podium'       => null,
                'alerts'       => [],
            ]);
        }

        // IDs de grupos del profesor
        $allGroupIds = $groups->pluck('id');

        // ── Construir mapa alumno → métricas (una sola consulta por alumno único) ──
        $allStudentIds = $groups->flatMap(fn($g) => $g->students->pluck('id'))->unique()->values();

        // Asistencia por alumno (sobre todos los grupos del profesor)
        $attAll = Attendance::whereIn('group_id', $allGroupIds)
            ->whereIn('student_id', $allStudentIds)
            ->whereNotNull('present')
            ->select('student_id', DB::raw('COUNT(*) as total'), DB::raw('SUM(present) as present_count'))
            ->groupBy('student_id')
            ->get()
            ->keyBy('student_id');

        // Turnos asignados por grupo
        $shiftsByGroup = GroupShift::whereIn('group_id', $allGroupIds)->get()->groupBy('group_id');

        // Cargar todas las contribuciones cualificadas en una sola consulta optimizada
        $allLogs = ActivityLog::whereIn('user_id', $allStudentIds)
            ->contributions()
            ->get();

        $logsByUser = $allLogs->groupBy('user_id');

        // ── Construir respuesta por grupo ──
        $groupsData = $groups->map(function ($group) use ($logsByUser, $shiftsByGroup, $attAll) {
            $groupId = $group->id;
            $shifts = $shiftsByGroup->get($groupId, collect());

            $members = $group->students->map(function ($student) use ($logsByUser, $shifts, $attAll) {
                $studentId = $student->id;

                /* ── Asistencia ── */
                $att        = $attAll->get($studentId);
                $attTotal   = $att ? (int) $att->total : 0;
                $attPresent = $att ? (int) $att->present_count : 0;
                $attPct     = $attTotal > 0 ? round(($attPresent / $attTotal) * 100) : null;

                /* ── Actividad ── */
                $studentLogs = $logsByUser->get($studentId, collect());

                // 1. activity_total: total de contribuciones cualificadas históricas del alumno
                $activityTotal = $studentLogs->count();

                // 2. group_activity_total: contribuciones cualificadas realizadas durante la guardia/semana de este grupo
                if ($shifts->isEmpty()) {
                    $groupActivityTotal = $activityTotal;
                } else {
                    $groupActivityTotal = $studentLogs->filter(function ($log) use ($shifts) {
                        $logDate = $log->created_at->toDateString();
                        return $shifts->contains(function ($shift) use ($logDate) {
                            return $logDate >= $shift->start_date->toDateString() && $logDate <= $shift->end_date->toDateString();
                        });
                    })->count();
                }

                // 3. activity_by_type: desglose de todas sus contribuciones cualificadas históricas
                $activityByType = $studentLogs->groupBy('action')->map(fn($g) => $g->count())->toArray();

                return [
                    'id'                   => $studentId,
                    'name'                 => $student->name,
                    'username'             => $student->username,
                    'attendance_pct'       => $attPct,
                    'att_present'          => $attPresent,
                    'att_total'            => $attTotal,
                    'activity_total'       => $activityTotal,
                    'group_activity_total' => $groupActivityTotal,
                    'activity_by_type'     => $activityByType,
                ];
            })->values();

            // Promedio grupal: promedio de asistencia y promedio de actividad EN SU TURNO (group_activity_total)
            $attValues  = $members->whereNotNull('attendance_pct')->pluck('attendance_pct');
            $actValues  = $members->pluck('group_activity_total');

            $avgAtt = $attValues->count() > 0 ? round($attValues->avg()) : null;
            $avgAct = $actValues->count() > 0 ? round($actValues->avg()) : null;

            return [
                'id'                 => $group->id,
                'name'               => $group->name,
                'member_count'       => $members->count(),
                'members'            => $members,
                'avg_attendance_pct' => $avgAtt,
                'avg_activity'       => $avgAct,
            ];
        })->values();

        // ── Pódio y alertas (sobre todos los alumnos únicos del profesor) ──
        $flatMembers = [];
        foreach ($groupsData as $groupData) {
            foreach ($groupData['members'] as $member) {
                $studentId = $member['id'];
                if (!isset($flatMembers[$studentId])) {
                    $flatMembers[$studentId] = [
                        'id'                   => $member['id'],
                        'name'                 => $member['name'],
                        'username'             => $member['username'],
                        'attendance_pct'       => $member['attendance_pct'],
                        'att_present'          => $member['att_present'],
                        'att_total'            => $member['att_total'],
                        'activity_total'       => $member['activity_total'],
                        'group_activity_total' => $member['group_activity_total'],
                        'activity_by_type'     => $member['activity_by_type'],
                        'groups'               => [$groupData['name']],
                    ];
                } else {
                    $flatMembers[$studentId]['groups'][] = $groupData['name'];
                }
            }
        }
        $flatMembers = array_values($flatMembers);

        // Estadística global (media de todos los alumnos únicos)
        $allAtt = collect($flatMembers)->whereNotNull('attendance_pct')->pluck('attendance_pct');
        $allAct = collect($flatMembers)->pluck('activity_total'); // media global basada en actividad total histórica

        $globalAvgAtt = $allAtt->count() > 0 ? round($allAtt->avg()) : null;
        $globalAvgAct = $allAct->count() > 0 ? round($allAct->avg()) : null;

        // Top en asistencia (con datos, ordenados desc)
        $byAtt = collect($flatMembers)->whereNotNull('attendance_pct')->sortByDesc('attendance_pct')->values();
        // Top en actividad (basado en contribuciones cualificadas totales para premiar la colaboración)
        $byAct = collect($flatMembers)->sortByDesc('activity_total')->values();

        $podium = [
            'top_attendance'    => $byAtt->take(3)->values(),
            'top_activity'      => $byAct->take(3)->values(),
        ];

        // Alertas: por debajo de la media en asistencia o en actividad global
        $alerts = collect($flatMembers)->filter(function ($m) use ($globalAvgAtt, $globalAvgAct) {
            $attBelow = $globalAvgAtt !== null && $m['attendance_pct'] !== null && $m['attendance_pct'] < $globalAvgAtt;
            $actBelow = $globalAvgAct !== null && $m['activity_total'] < $globalAvgAct;
            return $attBelow || $actBelow;
        })->map(function ($m) use ($globalAvgAtt, $globalAvgAct) {
            $reasons = [];
            if ($globalAvgAtt !== null && $m['attendance_pct'] !== null && $m['attendance_pct'] < $globalAvgAtt) {
                $reasons[] = 'asistencia';
            }
            if ($globalAvgAct !== null && $m['activity_total'] < $globalAvgAct) {
                $reasons[] = 'actividad';
            }
            return array_merge($m, ['alert_reasons' => $reasons]);
        })->values();

        return response()->json([
            'groups'       => $groupsData,
            'global_stats' => [
                'avg_attendance_pct' => $globalAvgAtt,
                'avg_activity'       => $globalAvgAct,
                'total_students'     => count($flatMembers),
            ],
            'podium'       => $podium,
            'alerts'       => $alerts,
        ]);
    }

    /* ══════════════════════════════════════════════════════════
     * GET /profesor/api/bitacora
     *
     * Devuelve el historial completo de logs de actividades de los
     * alumnos que pertenecen a los grupos a cargo del profesor logueado.
     * ══════════════════════════════════════════════════════════ */
    public function bitacora(Request $request)
    {
        $professorId = Auth::id();

        // Obtener los IDs de los alumnos bajo el cargo del profesor en sus grupos
        $studentIds = DB::table('group_user')
            ->join('groups', 'groups.id', '=', 'group_user.group_id')
            ->where('groups.professor_id', $professorId)
            ->pluck('group_user.user_id')
            ->unique();

        // Consultar la tabla de logs de actividades filtrando por esos alumnos
        $logs = ActivityLog::whereIn('user_id', $studentIds)
            ->with('user:id,name,username,role')
            ->orderBy('created_at', 'desc')
            ->limit(200) // limitar a los últimos 200 para optimización
            ->get()
            ->map(function ($l) {
                return [
                    'id'          => $l->id,
                    'user_id'     => $l->user_id,
                    'user_name'   => $l->user ? $l->user->name : 'Usuario Desconocido',
                    'user_role'   => $l->user ? $l->user->role : 'alumno',
                    'action'      => $l->action,
                    'module'      => $l->module,
                    'description' => $l->description,
                    'created_at'  => $l->created_at ? $l->created_at->toIso8601String() : null,
                    'time_ago'    => $l->created_at ? $l->created_at->diffForHumans() : '',
                ];
            });

        return response()->json([
            'logs' => $logs
        ]);
    }

    /* ══════════════════════════════════════════════════════════
     * GET /profesor/api/performance/economico
     *
     * Devuelve el rendimiento económico consolidad e histórico
     * y por rango de fechas para los grupos a cargo del profesor.
     * ══════════════════════════════════════════════════════════ */
    public function economico(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date'   => 'nullable|date_format:Y-m-d',
        ]);

        $professorId = Auth::id();

        // 1. Obtener grupos a cargo del profesor
        $groups = Group::where('professor_id', $professorId)
            ->with('students:id,name,username')
            ->get();

        if ($groups->isEmpty()) {
            return response()->json([
                'success' => true,
                'groups' => [],
                'totals' => [
                    'ganancia_efectiva' => 0,
                    'perdida_efectiva'  => 0,
                    'balance_neto'      => 0,
                    'margen'            => 0,
                ],
            ]);
        }

        // Obtener todos los IDs de alumnos únicos bajo este profesor
        $allStudentIds = $groups->flatMap(fn($g) => $g->students->pluck('id'))->unique()->values()->toArray();

        // ─────────────────────────────────────────────────────────────────
        // 2. CÁLCULO HISTÓRICO CONSOLIDADO (Banners Superiores)
        // ─────────────────────────────────────────────────────────────────
        if (empty($allStudentIds)) {
            $histGanancia = 0;
            $histPerdida  = 0;
        } else {
            // Ventas reales cobradas (pagado)
            $histSalesTotal = \App\Models\Sale::whereIn('user_id', $allStudentIds)
                ->where('estado', 'pagado')
                ->sum('total');

            // Ingresos manuales efectuados (excluyendo tipo venta_kiosco)
            $histIngresosTotal = \App\Models\Ingreso::whereIn('user_id', $allStudentIds)
                ->where('estado', 'efectuado')
                ->where('tipo', '!=', 'venta_kiosco')
                ->sum('monto');

            // Compras automáticas (egresos de tipo insumos con descripción Compra mercadería #)
            $histComprasTotal = \App\Models\Egreso::whereIn('user_id', $allStudentIds)
                ->where('tipo', 'insumos')
                ->where('descripcion', 'like', 'Compra mercadería #%')
                ->where('estado', 'efectuado')
                ->sum('monto');

            // Egresos manuales efectuados (excluyendo egresos automáticos de compras)
            $histEgresosTotal = \App\Models\Egreso::whereIn('user_id', $allStudentIds)
                ->where('estado', 'efectuado')
                ->where(function ($q) {
                    $q->where('tipo', '!=', 'insumos')
                      ->orWhere('descripcion', 'not like', 'Compra mercadería #%');
                })
                ->sum('monto');

            $histGanancia = floatval($histSalesTotal) + floatval($histIngresosTotal);
            $histPerdida  = floatval($histComprasTotal) + floatval($histEgresosTotal);
        }

        $histBalance = $histGanancia - $histPerdida;
        $histMargen  = $histGanancia > 0 ? round(($histBalance / $histGanancia) * 100, 1) : 0;

        // ─────────────────────────────────────────────────────────────────
        // 3. CÁLCULO POR RANGO DE FECHAS (Tarjetas de Grupo)
        // ─────────────────────────────────────────────────────────────────
        $startDate = $request->start_date 
            ? Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay() 
            : Carbon::now()->startOfMonth()->startOfDay();

        $endDate = $request->end_date 
            ? Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay() 
            : Carbon::now()->endOfMonth()->endOfDay();

        $groupsData = [];

        foreach ($groups as $group) {
            $studentIds = $group->students->pluck('id')->toArray();
            
            if (empty($studentIds)) {
                $groupsData[] = [
                    'id'                => $group->id,
                    'name'              => $group->name,
                    'member_count'      => 0,
                    'ventas_total'      => 0,
                    'compras_total'     => 0,
                    'ingresos_total'    => 0,
                    'egresos_total'     => 0,
                    'ganancia_efectiva' => 0,
                    'perdida_efectiva'  => 0,
                    'balance_neto'      => 0,
                    'pct_ingreso'       => 50,
                ];
                continue;
            }

            // Ventas efectuadas en el período
            $sales = \App\Models\Sale::whereIn('user_id', $studentIds)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();
            $ventasTotal = floatval($sales->where('estado', 'pagado')->sum('total'));

            // Compras efectuadas en el período
            $comprasTotal = floatval(
                \App\Models\Egreso::whereIn('user_id', $studentIds)
                    ->where('tipo', 'insumos')
                    ->where('descripcion', 'like', 'Compra mercadería #%')
                    ->where('estado', 'efectuado')
                    ->whereBetween('fecha', [$startDate->toDateString(), $endDate->toDateString()])
                    ->sum('monto')
            );

            // Ingresos efectuados en el período (excluyendo tipo venta_kiosco)
            $ingresos = \App\Models\Ingreso::whereIn('user_id', $studentIds)
                ->where('estado', 'efectuado')
                ->where('tipo', '!=', 'venta_kiosco')
                ->whereBetween('fecha', [$startDate->toDateString(), $endDate->toDateString()])
                ->get();
            $ingresosTotal = floatval($ingresos->sum('monto'));

            // Egresos efectuados en el período (excluyendo egresos automáticos de compras)
            $egresos = \App\Models\Egreso::whereIn('user_id', $studentIds)
                ->where('estado', 'efectuado')
                ->where(function ($q) {
                    $q->where('tipo', '!=', 'insumos')
                      ->orWhere('descripcion', 'not like', 'Compra mercadería #%');
                })
                ->whereBetween('fecha', [$startDate->toDateString(), $endDate->toDateString()])
                ->get();
            $egresosTotal = floatval($egresos->sum('monto'));

            $gananciaEfectiva = $ventasTotal + $ingresosTotal;
            $perdidaEfectiva  = $comprasTotal + $egresosTotal;
            $balanceNeto      = $gananciaEfectiva - $perdidaEfectiva;

            $totalFlujo = $gananciaEfectiva + $perdidaEfectiva;
            $pctIngreso = $totalFlujo > 0 ? intval(round(($gananciaEfectiva / $totalFlujo) * 100)) : 50;

            $groupsData[] = [
                'id'                => $group->id,
                'name'              => $group->name,
                'member_count'      => count($studentIds),
                'ventas_total'      => $ventasTotal,
                'compras_total'     => $comprasTotal,
                'ingresos_total'    => $ingresosTotal,
                'egresos_total'     => $egresosTotal,
                'ganancia_efectiva' => $gananciaEfectiva,
                'perdida_efectiva'  => $perdidaEfectiva,
                'balance_neto'      => $balanceNeto,
                'pct_ingreso'       => $pctIngreso,
            ];
        }

        return response()->json([
            'success' => true,
            'period'  => [
                'start' => $startDate->toDateString(),
                'end'   => $endDate->toDateString(),
            ],
            'totals' => [
                'ganancia_efectiva' => $histGanancia,
                'perdida_efectiva'  => $histPerdida,
                'balance_neto'      => $histBalance,
                'margen'            => $histMargen,
            ],
            'groups' => $groupsData,
        ]);
    }
}

