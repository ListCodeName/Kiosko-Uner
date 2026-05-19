<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'sort_order',
        'is_produced',
    ];

    protected $casts = [
        'is_produced' => 'boolean',
    ];

    /* ── Relaciones ──────────────────────────────────────────── */

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    /* ── Scopes ──────────────────────────────────────────────── */

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
