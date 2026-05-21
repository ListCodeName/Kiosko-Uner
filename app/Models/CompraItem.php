<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompraItem extends Model
{
    protected $table = 'compra_items';

    protected $fillable = [
        'compra_id',
        'product_id',
        'producto_nombre',
        'tipo_producto',
        'cantidad',
        'precio_unitario',
    ];

    protected $casts = [
        'cantidad'        => 'decimal:2',
        'precio_unitario' => 'decimal:2',
    ];

    /* ── Relaciones ── */

    public function compra()
    {
        return $this->belongsTo(Compra::class, 'compra_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /* ── Accessor ── */

    public function getSubtotalAttribute(): float
    {
        return round((float) $this->cantidad * (float) $this->precio_unitario, 2);
    }
}
