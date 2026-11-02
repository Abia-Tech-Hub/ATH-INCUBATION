<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = ['actor_id', 'action', 'target'];

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
