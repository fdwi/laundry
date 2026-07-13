<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Models\Notification;
use App\Models\MitraProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MitraController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $mitraId = $user->id;

        // Statistics
        $totalToday = Order::where('mitra_id', $mitraId)->whereDate('created_at', Carbon::today())->count();
        $pending = Order::where('mitra_id', $mitraId)->where('status', Order::STATUS_PENDING)->count();
        $inProgress = Order::where('mitra_id', $mitraId)->whereIn('status', [
            Order::STATUS_CONFIRMED,
            Order::STATUS_PICKED_UP,
            Order::STATUS_WASHING
        ])->count();
        $completed = Order::where('mitra_id', $mitraId)->whereIn('status', [
            Order::STATUS_DONE,
            Order::STATUS_DELIVERED
        ])->count();

        // Chart Data (Last 7 Days)
        $chartData = [];
        $chartLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->format('d M');
            $chartData[] = Order::where('mitra_id', $mitraId)->whereDate('created_at', $date)->count();
        }

        // Recent Orders
        $recentOrders = Order::where('mitra_id', $mitraId)
            ->with(['customer', 'service'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('mitra.dashboard', compact('totalToday', 'pending', 'inProgress', 'completed', 'chartLabels', 'chartData', 'recentOrders'));
    }

    public function orders(Request $request)
    {
        $user = Auth::user();
        $query = Order::where('mitra_id', $user->id)->with(['customer', 'service']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);
        $statusLabels = Order::getStatusLabels();

        return view('mitra.orders', compact('orders', 'statusLabels'));
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,confirmed,picked_up,washing,done,delivered',
            'weight_kg' => 'nullable|numeric|min:0.1',
            'note' => 'nullable|string|max:255',
        ]);

        $order = Order::where('mitra_id', Auth::id())->findOrFail($id);
        $oldStatus = $order->status;
        $newStatus = $request->status;

        $updateData = ['status' => $newStatus];

        // If weight is provided, update weight and recalculate price
        if ($request->filled('weight_kg')) {
            $updateData['weight_kg'] = $request->weight_kg;
            $updateData['total_price'] = $order->service->price_per_kg * $request->weight_kg;
        }

        $order->update($updateData);

        // Add Log
        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => $newStatus,
            'changed_by' => Auth::id(),
            'note' => $request->note ?? 'Status diperbarui oleh Mitra.',
        ]);

        // Notify Customer
        Notification::create([
            'user_id' => $order->customer_id,
            'order_id' => $order->id,
            'message' => 'Pesanan #' . $order->id . ' diperbarui ke status: ' . $order->status_label . ($request->filled('weight_kg') ? '. Berat: ' . $request->weight_kg . ' kg, Total: Rp' . number_format($order->total_price, 0, ',', '.') : ''),
            'is_read' => false,
        ]);

        // Dispatch broadcast event
        try {
            event(new \App\Events\OrderStatusUpdated($order));
        } catch (\Exception $e) {
            // Ignore broadcasting failure if driver is not configured
        }

        return redirect()->back()->with('success', 'Status pesanan #' . $order->id . ' berhasil diperbarui!');
    }

    public function profile()
    {
        $user = Auth::user();
        $profile = $user->mitraProfile;
        return view('mitra.profile', compact('user', 'profile'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $profile = $user->mitraProfile;

        $request->validate([
            'name' => 'required|string|max:255',
            'business_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'operating_hours' => 'required|string|max:100',
            'address' => 'required|string',
        ]);

        // Update User info
        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        // Update Mitra Profile info
        $profile->update([
            'business_name' => $request->business_name,
            'address' => $request->address,
            'operating_hours' => $request->operating_hours,
        ]);

        return redirect()->route('mitra.profile')->with('success', 'Profil bisnis Anda berhasil diperbarui!');
    }
}
