<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Edit Akun Kurir') }} — {{ $kurir->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm">
                <form method="POST" action="{{ route('admin.kurir.update', $kurir->id) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Nama Lengkap -->
                    <div>
                        <x-input-label for="name" :value="__('Nama Lengkap')" />
                        <x-text-input id="name" class="block mt-1.5 w-full" type="text" name="name" :value="old('name', $kurir->name)" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email -->
                    <div>
                        <x-input-label for="email" :value="__('Alamat Email')" />
                        <x-text-input id="email" class="block mt-1.5 w-full" type="email" name="email" :value="old('email', $kurir->email)" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <x-input-label for="password" :value="__('Password Baru (Kosongkan jika tidak ingin diubah)')" />
                        <div class="relative">
                            <x-text-input id="password" class="block mt-1.5 w-full pr-12" type="password" name="password" placeholder="Masukkan password baru untuk reset" />
                            <button type="button" 
                                    onclick="togglePasswordVisibility('password', this)"
                                    class="absolute inset-y-0 right-0 pr-4 pt-1 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                                <i data-lucide="eye" class="w-5 h-5"></i>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Nomor Kontak -->
                    <div>
                        <x-input-label for="phone" :value="__('Nomor WhatsApp')" />
                        <x-text-input id="phone" class="block mt-1.5 w-full" type="text" name="phone" :value="old('phone', $kurir->phone)" required />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>

                    <!-- Alamat -->
                    <div>
                        <x-input-label for="address" :value="__('Alamat Tempat Tinggal')" />
                        <textarea id="address" name="address" rows="3" class="block mt-1.5 w-full border-slate-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm" required>{{ old('address', $kurir->address) }}</textarea>
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>

                    <!-- Status Login -->
                    <div>
                        <x-input-label for="is_active" :value="__('Status Login Kurir')" />
                        <select id="is_active" name="is_active" required class="block mt-1.5 w-full border-slate-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm">
                            <option value="1" {{ old('is_active', $kurir->is_active) == 1 ? 'selected' : '' }}>Aktif (Diizinkan Login)</option>
                            <option value="0" {{ old('is_active', $kurir->is_active) == 0 ? 'selected' : '' }}>Ditangguhkan (Blokir Login)</option>
                        </select>
                        <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end space-x-4 border-t border-slate-100 pt-6">
                        <a href="{{ route('admin.kurir.index') }}" class="border border-slate-200 text-slate-500 hover:bg-slate-50 font-bold px-6 py-2.5 rounded-xl transition text-sm">
                            Kembali
                        </a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold px-6 py-2.5 rounded-xl shadow-md transition text-sm">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
