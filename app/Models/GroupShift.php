<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupShift extends Model
{
    protected $fillable = [
        'group_id',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
