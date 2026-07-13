<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Events\OrderStatusUpdated;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use App\Models\OrderStatusLog;
use App\Helpers\OrderHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Admin Dashboard
     */
    public function dashboard()
    {
        // Monthly Income (Only from completed orders)
        $monthlyIncome = Order::where('status', Order::STATUS_DELIVERED)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_price');

        $completedOrdersCount = Order::where('status', Order::STATUS_DELIVERED)->count();
        
        $activeKurirCount = User::where('role', 'kurir')->where('is_active', true)->count();
        
        $activeServicesCount = Service::where('is_active', true)->count();

        // Income breakdown per service
        $servicesBreakdown = Service::withCount(['orders' => function ($query) {
                $query->where('status', Order::STATUS_DELIVERED);
            }])
            ->get()
            ->map(function ($service) {
                $service->income = Order::where('service_id', $service->id)
                    ->where('status', Order::STATUS_DELIVERED)
                    ->sum('total_price');
                return $service;
            });

        // 10 recent orders
        $recentOrders = Order::with(['customer', 'service', 'kurir'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'monthlyIncome', 
            'completedOrdersCount', 
            'activeKurirCount', 
            'activeServicesCount',
            'servicesBreakdown',
            'recentOrders'
        ));
    }

    /**
     * Manage all orders list (with delete/cancel action)
     */
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'service', 'kurir']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('delivery_method')) {
            $query->where('delivery_method', $request->delivery_method);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);
        $statuses = Order::getStatusLabels();

        return view('admin.orders.index', compact('orders', 'statuses'));
    }

    /**
     * Process/Update order status, weight, and delivery (Admin override)
     */
    public function process(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(Order::getStatusLabels())),
            'weight_kg' => 'nullable|numeric|min:0.1',
            'estimated_finish' => 'nullable|date|after_or_equal:today',
            'note' => 'nullable|string|max:500',
        ]);

        if ($request->filled('weight_kg')) {
            $order->weight_kg = $request->weight_kg;
            $service = $order->service;
            $pricePerUnit = $service->unit === 'kg' ? $service->price_per_kg : $service->price_per_pcs;
            $order->total_price = OrderHelpers::calculatePrice($request->weight_kg, $pricePerUnit);
        }

        if ($request->filled('estimated_finish')) {
            $order->estimated_finish = Carbon::parse($request->estimated_finish);
        }

        $order->status = $request->status;
        $order->kurir_id = Auth::id(); // Admin processes it
        $order->save();

        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => $request->status,
            'updated_by' => Auth::id(),
            'note' => $request->note ?? ('Status pesanan diperbarui oleh Admin menjadi ' . Order::getStatusLabels()[$request->status]),
        ]);

        // Fire event → triggers SendStatusNotification listener → saves DB notification
        event(new OrderStatusUpdated($order));

        return redirect()->back()->with('success', 'Status pesanan ' . $order->order_number . ' berhasil diperbarui!');
    }

    /**
     * Delete/Cancel order
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        
        // Remove status logs first due to FK constraints (or reliance on cascade)
        $order->statusLogs()->delete();
        $order->delete();

        return redirect()->back()->with('success', 'Pesanan ' . $order->order_number . ' berhasil dihapus dari sistem.');
    }
}
