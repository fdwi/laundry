<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Dashboard Owner / Admin') }}
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

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Pendapatan Bulan Ini -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                    <div class="space-y-1">
                        <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Pendapatan Bulan Ini</p>
                        <h3 class="text-2xl font-black text-slate-800">Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-600/10 rounded-xl flex items-center justify-center text-blue-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Total Selesai -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                    <div class="space-y-1">
                        <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Total Cucian Selesai</p>
                        <h3 class="text-3xl font-bold text-slate-800">{{ $completedOrdersCount }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-green-500/10 rounded-xl flex items-center justify-center text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Kurir Aktif -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                    <div class="space-y-1">
                        <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Kurir Aktif</p>
                        <h3 class="text-3xl font-bold text-slate-800">{{ $activeKurirCount }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-500/10 rounded-xl flex items-center justify-center text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Layanan Aktif -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                    <div class="space-y-1">
                        <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Layanan Aktif</p>
                        <h3 class="text-3xl font-bold text-slate-800">{{ $activeServicesCount }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-purple-500/10 rounded-xl flex items-center justify-center text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Two columns: Quick links/Summary vs Services breakdown -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Admin Action Card -->
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex flex-col justify-between space-y-6">
                    <div class="space-y-4">
                        <div class="w-12 h-12 bg-blue-600/10 rounded-2xl flex items-center justify-center text-blue-700">
                            <i data-lucide="file-text" class="w-6 h-6"></i>
                        </div>
                        <div class="space-y-1.5">
                            <h3 class="text-base font-bold text-slate-800">Laporan Keuangan</h3>
                            <p class="text-xs text-slate-400 leading-relaxed">Ekspor detail seluruh pesanan, rincian omzet, berat cucian harian ke dokumen PDF profesional siap cetak.</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.reports.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-extrabold text-xs text-center py-3.5 rounded-xl transition duration-200 block shadow-md shadow-blue-600/20">
                        Buka Laporan Keuangan &rarr;
                    </a>
                </div>

                <!-- Services Breakdown -->
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm lg:col-span-2 space-y-6">
                    <div>
                        <h3 class="text-base font-bold text-slate-800">Rincian Pendapatan per Layanan</h3>
                        <p class="text-xs text-slate-400">Menampilkan pendapatan kotor kumulatif dari transaksi selesai (delivered).</p>
                    </div>

                    <div class="space-y-4">
                        @foreach($servicesBreakdown as $srv)
                            @php
                                $percentage = $monthlyIncome > 0 ? min(100, round(($srv->income / $monthlyIncome) * 100)) : 0;
                            @endphp
                            <div class="space-y-1.5">
                                <div class="flex justify-between text-xs font-bold text-slate-700">
                                    <span>{{ $srv->name }} ({{ $srv->orders_count }} Order)</span>
                                    <span>Rp {{ number_format($srv->income, 0, ',', '.') }}</span>
                                </div>
                                <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                                    <div class="bg-blue-500 h-full rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Recent Orders Table -->
            <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-800">10 Pesanan Terbaru Masuk</h3>
                    <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-blue-700 hover:text-blue-800 hover:underline">Kelola Semua Pesanan</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                <th class="px-6 py-4">No. Order</th>
                                <th class="px-6 py-4">Pelanggan</th>
                                <th class="px-6 py-4">Layanan</th>
                                <th class="px-6 py-4">Proses Oleh</th>
                                <th class="px-6 py-4">Berat / Tarif</th>
                                <th class="px-6 py-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($recentOrders as $order)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-4 font-bold text-slate-800">{{ $order->order_number }}</td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-slate-700">{{ $order->customer->name }}</div>
                                        <div class="text-[11px] text-slate-400 font-medium">{{ $order->customer->phone }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 font-medium">{{ $order->service->name }}</td>
                                    <td class="px-6 py-4 text-slate-500 font-medium">
                                        {{ $order->kurir ? $order->kurir->name : '-' }}
                                    </td>
                                    <td class="px-6 py-4 font-bold text-slate-800">
                                        @if($order->weight_kg)
                                            {{ $order->weight_kg }} {{ $order->service->unit }} / Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                        @else
                                            <span class="text-slate-400 font-normal italic text-xs">Belum diinput</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold {{ $order->status_badge_class }}">
                                            {{ $order->status_label }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">Belum ada data pesanan laundry masuk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
