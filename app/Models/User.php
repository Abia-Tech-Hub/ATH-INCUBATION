<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /** Higher can do more. The old Postgres enum and RLS used the same order. */
    public const RANKS = ['CUSTOMER' => 0, 'STAFF' => 1, 'ADMIN' => 2, 'OWNER' => 3];

    protected $fillable = [
        'email',
        'password',
        'role',
        'totp_secret',
        'totp_pending_secret',
        'totp_enabled',
        'totp_last_step',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'totp_secret',
        'totp_pending_secret',
        'pending_role',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'totp_secret' => 'encrypted',
            'totp_pending_secret' => 'encrypted',
            'totp_enabled' => 'boolean',
            'totp_last_step' => 'integer',
        ];
    }

    public function isStaff(): bool
    {
        return $this->role !== 'CUSTOMER';
    }

    public function rank(): int
    {
        return self::RANKS[$this->role] ?? 0;
    }

    public function canSeeContacts(): bool
    {
        return in_array($this->role, ['OWNER', 'ADMIN'], true);
    }

    public function requiresMfa(): bool
    {
        return in_array($this->role, ['OWNER', 'ADMIN'], true);
    }

    public function atLeast(string $role): bool
    {
        return $this->rank() >= (self::RANKS[$role] ?? 0);
    }
}
