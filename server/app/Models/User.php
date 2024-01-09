<?php

namespace App\Models;

use Framework\Services\Auth\User as Authenticatable;
use Framework\Services\Auth\Token\Tokenable;

class User extends Authenticatable
{
    use Tokenable;

    /**
     * The attributes that are assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'login',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $visible = [
        'login',
    ];

}
