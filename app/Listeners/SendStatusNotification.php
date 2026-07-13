<?php

namespace App\Listeners;

use App\Events\OrderStatusUpdated;
use App\Models\Notification;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendStatusNotification implements ShouldQueue
{
    /**
     * Map of status => pesan notifikasi yang ramah.
     */
    private static function getNotificationContent(string $status, string $orderNumber): array
    {
        return match ($status) {
            Order::STATUS_CONFIRMED => [
                'title'   => '✅ Pesanan Dikonfirmasi',
                'message' => "Pesanan #{$orderNumber} telah dikonfirmasi oleh admin. Kurir akan segera menjemput cucian Anda.",
            ],
            Order::STATUS_PICKED_UP => [
                'title'   => '🚚 Cucian Dijemput',
                'message' => "Cucian #{$orderNumber} telah dijemput dan sedang dalam perjalanan ke tempat cuci.",
            ],
            Order::STATUS_WASHING => [
                'title'   => '🫧 Sedang Dicuci',
                'message' => "Cucian #{$orderNumber} sedang dalam proses pencucian. Kami pastikan pakaian Anda bersih!",
            ],
            Order::STATUS_DONE => [
                'title'   => '👕 Cucian Dijemur/Disetrika',
                'message' => "Cucian #{$orderNumber} telah selesai dicuci dan sedang dijemur/disetrika.",
            ],
            Order::STATUS_READY => [
                'title'   => '🎉 Siap Diambil!',
                'message' => "Cucian #{$orderNumber} sudah bersih dan siap untuk diambil atau diantarkan ke alamat Anda.",
            ],
            Order::STATUS_DELIVERED => [
                'title'   => '✔️ Pesanan Selesai',
                'message' => "Pesanan #{$orderNumber} telah selesai diantarkan. Terima kasih telah menggunakan L-DRY!",
            ],
            default => [
                'title'   => '🔔 Status Pesanan Diperbarui',
                'message' => "Status pesanan #{$orderNumber} telah diperbarui.",
            ],
        };
    }

    /**
     * Handle the event.
     */
    public function handle(OrderStatusUpdated $event): void
    {
        $order = $event->order;

        $content = self::getNotificationContent($order->status, $order->order_number);

        Notification::create([
            'user_id'  => $order->customer_id,
            'order_id' => $order->id,
            'type'     => 'status_update',
            'title'    => $content['title'],
            'message'  => $content['message'],
            'is_read'  => false,
        ]);
    }
}
