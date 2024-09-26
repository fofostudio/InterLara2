<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    protected $fillable = ['user_id', 'amount', 'description', 'cashier_id', 'status'];

    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function markAsPaid()
    {
        $this->status = self::STATUS_PAID;
        $this->save();
    }

    public function isPaid()
    {
        return $this->status === self::STATUS_PAID;
    }
}
