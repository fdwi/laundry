<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\Service;
use App\Models\Notification;
use App\Jobs\SendEstimationReminder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use App\Events\OrderStatusUpdated;
use Tests\TestCase;

class NotificationFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;
    private Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Create standard roles
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@laundrai.test',
        ]);

        $this->customer = User::factory()->create([
            'role' => 'customer',
            'email' => 'customer@laundrai.test',
        ]);

        $this->service = Service::create([
            'name' => 'Cuci Setrika',
            'slug' => 'cuci-setrika',
            'price_per_kg' => 6000,
            'unit' => 'kg',
            'duration_hours' => 24,
            'description' => 'Cuci dan Setrika rapi',
            'is_active' => true,
        ]);
    }

    public function test_order_status_update_creates_in_app_notification(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-1001',
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'delivery_method' => 'pickup_delivery',
            'status' => Order::STATUS_WAITING,
            'total_price' => 12000,
        ]);

        // Act: Update status as admin
        $response = $this->actingAs($this->admin)->patch(route('admin.orders.process', $order->id), [
            'status' => Order::STATUS_WASHING,
            'estimated_finish' => now()->addHours(2)->toDateTimeString(),
            'notes' => 'Sedang dicuci dengan mesin 1',
        ]);

        $response->assertRedirect();
        
        // Assert order updated
        $order->refresh();
        $this->assertEquals(Order::STATUS_WASHING, $order->status);

        // Assert notification created for customer
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->customer->id,
            'order_id' => $order->id,
            'type' => 'status_update',
            'title' => '🫧 Sedang Dicuci',
        ]);
    }

    public function test_customer_can_retrieve_notifications_api(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-1002',
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'delivery_method' => 'pickup_delivery',
            'status' => Order::STATUS_WAITING,
            'total_price' => 12000,
        ]);

        $notification = Notification::create([
            'user_id' => $this->customer->id,
            'order_id' => $order->id,
            'type' => 'status_update',
            'title' => 'Test Notification',
            'message' => 'Status laundry Anda diperbarui.',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->customer)->getJson(route('notifications.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'notifications',
                'unread_count'
            ])
            ->assertJsonFragment([
                'id' => $notification->id,
                'title' => 'Test Notification',
                'unread_count' => 1,
            ]);
    }

    public function test_customer_can_mark_notification_as_read(): void
    {
        $notification = Notification::create([
            'user_id' => $this->customer->id,
            'type' => 'info',
            'title' => 'Test Info',
            'message' => 'Detail Info',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->customer)->postJson(route('notifications.read', $notification->id));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $notification->refresh();
        $this->assertTrue($notification->is_read);
    }

    public function test_customer_can_mark_all_notifications_as_read(): void
    {
        Notification::create([
            'user_id' => $this->customer->id,
            'type' => 'info',
            'title' => 'Test 1',
            'message' => 'Detail 1',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $this->customer->id,
            'type' => 'info',
            'title' => 'Test 2',
            'message' => 'Detail 2',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->customer)->postJson(route('notifications.read-all'));

        $response->assertStatus(200);

        $this->assertEquals(0, $this->customer->unreadNotificationsCount());
    }

    public function test_customer_can_clear_read_notifications(): void
    {
        $n1 = Notification::create([
            'user_id' => $this->customer->id,
            'type' => 'info',
            'title' => 'Test 1',
            'message' => 'Detail 1',
            'is_read' => true,
        ]);

        $n2 = Notification::create([
            'user_id' => $this->customer->id,
            'type' => 'info',
            'title' => 'Test 2',
            'message' => 'Detail 2',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->customer)->deleteJson(route('notifications.clear-read'));

        $response->assertStatus(200);

        $this->assertDatabaseMissing('notifications', ['id' => $n1->id]);
        $this->assertDatabaseHas('notifications', ['id' => $n2->id]);
    }

    public function test_customer_can_reschedule_order_pickup(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-1003',
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'delivery_method' => 'pickup_delivery',
            'status' => Order::STATUS_WAITING,
            'pickup_datetime' => now()->addDay(),
            'total_price' => 12000,
        ]);

        $newPickupTime = now()->addDays(2)->format('Y-m-d H:i:s');

        $this->withoutExceptionHandling();

        $response = $this->actingAs($this->customer)->patch(route('customer.orders.reschedule', $order->id), [
            'pickup_datetime' => $newPickupTime,
        ]);

        $response->assertRedirect();
        
        $order->refresh();
        $this->assertEquals($newPickupTime, $order->pickup_datetime->format('Y-m-d H:i:s'));
    }

    public function test_send_estimation_reminder_job_creates_notifications(): void
    {
        // Create order with estimated_finish in 62 minutes (which is inside the 60-65 minute window)
        $order = Order::create([
            'order_number' => 'ORD-1004',
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'delivery_method' => 'pickup_delivery',
            'status' => Order::STATUS_WASHING,
            'estimated_finish' => now()->addMinutes(62),
            'total_price' => 12000,
        ]);

        // Run the job
        $job = new SendEstimationReminder();
        $job->handle();

        // Assert notification created
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->customer->id,
            'order_id' => $order->id,
            'type' => 'reminder',
            'title' => '⏰ Cucian Hampir Selesai!',
        ]);

        // Running job again should not create duplicate notification due to protection
        $job->handle();

        $count = Notification::where('order_id', $order->id)
            ->where('type', 'reminder')
            ->count();

        $this->assertEquals(1, $count);
    }
}
