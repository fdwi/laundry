<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-extrabold text-slate-900">Selamat Datang Kembali</h2>
        <p class="text-sm text-slate-500 mt-1.5">Masuk untuk mengelola pesanan & melacak laundry Anda</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Hidden Service selection query parameter propagation -->
        <input type="hidden" name="service" value="{{ request()->query('service') }}">

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Alamat Email</label>
            <input id="email" 
                   type="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   required 
                   autofocus 
                   autocomplete="username"
                   placeholder="nama@email.com"
                   class="block w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition duration-200">
            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-red-500" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex justify-between items-center mb-1.5">
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-600">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition duration-150" href="{{ route('password.request') }}">
                        Lupa password?
                    </a>
                @endif
            </div>
            <div class="relative">
                <input id="password" 
                       type="password" 
                       name="password" 
                       required 
                       autocomplete="current-password"
                       placeholder="••••••••"
                       class="block w-full rounded-xl border border-slate-200 pl-4 pr-12 py-3 text-sm text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition duration-200">
                <button type="button" 
                        onclick="togglePasswordVisibility('password', this)"
                        class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                    <i data-lucide="eye" class="w-5 h-5"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-red-500" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input id="remember_me" 
                   type="checkbox" 
                   name="remember"
                   class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-4 h-4 transition duration-200">
            <span class="ms-2.5 text-xs font-semibold text-slate-500">Ingat perangkat saya</span>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-md hover:shadow-lg hover:shadow-blue-600/20 transition duration-200 text-sm">
                Masuk ke Akun
            </button>
        </div>
    </form>

    <div class="mt-8 text-center text-xs font-semibold text-slate-400 border-t border-slate-100 pt-6">
        Belum memiliki akun? 
        <a href="{{ route('register', ['service' => request()->query('service')]) }}" class="text-blue-600 hover:text-blue-700 font-bold transition duration-150 ml-1">Daftar Sekarang</a>
    </div>
</x-guest-layout>
