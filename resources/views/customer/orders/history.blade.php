<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-gray-900 leading-tight flex items-center gap-2">
            <i data-lucide="history" class="text-blue-700 w-6 h-6"></i>
            {{ __('Riwayat Pesanan Laundry') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        <!-- Filter Card -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-gray-900 text-sm mb-4 flex items-center gap-1.5">
                <i data-lucide="filter" class="w-4 h-4 text-blue-500"></i> Saring Riwayat
            </h3>

            <form method="GET" action="{{ route('customer.orders.history') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                <!-- Status Filter -->
                <div class="space-y-1">
                    <label for="status-filter" class="text-xs font-bold text-gray-500">Status Pesanan</label>
                    <select name="status" id="status-filter" class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-xs shadow-sm transition">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Start Date -->
                <div class="space-y-1">
                    <label for="history-start-date" class="text-xs font-bold text-gray-500">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="history-start-date"
                           value="{{ request('start_date') }}"
                           class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-xs shadow-sm transition">
                </div>

                <!-- End Date -->
                <div class="space-y-1">
                    <label for="history-end-date" class="text-xs font-bold text-gray-500">Tanggal Selesai</label>
                    <input type="date" name="end_date" id="history-end-date"
                           value="{{ request('end_date') }}"
                           class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-xs shadow-sm transition">
                </div>

                <!-- Filter Actions -->
                <div class="flex gap-2">
                    <button type="submit" id="history-filter-btn"
                            class="flex-grow bg-blue-500 hover:bg-blue-700 text-white rounded-xl px-4 py-2 text-xs font-bold shadow-md shadow-blue-600/10 transition">
                        Terapkan
                    </button>
                    <a href="{{ route('customer.orders.history') }}"
                       class="border border-gray-200 text-gray-500 hover:bg-gray-50 rounded-xl px-4 py-2 text-xs font-bold transition flex items-center justify-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Stats Row -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-100 rounded-2xl p-4 flex items-center gap-4 shadow-sm">
                <div class="w-10 h-10 bg-blue-50 text-blue-700 rounded-xl flex items-center justify-center">
                    <i data-lucide="package" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Tampil</p>
                    <p class="text-xl font-extrabold text-slate-800">{{ $orders->total() }}</p>
                </div>
            </div>
            <div class="bg-white border border-slate-100 rounded-2xl p-4 flex items-center gap-4 shadow-sm">
                <div class="w-10 h-10 bg-green-50 text-green-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Selesai</p>
                    <p class="text-xl font-extrabold text-slate-800">{{ $orders->filter(fn($o) => $o->status === 'delivered')->count() }}</p>
                </div>
            </div>
            <div class="bg-white border border-slate-100 rounded-2xl p-4 flex items-center gap-4 shadow-sm">
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Masih Proses</p>
                    <p class="text-xl font-extrabold text-slate-800">{{ $orders->filter(fn($o) => $o->status !== 'delivered')->count() }}</p>
                </div>
            </div>
        </div>

        <!-- History Table Card -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/70 border-b border-gray-100">
                            <th class="p-5 text-xs font-bold uppercase tracking-wider text-gray-500">No. Order</th>
                            <th class="p-5 text-xs font-bold uppercase tracking-wider text-gray-500">Tanggal Masuk</th>
                            <th class="p-5 text-xs font-bold uppercase tracking-wider text-gray-500">Jenis Layanan</th>
                            <th class="p-5 text-xs font-bold uppercase tracking-wider text-gray-500">Metode</th>
                            <th class="p-5 text-xs font-bold uppercase tracking-wider text-gray-500">Berat / Harga</th>
                            <th class="p-5 text-xs font-bold uppercase tracking-wider text-gray-500">Est. Selesai</th>
                            <th class="p-5 text-xs font-bold uppercase tracking-wider text-gray-500">Status</th>
                            <th class="p-5 text-xs font-bold uppercase tracking-wider text-gray-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($orders as $order)
                            <tr class="hover:bg-gray-50/50 transition group">
                                <td class="p-5">
                                    <span class="text-xs font-bold text-gray-800">{{ $order->order_number }}</span>
                                </td>
                                <td class="p-5 text-xs font-semibold text-gray-600">
                                    {{ $order->created_at->format('d M Y') }}<br>
                                    <span class="text-gray-400 font-normal">{{ $order->created_at->format('H:i') }}</span>
                                </td>
                                <td class="p-5 text-xs font-bold text-gray-900">{{ $order->service->name }}</td>
                                <td class="p-5 text-xs font-medium text-gray-600">
                                    @if($order->delivery_method === 'pickup')
                                        <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 px-2 py-1 rounded-lg text-[10px] font-bold">
                                            <i data-lucide="truck" class="w-3 h-3"></i> Jemput
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 bg-slate-50 text-slate-600 px-2 py-1 rounded-lg text-[10px] font-bold">
                                            <i data-lucide="store" class="w-3 h-3"></i> Antar Sendiri
                                        </span>
                                    @endif
                                </td>
                                <td class="p-5 text-xs font-semibold text-gray-800">
                                    @if($order->weight_kg)
                                        <span class="text-blue-700 font-bold">{{ $order->weight_kg }} kg</span><br>
                                        <span class="text-slate-500">Rp{{ number_format($order->total_price ?? 0, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-slate-400 italic text-[10px]">Menunggu konfirmasi</span>
                                    @endif
                                </td>
                                <td class="p-5 text-xs text-gray-600">
                                    @if($order->estimated_finish)
                                        <span class="font-semibold">{{ $order->estimated_finish->format('d M Y') }}</span><br>
                                        <span class="text-blue-700 font-bold">{{ $order->estimated_finish->format('H:i') }}</span>
                                    @else
                                        <span class="text-slate-400 italic text-[10px]">Belum ditentukan</span>
                                    @endif
                                </td>
                                <td class="p-5">
                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-full
                                        @if($order->status === 'waiting')   bg-gray-100 text-gray-600
                                        @elseif($order->status === 'confirmed') bg-blue-50 text-blue-700
                                        @elseif($order->status === 'picked_up') bg-indigo-50 text-indigo-700
                                        @elseif($order->status === 'washing')  bg-purple-50 text-purple-700
                                        @elseif($order->status === 'done')     bg-blue-50 text-blue-800
                                        @elseif($order->status === 'ready')    bg-green-50 text-green-700
                                        @elseif($order->status === 'delivered') bg-emerald-50 text-emerald-700
                                        @else bg-gray-50 text-gray-500
                                        @endif">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td class="p-5">
                                    <a href="{{ route('customer.orders.show', $order->id) }}"
                                       class="inline-flex items-center gap-1 text-blue-700 hover:text-blue-800 font-bold text-xs bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition">
                                        Detail <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <i data-lucide="inbox" class="w-12 h-12 text-slate-200"></i>
                                        <p class="text-sm font-semibold text-gray-400">Tidak ada data riwayat pesanan.</p>
                                        <a href="{{ route('customer.orders.create') }}"
                                           class="text-xs font-bold text-blue-700 hover:underline">
                                            Buat pesanan pertama Anda →
                                        </a>
                                    </div>
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
