<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 sticky top-0 z-40">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('landing') }}" class="flex items-center">
                        <img src="{{ asset('logo.png') }}" class="h-9 w-auto object-contain" alt="L-DRY Logo">
                    </a>
                </div>

                <!-- Navigation Links based on user role -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @if(Auth::user()->role === 'admin')
                        <!-- Admin Links -->
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            Dashboard
                        </x-nav-link>
                        <x-nav-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.index')">
                            Semua Pesanan
                        </x-nav-link>
                        <x-nav-link :href="route('admin.services.index')" :active="request()->routeIs('admin.services.index')">
                            Kelola Layanan
                        </x-nav-link>
                        <x-nav-link :href="route('admin.kurir.index')" :active="request()->routeIs('admin.kurir.index')">
                            Kelola Kurir
                        </x-nav-link>
                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                            Kelola Pelanggan
                        </x-nav-link>
                        <x-nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.index')">
                            Laporan
                        </x-nav-link>
                        <x-nav-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.index')">
                            Pengaturan
                        </x-nav-link>
                    @elseif(Auth::user()->role === 'kurir')
                        <!-- Kurir Links -->
                        <x-nav-link :href="route('kurir.dashboard')" :active="request()->routeIs('kurir.dashboard')">
                            Dashboard
                        </x-nav-link>
                        <x-nav-link :href="route('kurir.orders.index')" :active="request()->routeIs('kurir.orders.index')">
                            Kelola Pesanan
                        </x-nav-link>
                        <x-nav-link :href="route('kurir.orders.pickup')" :active="request()->routeIs('kurir.orders.pickup')">
                            Jadwal Penjemputan
                        </x-nav-link>
                    @else
                        <!-- Customer Links -->
                        <x-nav-link :href="route('customer.dashboard')" :active="request()->routeIs('customer.dashboard')">
                            Dashboard
                        </x-nav-link>
                        <x-nav-link :href="route('customer.orders.create')" :active="request()->routeIs('customer.orders.create')">
                            Pesan Laundry
                        </x-nav-link>
                        <x-nav-link :href="route('customer.orders.history')" :active="request()->routeIs('customer.orders.history')">
                            Riwayat Pesanan
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-semibold rounded-md text-gray-600 bg-white hover:text-blue-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <span class="ms-1.5 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded bg-blue-50 text-blue-800">
                                {{ Auth::user()->role }}
                            </span>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profil Akun') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                 {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-gray-50">
        <div class="pt-2 pb-3 space-y-1">
            @if(Auth::user()->role === 'admin')
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    Dashboard
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.index')">
                    Semua Pesanan
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.services.index')" :active="request()->routeIs('admin.services.index')">
                    Kelola Layanan
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.kurir.index')" :active="request()->routeIs('admin.kurir.index')">
                    Kelola Kurir
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                    Kelola Pelanggan
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.index')">
                    Laporan
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.index')">
                    Pengaturan
                </x-responsive-nav-link>
            @elseif(Auth::user()->role === 'kurir')
                <x-responsive-nav-link :href="route('kurir.dashboard')" :active="request()->routeIs('kurir.dashboard')">
                    Dashboard
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('kurir.orders.index')" :active="request()->routeIs('kurir.orders.index')">
                    Kelola Pesanan
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('kurir.orders.pickup')" :active="request()->routeIs('kurir.orders.pickup')">
                    Jadwal Penjemputan
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('customer.dashboard')" :active="request()->routeIs('customer.dashboard')">
                    Dashboard
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('customer.orders.create')" :active="request()->routeIs('customer.orders.create')">
                    Pesan Laundry
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('customer.orders.history')" :active="request()->routeIs('customer.orders.history')">
                    Riwayat Pesanan
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profil Akun') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
