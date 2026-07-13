<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Redemption extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reward_id',
        'points_spent',
        'code',
        'status', // active, used, completed, cancelled
    ];

    protected $casts = [
        'user_id' => 'integer',
        'reward_id' => 'integer',
        'points_spent' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }
}
