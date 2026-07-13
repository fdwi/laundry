<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-gray-900 leading-tight flex items-center gap-2">
            <i data-lucide="history" class="text-blue-700 w-6 h-6"></i>
            {{ __('Riwayat Pesanan Laundry') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Filter Card -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-8">
            <h3 class="font-bold text-gray-900 text-sm mb-4 flex items-center gap-1.5">
                <i data-lucide="filter" class="w-4 h-4 text-blue-500"></i> Saring Riwayat
            </h3>
            
            <form method="GET" action="{{ route('customer.orders.history') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                <!-- Status Filter -->
                <div class="space-y-1">
                    <label for="status" class="text-xs font-bold text-gray-500">Status Pesanan</label>
                    <select name="status" id="status" class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-xs shadow-sm transition">
                        <option value="">Semua Status</option>
                        @foreach($statusLabels as $value => $label)
                            <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Start Date -->
                <div class="space-y-1">
                    <label for="start_date" class="text-xs font-bold text-gray-500">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                           class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-xs shadow-sm transition">
                </div>

                <!-- End Date -->
                <div class="space-y-1">
                    <label for="end_date" class="text-xs font-bold text-gray-500">Tanggal Selesai</label>
                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                           class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-xs shadow-sm transition">
                </div>

                <!-- Filter Actions -->
                <div class="flex gap-2">
                    <button type="submit" class="flex-grow bg-blue-500 hover:bg-blue-700 text-white rounded-xl px-4 py-2 text-xs font-bold shadow-md shadow-blue-600/10 transition">
                        Terapkan Filter
                    </button>
                    <a href="{{ route('customer.orders.history') }}" class="border border-gray-200 text-gray-500 hover:bg-gray-50 rounded-xl px-4 py-2 text-xs font-bold transition flex items-center justify-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- History Table Card -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/70 border-b border-gray-100">
                            <th class="p-5 text-xs font-bold uppercase tracking-wider text-gray-500">Order ID</th>
                            <th class="p-5 text-xs font-bold uppercase tracking-wider text-gray-500">Tanggal Masuk</th>
                            <th class="p-5 text-xs font-bold uppercase tracking-wider text-gray-500">Jenis Layanan</th>
                            <th class="p-5 text-xs font-bold uppercase tracking-wider text-gray-500">Mitra Laundry</th>
                            <th class="p-5 text-xs font-bold uppercase tracking-wider text-gray-500">Berat (Kg)</th>
                            <th class="p-5 text-xs font-bold uppercase tracking-wider text-gray-500">Total Biaya</th>
                            <th class="p-5 text-xs font-bold uppercase tracking-wider text-gray-500">Status</th>
                            <th class="p-5 text-xs font-bold uppercase tracking-wider text-gray-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($orders as $order)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="p-5 text-xs font-bold text-gray-400">#LD-{{ $order->id }}</td>
                                <td class="p-5 text-xs font-semibold text-gray-800">{{ $order->created_at->format('d M Y, H:i') }}</td>
                                <td class="p-5 text-xs font-bold text-gray-900">{{ $order->service->name }}</td>
                                <td class="p-5 text-xs font-medium text-gray-600 flex items-center gap-1.5 mt-2.5">
                                    <i data-lucide="store" class="w-3.5 h-3.5 text-blue-700"></i>
                                    {{ $order->mitra->mitraProfile->business_name }}
                                </td>
                                <td class="p-5 text-xs font-semibold text-gray-800">{{ $order->weight_kg ?? '0' }} kg</td>
                                <td class="p-5 text-xs font-bold text-blue-700">Rp{{ number_format($order->total_price ?? 0, 0, ',', '.') }}</td>
                                <td class="p-5">
                                    <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full 
                                        @if($order->status === 'pending') bg-yellow-50 text-yellow-700 border border-yellow-250
                                        @elseif($order->status === 'confirmed') bg-blue-50 text-blue-700 border border-blue-250
                                        @elseif($order->status === 'picked_up') bg-indigo-50 text-indigo-700 border border-indigo-250
                                        @elseif($order->status === 'washing') bg-purple-50 text-purple-700 border border-purple-250
                                        @elseif($order->status === 'done') bg-emerald-50 text-emerald-700 border border-emerald-250
                                        @else bg-gray-50 text-gray-700 border border-gray-250
                                        @endif">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td class="p-5">
                                    <a href="{{ route('customer.orders.show', $order->id) }}" class="inline-flex items-center gap-1 text-blue-700 hover:text-blue-800 font-bold text-xs">
                                        Detail <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-10 text-center text-sm font-semibold text-gray-400">
                                    Tidak ada data riwayat pesanan yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($orders->hasPages())
                <div class="bg-gray-50 border-t border-gray-100 p-5">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
