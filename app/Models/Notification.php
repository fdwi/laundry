<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'order_id',
        'type',
        'title',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read'    => 'boolean',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Helper: icon per type
    public function getIconAttribute(): string
    {
        return match ($this->type) {
            'reminder'    => 'clock',
            'status_update' => 'refresh-cw',
            default       => 'bell',
        };
    }
}
