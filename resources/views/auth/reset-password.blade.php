<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <div class="relative">
                <x-text-input id="password" class="block mt-1 w-full pr-12" type="password" name="password" required autocomplete="new-password" />
                <button type="button" 
                        onclick="togglePasswordVisibility('password', this)"
                        class="absolute inset-y-0 right-0 pr-4 pt-1 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                    <i data-lucide="eye" class="w-5 h-5"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <div class="relative">
                <x-text-input id="password_confirmation" class="block mt-1 w-full pr-12"
                                    type="password"
                                    name="password_confirmation" required autocomplete="new-password" />
                <button type="button" 
                        onclick="togglePasswordVisibility('password_confirmation', this)"
                        class="absolute inset-y-0 right-0 pr-4 pt-1 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                    <i data-lucide="eye" class="w-5 h-5"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
