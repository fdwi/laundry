<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Service;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Admin
        $admin = User::create([
            'name' => 'Admin L-Dry Owner',
            'email' => 'admin@ldry.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567890',
            'address' => 'Kantor Pusat L-Dry, Surabaya',
            'is_active' => true,
        ]);

        // 2. Seed Kurir (2 accounts)
        $kurir1 = User::create([
            'name' => 'Kurir Budi',
            'email' => 'kurir1@ldry.com',
            'password' => Hash::make('password'),
            'role' => 'kurir',
            'phone' => '082234567891',
            'address' => 'Mess Karyawan L-Dry, Surabaya',
            'is_active' => true,
        ]);

        $kurir2 = User::create([
            'name' => 'Kurir Ani',
            'email' => 'kurir2@ldry.com',
            'password' => Hash::make('password'),
            'role' => 'kurir',
            'phone' => '082234567892',
            'address' => 'Mess Karyawan L-Dry, Surabaya',
            'is_active' => true,
        ]);

        // 3. Seed Services (4 services)
        $serviceReguler = Service::create([
            'name' => 'Laundry Reguler',
            'slug' => 'laundry-reguler',
            'price_per_kg' => 7000.00,
            'price_per_pcs' => null,
            'unit' => 'kg',
            'duration_hours' => 48,
            'description' => 'Cuci kering setrika reguler selesai dalam 2 hari.',
            'is_active' => true,
        ]);

        $serviceExpress = Service::create([
            'name' => 'Express Service',
            'slug' => 'express-service',
            'price_per_kg' => 12000.00,
            'price_per_pcs' => null,
            'unit' => 'kg',
            'duration_hours' => 24,
            'description' => 'Cuci kering setrika kilat selesai dalam 24 jam.',
            'is_active' => true,
        ]);

        $serviceDry = Service::create([
            'name' => 'Dry Cleaning',
            'slug' => 'dry-cleaning',
            'price_per_kg' => null,
            'price_per_pcs' => 25000.00,
            'unit' => 'pcs',
            'duration_hours' => 72,
            'description' => 'Cuci kering profesional untuk jas, gaun, dan pakaian berbahan sensitif.',
            'is_active' => true,
        ]);

        $serviceSepatu = Service::create([
            'name' => 'Sepatu & Tas',
            'slug' => 'sepatu-tas',
            'price_per_kg' => null,
            'price_per_pcs' => 15000.00,
            'unit' => 'pcs',
            'duration_hours' => 96,
            'description' => 'Pembersihan mendalam dan perawatan untuk sepatu olahraga/kulit dan tas kesayangan.',
            'is_active' => true,
        ]);

        // 4. Seed Customers (5 accounts)
        $customers = [];
        $customerNames = [
            ['name' => 'Andi Wijaya', 'email' => 'customer1@example.com', 'phone' => '085234567801', 'address' => 'Jl. Gubeng Masjid No. 12, Surabaya'],
            ['name' => 'Sinta Permata', 'email' => 'customer2@example.com', 'phone' => '085234567802', 'address' => 'Jl. Raya Darmo No. 44, Surabaya'],
            ['name' => 'Rian Hidayat', 'email' => 'customer3@example.com', 'phone' => '085234567803', 'address' => 'Jl. Kertajaya No. 8, Surabaya'],
            ['name' => 'Citra Lestari', 'email' => 'customer4@example.com', 'phone' => '085234567804', 'address' => 'Jl. Manyar Kertoarjo No. 90, Surabaya'],
            ['name' => 'Eka Putra', 'email' => 'customer5@example.com', 'phone' => '085234567805', 'address' => 'Jl. Tunjungan No. 3, Surabaya']
        ];

        foreach ($customerNames as $c) {
            $customers[] = User::create([
                'name' => $c['name'],
                'email' => $c['email'],
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => $c['phone'],
                'address' => $c['address'],
                'is_active' => true,
            ]);
        }

        // 5. Seed Settings
        Setting::setValue('business_name', 'L-DRY');
        Setting::setValue('address', 'Jl. Sukacita No. 45, Surabaya, Indonesia');
        Setting::setValue('whatsapp', '082332855157');
        Setting::setValue('operating_hours', '08:00 - 20:00');

        // 6. Seed 15 Orders
        $statuses = ['waiting', 'confirmed', 'picked_up', 'washing', 'done', 'ready', 'delivered'];
        $servicesList = [$serviceReguler, $serviceExpress, $serviceDry, $serviceSepatu];

        for ($i = 1; $i <= 15; $i++) {
            $customer = $customers[($i - 1) % 5];
            $service = $servicesList[($i - 1) % 4];
            $status = $statuses[($i - 1) % 7];
            $kurir = ($status === 'waiting') ? null : (($i % 2 === 0) ? $kurir1 : $kurir2);

            $date = Carbon::now()->subDays(15 - $i)->subHours($i * 2);
            $weight = ($status === 'waiting') ? null : (($i % 3 === 0) ? 2.5 : 4.0);
            
            // Calculate total price based on unit type and weight/pcs count
            $pricePerUnit = $service->unit === 'kg' ? $service->price_per_kg : $service->price_per_pcs;
            $totalPrice = $weight ? ($weight * $pricePerUnit) : null;
            $deliveryMethod = ($i % 2 === 0) ? 'pickup' : 'self_drop';

            $order = Order::create([
                'order_number' => 'LDR-' . $date->format('Ymd') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'customer_id' => $customer->id,
                'kurir_id' => $kurir ? $kurir->id : null,
                'service_id' => $service->id,
                'delivery_method' => $deliveryMethod,
                'pickup_address' => ($deliveryMethod === 'pickup') ? $customer->address : null,
                'pickup_datetime' => ($deliveryMethod === 'pickup') ? $date->copy()->addHours(2) : null,
                'weight_kg' => $weight,
                'total_price' => $totalPrice,
                'estimated_finish' => $weight ? $date->copy()->addHours($service->duration_hours) : null,
                'status' => $status,
                'customer_notes' => 'Catatan pesanan nomor ' . $i,
                'kurir_notes' => $weight ? 'Ditimbang oleh kurir.' : null,
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            // Seed logs up to the current status index
            $currentStatusIdx = array_search($status, $statuses);
            for ($j = 0; $j <= $currentStatusIdx; $j++) {
                OrderStatusLog::create([
                    'order_id' => $order->id,
                    'status' => $statuses[$j],
                    'updated_by' => ($j === 0) ? $customer->id : ($kurir ? $kurir->id : $admin->id),
                    'note' => 'Status diubah ke ' . $statuses[$j],
                    'created_at' => $date->copy()->addHours($j * 2),
                ]);
            }
        }
    }
}
