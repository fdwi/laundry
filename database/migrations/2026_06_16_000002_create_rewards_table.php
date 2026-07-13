<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->integer('points_cost');
            $table->integer('stock')->default(99);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed some initial rewards
        DB::table('rewards')->insert([
            [
                'name' => 'Voucher Diskon Rp 10.000',
                'description' => 'Mendapatkan potongan langsung sebesar Rp 10.000 untuk transaksi laundry Anda berikutnya.',
                'points_cost' => 100,
                'stock' => 999,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Voucher Diskon Rp 25.000',
                'description' => 'Mendapatkan potongan langsung sebesar Rp 25.000 untuk transaksi laundry Anda berikutnya.',
                'points_cost' => 200,
                'stock' => 999,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Free Extra Premium Perfume',
                'description' => 'Tambahan keharuman parfum premium eksklusif pada cucian Anda secara gratis.',
                'points_cost' => 50,
                'stock' => 150,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gantungan Kunci L-Dry',
                'description' => 'Merchandise resmi gantungan kunci akrilik L-DRY dengan desain stylish.',
                'points_cost' => 75,
                'stock' => 50,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rewards');
    }
};
