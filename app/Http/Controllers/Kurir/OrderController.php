<?php

namespace App\Http\Controllers\Kurir;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use App\Models\OrderStatusLog;
use App\Helpers\OrderHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Kurir Dashboard
     */
    public function dashboard()
    {
        $todayOrders = Order::whereDate('created_at', Carbon::today())->count();
        $waitingOrders = Order::where('status', Order::STATUS_WAITING)->count();
        $washingOrders = Order::where('status', Order::STATUS_WASHING)->count();
        $doneOrders = Order::where('status', Order::STATUS_DONE)->count();

        $activeOrders = Order::whereNotIn('status', [Order::STATUS_DELIVERED])
            ->with(['customer', 'service'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('kurir.dashboard', compact('todayOrders', 'waitingOrders', 'washingOrders', 'doneOrders', 'activeOrders'));
    }

    /**
     * Manage Orders list
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

        return view('kurir.orders.index', compact('orders', 'statuses'));
    }

    /**
     * Process/Update order status, weight, and delivery
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

        // Perform weight-based calculations if weight is updated/provided
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
        $order->kurir_id = Auth::id(); // Assign kurir handling the order
        $order->save();

        // Log status change
        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => $request->status,
            'updated_by' => Auth::id(),
            'note' => $request->note ?? ('Status pesanan diperbarui menjadi ' . Order::getStatusLabels()[$request->status]),
        ]);

        return redirect()->back()->with('success', 'Status pesanan ' . $order->order_number . ' berhasil diperbarui!');
    }

    /**
     * List pickup delivery schedules
     */
    public function pickup()
    {
        $pickups = Order::where('delivery_method', 'pickup')
            ->whereIn('status', [Order::STATUS_WAITING, Order::STATUS_CONFIRMED])
            ->with(['customer', 'service'])
            ->orderBy('pickup_datetime', 'asc')
            ->get();

        return view('kurir.orders.pickup', compact('pickups'));
    }

    /**
     * Mark order as picked up
     */
    public function markPickedUp($id)
    {
        $order = Order::findOrFail($id);

        $order->status = Order::STATUS_PICKED_UP;
        $order->kurir_id = Auth::id();
        $order->save();

        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => Order::STATUS_PICKED_UP,
            'updated_by' => Auth::id(),
            'note' => 'Pakaian telah berhasil dijemput oleh kurir dan tiba di workshop.',
        ]);

        return redirect()->back()->with('success', 'Pesanan ' . $order->order_number . ' ditandai telah dijemput!');
    }
}
