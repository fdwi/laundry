<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display financial reports listing with filters
     */
    public function index(Request $request)
    {
        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date)->startOfDay() 
            : Carbon::now()->startOfMonth();
            
        $endDate = $request->filled('end_date') 
            ? Carbon::parse($request->end_date)->endOfDay() 
            : Carbon::now()->endOfDay();

        $query = Order::where('status', Order::STATUS_DELIVERED)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['customer', 'service', 'kurir']);

        $orders = $query->orderBy('created_at', 'asc')->get();
        
        $totalOrders = $orders->count();
        $totalRevenue = $orders->sum('total_price');
        $totalWeight = $orders->where('service.unit', 'kg')->sum('weight_kg');

        return view('admin.reports.index', compact('orders', 'startDate', 'endDate', 'totalOrders', 'totalRevenue', 'totalWeight'));
    }

    /**
     * Export reports to PDF
     */
    public function exportPdf(Request $request)
    {
        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date)->startOfDay() 
            : Carbon::now()->startOfMonth();
            
        $endDate = $request->filled('end_date') 
            ? Carbon::parse($request->end_date)->endOfDay() 
            : Carbon::now()->endOfDay();

        $orders = Order::where('status', Order::STATUS_DELIVERED)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['customer', 'service', 'kurir'])
            ->orderBy('created_at', 'asc')
            ->get();

        $totalOrders = $orders->count();
        $totalRevenue = $orders->sum('total_price');
        $totalWeight = $orders->where('service.unit', 'kg')->sum('weight_kg');

        $businessName = Setting::getValue('business_name', 'L-DRY');
        $address = Setting::getValue('address', 'Jl. Sukacita No. 45, Jakarta Selatan');
        $whatsapp = Setting::getValue('whatsapp', '081234567890');

        $pdf = Pdf::loadView('admin.reports.pdf', compact(
            'orders', 
            'startDate', 
            'endDate', 
            'totalOrders', 
            'totalRevenue', 
            'totalWeight',
            'businessName',
            'address',
            'whatsapp'
        ));

        $fileName = 'L-Dry_Report_' . $startDate->format('Ymd') . '-' . $endDate->format('Ymd') . '.pdf';

        return $pdf->download($fileName);
    }
}
