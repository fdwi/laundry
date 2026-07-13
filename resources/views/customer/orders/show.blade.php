<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Lacak Pesanan') }} — {{ $order->order_number }}
        </h2>
    </x-slot>

    <!-- Midtrans Snap JS SDK -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

    <div class="py-12"
         x-data="orderTracker()"
         x-init="init()">

        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if(session('success'))
                <div class="bg-blue-50 border border-blue-200 text-blue-900 px-4 py-3 rounded-xl flex items-center space-x-2">
                    <i data-lucide="check-circle" class="w-5 h-5 text-blue-500"></i>
                    <span class="font-medium text-sm">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center space-x-2">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-red-500"></i>
                    <span class="font-medium text-sm">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Header card -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nomor Pesanan</span>
                    <h1 class="text-2xl font-extrabold text-slate-800">{{ $order->order_number }}</h1>
                    <span class="text-xs text-slate-400 font-medium">Dibuat {{ $order->created_at->format('d M Y, H:i') }}</span>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <span :class="statusBadgeClass" class="inline-flex px-4 py-1.5 rounded-full text-xs font-bold transition-all duration-300" x-text="statusLabel">
                        {{ $order->status_label }}
                    </span>
                    <a href="{{ $whatsappUrl }}" target="_blank" class="bg-blue-500 hover:bg-blue-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.012 2c-5.506 0-9.989 4.478-9.99 9.984a9.96 9.96 0 001.333 4.982L2 22l5.202-1.364a9.92 9.92 0 004.808 1.238h.005c5.502 0 9.99-4.477 9.99-9.982C22 6.478 17.514 2 12.012 2zm6.924 14.13c-.279.79-1.393 1.45-2.28 1.636-.61.127-1.4.225-4.08-.885-3.428-1.42-5.642-4.914-5.813-5.14-.17-.225-1.385-1.84-1.385-3.51 0-1.673.873-2.495 1.186-2.834.312-.34.682-.424.908-.424.227 0 .454.002.653.01.21.01.493-.08.772.59.284.682.97 2.368 1.055 2.538.085.17.142.368.028.596-.114.226-.17.368-.34.568-.17.2-.358.447-.512.6-.17.17-.348.354-.15.695.198.34.88 1.442 1.888 2.336 1.3 1.157 2.395 1.517 2.736 1.687.34.17.54.14.739-.086.2-.227.85-1.008 1.08-1.348.228-.34.453-.284.767-.17 3.14 1.157 3.28.17 3.564-.114z"/></svg>
                        <span>Hubungi via WhatsApp</span>
                    </a>
                </div>
            </div>

            <!-- Estimasi Selesai Card (hanya tampil jika ada estimated_finish) -->
            @if($order->estimated_finish)
            <div x-data="countdownTimer('{{ $order->estimated_finish->toIso8601String() }}', '{{ $order->created_at->toIso8601String() }}')"
                 x-init="startCountdown()"
                 :class="urgencyClass"
                 class="rounded-3xl p-6 shadow-sm transition-all duration-500">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <i data-lucide="clock" class="w-5 h-5"></i>
                            <span class="text-sm font-bold">Estimasi Selesai</span>
                        </div>
                        <p class="text-2xl font-black" x-text="targetDateFormatted">{{ $order->estimated_finish->format('d M Y, H:i') }}</p>
                        <div x-show="!isExpired" class="flex items-center gap-2 text-sm font-semibold opacity-80">
                            <i data-lucide="timer" class="w-4 h-4"></i>
                            <span>Sisa waktu: </span>
                            <span class="font-black text-lg" x-text="countdown"></span>
                        </div>
                        <p x-show="isExpired" class="text-sm font-bold opacity-90 flex items-center gap-1.5">
                            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500 shrink-0"></i>
                            <span>Estimasi selesai sudah terlewati.</span>
                        </p>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full md:w-64 space-y-2">
                        <div class="flex justify-between text-xs font-bold opacity-70">
                            <span>Progress</span>
                            <span x-text="progressPct + '%'">0%</span>
                        </div>
                        <div class="h-3 rounded-full bg-white/30 overflow-hidden">
                            <div class="h-full rounded-full bg-white/70 transition-all duration-1000"
                                 :style="'width: ' + progressPct + '%'"></div>
                        </div>
                        <p class="text-[10px] opacity-60 font-medium">
                            Dari {{ $order->created_at->format('H:i') }} s/d {{ $order->estimated_finish->format('H:i') }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Stepper Container -->
            <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6 overflow-hidden">
                <h3 class="text-sm font-bold text-slate-800">Status Proses Laundry</h3>

                <!-- Horizontal Stepper -->
                <div class="relative w-full flex items-center justify-between">
                    <!-- Progress Line Background -->
                    <div class="absolute left-0 right-0 top-1/2 -translate-y-1/2 h-1 bg-slate-100 -z-10 rounded-full"></div>
                    <!-- Filled line based on status -->
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-blue-500 -z-10 rounded-full transition-all duration-700"
                         :style="
                            status === 'waiting'   ? 'width: 0%;' :
                            status === 'confirmed' ? 'width: 16%;' :
                            status === 'picked_up' ? 'width: 33%;' :
                            status === 'washing'   ? 'width: 50%;' :
                            status === 'done'      ? 'width: 66%;' :
                            status === 'ready'     ? 'width: 83%;' :
                            'width: 100%;'
                         "></div>

                    @php
                    $steps = [
                        ['key' => 'waiting',   'label' => 'Menunggu', 'icon' => 'clock'],
                        ['key' => 'confirmed', 'label' => 'Dikonfirmasi', 'icon' => 'check'],
                        ['key' => 'picked_up', 'label' => 'Diterima', 'icon' => 'truck'],
                        ['key' => 'washing',   'label' => 'Dicuci', 'icon' => 'droplets'],
                        ['key' => 'done',      'label' => 'Selesai', 'icon' => 'wind'],
                        ['key' => 'ready',     'label' => 'Siap', 'icon' => 'package-check'],
                        ['key' => 'delivered', 'label' => 'Diantar', 'icon' => 'home'],
                    ];
                    $statusOrder = ['waiting','confirmed','picked_up','washing','done','ready','delivered'];
                    $currentIdx  = array_search($order->status, $statusOrder);
                    @endphp

                    @foreach($steps as $i => $step)
                    <div class="flex flex-col items-center">
                        <div class="w-9 h-9 rounded-full border-2 flex items-center justify-center font-bold text-xs transition duration-300
                            @if($i < $currentIdx) border-blue-500 bg-blue-500 text-white
                            @elseif($i === $currentIdx) border-blue-500 bg-white text-blue-700 ring-4 ring-blue-100
                            @else border-slate-200 bg-white text-slate-400 @endif">
                            @if($i < $currentIdx)
                                <i data-lucide="check" class="w-4 h-4"></i>
                            @else
                                {{ $i + 1 }}
                            @endif
                        </div>
                        <span class="text-[10px] font-bold mt-2
                            @if($i <= $currentIdx) text-blue-700
                            @else text-slate-400 @endif">
                            {{ $step['label'] }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Two Columns: Order Info + Pickup Schedule vs Timeline Logs -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Left Column: Order Info + Pickup Schedule -->
                <div class="space-y-6 md:col-span-1">

                    <!-- Order Info Card -->
                    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-5">
                        <h3 class="text-sm font-bold text-slate-800 border-b border-slate-50 pb-3">Detail Pesanan</h3>

                        <div class="space-y-4">
                            <div class="space-y-0.5">
                                <span class="text-[10px] font-bold text-slate-400 uppercase">Jenis Layanan</span>
                                <span class="block text-sm font-bold text-slate-700">{{ $order->service->name }}</span>
                            </div>
                            <div class="space-y-0.5">
                                <span class="text-[10px] font-bold text-slate-400 uppercase">Metode Pengiriman</span>
                                <span class="flex items-center gap-1.5 text-sm font-semibold text-slate-600 mt-1">
                                    @if($order->delivery_method === 'pickup')
                                        <i data-lucide="truck" class="w-4 h-4 text-blue-600 shrink-0"></i>
                                        <span>Dijemput Kurir</span>
                                    @else
                                        <i data-lucide="store" class="w-4 h-4 text-blue-600 shrink-0"></i>
                                        <span>Antar Sendiri</span>
                                    @endif
                                </span>
                            </div>
                            @if($order->pickup_address)
                            <div class="space-y-0.5">
                                <span class="text-[10px] font-bold text-slate-400 uppercase">Alamat Jemput</span>
                                <span class="block text-xs font-medium text-slate-500 leading-relaxed">{{ $order->pickup_address }}</span>
                            </div>
                            @endif
                            <div class="space-y-0.5">
                                <span class="text-[10px] font-bold text-slate-400 uppercase">Berat Aktual</span>
                                <span class="block text-sm font-bold text-blue-700" x-text="weightKg">{{ $order->weight_kg ? $order->weight_kg . ' kg' : '-' }}</span>
                            </div>
                            <div class="space-y-0.5">
                                <span class="text-[10px] font-bold text-slate-400 uppercase">Total Harga</span>
                                <span class="block text-lg font-black text-slate-900" x-text="totalPrice">{{ $order->total_price ? 'Rp ' . number_format($order->total_price, 0, ',', '.') : 'Belum dihitung' }}</span>
                            </div>

                            <!-- Status Pembayaran & Tombol Bayar -->
                            <div class="space-y-2 pt-3 border-t border-slate-100" x-data="paymentHandler('{{ $order->id }}', '{{ $order->payment_status }}', '{{ $order->snap_token }}')" x-init="init()">
                                <span class="text-[10px] font-bold text-slate-400 uppercase block">Status Pembayaran</span>
                                <div class="flex items-center justify-between gap-3">
                                    <span :class="paymentBadgeClass" class="inline-flex px-3 py-1 rounded-full text-[11px] font-bold transition-all duration-300" x-text="paymentStatusLabel">
                                        {{ $order->payment_status_label }}
                                    </span>
                                    
                                    <template x-if="showPayButton">
                                        <button @click="payNow()" 
                                                :disabled="loading"
                                                class="bg-blue-500 hover:bg-blue-700 disabled:bg-slate-200 text-white font-extrabold text-[11px] px-3.5 py-1.5 rounded-xl transition flex items-center gap-1.5 shadow-md shadow-blue-600/10">
                                            <i data-lucide="credit-card" class="w-3.5 h-3.5"></i>
                                            <span x-text="loading ? 'Memproses...' : 'Bayar'"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Jadwal Pengambilan Card -->
                    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4"
                         x-data="{ showReschedule: false }">
                        <div class="flex items-center justify-between border-b border-slate-50 pb-3">
                            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                                <i data-lucide="calendar" class="w-4 h-4 text-blue-700"></i>
                                Jadwal Pengambilan
                            </h3>
                            @if(!in_array($order->status, ['delivered']))
                            <button @click="showReschedule = !showReschedule"
                                    class="text-[11px] font-bold text-blue-700 hover:text-blue-800 hover:underline flex items-center gap-1">
                                <i data-lucide="pencil" class="w-3 h-3"></i>
                                <span x-text="showReschedule ? 'Batal' : 'Ubah'"></span>
                            </button>
                            @endif
                        </div>

                        @if($order->pickup_datetime)
                            <div class="text-center py-2">
                                <p class="text-xs text-slate-400 font-semibold">Jadwal Dipilih</p>
                                <p class="text-lg font-black text-slate-800 mt-1">{{ $order->pickup_datetime->format('d M Y') }}</p>
                                <p class="text-2xl font-extrabold text-blue-700">{{ $order->pickup_datetime->format('H:i') }}</p>
                                @php
                                    $isUpcoming = $order->pickup_datetime->isFuture();
                                    $diffHours  = now()->diffInHours($order->pickup_datetime, false);
                                @endphp
                                @if($isUpcoming)
                                    <span class="inline-flex items-center gap-1 mt-2 bg-green-50 text-green-700 text-[10px] font-bold px-3 py-1 rounded-full">
                                        <i data-lucide="clock" class="w-3 h-3 text-green-600 shrink-0"></i>
                                        <span>Dalam {{ round($diffHours) }} jam lagi</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 mt-2 bg-slate-50 text-slate-500 text-[10px] font-bold px-3 py-1 rounded-full">
                                        <i data-lucide="check" class="w-3 h-3 text-slate-400 shrink-0"></i>
                                        <span>Jadwal sudah terlewati</span>
                                    </span>
                                @endif
                            </div>
                        @else
                            <p class="text-sm text-slate-400 italic text-center py-2">Belum ada jadwal pengambilan yang dipilih.</p>
                        @endif

                        <!-- Reschedule Form -->
                        @if(!in_array($order->status, ['delivered']))
                        <div x-show="showReschedule"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             style="display:none;">
                            <form method="POST" action="{{ route('customer.orders.reschedule', $order->id) }}" class="space-y-3">
                                @csrf
                                @method('PATCH')
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase">Pilih Tanggal & Waktu Baru</label>
                                    <input type="datetime-local"
                                           name="pickup_datetime"
                                           min="{{ now()->addHour()->format('Y-m-d\TH:i') }}"
                                           value="{{ $order->pickup_datetime ? $order->pickup_datetime->format('Y-m-d\TH:i') : '' }}"
                                           class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-sm shadow-sm">
                                </div>

                                <!-- Slot Waktu Cepat -->
                                <div class="grid grid-cols-2 gap-2">
                                    @php
                                    $slots = [
                                        'Pagi (08:00)'  => now()->addDay()->setTime(8, 0)->format('Y-m-d\TH:i'),
                                        'Siang (12:00)' => now()->addDay()->setTime(12, 0)->format('Y-m-d\TH:i'),
                                        'Sore (16:00)'  => now()->addDay()->setTime(16, 0)->format('Y-m-d\TH:i'),
                                        'Malam (19:00)' => now()->addDay()->setTime(19, 0)->format('Y-m-d\TH:i'),
                                    ];
                                    @endphp
                                    @foreach($slots as $label => $value)
                                        <button type="button"
                                                onclick="document.querySelector('input[name=pickup_datetime]').value = '{{ $value }}'"
                                                class="text-[10px] font-bold bg-slate-50 hover:bg-blue-50 text-slate-600 hover:text-blue-800 border border-slate-200 hover:border-blue-300 rounded-lg px-2 py-1.5 transition">
                                            {{ $label }}
                                        </button>
                                    @endforeach
                                </div>

                                <button type="submit"
                                        class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl text-xs transition shadow-md shadow-blue-100">
                                    Simpan Jadwal
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Right Column: Timeline Logs -->
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-6 md:col-span-2">
                    <h3 class="text-sm font-bold text-slate-800 border-b border-slate-50 pb-3 flex items-center gap-2">
                        <i data-lucide="history" class="w-4 h-4 text-blue-700"></i>
                        Riwayat Log Status
                    </h3>

                    <div class="relative pl-6 space-y-6 border-l-2 border-slate-100 ml-3">
                        <template x-for="log in logs" :key="log.created_at">
                            <div class="relative">
                                <!-- Dot indicator -->
                                <div class="absolute -left-[31px] top-1.5 w-3.5 h-3.5 rounded-full border-2 border-blue-500 bg-white"></div>
                                <div class="space-y-1 pl-2">
                                    <div class="flex items-start justify-between gap-2 text-xs">
                                        <span class="font-bold text-slate-800 leading-snug" x-text="log.status_label"></span>
                                        <span class="text-slate-400 shrink-0" x-text="log.created_at"></span>
                                    </div>
                                    <p class="text-xs text-slate-500 font-medium" x-text="log.note"></p>
                                    <span class="inline-block text-[9px] font-semibold text-slate-400" x-text="'Diupdate oleh: ' + log.updater_name"></span>
                                </div>
                            </div>
                        </template>

                        <!-- Loading state -->
                        <template x-if="logs.length === 0">
                            <div class="flex items-center gap-2 text-slate-400">
                                <i data-lucide="loader" class="w-4 h-4 animate-spin"></i>
                                <span class="text-xs">Memuat riwayat...</span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Main order tracking data
        function orderTracker() {
            return {
                orderId: '{{ $order->id }}',
                status: '{{ $order->status }}',
                statusLabel: '{{ $order->status_label }}',
                statusBadgeClass: '{{ $order->status_badge_class }}',
                totalPrice: '{{ $order->total_price ? "Rp " . number_format($order->total_price, 0, ",", ".") : "Belum dihitung" }}',
                weightKg: '{{ $order->weight_kg ? $order->weight_kg . " kg" : "-" }}',
                logs: [],

                init() {
                    window.orderTrackerInstance = this;
                    this.fetchStatus();
                    setInterval(() => this.fetchStatus(), 30000);
                },

                fetchStatus() {
                    fetch('/customer/orders/' + this.orderId + '/status')
                        .then(r => r.json())
                        .then(data => {
                            this.status         = data.status;
                            this.statusLabel    = data.status_label;
                            this.statusBadgeClass = data.status_badge_class;
                            this.totalPrice     = data.total_price;
                            this.weightKg       = data.weight_kg;
                            this.logs           = data.logs;

                            // Update payment status if present
                            if (data.payment_status && window.paymentHandlerInstance) {
                                window.paymentHandlerInstance.paymentStatus = data.payment_status;
                            }

                            this.$nextTick(() => lucide.createIcons());
                        });
                }
            };
        }

        // Midtrans Payment Handler component
        function paymentHandler(orderId, initialStatus, initialToken) {
            return {
                orderId: orderId,
                paymentStatus: initialStatus,
                snapToken: initialToken,
                loading: false,

                init() {
                    window.paymentHandlerInstance = this;
                },

                get paymentStatusLabel() {
                    const labels = {
                        'unpaid': 'Belum Dibayar',
                        'pending': 'Menunggu Pembayaran',
                        'settlement': 'Lunas',
                        'expire': 'Kadaluarsa',
                        'cancel': 'Dibatalkan',
                        'deny': 'Ditolak'
                    };
                    return labels[this.paymentStatus] || this.paymentStatus;
                },

                get paymentBadgeClass() {
                    const classes = {
                        'settlement': 'bg-green-50 text-green-700 border border-green-200',
                        'pending': 'bg-amber-50 text-amber-700 border border-amber-200',
                        'unpaid': 'bg-slate-50 text-slate-500 border border-slate-200',
                        'expire': 'bg-red-50 text-red-700 border border-red-200',
                        'cancel': 'bg-red-50 text-red-700 border border-red-200',
                        'deny': 'bg-red-50 text-red-700 border border-red-200'
                    };
                    return classes[this.paymentStatus] || 'bg-slate-50 text-slate-500';
                },

                get showPayButton() {
                    return this.paymentStatus !== 'settlement' && parseFloat('{{ $order->total_price ?? 0 }}') > 0;
                },

                payNow() {
                    if (this.loading) return;

                    // Jika token sudah ada di database, langsung picu popup Midtrans
                    if (this.snapToken) {
                        this.triggerSnap();
                        return;
                    }

                    this.loading = true;

                    fetch('/customer/orders/' + this.orderId + '/payment-token')
                        .then(r => r.json())
                        .then(data => {
                            this.loading = false;
                            if (data.success) {
                                this.snapToken = data.snap_token;
                                this.triggerSnap();
                            } else {
                                alert(data.message || 'Gagal memulai pembayaran.');
                            }
                        })
                        .catch(err => {
                            this.loading = false;
                            alert('Gagal menghubungi server.');
                        });
                },

                syncPaymentStatus(result) {
                    this.loading = true;
                    fetch('/payment/midtrans-notification', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(result)
                    })
                    .then(r => r.json())
                    .then(data => {
                        this.loading = false;
                        if (window.orderTrackerInstance) {
                            window.orderTrackerInstance.fetchStatus();
                        } else {
                            window.location.reload();
                        }
                    })
                    .catch(err => {
                        this.loading = false;
                        if (window.orderTrackerInstance) {
                            window.orderTrackerInstance.fetchStatus();
                        } else {
                            window.location.reload();
                        }
                    });
                },

                triggerSnap() {
                    window.snap.pay(this.snapToken, {
                        onSuccess: (result) => {
                            this.paymentStatus = 'settlement';
                            this.syncPaymentStatus(result);
                        },
                        onPending: (result) => {
                            this.paymentStatus = 'pending';
                            this.syncPaymentStatus(result);
                        },
                        onError: (result) => {
                            this.paymentStatus = 'cancel';
                            this.syncPaymentStatus(result);
                        },
                        onClose: () => {
                            if (window.orderTrackerInstance) {
                                window.orderTrackerInstance.fetchStatus();
                            }
                        }
                    });
                }
            };
        }

        // Countdown timer component
        function countdownTimer(targetIso, startIso) {
            return {
                countdown: '--:--:--',
                progressPct: 0,
                isExpired: false,
                urgencyClass: 'bg-blue-500 text-white',
                targetDateFormatted: '',
                timer: null,

                startCountdown() {
                    const target = new Date(targetIso);
                    const start  = new Date(startIso);
                    const totalMs = target - start;

                    // Format target date in Indonesian
                    this.targetDateFormatted = target.toLocaleString('id-ID', {
                        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric',
                        hour: '2-digit', minute: '2-digit'
                    });

                    const update = () => {
                        const now     = new Date();
                        const remainMs = target - now;

                        if (remainMs <= 0) {
                            this.countdown  = '00:00:00';
                            this.progressPct = 100;
                            this.isExpired  = true;
                            this.urgencyClass = 'bg-slate-100 text-slate-600';
                            clearInterval(this.timer);
                            return;
                        }

                        // Countdown text
                        const hours   = Math.floor(remainMs / 3600000);
                        const minutes = Math.floor((remainMs % 3600000) / 60000);
                        const seconds = Math.floor((remainMs % 60000) / 1000);
                        this.countdown = `${String(hours).padStart(2,'0')}:${String(minutes).padStart(2,'0')}:${String(seconds).padStart(2,'0')}`;

                        // Progress
                        const elapsedMs  = now - start;
                        this.progressPct = Math.min(100, Math.round((elapsedMs / totalMs) * 100));

                        // Urgency color
                        if (remainMs < 30 * 60 * 1000) {
                            this.urgencyClass = 'bg-red-500 text-white'; // < 30 min
                        } else if (remainMs < 60 * 60 * 1000) {
                            this.urgencyClass = 'bg-amber-400 text-amber-900'; // < 1 hour
                        } else {
                            this.urgencyClass = 'bg-blue-500 text-white';
                        }
                    };

                    update();
                    this.timer = setInterval(update, 1000);
                }
            };
        }
    </script>
</x-app-layout>
