<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Pesan Laundry Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm" 
                 x-data="{ 
                    deliveryMethod: 'pickup', 
                    serviceId: new URLSearchParams(window.location.search).get('service') || '', 
                    services: {{ $services->toJson() }},
                    get selectedService() {
                        return this.services.find(s => s.id == this.serviceId) || null;
                    }
                 }">
                <div class="border-b border-slate-100 pb-6 mb-8">
                    <h3 class="text-lg font-bold text-slate-800">Form Pemesanan Online</h3>
                    <p class="text-xs text-slate-400">Silakan isi data di bawah ini. Estimasi berat aktual dan harga akhir akan diinputkan oleh staff kami setelah barang diterima di workshop.</p>
                </div>

                <form method="POST" action="{{ route('customer.orders.store') }}" class="space-y-6">
                    @csrf

                    <!-- Jenis Layanan -->
                    <div>
                        <x-input-label for="service_id" :value="__('Jenis Layanan')" />
                        <select id="service_id" name="service_id" x-model="serviceId" required class="block mt-1.5 w-full border-slate-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm transition">
                            <option value="">-- Pilih Layanan --</option>
                            <template x-for="item in services" :key="item.id">
                                <option :value="item.id" x-text="item.name"></option>
                            </template>
                        </select>
                        <x-input-error :messages="$errors->get('service_id')" class="mt-2" />
                        
                        <!-- Service details card -->
                        <div x-show="selectedService" class="mt-3 p-4 bg-slate-50 border border-slate-100 rounded-xl space-y-1.5 transition" style="display: none;">
                            <div class="flex justify-between items-center text-xs font-semibold text-slate-700">
                                <span>Tarif Dasar:</span>
                                <span class="text-blue-700 font-extrabold" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(selectedService?.unit === 'kg' ? selectedService?.price_per_kg : selectedService?.price_per_pcs) + ' / ' + selectedService?.unit"></span>
                            </div>
                            <div class="flex justify-between items-center text-xs font-semibold text-slate-700">
                                <span>Estimasi Waktu:</span>
                                <span x-text="selectedService?.duration_hours + ' Jam'"></span>
                            </div>
                            <p class="text-[11px] text-slate-400 italic mt-1.5" x-text="selectedService?.description"></p>
                        </div>
                    </div>

                    <!-- Metode Pengiriman -->
                    <div>
                        <x-input-label :value="__('Metode Pengiriman')" />
                        <div class="grid grid-cols-2 gap-4 mt-2">
                            <!-- Option 1: Pickup -->
                            <label class="border p-4 rounded-xl cursor-pointer hover:border-blue-500 transition flex items-center space-x-3"
                                   :class="deliveryMethod === 'pickup' ? 'border-blue-500 bg-blue-50/20' : 'border-slate-200 bg-white'">
                                <input type="radio" name="delivery_method" value="pickup" x-model="deliveryMethod" class="text-blue-500 focus:ring-blue-500 border-slate-300">
                                <div>
                                    <span class="block text-sm font-bold text-slate-800">Minta Dijemput</span>
                                    <span class="block text-[11px] text-slate-400">Kurir mengambil ke alamat Anda</span>
                                </div>
                            </label>
                            
                            <!-- Option 2: Self Drop -->
                            <label class="border p-4 rounded-xl cursor-pointer hover:border-blue-500 transition flex items-center space-x-3"
                                   :class="deliveryMethod === 'self_drop' ? 'border-blue-500 bg-blue-50/20' : 'border-slate-200 bg-white'">
                                <input type="radio" name="delivery_method" value="self_drop" x-model="deliveryMethod" class="text-blue-500 focus:ring-blue-500 border-slate-300">
                                <div>
                                    <span class="block text-sm font-bold text-slate-800">Antar Sendiri</span>
                                    <span class="block text-[11px] text-slate-400">Anda membawa pakaian ke outlet</span>
                                </div>
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('delivery_method')" class="mt-2" />
                    </div>

                    <!-- Alamat & Waktu Penjemputan (Tampil jika pickup) -->
                    <div x-show="deliveryMethod === 'pickup'" class="space-y-6 border-t border-slate-100 pt-6 transition" style="display: none;">
                        <!-- Alamat Penjemputan -->
                        <div>
                            <x-input-label for="pickup_address" :value="__('Alamat Penjemputan')" />
                            <textarea id="pickup_address" name="pickup_address" rows="3" class="block mt-1.5 w-full border-slate-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm" placeholder="Tuliskan alamat lengkap penjemputan">{{ old('pickup_address', Auth::user()->address) }}</textarea>
                            <x-input-error :messages="$errors->get('pickup_address')" class="mt-2" />
                        </div>

                        <!-- Waktu Penjemputan -->
                        <div>
                            <x-input-label for="pickup_datetime" :value="__('Tanggal & Jam Penjemputan')" />
                            <input id="pickup_datetime" type="datetime-local" name="pickup_datetime" value="{{ old('pickup_datetime') }}" class="block mt-1.5 w-full border-slate-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm" />
                            <x-input-error :messages="$errors->get('pickup_datetime')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Catatan tambahan -->
                    <div>
                        <x-input-label for="customer_notes" :value="__('Catatan Tambahan (Optional)')" />
                        <textarea id="customer_notes" name="customer_notes" rows="2" class="block mt-1.5 w-full border-slate-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm" placeholder="Contoh: Pisahkan pakaian putih dari pakaian luntur.">{{ old('customer_notes') }}</textarea>
                        <x-input-error :messages="$errors->get('customer_notes')" class="mt-2" />
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end space-x-4 border-t border-slate-100 pt-6">
                        <a href="{{ route('customer.dashboard') }}" class="border border-slate-200 text-slate-500 hover:bg-slate-50 font-bold px-6 py-2.5 rounded-xl transition text-sm">
                            Kembali
                        </a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold px-6 py-2.5 rounded-xl shadow-md hover:shadow-lg transition text-sm">
                            Kirim Pesanan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
