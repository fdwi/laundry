<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'price_per_kg',
        'price_per_pcs',
        'unit',
        'duration_hours',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price_per_kg' => 'float',
        'price_per_pcs' => 'float',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'service_id');
    }
}
