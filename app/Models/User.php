<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // Role constants
    const ROLE_KEUANGAN = 'keuangan';
    const ROLE_KEPSEK = 'kepsek';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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

    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->role, [self::ROLE_KEUANGAN, self::ROLE_KEPSEK]);
    }

    /**
     * Check if user is keuangan
     */
    public function isKeuangan(): bool
    {
        return $this->role === self::ROLE_KEUANGAN;
    }

    /**
     * Check if user is kepsek
     */
    public function isKepsek(): bool
    {
        return $this->role === self::ROLE_KEPSEK;
    }
}
