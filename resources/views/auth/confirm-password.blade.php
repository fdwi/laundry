<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <div class="relative">
                <x-text-input id="password" class="block mt-1 w-full pr-12"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />
                <button type="button" 
                        onclick="togglePasswordVisibility('password', this)"
                        class="absolute inset-y-0 right-0 pr-4 pt-1 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                    <i data-lucide="eye" class="w-5 h-5"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
