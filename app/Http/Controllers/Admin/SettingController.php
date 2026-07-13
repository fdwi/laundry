<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        $settings = [
            'business_name' => Setting::getValue('business_name', 'L-DRY'),
            'address' => Setting::getValue('address', 'Jl. Sukacita No. 45, Surabaya, Indonesia'),
            'whatsapp' => Setting::getValue('whatsapp', '081234567890'),
            'operating_hours' => Setting::getValue('operating_hours', '08:00 - 20:00'),
            'late_delivery_points' => Setting::getValue('late_delivery_points', '50'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update settings keys
     */
    public function update(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:20',
            'operating_hours' => 'required|string|max:100',
            'late_delivery_points' => 'required|integer|min:0',
        ]);

        Setting::setValue('business_name', $request->business_name);
        Setting::setValue('address', $request->address);
        Setting::setValue('whatsapp', $request->whatsapp);
        Setting::setValue('operating_hours', $request->operating_hours);
        Setting::setValue('late_delivery_points', $request->late_delivery_points);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan operasional berhasil diperbarui!');
    }
}
