<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginToken extends Model
{
    protected $primaryKey = 'token_hash';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['token_hash', 'purpose', 'email', 'expires_at', 'used_at'];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }
}
