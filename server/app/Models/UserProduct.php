<?php

namespace App\Models;

use Framework\Database\ORM\Model;

class UserProduct extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'max_price',
        'enable_notifications'
    ];
}