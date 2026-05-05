<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Personnel extends Model
{
    protected $table = 'personnel';

    protected $fillable = [
        'dni',
        'nombre',
        'apellido',
        'telefono',
        'correo',
        'user_id',
    ];

    /* ── Relationships ─────────────────────────────────────── */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /* ── Accessors ─────────────────────────────────────────── */

    /**
     * Full name: "Apellido, Nombre"
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->apellido}, {$this->nombre}";
    }
}
