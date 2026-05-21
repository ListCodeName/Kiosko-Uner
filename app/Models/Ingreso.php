<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingreso extends Model
{
    use HasFactory;

    protected $table = 'ingresos';

    protected $fillable = [
        'fecha',
        'tipo',
        'descripcion',
        'monto',
        'estado',
        'detalle',
        'user_id',
    ];

    protected $casts = [
        'fecha' => 'date:Y-m-d',
        'monto' => 'decimal:2',
    ];

    /**
     * Relación con el usuario que registró el ingreso.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
