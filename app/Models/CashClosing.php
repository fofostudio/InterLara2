<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashClosing extends Model
{
    protected $fillable = [
        'date',
        'total_sales',
        'expenses',
        'cash',
        'cancelled_guides',
        'debt',
        'user_id'
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
