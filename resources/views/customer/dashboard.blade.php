<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Dashboard Pelanggan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Alert Banner -->
            @if(session('success'))
                <div class="bg-blue-50 border border-blue-200 text-blue-900 px-4 py-3 rounded-xl flex items-center space-x-2" role="alert">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium text-sm">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Welcome Greeting -->
            <div class="bg-blue-700 rounded-3xl p-8 text-white shadow-lg shadow-blue-100 flex flex-col md:flex-row justify-between items-center relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                <div class="space-y-2 relative z-10 text-center md:text-left">
                    <h1 class="text-3xl font-extrabold">Selamat Datang, {{ Auth::user()->name }}!</h1>
                    <p class="text-blue-50 text-sm md:max-w-md">Butuh pakaian bersih dan wangi hari ini? Silakan buat pesanan laundry Anda dengan mudah secara online.</p>
                </div>
                <a href="{{ route('customer.orders.create') }}" class="mt-6 md:mt-0 bg-white hover:bg-blue-50 text-blue-700 font-bold px-6 py-3 rounded-2xl shadow-md hover:shadow-lg transition duration-200 relative z-10 text-sm flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Buat Pesanan Laundry</span>
                </a>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Orders -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                    <div class="space-y-1">
                        <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Total Pesanan</p>
                        <h3 class="text-3xl font-bold text-slate-800">{{ $totalOrders }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-600/10 rounded-xl flex items-center justify-center text-blue-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                </div>

                <!-- Active Orders -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                    <div class="space-y-1">
                        <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Pesanan Aktif</p>
                        <h3 class="text-3xl font-bold text-slate-800">{{ $activeOrders }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-500/10 rounded-xl flex items-center justify-center text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Completed Orders -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                    <div class="space-y-1">
                        <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Pesanan Selesai</p>
                        <h3 class="text-3xl font-bold text-slate-800">{{ $completedOrders }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-green-500/10 rounded-xl flex items-center justify-center text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Loyalty Points -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition">
                    <div class="space-y-1">
                        <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Poin Loyalty</p>
                        <h3 class="text-3xl font-bold text-amber-500">{{ Auth::user()->points ?? 0 }} <span class="text-xs text-slate-400 font-normal">pts</span></h3>
                    </div>
                    <div class="w-12 h-12 bg-amber-500/10 rounded-xl flex items-center justify-center text-amber-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5a2 2 0 10-2 2h2zm0 0H4m8 0h8" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Recent Orders Table -->
            <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-800">5 Pesanan Terakhir</h3>
                    <a href="{{ route('customer.orders.history') }}" class="text-xs font-bold text-blue-700 hover:text-blue-800 hover:underline">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                <th class="px-6 py-4">No. Order</th>
                                <th class="px-6 py-4">Tanggal Masuk</th>
                                <th class="px-6 py-4">Jenis Layanan</th>
                                <th class="px-6 py-4">Metode Pengiriman</th>
                                <th class="px-6 py-4">Berat / Harga</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($recentOrders as $order)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-4 font-bold text-slate-800">{{ $order->order_number }}</td>
                                    <td class="px-6 py-4 text-slate-500">{{ $order->created_at->format('d M Y H:i') }}</td>
                                    <td class="px-6 py-4 text-slate-600 font-medium">{{ $order->service->name }}</td>
                                    <td class="px-6 py-4 text-slate-500">
                                        {{ $order->delivery_method === 'pickup' ? 'Dijemput' : 'Antar Sendiri' }}
                                    </td>
                                    <td class="px-6 py-4 font-bold text-slate-800">
                                        @if($order->weight_kg)
                                            {{ $order->weight_kg }} {{ $order->service->unit }} / Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                        @else
                                            <span class="text-slate-400 font-normal italic text-xs">Menunggu konfirmasi kurir</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold {{ $order->status_badge_class }}">
                                            {{ $order->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('customer.orders.show', $order->id) }}" class="text-blue-700 hover:text-blue-800 font-bold text-xs bg-blue-50 hover:bg-blue-100 px-3.5 py-1.5 rounded-lg transition">Lacak</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-400 italic">Belum ada riwayat pesanan laundry.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
