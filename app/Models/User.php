<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'address',
        'phone',
        'role_id',
        'point_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the role that owns the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function point()
    {
        return $this->belongsTo(Point::class);
    }

    /**
     * Get the guides for the user.
     */
    public function guides()
    {
        return $this->hasMany(Guide::class);
    }

    /**
     * Get the debts for the user.
     */
    public function debts()
    {
        return $this->hasMany(Debt::class);
    }

    /**
     * Check if the user has a specific role.
     *
     * @param string $roleName
     * @return bool
     */
    public function hasRole($roleName)
    {
        return $this->role->name === $roleName;
    }

    /**
     * Get the total value of guides for the user.
     *
     * @return float
     */
    public function getTotalGuidesValue()
    {
        return $this->guides()->sum('value');
    }
    public function getRoleName()
    {
        return $this->role ? $this->role->name : 'Sin rol';
    }

    /**
     * Get the total amount of debts for the user.
     *
     * @return float
     */
    public function getTotalDebtsAmount()
    {
        return $this->debts()->sum('amount');
    }
}
