<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'cliente',
        'fecha',
        'hora',
        'hora_entrega',
        'estado',
        'total',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    /* ── Relaciones ──────────────────────────────────────────── */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
