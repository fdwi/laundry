<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Service;
use App\Models\OrderStatusLog;
use App\Models\Setting;
use App\Models\Reward;
use App\Models\PointTransaction;
use App\Models\Redemption;
use App\Models\Notification;
use App\Helpers\OrderHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ApiController extends Controller
{
    /**
     * User Login API
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'phone' => $user->phone,
                'address' => $user->address,
                'points' => $user->points,
                'avatar_url' => $user->avatar ? asset('storage/' . $user->avatar) : null,
            ]
        ], 200);
    }

    /**
     * User Registration API
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'is_active' => true,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'phone' => $user->phone,
                'address' => $user->address,
                'points' => $user->points,
            ]
        ], 201);
    }

    /**
     * Get active services
     */
    public function getServices()
    {
        $services = Service::where('is_active', true)->get();
        return response()->json([
            'success' => true,
            'services' => $services
        ], 200);
    }

    /**
     * Get user profile details
     */
    public function getProfile(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'phone' => $user->phone,
                'address' => $user->address,
                'points' => $user->points,
                'avatar_url' => $user->avatar ? asset('storage/' . $user->avatar) : null,
            ]
        ], 200);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->address = $request->address;

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
                \Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('profiles', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'phone' => $user->phone,
                'address' => $user->address,
                'points' => $user->points,
                'avatar_url' => asset('storage/' . $user->avatar),
            ]
        ], 200);
    }

    /**
     * Fetch user orders history or active orders
     */
    public function getOrders(Request $request)
    {
        $user = $request->user();

        $query = Order::with('service');

        if ($user->role === 'admin') {
            // Admin sees all
        } elseif ($user->role === 'kurir') {
            // Kurir sees all orders they process or available orders to pickup
            $query->where(function($q) use ($user) {
                $q->where('kurir_id', $user->id)
                  ->orWhereNull('kurir_id');
            });
        } else {
            // Customer sees their own
            $query->where('customer_id', $user->id);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'orders' => $orders->map(function($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'service_name' => $order->service?->name ?? 'Laundry',
                    'service_unit' => $order->service?->unit ?? 'kg',
                    'delivery_method' => $order->delivery_method,
                    'pickup_address' => $order->pickup_address,
                    'weight_kg' => $order->weight_kg,
                    'total_price' => $order->total_price,
                    'status' => $order->status,
                    'status_label' => $order->status_label,
                    'payment_status' => $order->payment_status,
                    'payment_status_label' => $order->payment_status_label,
                    'snap_token' => $order->snap_token ? explode('|', $order->snap_token)[0] : null,
                    'created_at' => Carbon::parse($order->created_at)->format('d M Y, H:i'),
                ];
            })
        ], 200);
    }

    /**
     * Place new laundry order
     */
    public function createOrder(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,id',
            'delivery_method' => 'required|in:self_drop,pickup',
            'pickup_address' => 'required_if:delivery_method,pickup|nullable|string',
            'pickup_datetime' => 'required_if:delivery_method,pickup|nullable|string',
            'customer_notes' => 'nullable|string',
            'weight_kg' => 'nullable|numeric',
            'total_price' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $orderNumber = OrderHelpers::generateOrderNumber();

        $order = Order::create([
            'order_number' => $orderNumber,
            'customer_id' => $user->id,
            'service_id' => $request->service_id,
            'delivery_method' => $request->delivery_method,
            'pickup_address' => $request->delivery_method === 'pickup' ? $request->pickup_address : null,
            'pickup_datetime' => $request->delivery_method === 'pickup' && $request->pickup_datetime ? Carbon::parse($request->pickup_datetime) : null,
            'status' => Order::STATUS_WAITING,
            'customer_notes' => $request->customer_notes,
            'weight_kg' => $request->weight_kg,
            'total_price' => $request->total_price,
        ]);

        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => Order::STATUS_WAITING,
            'updated_by' => $user->id,
            'note' => 'Cucian baru dipesan dari aplikasi mobile.',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibuat!',
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'status_label' => $order->status_label,
            ]
        ], 201);
    }

    /**
     * Get single order details with status logs
     */
    public function getOrderDetails(Request $request, $id)
    {
        $user = $request->user();
        $order = Order::where('customer_id', $user->id)
            ->with(['service', 'statusLogs.updater', 'kurir'])
            ->findOrFail($id);

        // Auto-sync payment status from Midtrans if pending
        if ($order->payment_status === 'pending' && $order->snap_token && strpos($order->snap_token, '|') !== false) {
            $parts = explode('|', $order->snap_token);
            $snapToken = $parts[0];
            $midtransOrderId = $parts[1] ?? null;

            if ($midtransOrderId) {
                try {
                    \Midtrans\Config::$serverKey = config('midtrans.server_key');
                    \Midtrans\Config::$isProduction = config('midtrans.is_production', false);
                    
                    $statusResponse = \Midtrans\Transaction::status($midtransOrderId);
                    $transactionStatus = $statusResponse->transaction_status ?? null;
                    $paymentType = $statusResponse->payment_type ?? null;
                    
                    if ($transactionStatus === 'settlement' || $transactionStatus === 'capture') {
                        $order->update([
                            'payment_status' => 'settlement',
                            'payment_method' => $paymentType
                        ]);
                        
                        OrderStatusLog::create([
                            'order_id' => $order->id,
                            'status' => $order->status,
                            'updated_by' => $order->customer_id,
                            'note' => "Pembayaran via Midtrans terverifikasi otomatis: Lunas ({$paymentType}).",
                        ]);

                        Notification::create([
                            'user_id' => $order->customer_id,
                            'order_id' => $order->id,
                            'type' => 'info',
                            'title' => 'Pembayaran Berhasil! 💳',
                            'message' => "Pembayaran sebesar Rp " . number_format($order->total_price, 0, ',', '.') . " via {$paymentType} untuk pesanan #{$order->order_number} telah diterima.",
                            'is_read' => false,
                        ]);
                        
                        // Reload logs relationship
                        $order->load(['statusLogs.updater']);
                    }
                } catch (\Exception $e) {
                    \Log::error('Auto-sync Midtrans failed for Order #' . $order->order_number . ': ' . $e->getMessage());
                }
            }
        }

        // Auto-update whatsapp number in database settings if it is the old dummy number
        try {
            $whatsappSetting = Setting::where('key', 'whatsapp')->first();
            if (!$whatsappSetting) {
                Setting::create(['key' => 'whatsapp', 'value' => '082332855157']);
            } elseif ($whatsappSetting->value === '081234567890' || $whatsappSetting->value === '6281234567890') {
                $whatsappSetting->update(['value' => '082332855157']);
            }
        } catch (\Exception $e) {
            \Log::error('Auto-update whatsapp setting failed: ' . $e->getMessage());
        }

        $whatsappNumber = Setting::getValue('whatsapp', '082332855157');
        $waMessage = rawurlencode("Halo L-Dry, saya ingin bertanya tentang status pesanan saya dengan nomor " . $order->order_number);
        $whatsappUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $whatsappNumber) . "?text=" . $waMessage;

        $snapTokenVal = $order->snap_token ? explode('|', $order->snap_token)[0] : null;

        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'service_name' => $order->service?->name ?? 'Laundry',
                'service_unit' => $order->service?->unit ?? 'kg',
                'delivery_method' => $order->delivery_method,
                'pickup_address' => $order->pickup_address,
                'pickup_datetime' => $order->pickup_datetime ? Carbon::parse($order->pickup_datetime)->format('d M Y, H:i') : null,
                'weight_kg' => $order->weight_kg,
                'total_price' => $order->total_price,
                'estimated_finish' => $order->estimated_finish ? Carbon::parse($order->estimated_finish)->format('d M Y, H:i') : null,
                'status' => $order->status,
                'status_label' => $order->status_label,
                'payment_status' => $order->payment_status,
                'payment_status_label' => $order->payment_status_label,
                'snap_token' => $snapTokenVal,
                'whatsapp_url' => $whatsappUrl,
                'logs' => $order->statusLogs->map(function ($log) {
                    return [
                        'status_label' => Order::getStatusLabels()[$log->status] ?? $log->status,
                        'created_at'   => Carbon::parse($log->created_at)->format('d M Y, H:i'),
                        'updater_name' => $log->updater?->name ?? 'Sistem',
                        'note'         => $log->note,
                    ];
                })
            ]
        ], 200);
    }

    /**
     * Reschedule pickup datetime
     */
    public function rescheduleOrder(Request $request, $id)
    {
        $user = $request->user();
        $order = Order::where('customer_id', $user->id)
            ->whereNotIn('status', [Order::STATUS_DELIVERED])
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'pickup_datetime' => 'required|date|after:now',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $order->pickup_datetime = Carbon::parse($request->pickup_datetime);
        $order->save();

        OrderStatusLog::create([
            'order_id'   => $order->id,
            'status'     => $order->status,
            'updated_by' => $user->id,
            'note'       => 'Pelanggan mengubah jadwal pengambilan ke: ' . Carbon::parse($request->pickup_datetime)->format('d M Y, H:i') . ' via mobile app.',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal pengambilan berhasil diubah!',
            'pickup_datetime' => Carbon::parse($order->pickup_datetime)->format('d M Y, H:i'),
        ], 200);
    }

    /**
     * Get Midtrans payment token
     */
    public function getPaymentToken(Request $request, $id)
    {
        $user = $request->user();
        $order = Order::where('customer_id', $user->id)
            ->whereNotIn('payment_status', ['settlement'])
            ->findOrFail($id);

        if (!$order->total_price || $order->total_price <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Total biaya pesanan belum ditentukan oleh Admin.',
            ], 400);
        }

        $isProduction = config('midtrans.is_production', false);
        $redirectBase = $isProduction 
            ? 'https://app.midtrans.com/snap/v2/vtweb/' 
            : 'https://app.sandbox.midtrans.com/snap/v2/vtweb/';

        if ($order->snap_token) {
            $parts = explode('|', $order->snap_token);
            $snapToken = $parts[0];
            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'redirect_url' => $redirectBase . $snapToken,
            ], 200);
        }

        // Initialize Midtrans configuration
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = $isProduction;
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized', true);
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds', true);

        $midtransOrderId = $order->order_number . '-' . time();
        $params = [
            'transaction_details' => [
                'order_id' => $midtransOrderId,
                'gross_amount' => (int) $order->total_price,
            ],
            'customer_details' => [
                'first_name' => $order->customer->name,
                'email' => $order->customer->email,
                'phone' => $order->customer->phone,
            ],
            'item_details' => [
                [
                    'id' => $order->service->id,
                    'price' => (int) $order->total_price,
                    'quantity' => 1,
                    'name' => 'Layanan ' . $order->service->name,
                ]
            ],
            'expiry' => [
                'start_time' => date("Y-m-d H:i:s O"),
                'unit' => 'hours',
                'duration' => 24
            ],
            'callbacks' => [
                'notification_url' => route('payment.notification'),
            ]
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            
            $order->update([
                'snap_token' => $snapToken . '|' . $midtransOrderId,
                'payment_status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'redirect_url' => $redirectBase . $snapToken,
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Midtrans Exception: ' . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Gagal terhubung dengan layanan pembayaran: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get active rewards catalog
     */
    public function getRewards()
    {
        $rewards = Reward::where('is_active', true)
            ->where('stock', '>', 0)
            ->orderBy('points_cost', 'asc')
            ->get();
        return response()->json([
            'success' => true,
            'rewards' => $rewards
        ], 200);
    }

    /**
     * Claim/Redeem a reward using points
     */
    public function redeemReward(Request $request, $id)
    {
        $reward = Reward::where('is_active', true)->findOrFail($id);
        $user = $request->user();

        if ($reward->stock <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Stok hadiah ini sudah habis.'
            ], 400);
        }

        if ($user->points < $reward->points_cost) {
            return response()->json([
                'success' => false,
                'message' => 'Poin Anda tidak mencukupi.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Deduct user points
            $user->decrement('points', $reward->points_cost);

            // Deduct reward stock
            $reward->decrement('stock', 1);

            // Generate unique voucher/redemption code
            $code = 'LDRY-' . strtoupper(Str::random(6));
            while (Redemption::where('code', $code)->exists()) {
                $code = 'LDRY-' . strtoupper(Str::random(6));
            }

            // Create Redemption
            $redemption = Redemption::create([
                'user_id' => $user->id,
                'reward_id' => $reward->id,
                'points_spent' => $reward->points_cost,
                'code' => $code,
                'status' => 'active',
            ]);

            // Create Point Transaction
            PointTransaction::create([
                'user_id' => $user->id,
                'amount' => -$reward->points_cost,
                'type' => 'redeem',
                'description' => "Penukaran hadiah: {$reward->name} ({$code})",
            ]);

            // Create notification
            Notification::create([
                'user_id' => $user->id,
                'type' => 'info',
                'title' => '🎉 Klaim Hadiah Berhasil!',
                'message' => "Anda telah berhasil menukarkan {$reward->points_cost} poin dengan {$reward->name}. Kode voucher Anda: {$code}.",
                'is_read' => false,
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "Berhasil menukarkan poin untuk {$reward->name}! Voucher Anda: {$code}",
                'redemption' => [
                    'id' => $redemption->id,
                    'code' => $redemption->code,
                    'status' => $redemption->status,
                    'points_spent' => $redemption->points_spent,
                ],
                'updated_points' => $user->points,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat memproses penukaran poin.'
            ], 500);
        }
    }

    /**
     * Get user point transaction history
     */
    public function getPointTransactions(Request $request)
    {
        $user = $request->user();
        $transactions = PointTransaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json([
            'success' => true,
            'transactions' => $transactions->map(function($t) {
                return [
                    'id' => $t->id,
                    'amount' => $t->amount,
                    'type' => $t->type,
                    'description' => $t->description,
                    'created_at' => Carbon::parse($t->created_at)->format('d M Y, H:i'),
                ];
            })
        ], 200);
    }

    /**
     * Get user redeemed vouchers
     */
    public function getRedemptions(Request $request)
    {
        $user = $request->user();
        $redemptions = Redemption::where('user_id', $user->id)
            ->with('reward')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json([
            'success' => true,
            'redemptions' => $redemptions->map(function($r) {
                return [
                    'id' => $r->id,
                    'reward_name' => $r->reward?->name ?? 'Hadiah',
                    'reward_description' => $r->reward?->description ?? '',
                    'points_spent' => $r->points_spent,
                    'code' => $r->code,
                    'status' => $r->status,
                    'created_at' => Carbon::parse($r->created_at)->format('d M Y, H:i'),
                ];
            })
        ], 200);
    }
}
