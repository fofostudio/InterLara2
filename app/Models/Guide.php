<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guide extends Model
{
    use HasFactory;

    protected $fillable = [
        'guide_number',
        'user_id',
        'value',
        'client',
        'observation',
        'cashier_id',
        'registration_date',
        'status',
    ];

    // Relación con el usuario (operador)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope para filtrar guías por estado
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
    public function point()
{
    return $this->belongsTo(Point::class);
}

    // Método para calcular el total de guías por operador
    public static function totalByOperator($userId)
    {
        return self::where('user_id', $userId)->sum('value');
    }
    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    // Método para obtener el número de guías por operador
    public static function countByOperator($userId)
    {
        return self::where('user_id', $userId)->count();
    }
}
