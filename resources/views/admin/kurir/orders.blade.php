<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Kinerja Kurir') }} — {{ $kurir->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Back Link -->
            <div>
                <a href="{{ route('admin.kurir.index') }}" class="inline-flex items-center space-x-2 text-xs font-bold text-slate-500 hover:text-blue-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    <span>Kembali ke Kelola Kurir</span>
                </a>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                    <div class="space-y-1">
                        <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Total Handled Orders</p>
                        <h3 class="text-3xl font-bold text-slate-800">{{ $totalProcessed }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-600/10 rounded-xl flex items-center justify-center text-blue-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                    <div class="space-y-1">
                        <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Completed Processed</p>
                        <h3 class="text-3xl font-bold text-slate-800">{{ $completedProcessed }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-green-500/10 rounded-xl flex items-center justify-center text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
                <div class="px-6 py-5 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-800">Daftar Order yang Ditangani</h3>
                    <p class="text-xs text-slate-400">Menampilkan seluruh pesanan laundry yang diupdate terakhir kali oleh kurir ini.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                <th class="px-6 py-4">No. Order</th>
                                <th class="px-6 py-4">Tanggal Masuk</th>
                                <th class="px-6 py-4">Pelanggan</th>
                                <th class="px-6 py-4">Layanan</th>
                                <th class="px-6 py-4">Berat</th>
                                <th class="px-6 py-4">Total Harga</th>
                                <th class="px-6 py-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($orders as $order)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-4 font-bold text-slate-800">{{ $order->order_number }}</td>
                                    <td class="px-6 py-4 text-slate-500">{{ $order->created_at->format('d M Y H:i') }}</td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-slate-700">{{ $order->customer->name }}</div>
                                        <div class="text-xs text-slate-400 font-medium">{{ $order->customer->phone }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 font-medium">{{ $order->service->name }}</td>
                                    <td class="px-6 py-4 text-slate-600">
                                        {{ $order->weight_kg ? $order->weight_kg . ' ' . $order->service->unit : '-' }}
                                    </td>
                                    <td class="px-6 py-4 font-bold text-slate-800">
                                        {{ $order->total_price ? 'Rp ' . number_format($order->total_price, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold {{ $order->status_badge_class }}">
                                            {{ $order->status_label }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-400 italic">Belum ada order yang ditangani oleh kurir ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination footer -->
                @if($orders->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
