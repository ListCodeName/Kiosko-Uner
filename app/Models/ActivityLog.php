<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    const UPDATED_AT = null; // tabla inmutable

    protected $fillable = [
        'user_id',
        'action',
        'module',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /* ── Mutator for Action ──────────────────────────────────── */

    public function setActionAttribute($value)
    {
        $action = strtolower($value);

        // Map custom actions to valid enum values to satisfy SQLite enum CHECK constraints
        $map = [
            'sale_collect'   => 'sale',
            'sale_return'    => 'sale',
            'order_delivery' => 'sale',
        ];

        $this->attributes['action'] = $map[$action] ?? $action;
    }

    /* ── Relaciones ──────────────────────────────────────────── */
 
    public function user()
    {
        return $this->belongsTo(User::class);
    }
 
    /* ── Helper: registrar una acción ───────────────────────── */
 
    public static function log(int $userId, string $action, ?string $module = null, ?string $description = null): self
    {
        return static::create([
            'user_id'     => $userId,
            'action'      => $action,
            'module'      => $module,
            'description' => $description,
        ]);
    }

    /* ── Scope para filtrar sólo Contribuciones Cualificadas ── */

    public function scopeContributions($query)
    {
        return $query->where(function ($q) {
            // Ventas
            $q->orWhere(function ($sub) {
                $sub->where('module', 'Kiosco')
                    ->whereIn('action', ['sale', 'sale_collect', 'sale_return']);
            });

            // Compras
            $q->orWhere(function ($sub) {
                $sub->where('module', 'Compras')
                    ->whereIn('action', ['insert', 'delete', 'INSERT', 'DELETE']);
            });

            // Elaboraciones
            $q->orWhere(function ($sub) {
                $sub->where('module', 'Productos')
                    ->whereIn('action', ['update', 'UPDATE'])
                    ->where(function ($descQuery) {
                        $descQuery->where('description', 'like', '%elaborado%')
                                  ->orWhere('description', 'like', '%sobrantes%');
                    });
            });

            // Registro de Pedidos
            $q->orWhere(function ($sub) {
                $sub->where('module', 'Pedidos')
                    ->whereIn('action', ['insert', 'update', 'delete', 'confirm', 'reject', 'reactivate', 'INSERT', 'UPDATE', 'DELETE']);
            });

            // Entregas
            $q->orWhere(function ($sub) {
                $sub->where('module', 'Kiosco')
                    ->whereIn('action', ['order_delivery', 'sale']); // order_delivery is mapped to sale
            });
        });
    }

    /* ── Helper para evaluar si un log es una Contribución ── */

    public static function isContribution(?string $module, ?string $action, ?string $description = null): bool
    {
        if (!$module || !$action) {
            return false;
        }

        $module = strtolower($module);
        $action = strtolower($action);
        $description = $description ? strtolower($description) : '';

        // Ventas
        if ($module === 'kiosco' && in_array($action, ['sale', 'sale_collect', 'sale_return'])) {
            return true;
        }

        // Compras
        if ($module === 'compras' && in_array($action, ['insert', 'delete'])) {
            return true;
        }

        // Elaboraciones
        if ($module === 'productos' && $action === 'update' && (strpos($description, 'elaborado') !== false || strpos($description, 'sobrantes') !== false)) {
            return true;
        }

        // Registro de Pedidos
        if ($module === 'pedidos' && in_array($action, ['insert', 'update', 'delete', 'confirm', 'reject', 'reactivate'])) {
            return true;
        }

        // Entregas
        if ($module === 'kiosco' && $action === 'order_delivery') {
            return true;
        }

        return false;
    }

    /* ── Booted: Registro Reactivo de Asistencias ── */

    protected static function booted()
    {
        static::created(function ($activity) {
            if (self::isContribution($activity->module, $activity->action, $activity->description)) {
                $user = $activity->user;
                if ($user && $user->isAlumno()) {
                    $today = \Carbon\Carbon::today()->toDateString();
                    $groups = $user->studentGroups;
                    foreach ($groups as $group) {
                        \App\Models\Attendance::updateOrCreate(
                            [
                                'group_id'   => $group->id,
                                'student_id' => $user->id,
                                'date'       => $today,
                            ],
                            [
                                'present'    => 1,
                            ]
                        );
                    }
                }
            }
        });
    }
}
