<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use App\Models\Setting;
use App\Models\OrderStatusLog;
use App\Helpers\OrderHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Customer Dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        $totalOrders = Order::where('customer_id', $user->id)->count();
        
        $activeOrders = Order::where('customer_id', $user->id)
            ->whereNotIn('status', [Order::STATUS_DELIVERED])
            ->count();
            
        $completedOrders = Order::where('customer_id', $user->id)
            ->where('status', Order::STATUS_DELIVERED)
            ->count();
            
        $recentOrders = Order::where('customer_id', $user->id)
            ->with('service')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        return view('customer.dashboard', compact('totalOrders', 'activeOrders', 'completedOrders', 'recentOrders'));
    }

    /**
     * Show order placement form
     */
    public function create()
    {
        $services = Service::where('is_active', true)->get();
        return view('customer.orders.create', compact('services'));
    }

    /**
     * Store new order
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'delivery_method' => 'required|in:self_drop,pickup',
            'pickup_address' => 'required_if:delivery_method,pickup|nullable|string',
            'pickup_datetime' => 'required_if:delivery_method,pickup|nullable|date|after_or_equal:today',
            'customer_notes' => 'nullable|string',
        ]);

        $orderNumber = OrderHelpers::generateOrderNumber();

        $order = Order::create([
            'order_number' => $orderNumber,
            'customer_id' => Auth::id(),
            'service_id' => $request->service_id,
            'delivery_method' => $request->delivery_method,
            'pickup_address' => $request->delivery_method === 'pickup' ? $request->pickup_address : null,
            'pickup_datetime' => $request->delivery_method === 'pickup' ? Carbon::parse($request->pickup_datetime) : null,
            'status' => Order::STATUS_WAITING,
            'customer_notes' => $request->customer_notes,
        ]);

        // Log the initial status
        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => Order::STATUS_WAITING,
            'updated_by' => Auth::id(),
            'note' => 'Cucian baru dipesan oleh Pelanggan.',
        ]);

        return redirect()->route('customer.orders.show', $order->id)
            ->with('success', 'Pesanan laundry berhasil dibuat!');
    }

    /**
     * Order history
     */
    public function history(Request $request)
    {
        $user = Auth::user();

        $query = Order::where('customer_id', $user->id)->with('service');

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

        $orders   = $query->orderBy('created_at', 'desc')->paginate(10);
        $statuses = Order::getStatusLabels();

        return view('customer.orders.history', compact('orders', 'statuses'));
    }

    /**
     * Track / view single order details
     */
    public function show($id)
    {
        $order = Order::where('customer_id', Auth::id())
            ->with(['service', 'statusLogs.updater', 'kurir'])
            ->findOrFail($id);

        $whatsappNumber = Setting::getValue('whatsapp', '081234567890');
        
        // Format message for WhatsApp link
        $waMessage = rawurlencode("Halo L-Dry, saya ingin bertanya tentang status pesanan saya dengan nomor " . $order->order_number);
        $whatsappUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $whatsappNumber) . "?text=" . $waMessage;

        return view('customer.orders.show', compact('order', 'whatsappUrl'));
    }

    /**
     * Poll order status for auto-refresh
     */
    public function status($id)
    {
        $order = Order::where('customer_id', Auth::id())->findOrFail($id);

        return response()->json([
            'status'             => $order->status,
            'status_label'       => $order->status_label,
            'status_badge_class' => $order->status_badge_class,
            'payment_status'     => $order->payment_status,
            'payment_status_label' => $order->payment_status_label,
            'total_price'        => $order->total_price ? 'Rp ' . number_format($order->total_price, 0, ',', '.') : 'Belum dihitung',
            'weight_kg'          => $order->weight_kg ? $order->weight_kg . ' kg' : '-',
            'estimated_finish'   => $order->estimated_finish ? $order->estimated_finish->format('d M Y H:i') : '-',
            'logs'               => $order->statusLogs()->with('updater')->get()->map(function ($log) {
                return [
                    'status_label' => Order::getStatusLabels()[$log->status] ?? $log->status,
                    'created_at'   => Carbon::parse($log->created_at)->format('d M Y H:i'),
                    'updater_name' => $log->updater->name ?? 'Sistem',
                    'note'         => $log->note,
                ];
            }),
        ]);
    }

    /**
     * Ubah jadwal pengambilan oleh customer
     */
    public function reschedule(Request $request, $id)
    {
        $order = Order::where('customer_id', Auth::id())
            ->whereNotIn('status', [Order::STATUS_DELIVERED])
            ->findOrFail($id);

        $request->validate([
            'pickup_datetime' => 'required|date|after:now',
        ], [
            'pickup_datetime.required' => 'Pilih jadwal pengambilan terlebih dahulu.',
            'pickup_datetime.after'    => 'Jadwal pengambilan harus di masa depan.',
        ]);

        $order->pickup_datetime = Carbon::parse($request->pickup_datetime);
        $order->save();

        // Log status (informational)
        OrderStatusLog::create([
            'order_id'   => $order->id,
            'status'     => $order->status,
            'updated_by' => Auth::id(),
            'note'       => 'Pelanggan mengubah jadwal pengambilan ke: ' . Carbon::parse($request->pickup_datetime)->format('d M Y, H:i'),
        ]);

        return redirect()->route('customer.orders.show', $order->id)
            ->with('success', 'Jadwal pengambilan berhasil diubah ke ' . Carbon::parse($request->pickup_datetime)->format('d M Y, H:i') . '!');
    }
}
