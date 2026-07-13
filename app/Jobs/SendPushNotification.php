<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;
    public $message;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order, string $message)
    {
        $this->order = $order;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Simulate sending a real-time push notification via services like OneSignal or FCM
        Log::info("PUSH NOTIFICATION SIMULATION [Order #{$this->order->id}] - Sent to Customer #{$this->order->customer_id}: \"{$this->message}\"");
    }
}
