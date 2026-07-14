<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Notification;

class NotificationService
{
    /**
     * Send order status notification both in-app and via WhatsApp
     *
     * @param Order $order
     * @return void
     */
    public static function sendOrderStatusNotification(Order $order): void
    {
        $statusLabel = $order->status_label;
        $customer = $order->customer;

        if (!$customer) {
            return;
        }

        $title = "Status Pesanan #{$order->order_number}";
        $message = "Halo {$customer->name}, pesanan laundry Anda (#{$order->order_number}) sekarang berstatus: {$statusLabel}.";

        if ($order->status === Order::STATUS_READY) {
            $message .= " Pakaian Anda sudah bersih dan wangi, siap untuk diambil atau dikirim!";
        } elseif ($order->status === Order::STATUS_DELIVERED) {
            $message .= " Terima kasih telah menggunakan layanan kami. Semoga hari Anda menyenangkan!";
        }

        // 1. Create In-App Notification
        Notification::create([
            'user_id' => $customer->id,
            'order_id' => $order->id,
            'type' => 'status_update',
            'title' => $title,
            'message' => $message,
            'is_read' => false,
        ]);

        // 2. Send WhatsApp Notification
        if (!empty($customer->phone)) {
            $waMessage = "*L-DRY Laundry*\n\n" . $message;
            WhatsAppService::sendMessage($customer->phone, $waMessage);
        }
    }
}
