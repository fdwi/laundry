<?php

namespace App\Helpers;

use App\Models\Order;
use Carbon\Carbon;

class OrderHelpers
{
    /**
     * Generate unique order number (format: LDR-YYYYMMDD-XXX)
     */
    public static function generateOrderNumber(): string
    {
        $todayStr = Carbon::today()->format('Ymd');
        
        // Count how many orders created today
        $count = Order::whereDate('created_at', Carbon::today())->count();
        
        $nextNumber = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
        
        $orderNumber = "LDR-{$todayStr}-{$nextNumber}";
        
        // Ensure uniqueness
        while (Order::where('order_number', $orderNumber)->exists()) {
            $count++;
            $nextNumber = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
            $orderNumber = "LDR-{$todayStr}-{$nextNumber}";
        }
        
        return $orderNumber;
    }

    /**
     * Calculate price: weight × service price
     */
    public static function calculatePrice(float $weight, float $pricePerUnit): float
    {
        return $weight * $pricePerUnit;
    }
}
