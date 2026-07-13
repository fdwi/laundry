<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Notification;
use App\Models\OrderStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class PaymentController extends Controller
{
    /**
     * Konfigurasi dasar SDK Midtrans
     */
    private function initMidtrans(): void
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Dapatkan/buat Snap Token dari Midtrans untuk pembayaran customer
     */
    public function getSnapToken($id)
    {
        $order = Order::where('customer_id', Auth::id())
            ->whereNotIn('payment_status', ['settlement'])
            ->findOrFail($id);

        if (!$order->total_price || $order->total_price <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Total biaya pesanan belum ditentukan oleh Admin.',
            ], 400);
        }

        // Jika snap_token sudah ada dan valid di database, kembalikan langsung untuk menghemat API call
        if ($order->snap_token) {
            return response()->json([
                'success' => true,
                'snap_token' => $order->snap_token,
            ]);
        }

        $this->initMidtrans();

        // Siapkan parameter transaksi
        $params = [
            'transaction_details' => [
                'order_id' => $order->order_number . '-' . time(), // Tambah timestamp agar ID transaksi selalu unik
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
            
            // Simpan snap_token ke database
            $order->update([
                'snap_token' => $snapToken,
                'payment_status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
            ]);
        } catch (Exception $e) {
            Log::error('Gagal mendapatkan Snap Token Midtrans: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal terhubung dengan layanan pembayaran.',
            ], 500);
        }
    }

    /**
     * Tangani Webhook/Notifikasi dari Midtrans secara asinkronus
     */
    public function handleNotification(Request $request)
    {
        $this->initMidtrans();

        try {
            $notif = app(\Midtrans\Notification::class);
        } catch (Exception $e) {
            Log::error('Notifikasi Midtrans Gagal Diparsing: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Invalid notification payload'
            ], 400);
        }

        // Mendukung objek notifikasi asli (Midtrans SDK) maupun objek tiruan (Mock) untuk pengujian
        $responseObj = (method_exists($notif, 'getResponse') && $notif->getResponse()) ? $notif->getResponse() : null;
        
        $midtransOrderId = $responseObj ? ($responseObj->order_id ?? $notif->order_id) : $notif->order_id;
        $transactionStatus = $responseObj ? ($responseObj->transaction_status ?? $notif->transaction_status) : $notif->transaction_status;
        $paymentType = $responseObj ? ($responseObj->payment_type ?? $notif->payment_type) : $notif->payment_type;
        $fraudStatus = $responseObj ? ($responseObj->fraud_status ?? $notif->fraud_status) : $notif->fraud_status;

        // Midtrans Order ID dikirim dalam format "ORDER_NUMBER-TIMESTAMP"
        $parts = explode('-', $midtransOrderId ?? '');
        if (count($parts) > 1) {
            array_pop($parts); // Hapus timestamp
            $orderNumber = implode('-', $parts);
        } else {
            $orderNumber = $midtransOrderId;
        }

        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            Log::error("Order #{$orderNumber} tidak ditemukan saat memproses Webhook Midtrans.");
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        Log::info("Webhook Midtrans Order #{$order->order_number}: Status = {$transactionStatus}, Tipe = {$paymentType}");

        // Petakan status transaksi Midtrans ke status pembayaran lokal
        $localStatus = 'pending';
        $isSuccess = false;

        if ($transactionStatus == 'capture') {
            // Untuk metode Credit Card
            if ($fraudStatus == 'challenge') {
                $localStatus = 'pending';
            } else {
                $localStatus = 'settlement';
                $isSuccess = true;
            }
        } elseif ($transactionStatus == 'settlement') {
            $localStatus = 'settlement';
            $isSuccess = true;
        } elseif ($transactionStatus == 'pending') {
            $localStatus = 'pending';
        } elseif (in_array($transactionStatus, ['deny', 'cancel'])) {
            $localStatus = 'cancel';
        } elseif ($transactionStatus == 'expire') {
            $localStatus = 'expire';
        }

        // Simpan pembaruan status pembayaran
        $order->update([
            'payment_status' => $localStatus,
            'payment_method' => $paymentType
        ]);

        // Catat ke log status
        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => $order->status,
            'updated_by' => $order->customer_id, // Atas nama customer
            'note' => "Pembayaran via Midtrans status: {$order->payment_status_label} ({$paymentType}).",
        ]);

        // Kirim notifikasi in-app ke customer jika pembayaran berhasil
        if ($isSuccess) {
            Notification::create([
                'user_id' => $order->customer_id,
                'order_id' => $order->id,
                'type' => 'info',
                'title' => 'Pembayaran Berhasil! 💳',
                'message' => "Pembayaran sebesar Rp " . number_format($order->total_price, 0, ',', '.') . " via {$paymentType} untuk pesanan #{$order->order_number} telah diterima.",
                'is_read' => false,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification processed successfully'
        ]);
    }
}
