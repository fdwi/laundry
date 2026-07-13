<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Tambah Layanan Laundry Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm">
                <form method="POST" action="{{ route('admin.services.store') }}" class="space-y-6">
                    @csrf

                    <!-- Nama Layanan -->
                    <div>
                        <x-input-label for="name" :value="__('Nama Layanan')" />
                        <x-text-input id="name" class="block mt-1.5 w-full" type="text" name="name" :value="old('name')" required autofocus placeholder="Contoh: Express Service, Cuci Sepatu" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <x-input-label for="description" :value="__('Deskripsi Singkat')" />
                        <textarea id="description" name="description" rows="3" class="block mt-1.5 w-full border-slate-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm" required placeholder="Tuliskan spesifikasi & detail penanganan layanan">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <!-- Satuan & Tarif Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Satuan -->
                        <div>
                            <x-input-label for="unit" :value="__('Satuan Tarif')" />
                            <select id="unit" name="unit" required class="block mt-1.5 w-full border-slate-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm">
                                <option value="kg" {{ old('unit') === 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                                <option value="pcs" {{ old('unit') === 'pcs' ? 'selected' : '' }}>Satuan / Buah (pcs)</option>
                            </select>
                            <x-input-error :messages="$errors->get('unit')" class="mt-2" />
                        </div>

                        <!-- Tarif -->
                        <div>
                            <x-input-label for="price" :value="__('Harga per Satuan (Rupiah)')" />
                            <x-text-input id="price" class="block mt-1.5 w-full" type="number" name="price" :value="old('price')" required placeholder="Contoh: 12000" />
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Estimasi Waktu -->
                    <div>
                        <x-input-label for="duration_hours" :value="__('Estimasi Waktu Selesai (Jam)')" />
                        <x-text-input id="duration_hours" class="block mt-1.5 w-full" type="number" name="duration_hours" :value="old('duration_hours')" required placeholder="Contoh: 24 (untuk 1 hari selesai)" />
                        <x-input-error :messages="$errors->get('duration_hours')" class="mt-2" />
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end space-x-4 border-t border-slate-100 pt-6">
                        <a href="{{ route('admin.services.index') }}" class="border border-slate-200 text-slate-500 hover:bg-slate-50 font-bold px-6 py-2.5 rounded-xl transition text-sm">
                            Kembali
                        </a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold px-6 py-2.5 rounded-xl shadow-md transition text-sm">
                            Simpan Layanan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
