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
        'digital_wallets',
        'user_id',
        'point_id'  // Asegúrate de que esto esté incluido
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
