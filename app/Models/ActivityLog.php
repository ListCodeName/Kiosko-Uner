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
}
