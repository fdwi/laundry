<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Setting;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $services = Service::where('is_active', true)->get();
        
        // Fetch settings for footer / contact info
        $settings = [
            'business_name' => Setting::getValue('business_name', 'L-DRY'),
            'address' => Setting::getValue('address', 'Jl. Sukacita No. 45, Jakarta Selatan'),
            'whatsapp' => Setting::getValue('whatsapp', '081234567890'),
            'operating_hours' => Setting::getValue('operating_hours', '08:00 - 20:00'),
        ];
        
        return view('welcome', compact('services', 'settings'));
    }
}
