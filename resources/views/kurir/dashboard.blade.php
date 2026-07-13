<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Dashboard Kurir') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ openModal: false, selectedOrder: {} }">
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
                <!-- Hari Ini -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                    <div class="space-y-1">
                        <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Order Hari Ini</p>
                        <h3 class="text-3xl font-bold text-slate-800">{{ $todayOrders }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-600/10 rounded-xl flex items-center justify-center text-blue-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>

                <!-- Menunggu -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                    <div class="space-y-1">
                        <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Menunggu</p>
                        <h3 class="text-3xl font-bold text-slate-800">{{ $waitingOrders }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-amber-500/10 rounded-xl flex items-center justify-center text-amber-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Dicuci -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                    <div class="space-y-1">
                        <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Sedang Dicuci</p>
                        <h3 class="text-3xl font-bold text-slate-800">{{ $washingOrders }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-purple-500/10 rounded-xl flex items-center justify-center text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547" />
                        </svg>
                    </div>
                </div>

                <!-- Selesai -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                    <div class="space-y-1">
                        <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Selesai Cuci</p>
                        <h3 class="text-3xl font-bold text-slate-800">{{ $doneOrders }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-green-500/10 rounded-xl flex items-center justify-center text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Active Orders Table -->
            <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-800">10 Pesanan Aktif Terbaru</h3>
                    <a href="{{ route('kurir.orders.index') }}" class="text-xs font-bold text-blue-700 hover:text-blue-800 hover:underline">Kelola Semua Pesanan</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                <th class="px-6 py-4">No. Order</th>
                                <th class="px-6 py-4">Pelanggan</th>
                                <th class="px-6 py-4">Layanan</th>
                                <th class="px-6 py-4">Pengiriman</th>
                                <th class="px-6 py-4">Berat / Harga</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($activeOrders as $order)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-4 font-bold text-slate-800">{{ $order->order_number }}</td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-slate-700">{{ $order->customer->name }}</div>
                                        <div class="text-xs text-slate-400 font-medium">{{ $order->customer->phone }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 font-medium">
                                        {{ $order->service->name }}
                                        <span class="text-[10px] text-slate-400 font-bold block">Tarif: Rp {{ number_format($order->service->unit === 'kg' ? $order->service->price_per_kg : $order->service->price_per_pcs, 0, ',', '.') }}/{{ $order->service->unit }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-500">
                                        {{ $order->delivery_method === 'pickup' ? 'Jemput' : 'Antar Sendiri' }}
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
                                    <td class="px-6 py-4 text-right">
                                        <button @click="
                                            selectedOrder = {
                                                id: '{{ $order->id }}',
                                                number: '{{ $order->order_number }}',
                                                status: '{{ $order->status }}',
                                                weight: '{{ $order->weight_kg ?? '' }}',
                                                unit: '{{ $order->service->unit }}',
                                                est_finish: '{{ $order->estimated_finish ? $order->estimated_finish->format('Y-m-d\TH:i') : '' }}'
                                            }; 
                                            openModal = true;
                                        " class="text-blue-700 hover:text-blue-800 font-bold text-xs bg-blue-50 hover:bg-blue-100 px-3.5 py-1.5 rounded-lg transition">Update Status</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-400 italic">Belum ada pesanan aktif.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Update Status Modal -->
        <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak style="display: none;">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-6" @click.away="openModal = false">
                <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                    <h3 class="text-base font-bold text-slate-800">Update Pesanan <span class="text-blue-700" x-text="selectedOrder.number"></span></h3>
                    <button @click="openModal = false" class="text-slate-400 hover:text-slate-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form :action="'/kurir/orders/' + selectedOrder.id + '/process'" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <!-- Status Selection -->
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Status Pengerjaan</label>
                        <select name="status" x-model="selectedOrder.status" required class="w-full border-slate-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl">
                            <option value="waiting">Menunggu (Waiting)</option>
                            <option value="confirmed">Dikonfirmasi (Confirmed)</option>
                            <option value="picked_up">Diterima Workshop (Picked Up)</option>
                            <option value="washing">Sedang Dicuci (Washing)</option>
                            <option value="done">Selesai Dicuci (Done)</option>
                            <option value="ready">Siap Diambil/Kirim (Ready)</option>
                            <option value="delivered">Sudah Diterima Pelanggan (Delivered)</option>
                        </select>
                    </div>

                    <!-- Input Berat (Tampil jika confirmed/picked_up/washing/done dll) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Input Berat / Jumlah (<span x-text="selectedOrder.unit"></span>)</label>
                        <input type="number" step="0.01" name="weight_kg" x-model="selectedOrder.weight" placeholder="Contoh: 4.5" class="w-full border-slate-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl" />
                        <span class="text-[10px] text-slate-400 mt-1 block">Total harga akan otomatis terhitung setelah berat diisi & disimpan.</span>
                    </div>

                    <!-- Estimasi Selesai -->
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Estimasi Selesai</label>
                        <input type="datetime-local" name="estimated_finish" x-model="selectedOrder.est_finish" class="w-full border-slate-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl" />
                    </div>

                    <!-- Catatan Log -->
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Catatan Tambahan (Log Note)</label>
                        <textarea name="note" rows="2" placeholder="Contoh: Pakaian kotor telah diterima dan selesai ditimbang." class="w-full border-slate-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="openModal = false" class="border border-slate-200 text-slate-500 hover:bg-slate-50 font-bold px-5 py-2 rounded-xl text-xs transition">Batal</button>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold px-5 py-2 rounded-xl text-xs shadow-md transition">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
