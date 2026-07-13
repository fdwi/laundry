<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendEstimationReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     * Cari semua order yang estimated_finish antara 60–65 menit dari sekarang.
     */
    public function handle(): void
    {
        $windowStart = Carbon::now()->addMinutes(60);
        $windowEnd   = Carbon::now()->addMinutes(65);

        $orders = Order::whereBetween('estimated_finish', [$windowStart, $windowEnd])
            ->whereNotIn('status', [Order::STATUS_DELIVERED, Order::STATUS_READY])
            ->get();

        foreach ($orders as $order) {
            // Cek agar tidak kirim duplikat notifikasi
            $alreadySent = Notification::where('order_id', $order->id)
                ->where('type', 'reminder')
                ->where('title', 'like', '%Hampir Selesai%')
                ->exists();

            if ($alreadySent) {
                continue;
            }

            Notification::create([
                'user_id'  => $order->customer_id,
                'order_id' => $order->id,
                'type'     => 'reminder',
                'title'    => '⏰ Cucian Hampir Selesai!',
                'message'  => "Cucian #{$order->order_number} akan selesai sekitar 1 jam lagi, pada pukul {$order->estimated_finish->format('H:i')}. Siapkan jadwal pengambilan Anda!",
                'is_read'  => false,
            ]);

            Log::info("[EstimationReminder] Notifikasi H-1 jam terkirim untuk Order #{$order->order_number} (Customer #{$order->customer_id})");
        }
    }
}
