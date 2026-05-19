<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'group_id',
        'student_id',
        'date',
        'present',
    ];

    protected $casts = [
        'date'    => 'date:Y-m-d',
        'present' => 'integer',
    ];

    /* ── Relations ──────────────────────────────────────────── */

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
