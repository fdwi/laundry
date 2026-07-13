<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatusLog extends Model
{
    use HasFactory;

    public $timestamps = false; // we only use created_at

    protected $fillable = [
        'order_id',
        'status',
        'updated_by',
        'note',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
