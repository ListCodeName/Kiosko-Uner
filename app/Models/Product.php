<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'tipo',
        'price',
        'sale_price',
        'stock',
        'is_active',
    ];

    protected $casts = [
        'price'      => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_active'  => 'boolean',
    ];

    /* ── Constantes de tipo ────────────────────────────────── */

    const TIPO_REVENTA   = 'reventa';
    const TIPO_INSUMO    = 'insumo';
    const TIPO_ELABORADO = 'elaborado';

    const TIPOS = [
        self::TIPO_REVENTA   => 'Reventa',
        self::TIPO_INSUMO    => 'Insumo',
        self::TIPO_ELABORADO => 'Elaborado',
    ];

    /* ── Relaciones ────────────────────────────────────────── */

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /* ── Scopes ────────────────────────────────────────────── */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeReventa($query)
    {
        return $query->where('tipo', self::TIPO_REVENTA);
    }

    public function scopeInsumo($query)
    {
        return $query->where('tipo', self::TIPO_INSUMO);
    }

    public function scopeElaborado($query)
    {
        return $query->where('tipo', self::TIPO_ELABORADO);
    }

    /* ── Helpers ────────────────────────────────────────────── */

    /**
     * Indica si este producto controla stock
     * (solo reventa y elaborado lo hacen).
     */
    public function tieneStock(): bool
    {
        return in_array($this->tipo, [self::TIPO_REVENTA, self::TIPO_ELABORADO]);
    }

    /**
     * Indica si este tipo de producto actualiza stock al comprarse.
     */
    public function actualizaStockEnCompra(): bool
    {
        return $this->tipo === self::TIPO_REVENTA;
    }
}
