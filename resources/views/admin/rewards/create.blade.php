<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Tambah Hadiah Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm">
                <div class="border-b border-slate-100 pb-6 mb-8">
                    <h3 class="text-base font-bold text-slate-800">Detail Item Hadiah</h3>
                    <p class="text-xs text-slate-400">Pastikan informasi nama, poin, dan jumlah stok sesuai untuk ditampilkan ke katalog customer.</p>
                </div>

                <form method="POST" action="{{ route('admin.rewards.store') }}" class="space-y-6">
                    @csrf

                    <!-- Nama Hadiah -->
                    <div>
                        <x-input-label for="name" :value="__('Nama Hadiah / Voucher')" />
                        <x-text-input id="name" class="block mt-1.5 w-full" type="text" name="name" :value="old('name')" required placeholder="Contoh: Voucher Diskon Rp 10.000" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <x-input-label for="description" :value="__('Deskripsi Lengkap')" />
                        <textarea id="description" name="description" rows="3" class="block mt-1.5 w-full border-slate-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm" required placeholder="Jelaskan detail hadiah dan cara penggunaannya...">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Biaya Poin -->
                        <div>
                            <x-input-label for="points_cost" :value="__('Biaya Poin (Penukaran)')" />
                            <x-text-input id="points_cost" class="block mt-1.5 w-full" type="number" name="points_cost" :value="old('points_cost', 100)" min="1" required />
                            <x-input-error :messages="$errors->get('points_cost')" class="mt-2" />
                        </div>

                        <!-- Jumlah Stok -->
                        <div>
                            <x-input-label for="stock" :value="__('Jumlah Stok Hadiah')" />
                            <x-text-input id="stock" class="block mt-1.5 w-full" type="number" name="stock" :value="old('stock', 99)" min="0" required />
                            <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Status Keaktifan -->
                    <div>
                        <x-input-label for="is_active" :value="__('Status Keaktifan')" />
                        <select id="is_active" name="is_active" class="block mt-1.5 w-full border-slate-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm">
                            <option value="1" {{ old('is_active', '1') === '1' ? 'selected' : '' }}>Aktif (Ditampilkan ke katalog)</option>
                            <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>Non-Aktif (Disembunyikan)</option>
                        </select>
                        <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end space-x-4 border-t border-slate-100 pt-6">
                        <a href="{{ route('admin.rewards.index') }}" class="border border-slate-200 text-slate-500 hover:bg-slate-50 font-bold px-6 py-2.5 rounded-xl transition text-sm">
                            Batal
                        </a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2.5 rounded-xl shadow-md transition text-sm">
                            Simpan Hadiah
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
