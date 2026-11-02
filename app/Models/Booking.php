<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = ['service', 'day', 'time', 'name', 'phone', 'email', 'status'];
}
