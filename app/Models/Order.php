<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Setting;
use App\Models\PointTransaction;
use App\Models\Notification;

class Order extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::updating(function (Order $order) {
            // Check if status is transitioning to delivered
            if ($order->isDirty('status') && $order->status === self::STATUS_DELIVERED) {
                // Check if it is late
                if ($order->estimated_finish && now()->gt($order->estimated_finish)) {
                    $order->awardLateDeliveryPoints();
                }
            }
        });

        static::updated(function (Order $order) {
            if ($order->wasChanged('status')) {
                \App\Services\NotificationService::sendOrderStatusNotification($order);
            }
        });
    }

    public function awardLateDeliveryPoints()
    {
        $points = (int) Setting::getValue('late_delivery_points', '50');
        if ($points <= 0) {
            return;
        }

        // Prevent double points award for this order
        $exists = PointTransaction::where('order_id', $this->id)
            ->where('type', 'earn_late')
            ->exists();

        if (!$exists) {
            $customer = $this->customer;
            if ($customer) {
                $customer->increment('points', $points);

                PointTransaction::create([
                    'user_id' => $this->customer_id,
                    'order_id' => $this->id,
                    'amount' => $points,
                    'type' => 'earn_late',
                    'description' => "Kompensasi keterlambatan pesanan #{$this->order_number}",
                ]);

                // Create notification
                Notification::create([
                    'user_id' => $this->customer_id,
                    'order_id' => $this->id,
                    'type' => 'info',
                    'title' => '🎁 Poin Kompensasi Terlambat!',
                    'message' => "Anda mendapatkan {$points} poin kompensasi karena pesanan #{$this->order_number} diselesaikan melewati estimasi waktu. Terima kasih atas pengertian Anda!",
                    'is_read' => false,
                ]);
            }
        }
    }

    protected $fillable = [
        'order_number',
        'customer_id',
        'kurir_id',
        'service_id',
        'delivery_method',
        'pickup_address',
        'pickup_datetime',
        'weight_kg',
        'total_price',
        'estimated_finish',
        'status',
        'customer_notes',
        'kurir_notes',
        'payment_status',
        'snap_token',
        'payment_method',
    ];

    protected $casts = [
        'pickup_datetime' => 'datetime',
        'estimated_finish' => 'datetime',
        'weight_kg' => 'float',
        'total_price' => 'float',
    ];

    // Statuses
    public const STATUS_WAITING = 'waiting';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_PICKED_UP = 'picked_up';
    public const STATUS_WASHING = 'washing';
    public const STATUS_DONE = 'done';
    public const STATUS_READY = 'ready';
    public const STATUS_DELIVERED = 'delivered';

    // Status label mapper
    public static function getStatusLabels(): array
    {
        return [
            self::STATUS_WAITING => 'Menunggu Konfirmasi',
            self::STATUS_CONFIRMED => 'Pesanan Dikonfirmasi',
            self::STATUS_PICKED_UP => 'Dijemput/Diterima',
            self::STATUS_WASHING => 'Sedang Dicuci',
            self::STATUS_DONE => 'Selesai Dicuci',
            self::STATUS_READY => 'Siap Diambil/Dikirim',
            self::STATUS_DELIVERED => 'Sudah Diantar/Diterima',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::getStatusLabels()[$this->status] ?? $this->status;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_WAITING => 'bg-gray-100 text-gray-600',
            self::STATUS_CONFIRMED => 'bg-blue-100 text-blue-700',
            self::STATUS_PICKED_UP => 'bg-indigo-100 text-indigo-700',
            self::STATUS_WASHING => 'bg-purple-100 text-purple-700',
            self::STATUS_DONE => 'bg-teal-100 text-teal-700',
            self::STATUS_READY => 'bg-green-100 text-green-700',
            self::STATUS_DELIVERED => 'bg-emerald-100 text-emerald-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    // Payment Status Mappers
    public static function getPaymentStatusLabels(): array
    {
        return [
            'unpaid'     => 'Belum Dibayar',
            'pending'    => 'Menunggu Pembayaran',
            'settlement' => 'Lunas',
            'expire'     => 'Kadaluarsa',
            'cancel'     => 'Dibatalkan',
            'deny'       => 'Ditolak',
        ];
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return self::getPaymentStatusLabels()[$this->payment_status] ?? $this->payment_status;
    }

    public function getPaymentStatusBadgeClassAttribute(): string
    {
        return match ($this->payment_status) {
            'settlement' => 'bg-green-100 text-green-700 border border-green-200',
            'pending'    => 'bg-amber-100 text-amber-700 border border-amber-200',
            'unpaid'     => 'bg-gray-100 text-gray-600 border border-gray-200',
            'expire', 'cancel', 'deny' => 'bg-red-100 text-red-700 border border-red-200',
            default      => 'bg-gray-100 text-gray-800',
        };
    }

    // Relationships
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function kurir()
    {
        return $this->belongsTo(User::class, 'kurir_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function statusLogs()
    {
        return $this->hasMany(OrderStatusLog::class, 'order_id')->orderBy('created_at', 'desc');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
