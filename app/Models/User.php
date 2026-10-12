<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'password', 'role', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_MANAJER = 'manajer';

    public const ROLE_KASI = 'kasi';

    /**
     * @return array<string, string>
     */
    public static function roleLabels(): array
    {
        return [
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_MANAJER => 'Manajer',
            self::ROLE_KASI => 'Kasi/SPV',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isManajer(): bool
    {
        return $this->role === self::ROLE_MANAJER;
    }

    public function isKasi(): bool
    {
        return $this->role === self::ROLE_KASI;
    }

    public function isReadOnly(): bool
    {
        return $this->isKasi();
    }

    public function canViewInfluencers(): bool
    {
        return $this->isAdmin() || $this->isKasi();
    }

    public function canManageInfluencers(): bool
    {
        return $this->isAdmin();
    }

    public function canUseCalculationTools(): bool
    {
        return ! $this->isReadOnly();
    }

    public function roleLabel(): string
    {
        return self::roleLabels()[$this->role] ?? $this->role;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }
}
