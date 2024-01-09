<?php

namespace App\Models;

use Framework\Database\ORM\Model;

class Subscriber extends Model
{
    protected $fillable = [
        'user_id',
        'endpoint',
        'auth_token',
        'public_key',
    ];
}