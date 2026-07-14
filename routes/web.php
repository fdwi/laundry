<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\RewardController as CustomerRewardController;
use App\Http\Controllers\Kurir\OrderController as KurirOrderController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\KurirController as AdminKurirController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\RewardController as AdminRewardController;
use App\Http\Controllers\Admin\RedemptionController as AdminRedemptionController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::post('/payment/midtrans-notification', [PaymentController::class, 'handleNotification'])->name('payment.notification');

// Shared Auth routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Customer Portal Route Group
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerOrderController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders/create', [CustomerOrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [CustomerOrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/history', [CustomerOrderController::class, 'history'])->name('orders.history');
    Route::get('/orders/{id}', [CustomerOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{id}/status', [CustomerOrderController::class, 'status'])->name('orders.status');
    Route::patch('/orders/{id}/reschedule', [CustomerOrderController::class, 'reschedule'])->name('orders.reschedule');
    Route::get('/orders/{id}/payment-token', [PaymentController::class, 'getSnapToken'])->name('orders.payment-token');
    
    // Rewards & Points Catalog
    Route::get('/rewards', [CustomerRewardController::class, 'index'])->name('rewards.index');
    Route::post('/rewards/{id}/redeem', [CustomerRewardController::class, 'redeem'])->name('rewards.redeem');
});

// Notification routes (shared, any authenticated user)
Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
    Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
    Route::delete('/clear-read', [NotificationController::class, 'clearRead'])->name('clear-read');
});

// Kurir Portal Route Group
Route::middleware(['auth', 'role:kurir'])->prefix('kurir')->name('kurir.')->group(function () {
    Route::get('/dashboard', [KurirOrderController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [KurirOrderController::class, 'index'])->name('orders.index');
    Route::patch('/orders/{id}/process', [KurirOrderController::class, 'process'])->name('orders.process');
    Route::get('/pickup', [KurirOrderController::class, 'pickup'])->name('orders.pickup');
    Route::post('/pickup/{id}/mark', [KurirOrderController::class, 'markPickedUp'])->name('orders.pickup.mark');
});

// Admin Panel Route Group
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminOrderController::class, 'dashboard'])->name('dashboard');
    
    // Order management
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::patch('/orders/{id}/process', [AdminOrderController::class, 'process'])->name('orders.process');
    Route::delete('/orders/{id}/delete', [AdminOrderController::class, 'destroy'])->name('orders.destroy');
    
    // Services CRUD
    Route::get('/services', [AdminServiceController::class, 'index'])->name('services.index');
    Route::get('/services/create', [AdminServiceController::class, 'create'])->name('services.create');
    Route::post('/services', [AdminServiceController::class, 'store'])->name('services.store');
    Route::get('/services/{id}/edit', [AdminServiceController::class, 'edit'])->name('services.edit');
    Route::put('/services/{id}', [AdminServiceController::class, 'update'])->name('services.update');
    Route::post('/services/{id}/toggle', [AdminServiceController::class, 'toggleStatus'])->name('services.toggle');
    
    // Kurir CRUD
    Route::get('/kurir', [AdminKurirController::class, 'index'])->name('kurir.index');
    Route::get('/kurir/create', [AdminKurirController::class, 'create'])->name('kurir.create');
    Route::post('/kurir', [AdminKurirController::class, 'store'])->name('kurir.store');
    Route::get('/kurir/{id}/edit', [AdminKurirController::class, 'edit'])->name('kurir.edit');
    Route::put('/kurir/{id}', [AdminKurirController::class, 'update'])->name('kurir.update');
    Route::get('/kurir/{id}/orders', [AdminKurirController::class, 'orders'])->name('kurir.orders');

    // Reports & Financials
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [AdminReportController::class, 'exportPdf'])->name('reports.export');

    // Settings
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');

    // User / Customer management
    Route::get('/customers', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/customers/{id}/toggle', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');

    // Rewards CRUD Management
    Route::resource('rewards', AdminRewardController::class)->except(['show']);

    // Redemptions management
    Route::get('/redemptions', [AdminRedemptionController::class, 'index'])->name('redemptions.index');
    Route::post('/redemptions/{id}/process', [AdminRedemptionController::class, 'process'])->name('redemptions.process');
});

// Fallback/Redirect dashboard route for Breeze compatibility
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role === 'kurir') {
        return redirect()->route('kurir.dashboard');
    } else {
        return redirect()->route('customer.dashboard');
    }
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

// Route untuk jalankan migrasi database di hosting tanpa SSH (dilindungi token keamanan)
Route::get('/run-migration-laundrai', function (\Illuminate\Http\Request $request) {
    if ($request->query('token') !== 'laundraisurabaya2026') {
        abort(403, 'Akses Ditolak.');
    }

    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return 'Migrasi Database Berhasil Dijalankan!';
    } catch (\Exception $e) {
        return 'Gagal Migrasi: ' . $e->getMessage();
    }
});

// Route untuk membersihkan cache di hosting tanpa SSH (dilindungi token keamanan)
Route::get('/clear-laundrai-cache', function (\Illuminate\Http\Request $request) {
    if ($request->query('token') !== 'laundraisurabaya2026') {
        abort(403, 'Akses Ditolak.');
    }

    try {
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        return 'Semua Cache Aplikasi Berhasil Dibersihkan!';
    } catch (\Exception $e) {
        return 'Gagal Clear Cache: ' . $e->getMessage();
    }
});

// Route untuk mengetes WhatsApp secara langsung
Route::get('/test-wa-laundrai', function (\Illuminate\Http\Request $request) {
    $phone = $request->query('phone');
    if (!$phone) {
        return 'Masukkan nomor HP di URL. Contoh: /test-wa-laundrai?phone=08123456789';
    }
    
    $token = env('FONNTE_TOKEN');
    if (empty($token)) {
        return 'Gagal: FONNTE_TOKEN kosong di .env cPanel Anda! Pastikan sudah diisi dan disave.';
    }
    
    try {
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => $token,
        ])->withoutVerifying()->post('https://api.fonnte.com/send', [
            'target' => $phone,
            'message' => 'Tes koneksi Fonnte API dari website Laundry L-Dry!',
            'countryCode' => '62',
        ]);
        
        return response()->json([
            'http_status' => $response->status(),
            'fonnte_response' => $response->json() ?? $response->body()
        ]);
    } catch (\Exception $e) {
        return 'Error Koneksi: ' . $e->getMessage();
    }
});

