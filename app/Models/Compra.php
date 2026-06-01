<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;

    protected $table = 'compras';

    protected $fillable = [
        'fecha',
        'total',
        'observaciones',
        'sincronizado',
    ];

    protected $casts = [
        'fecha'        => 'date',
        'total'        => 'decimal:2',
        'sincronizado' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(CompraItem::class, 'compra_id');
    }
}
