<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Semua Pesanan & Override Admin') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ openModal: false, selectedOrder: {} }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Alert Banner -->
            @if(session('success'))
                <div class="bg-blue-50 border border-blue-200 text-blue-900 px-4 py-3 rounded-xl flex items-center space-x-2" role="alert">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium text-sm">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Filters -->
            <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
                <form method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <!-- Filter Status -->
                    <div>
                        <x-input-label for="status" :value="__('Status Pesanan')" />
                        <select id="status" name="status" class="block mt-1.5 w-full border-slate-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm">
                            <option value="">Semua Status</option>
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Pengiriman -->
                    <div>
                        <x-input-label for="delivery_method" :value="__('Metode Pengiriman')" />
                        <select id="delivery_method" name="delivery_method" class="block mt-1.5 w-full border-slate-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm">
                            <option value="">Semua Metode</option>
                            <option value="pickup" {{ request('delivery_method') === 'pickup' ? 'selected' : '' }}>Jemput</option>
                            <option value="self_drop" {{ request('delivery_method') === 'self_drop' ? 'selected' : '' }}>Antar Sendiri</option>
                        </select>
                    </div>

                    <!-- Filter Date -->
                    <div>
                        <x-input-label for="date" :value="__('Tanggal Masuk')" />
                        <input id="date" type="date" name="date" value="{{ request('date') }}" class="block mt-1.5 w-full border-slate-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm">
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-2">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold px-6 py-2.5 rounded-xl shadow-md transition text-sm flex-1">
                            Filter
                        </button>
                        <a href="{{ route('admin.orders.index') }}" class="border border-slate-200 text-slate-500 hover:bg-slate-50 font-bold px-4 py-2.5 rounded-xl transition text-sm">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Orders Table -->
            <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                <th class="px-6 py-4">No. Order</th>
                                <th class="px-6 py-4">Pelanggan</th>
                                <th class="px-6 py-4">Layanan</th>
                                <th class="px-6 py-4">Pengiriman</th>
                                <th class="px-6 py-4">Berat</th>
                                <th class="px-6 py-4">Total Harga</th>
                                <th class="px-6 py-4">Staff Handling</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($orders as $order)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-4 font-bold text-slate-800">{{ $order->order_number }}</td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-800">{{ $order->customer->name }}</div>
                                        <div class="text-xs text-slate-400 font-medium">{{ $order->customer->phone }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 font-medium">{{ $order->service->name }}</td>
                                    <td class="px-6 py-4 text-slate-500">
                                        {{ $order->delivery_method === 'pickup' ? 'Jemput' : 'Antar Sendiri' }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">
                                        {{ $order->weight_kg ? $order->weight_kg . ' ' . $order->service->unit : '-' }}
                                    </td>
                                    <td class="px-6 py-4 font-bold text-slate-800">
                                        {{ $order->total_price ? 'Rp ' . number_format($order->total_price, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-500 font-medium">
                                        {{ $order->staff ? $order->staff->name : '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold {{ $order->status_badge_class }}">
                                            {{ $order->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right flex items-center justify-end space-x-2">
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
                                        " class="text-blue-700 hover:text-blue-800 font-bold text-xs bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition">Edit</button>
                                        
                                        <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus permanen pesanan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 font-bold text-xs bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-12 text-center text-slate-400 italic">Tidak ditemukan pesanan laundry.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination footer -->
                @if($orders->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100">
                        {{ $orders->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Update Status Modal -->
        <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak style="display: none;">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-6" @click.away="openModal = false">
                <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                    <h3 class="text-base font-bold text-slate-800">Override Pesanan <span class="text-blue-700" x-text="selectedOrder.number"></span></h3>
                    <button @click="openModal = false" class="text-slate-400 hover:text-slate-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form :action="'/admin/orders/' + selectedOrder.id + '/process'" method="POST" class="space-y-4">
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

                    <!-- Input Berat -->
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
                        <textarea name="note" rows="2" placeholder="Contoh: Status di-override oleh Admin." class="w-full border-slate-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl"></textarea>
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
