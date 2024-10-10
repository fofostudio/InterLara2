<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecondExcelData extends Model
{
    use HasFactory;

    protected $fillable = [
        'ADM_NumeroGuia',
        'ADM_CreadoPor',
    ];
    public function point()
    {
        return $this->belongsTo(Point::class);
    }

    public function firstExcelData()
    {
        return $this->belongsTo(FirstExcelData::class, 'ADM_NumeroGuia', 'numero_guia');
    }
}
