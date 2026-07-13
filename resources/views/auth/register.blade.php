<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-extrabold text-slate-900">Buat Akun Baru</h2>
        <p class="text-sm text-slate-500 mt-1.5">Daftarkan diri Anda untuk mulai melakukan pemesanan laundry</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Hidden Service selection query parameter propagation -->
        <input type="hidden" name="service" value="{{ request()->query('service') }}">

        <!-- Name -->
        <div>
            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Nama Lengkap</label>
            <input id="name" 
                   type="text" 
                   name="name" 
                   value="{{ old('name') }}" 
                   required 
                   autofocus 
                   autocomplete="name"
                   placeholder="Nama Lengkap Anda"
                   class="block w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition duration-200">
            <x-input-error :messages="$errors->get('name')" class="mt-1.5 text-xs text-red-500" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Alamat Email</label>
            <input id="email" 
                   type="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   required 
                   autocomplete="username"
                   placeholder="nama@email.com"
                   class="block w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition duration-200">
            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-red-500" />
        </div>

        <!-- Phone number -->
        <div>
            <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Nomor WhatsApp</label>
            <input id="phone" 
                   type="text" 
                   name="phone" 
                   value="{{ old('phone') }}" 
                   required 
                   placeholder="Contoh: 08123456789"
                   class="block w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition duration-200">
            <x-input-error :messages="$errors->get('phone')" class="mt-1.5 text-xs text-red-500" />
        </div>

        <!-- Address -->
        <div>
            <label for="address" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Alamat Lengkap</label>
            <textarea id="address" 
                      name="address" 
                      required 
                      rows="3" 
                      placeholder="Nomor Rumah, Blok, Nama Jalan, Kelurahan/Kecamatan"
                      class="block w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition duration-200">{{ old('address') }}</textarea>
            <x-input-error :messages="$errors->get('address')" class="mt-1.5 text-xs text-red-500" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Password</label>
            <div class="relative">
                <input id="password" 
                       type="password" 
                       name="password" 
                       required 
                       autocomplete="new-password"
                       placeholder="Minimal 8 karakter"
                       class="block w-full rounded-xl border border-slate-200 pl-4 pr-12 py-3 text-sm text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition duration-200">
                <button type="button" 
                        onclick="togglePasswordVisibility('password', this)"
                        class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                    <i data-lucide="eye" class="w-5 h-5"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-red-500" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Konfirmasi Password</label>
            <div class="relative">
                <input id="password_confirmation" 
                       type="password" 
                       name="password_confirmation" 
                       required 
                       autocomplete="new-password"
                       placeholder="Ketik ulang password"
                       class="block w-full rounded-xl border border-slate-200 pl-4 pr-12 py-3 text-sm text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition duration-200">
                <button type="button" 
                        onclick="togglePasswordVisibility('password_confirmation', this)"
                        class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                    <i data-lucide="eye" class="w-5 h-5"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-xs text-red-500" />
        </div>

        <!-- Submit Button -->
        <div class="pt-3">
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-md hover:shadow-lg hover:shadow-blue-600/20 transition duration-200 text-sm">
                Daftar Akun
            </button>
        </div>
    </form>

    <div class="mt-8 text-center text-xs font-semibold text-slate-400 border-t border-slate-100 pt-6">
        Sudah memiliki akun? 
        <a href="{{ route('login', ['service' => request()->query('service')]) }}" class="text-blue-600 hover:text-blue-700 font-bold transition duration-150 ml-1">Masuk Sekarang</a>
    </div>
</x-guest-layout>
