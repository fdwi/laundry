<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\Service;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MidtransPaymentTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private User $admin;
    private Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create([
            'role' => 'customer',
            'email' => 'customer@laundrai.test',
        ]);

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->service = Service::create([
            'name' => 'Layanan Cuci',
            'slug' => 'layanan-cuci',
            'price_per_kg' => 5000,
            'unit' => 'kg',
            'duration_hours' => 24,
            'is_active' => true,
        ]);
    }

    public function test_get_snap_token_fails_if_total_price_not_set(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-999',
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'delivery_method' => 'pickup',
            'total_price' => null,
        ]);

        $response = $this->actingAs($this->customer)
            ->getJson(route('customer.orders.payment-token', $order->id));

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Total biaya pesanan belum ditentukan oleh Admin.',
            ]);
    }

    public function test_get_snap_token_returns_existing_token_if_available(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-998',
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'delivery_method' => 'pickup',
            'total_price' => 15000,
            'snap_token' => 'existing-token-xyz',
        ]);

        $response = $this->actingAs($this->customer)
            ->getJson(route('customer.orders.payment-token', $order->id));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'snap_token' => 'existing-token-xyz',
            ]);
    }

    public function test_get_snap_token_generates_new_token(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-997',
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'delivery_method' => 'pickup',
            'total_price' => 15000,
            'snap_token' => null,
        ]);

        // Mock \Midtrans\Snap
        $mockSnap = \Mockery::mock('alias:\Midtrans\Snap');
        $mockSnap->shouldReceive('getSnapToken')
            ->once()
            ->andReturn('newly-generated-snap-token-123');

        $response = $this->actingAs($this->customer)
            ->getJson(route('customer.orders.payment-token', $order->id));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'snap_token' => 'newly-generated-snap-token-123',
            ]);

        $order->refresh();
        $this->assertEquals('newly-generated-snap-token-123', $order->snap_token);
        $this->assertEquals('pending', $order->payment_status);
    }

    public function test_handle_notification_updates_payment_status(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-996',
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'delivery_method' => 'pickup',
            'total_price' => 15000,
            'payment_status' => 'pending',
        ]);

        // Mock \Midtrans\Notification using container binding
        $this->app->bind(\Midtrans\Notification::class, function () {
            $mock = \Mockery::mock(\Midtrans\Notification::class);
            $responseObj = new \stdClass();
            $responseObj->order_id = 'ORD-996-123456';
            $responseObj->transaction_status = 'settlement';
            $responseObj->payment_type = 'gopay';
            $responseObj->fraud_status = 'accept';

            $mock->shouldReceive('getResponse')->andReturn($responseObj);
            return $mock;
        });

        $this->withoutExceptionHandling();

        $response = $this->postJson(route('payment.notification'), [
            'order_id' => 'ORD-996-123456',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Notification processed successfully',
            ]);

        $order->refresh();
        $this->assertEquals('settlement', $order->payment_status);
        $this->assertEquals('gopay', $order->payment_method);

        // Verify in-app notification created
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->customer->id,
            'order_id' => $order->id,
            'type' => 'info',
            'title' => 'Pembayaran Berhasil! 💳',
        ]);
    }
}
