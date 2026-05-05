<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'role',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /* ── Relationships ─────────────────────────────────────── */

    public function personnel()
    {
        return $this->hasOne(Personnel::class);
    }

    /* ── Role Helpers ──────────────────────────────────────── */

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isAlumno(): bool
    {
        return $this->role === 'alumno';
    }

    public function isProfesor(): bool
    {
        return $this->role === 'profesor';
    }

    public function isDirectivo(): bool
    {
        return $this->role === 'directivo';
    }

    /**
     * Get the panel route for this user's role.
     */
    public function panelRoute(): string
    {
        return match ($this->role) {
            'superadmin' => '/superadmin',
            'profesor'   => '/profesor',
            'directivo'  => '/directivo',
            default      => '/panel',
        };
    }
}
