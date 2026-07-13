<?php

namespace App\Models;

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
        'role',
        'phone',
        'address',
        'is_active',
        'points',
        'avatar',
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
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'is_active' => true,
    ];

    // Helper functions for roles
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isKurir(): bool
    {
        return $this->role === 'kurir';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    // Relationships
    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
    }

    public function unreadNotificationsCount(): int
    {
        return $this->notifications()->where('is_read', false)->count();
    }

    public function kurirOrders()
    {
        return $this->hasMany(Order::class, 'kurir_id');
    }

    public function statusLogs()
    {
        return $this->hasMany(OrderStatusLog::class, 'updated_by');
    }

    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class)->orderBy('created_at', 'desc');
    }

    public function redemptions()
    {
        return $this->hasMany(Redemption::class)->orderBy('created_at', 'desc');
    }
}
