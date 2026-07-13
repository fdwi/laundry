<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Laporan Keuangan & Omzet') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Filter & Export Card -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
                <form method="GET" action="{{ route('admin.reports.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <!-- Start Date -->
                    <div>
                        <x-input-label for="start_date" :value="__('Tanggal Mulai')" />
                        <input id="start_date" type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" class="block mt-1.5 w-full border-slate-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm">
                    </div>

                    <!-- End Date -->
                    <div>
                        <x-input-label for="end_date" :value="__('Tanggal Selesai')" />
                        <input id="end_date" type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" class="block mt-1.5 w-full border-slate-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm">
                    </div>

                    <!-- Buttons -->
                    <div class="flex space-x-2 md:col-span-2">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold px-6 py-2.5 rounded-xl shadow-md transition text-sm flex-1">
                            Filter Laporan
                        </button>
                        <a href="{{ route('admin.reports.export', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="bg-slate-900 hover:bg-slate-850 text-white font-bold px-6 py-2.5 rounded-xl shadow-md transition text-sm flex-1 text-center flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            <span>Unduh PDF</span>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Stats grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Total Omzet -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                    <div class="space-y-1">
                        <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Total Pendapatan (Omzet)</p>
                        <h3 class="text-2xl font-black text-blue-700">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-600/10 rounded-xl flex items-center justify-center text-blue-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2" /></svg>
                    </div>
                </div>

                <!-- Total Orders -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                    <div class="space-y-1">
                        <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Total Transaksi Selesai</p>
                        <h3 class="text-3xl font-bold text-slate-800">{{ $totalOrders }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-slate-900/10 rounded-xl flex items-center justify-center text-slate-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2" /></svg>
                    </div>
                </div>

                <!-- Total Berat -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                    <div class="space-y-1">
                        <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Total Berat Cucian Kiloan</p>
                        <h3 class="text-3xl font-bold text-slate-800">{{ $totalWeight }} kg</h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-500/10 rounded-xl flex items-center justify-center text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9" /></svg>
                    </div>
                </div>
            </div>

            <!-- Itemized Table Card -->
            <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
                <div class="px-6 py-5 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-800">Detail Cucian Rincian</h3>
                    <p class="text-xs text-slate-400">Menampilkan rincian transaksi laundry berstatus delivered (selesai total) pada rentang tanggal terpilih.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                <th class="px-6 py-4">No. Order</th>
                                <th class="px-6 py-4">Tanggal Selesai</th>
                                <th class="px-6 py-4">Pelanggan</th>
                                <th class="px-6 py-4">Layanan</th>
                                <th class="px-6 py-4">Metode Kirim</th>
                                <th class="px-6 py-4">Berat Aktual</th>
                                <th class="px-6 py-4">Total Pendapatan</th>
                                <th class="px-6 py-4">Diproses Oleh</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($orders as $order)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-4 font-bold text-slate-800">{{ $order->order_number }}</td>
                                    <td class="px-6 py-4 text-slate-500">{{ $order->updated_at->format('d M Y H:i') }}</td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-slate-700">{{ $order->customer->name }}</div>
                                        <div class="text-xs text-slate-400 font-medium">{{ $order->customer->phone }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 font-medium">{{ $order->service->name }}</td>
                                    <td class="px-6 py-4 text-slate-500 uppercase text-xs font-bold">
                                        {{ $order->delivery_method === 'pickup' ? 'Jemput' : 'Antar Sendiri' }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">
                                        {{ $order->weight_kg ? $order->weight_kg . ' ' . $order->service->unit : '-' }}
                                    </td>
                                    <td class="px-6 py-4 font-extrabold text-blue-700">
                                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-500 font-medium">
                                        {{ $order->kurir ? $order->kurir->name : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-slate-400 italic">Tidak ada transaksi terdata pada rentang tanggal ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
