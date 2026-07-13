<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use App\Models\OrderStatusLog;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CustomerController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Active orders are those NOT delivered yet
        $activeOrders = Order::where('customer_id', $user->id)
            ->where('status', '!=', Order::STATUS_DELIVERED)
            ->with(['service', 'mitra.mitraProfile'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('customer.dashboard', compact('activeOrders'));
    }

    public function createOrder()
    {
        $services = Service::all();
        // Load active partners
        $mitras = User::where('role', 'mitra')
            ->whereHas('mitraProfile', function($query) {
                $query->where('is_active', true);
            })
            ->with('mitraProfile')
            ->get();

        return view('customer.orders.create', compact('services', 'mitras'));
    }

    public function storeOrder(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'mitra_id' => 'required|exists:users,id',
            'pickup_address' => 'required|string',
            'pickup_time' => 'required|date|after:now',
            'estimated_weight' => 'required|numeric|min:0.5',
            'notes' => 'nullable|string',
        ]);

        $service = Service::findOrFail($request->service_id);
        $totalPrice = $service->price_per_kg * $request->estimated_weight;

        $order = Order::create([
            'customer_id' => Auth::id(),
            'mitra_id' => $request->mitra_id,
            'service_id' => $request->service_id,
            'status' => Order::STATUS_PENDING,
            'pickup_address' => $request->pickup_address,
            'pickup_time' => $request->pickup_time,
            'weight_kg' => $request->estimated_weight,
            'total_price' => $totalPrice,
            'notes' => $request->notes,
        ]);

        // Add status log
        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => Order::STATUS_PENDING,
            'changed_by' => Auth::id(),
            'note' => 'Pesanan baru dibuat oleh pelanggan.',
        ]);

        // Notify Mitra
        Notification::create([
            'user_id' => $request->mitra_id,
            'order_id' => $order->id,
            'message' => 'Pesanan baru masuk dari ' . Auth::user()->name,
            'is_read' => false,
        ]);

        return redirect()->route('customer.orders.show', $order->id)
            ->with('success', 'Pesanan berhasil dibuat! Menunggu konfirmasi dari mitra.');
    }

    public function showOrder($id)
    {
        $order = Order::where('customer_id', Auth::id())
            ->with(['service', 'mitra.mitraProfile', 'statusLogs.changer'])
            ->findOrFail($id);

        return view('customer.orders.show', compact('order'));
    }

    public function getOrderStatus($id)
    {
        $order = Order::where('customer_id', Auth::id())
            ->with(['statusLogs' => function($q) {
                $q->orderBy('created_at', 'asc');
            }])
            ->findOrFail($id);

        $statuses = ['pending', 'confirmed', 'picked_up', 'washing', 'done', 'delivered'];
        $currentIdx = array_search($order->status, $statuses);

        return response()->json([
            'status' => $order->status,
            'status_label' => $order->status_label,
            'current_idx' => $currentIdx,
            'logs' => $order->statusLogs->map(function($log) {
                return [
                    'status' => $log->status,
                    'label' => Order::getStatusLabels()[$log->status] ?? $log->status,
                    'note' => $log->note,
                    'time' => Carbon::parse($log->created_at)->format('H:i, d M Y'),
                ];
            }),
        ]);
    }

    public function history(Request $request)
    {
        $user = Auth::user();
        $query = Order::where('customer_id', $user->id)
            ->with(['service', 'mitra.mitraProfile']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);
        $statusLabels = Order::getStatusLabels();

        return view('customer.history', compact('orders', 'statusLabels'));
    }
}
