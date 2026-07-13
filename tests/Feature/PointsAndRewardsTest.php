<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\Service;
use App\Models\Reward;
use App\Models\Redemption;
use App\Models\PointTransaction;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PointsAndRewardsTest extends TestCase
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
            'points' => 0,
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

        // Define setting for late delivery points
        Setting::setValue('late_delivery_points', '50');
    }

    public function test_late_delivery_awards_points_automatically(): void
    {
        // 1. Create order with estimated_finish in the past
        $order = Order::create([
            'order_number' => 'ORD-PT-01',
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'delivery_method' => 'pickup',
            'status' => Order::STATUS_WASHING,
            'estimated_finish' => now()->subHour(), // 1 hour ago (late)
            'total_price' => 12000,
        ]);

        // 2. Change status to delivered (acting as admin)
        $response = $this->actingAs($this->admin)->patch(route('admin.orders.process', $order->id), [
            'status' => Order::STATUS_DELIVERED,
            'notes' => 'Pesanan sudah diantarkan.',
        ]);

        $response->assertRedirect();
        
        // Refresh customer and assert points
        $this->customer->refresh();
        $this->assertEquals(50, $this->customer->points);

        // Assert transaction log exists
        $this->assertDatabaseHas('point_transactions', [
            'user_id' => $this->customer->id,
            'order_id' => $order->id,
            'amount' => 50,
            'type' => 'earn_late',
        ]);

        // Assert customer received notification
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->customer->id,
            'order_id' => $order->id,
            'title' => '🎁 Poin Kompensasi Terlambat!',
        ]);
    }

    public function test_on_time_delivery_does_not_award_points(): void
    {
        // 1. Create order with estimated_finish in the future
        $order = Order::create([
            'order_number' => 'ORD-PT-02',
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'delivery_method' => 'pickup',
            'status' => Order::STATUS_WASHING,
            'estimated_finish' => now()->addDay(), // 1 day in the future (on-time)
            'total_price' => 12000,
        ]);

        // 2. Change status to delivered
        $response = $this->actingAs($this->admin)->patch(route('admin.orders.process', $order->id), [
            'status' => Order::STATUS_DELIVERED,
            'notes' => 'Tepat waktu.',
        ]);

        $response->assertRedirect();
        
        // Refresh customer and assert points are unchanged
        $this->customer->refresh();
        $this->assertEquals(0, $this->customer->points);
        
        // Assert no transactions were created
        $this->assertEquals(0, PointTransaction::count());
    }

    public function test_customer_can_redeem_rewards_with_sufficient_points(): void
    {
        // 1. Award points to customer first
        $this->customer->update(['points' => 150]);

        // 2. Create a reward
        $reward = Reward::create([
            'name' => 'Voucher Diskon Rp 10.000',
            'description' => 'Diskon laundry',
            'points_cost' => 100,
            'stock' => 5,
            'is_active' => true,
        ]);

        // 3. Redeem reward
        $response = $this->actingAs($this->customer)->post(route('customer.rewards.redeem', $reward->id));

        $response->assertRedirect();
        
        // Refresh customer and reward
        $this->customer->refresh();
        $reward->refresh();

        $this->assertEquals(50, $this->customer->points);
        $this->assertEquals(4, $reward->stock);

        // Assert redemption record exists
        $this->assertDatabaseHas('redemptions', [
            'user_id' => $this->customer->id,
            'reward_id' => $reward->id,
            'points_spent' => 100,
            'status' => 'active',
        ]);

        // Assert transaction log exists
        $this->assertDatabaseHas('point_transactions', [
            'user_id' => $this->customer->id,
            'amount' => -100,
            'type' => 'redeem',
        ]);
    }

    public function test_customer_cannot_redeem_rewards_with_insufficient_points(): void
    {
        // 1. Give customer few points
        $this->customer->update(['points' => 30]);

        // 2. Create a reward
        $reward = Reward::create([
            'name' => 'Gantungan Kunci',
            'description' => 'Merchandise',
            'points_cost' => 50,
            'stock' => 5,
            'is_active' => true,
        ]);

        // 3. Try to redeem reward
        $response = $this->actingAs($this->customer)->post(route('customer.rewards.redeem', $reward->id));

        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Points and stock should remain unchanged
        $this->customer->refresh();
        $reward->refresh();

        $this->assertEquals(30, $this->customer->points);
        $this->assertEquals(5, $reward->stock);
    }

    public function test_admin_can_cancel_and_refund_redemption(): void
    {
        // 1. Setup user with an active redemption
        $this->customer->update(['points' => 50]); // remaining points

        $reward = Reward::create([
            'name' => 'Voucher',
            'description' => 'Voucher',
            'points_cost' => 100,
            'stock' => 9, // originally 10, decremented to 9
            'is_active' => true,
        ]);

        $redemption = Redemption::create([
            'user_id' => $this->customer->id,
            'reward_id' => $reward->id,
            'points_spent' => 100,
            'code' => 'LDRY-TESTCO',
            'status' => 'active',
        ]);

        // 2. Admin cancels the redemption
        $response = $this->actingAs($this->admin)->post(route('admin.redemptions.process', $redemption->id), [
            'status' => 'cancelled',
        ]);

        $response->assertRedirect();

        // Check if points refunded to customer and stock returned to reward
        $this->customer->refresh();
        $reward->refresh();
        $redemption->refresh();

        $this->assertEquals(150, $this->customer->points); // 50 + 100 refund
        $this->assertEquals(10, $reward->stock); // 9 + 1 stock refund
        $this->assertEquals('cancelled', $redemption->status);

        // Assert refund transaction log exists
        $this->assertDatabaseHas('point_transactions', [
            'user_id' => $this->customer->id,
            'amount' => 100,
            'type' => 'refund',
        ]);
    }
}
