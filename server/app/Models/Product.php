<?php

namespace App\Models;

use Framework\Database\ORM\Model;

class Product extends Model
{
    protected $fillable = [
        'id',
        'url',
        'name',
        'image',
        'is_available',
        'last_known_price',
        'last_available_at'
    ];
}