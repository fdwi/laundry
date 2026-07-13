<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Pengaturan Operasional Laundry') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <!-- Alert Banner -->
            @if(session('success'))
                <div class="bg-blue-50 border border-blue-200 text-blue-900 px-4 py-3 rounded-xl flex items-center space-x-2 mb-6" role="alert">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium text-sm">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm">
                <div class="border-b border-slate-100 pb-6 mb-8">
                    <h3 class="text-base font-bold text-slate-800">Detail Outlet & Profil Bisnis</h3>
                    <p class="text-xs text-slate-400">Informasi di bawah ini digunakan pada halaman utama (landing page), rincian kontak pesanan pelanggan, serta cetakan invoice PDF.</p>
                </div>

                <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
                    @csrf

                    <!-- Nama Bisnis -->
                    <div>
                        <x-input-label for="business_name" :value="__('Nama Laundry / Bisnis')" />
                        <x-text-input id="business_name" class="block mt-1.5 w-full" type="text" name="business_name" :value="old('business_name', $settings['business_name'])" required />
                        <x-input-error :messages="$errors->get('business_name')" class="mt-2" />
                    </div>

                    <!-- Nomor WhatsApp -->
                    <div>
                        <x-input-label for="whatsapp" :value="__('Nomor Kontak WhatsApp (Format API)')" />
                        <x-text-input id="whatsapp" class="block mt-1.5 w-full" type="text" name="whatsapp" :value="old('whatsapp', $settings['whatsapp'])" required placeholder="Contoh: 6281234567890" />
                        <span class="text-[10px] text-slate-400 mt-1 block">Gunakan kode negara di depan nomor tanpa tanda tambah (+) atau spasi. Contoh: 6281234567890</span>
                        <x-input-error :messages="$errors->get('whatsapp')" class="mt-2" />
                    </div>

                    <!-- Jam Operasional -->
                    <div>
                        <x-input-label for="operating_hours" :value="__('Jam Kerja / Jam Operasional')" />
                        <x-text-input id="operating_hours" class="block mt-1.5 w-full" type="text" name="operating_hours" :value="old('operating_hours', $settings['operating_hours'])" required placeholder="Contoh: 08:00 - 20:00" />
                        <x-input-error :messages="$errors->get('operating_hours')" class="mt-2" />
                    </div>

                    <!-- Poin Keterlambatan -->
                    <div>
                        <x-input-label for="late_delivery_points" :value="__('Poin Dispensasi Keterlambatan')" />
                        <x-text-input id="late_delivery_points" class="block mt-1.5 w-full" type="number" name="late_delivery_points" :value="old('late_delivery_points', $settings['late_delivery_points'])" required min="0" placeholder="Contoh: 50" />
                        <span class="text-[10px] text-slate-400 mt-1 block">Jumlah poin yang diberikan ke customer apabila laundry diselesaikan melebihi estimasi waktu pengerjaan.</span>
                        <x-input-error :messages="$errors->get('late_delivery_points')" class="mt-2" />
                    </div>

                    <!-- Alamat Outlet -->
                    <div>
                        <x-input-label for="address" :value="__('Alamat Outlet / Workshop')" />
                        <textarea id="address" name="address" rows="3" class="block mt-1.5 w-full border-slate-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm" required>{{ old('address', $settings['address']) }}</textarea>
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end border-t border-slate-100 pt-6">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold px-6 py-2.5 rounded-xl shadow-md transition text-sm">
                            Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
