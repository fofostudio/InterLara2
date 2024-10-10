<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    use HasFactory;

    protected $fillable = [
        'number', 
        'description', 
        'address', 
        'phone', 
        'maxusers', 
        'dateStart', 
        'dateLimit', 
        'status', 
        'resp_user'
    ];

    protected $casts = [
        'dateStart' => 'date',
        'dateLimit' => 'date',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function responsibleUser()
    {
        return $this->belongsTo(User::class, 'resp_user');
    }
}