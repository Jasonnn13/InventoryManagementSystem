<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const LEVEL_REQUEST = 0;
    public const LEVEL_ADMIN = 1;
    public const LEVEL_OWNER = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'level', // Add the role attribute here
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
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

    public function isRequest(): bool
    {
        return $this->level === self::LEVEL_REQUEST;
    }

    public function isAdmin(): bool
    {
        return $this->level === self::LEVEL_ADMIN;
    }

    public function isOwner(): bool
    {
        return $this->level === self::LEVEL_OWNER;
    }

    public function hasLevel(int $level): bool
    {
        return $this->level === $level;
    }
}
