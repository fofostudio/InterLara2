<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    use HasFactory;

    protected $fillable = ['number', 'description', 'address', 'phone'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
