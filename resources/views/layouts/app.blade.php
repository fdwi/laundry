<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>L-DRY — Dashboard</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Lucide Icons CDN -->
        <script src="https://unpkg.com/lucide@latest"></script>

        <style>
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
            }
            h1, h2, h3, h4, h5, h6 {
                font-family: 'Outfit', sans-serif;
            }
        </style>
    </head>
    <body class="antialiased bg-slate-50/50 text-slate-800">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen flex">
            <!-- Sidebar Overlay for mobile -->
            <div x-show="sidebarOpen" 
                 class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm lg:hidden" 
                 @click="sidebarOpen = false"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 style="display: none;"></div>

            <!-- Sidebar Container -->
            <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" 
                 class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-slate-100 flex flex-col justify-between transition-transform duration-300 ease-out border-r border-slate-800 lg:static lg:inset-auto lg:translate-x-0">
                 
                 <div>
                     <!-- Sidebar Header -->
                     <div class="h-20 flex items-center px-6 border-b border-slate-800/60 justify-between">
                         <a href="{{ route('landing') }}" class="flex items-center">
                             <img src="{{ asset('logo.png') }}" class="h-10 w-auto object-contain brightness-0 invert" alt="L-DRY Logo">
                         </a>
                         <!-- Close button for mobile -->
                         <button @click="sidebarOpen = false" class="text-slate-400 hover:text-white lg:hidden">
                             <i data-lucide="x" class="w-5 h-5"></i>
                         </button>
                     </div>

                     <!-- User Profile Widget -->
                     <div class="p-5 border-b border-slate-800/40 bg-slate-950/20">
                          <div class="flex items-center gap-3">
                              <div class="w-10 h-10 rounded-xl bg-blue-600/10 border border-blue-600/20 flex items-center justify-center text-blue-400 font-extrabold text-sm uppercase shrink-0">
                                  {{ substr(Auth::user()->name, 0, 2) }}
                              </div>
                              <div class="flex-grow min-w-0">
                                  <h4 class="text-sm font-bold text-slate-200 truncate leading-tight">{{ Auth::user()->name }}</h4>
                                  <div class="flex flex-wrap items-center gap-1.5 mt-1.5">
                                      <span class="inline-flex px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded bg-blue-600/10 text-blue-400 border border-blue-600/20">
                                          {{ Auth::user()->role === 'kurir' ? 'Kurir' : (Auth::user()->role === 'admin' ? 'Owner / Admin' : 'Pelanggan') }}
                                      </span>
                                      @if(Auth::user()->role === 'customer')
                                          <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded bg-amber-500/10 text-amber-400 border border-amber-500/20" title="Poin Saya">
                                              <i data-lucide="award" class="w-3 h-3 text-amber-400 shrink-0"></i>
                                              <span>{{ Auth::user()->points ?? 0 }} Pts</span>
                                          </span>
                                      @endif
                                  </div>
                              </div>
                          </div>
                     </div>

                     <!-- Navigation Links -->
                     <nav class="p-4 space-y-1.5">
                         @if(Auth::user()->role === 'admin')
                             <x-sidebar-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" icon="layout-dashboard">Dashboard</x-sidebar-link>
                             <x-sidebar-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')" icon="shopping-bag">Semua Pesanan</x-sidebar-link>
                             <x-sidebar-link :href="route('admin.services.index')" :active="request()->routeIs('admin.services.*')" icon="sparkles">Kelola Layanan</x-sidebar-link>
                             <x-sidebar-link :href="route('admin.kurir.index')" :active="request()->routeIs('admin.kurir.*')" icon="users">Kelola Kurir</x-sidebar-link>
                             <x-sidebar-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" icon="user">Kelola Pelanggan</x-sidebar-link>
                             <x-sidebar-link :href="route('admin.rewards.index')" :active="request()->routeIs('admin.rewards.*')" icon="gift">Kelola Hadiah</x-sidebar-link>
                             <x-sidebar-link :href="route('admin.redemptions.index')" :active="request()->routeIs('admin.redemptions.*')" icon="ticket">Penukaran Poin</x-sidebar-link>
                             <x-sidebar-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.*')" icon="bar-chart-3">Laporan Omzet</x-sidebar-link>
                             <x-sidebar-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.*')" icon="settings">Pengaturan</x-sidebar-link>
                         @elseif(Auth::user()->role === 'kurir')
                             <x-sidebar-link :href="route('kurir.dashboard')" :active="request()->routeIs('kurir.dashboard')" icon="layout-dashboard">Dashboard</x-sidebar-link>
                             <x-sidebar-link :href="route('kurir.orders.index')" :active="request()->routeIs('kurir.orders.index') || request()->routeIs('kurir.orders.index')" icon="package">Kelola Pesanan</x-sidebar-link>
                             <x-sidebar-link :href="route('kurir.orders.pickup')" :active="request()->routeIs('kurir.orders.pickup')" icon="calendar">Jadwal Jemput</x-sidebar-link>
                         @else
                             <x-sidebar-link :href="route('customer.dashboard')" :active="request()->routeIs('customer.dashboard')" icon="layout-dashboard">Dashboard</x-sidebar-link>
                             <x-sidebar-link :href="route('customer.orders.create')" :active="request()->routeIs('customer.orders.create')" icon="plus-circle">Pesan Laundry</x-sidebar-link>
                             <x-sidebar-link :href="route('customer.orders.history')" :active="request()->routeIs('customer.orders.history') || request()->routeIs('customer.orders.show')" icon="history">Riwayat Pesanan</x-sidebar-link>
                             <x-sidebar-link :href="route('customer.rewards.index')" :active="request()->routeIs('customer.rewards.*')" icon="gift">Hadiah & Poin</x-sidebar-link>
                         @endif
                     </nav>
                 </div>

                 <!-- Sidebar Footer -->
                 <div class="p-4 border-t border-slate-800/60 bg-slate-950/10 space-y-2">
                     <x-sidebar-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')" icon="user">Profil Akun</x-sidebar-link>
                     <form method="POST" action="{{ route('logout') }}" class="w-full">
                         @csrf
                         <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-400 hover:bg-red-500/10 hover:text-red-400 transition duration-200">
                             <i data-lucide="log-out" class="w-4 h-4"></i>
                             <span>Keluar</span>
                         </button>
                     </form>
                 </div>
            </div>

            <!-- Main Content Area -->
            <div class="flex-grow flex flex-col min-w-0 overflow-y-auto">
                <!-- Top Navigation Bar -->
                <header class="h-20 bg-white border-b border-slate-100 flex items-center justify-between px-6 md:px-8 sticky top-0 z-30">
                    <div class="flex items-center gap-4">
                        <!-- Hamburger Button -->
                        <button @click="sidebarOpen = true" class="p-2 -ml-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-50 lg:hidden">
                            <i data-lucide="menu" class="w-6 h-6"></i>
                        </button>
                        
                        <!-- Header Slot / Title -->
                        <div>
                            @if (isset($header))
                                {{ $header }}
                            @else
                                <h2 class="font-bold text-lg text-slate-800">Dashboard</h2>
                            @endif
                        </div>
                    </div>

                    <!-- Top right actions -->
                    <div class="flex items-center gap-3"
                         x-data="notificationBell()"
                         x-init="init()">

                        <!-- Notification Bell -->
                        <div class="relative">
                            <button
                                id="notif-bell-btn"
                                @click="open = !open; if(open) fetchNotifications()"
                                class="relative p-2.5 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition border border-slate-100"
                                aria-label="Notifikasi">
                                <i data-lucide="bell" class="w-5 h-5"></i>
                                <!-- Badge -->
                                <span
                                    x-show="unreadCount > 0"
                                    x-text="unreadCount > 9 ? '9+' : unreadCount"
                                    class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-black w-5 h-5 rounded-full flex items-center justify-center shadow-lg animate-pulse"
                                    style="display:none;"></span>
                            </button>

                            <!-- Dropdown Panel -->
                            <div
                                x-show="open"
                                @click.outside="open = false"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                                class="absolute right-0 mt-2 w-96 bg-white rounded-2xl shadow-2xl border border-slate-100 z-50 overflow-hidden"
                                style="display:none;">

                                <!-- Header -->
                                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="bell" class="w-4 h-4 text-blue-700"></i>
                                        <span class="text-sm font-bold text-slate-800">Notifikasi</span>
                                        <span x-show="unreadCount > 0"
                                              x-text="unreadCount + ' baru'"
                                              class="bg-red-100 text-red-600 text-[10px] font-bold px-2 py-0.5 rounded-full"></span>
                                    </div>
                                    <button
                                        x-show="unreadCount > 0"
                                        @click="markAllRead()"
                                        class="text-[11px] font-bold text-blue-700 hover:text-blue-800 hover:underline">
                                        Tandai semua dibaca
                                    </button>
                                </div>

                                <!-- List -->
                                <div class="max-h-80 overflow-y-auto divide-y divide-slate-50">
                                    <template x-if="notifications.length === 0">
                                        <div class="flex flex-col items-center justify-center py-10 gap-3">
                                            <i data-lucide="bell-off" class="w-10 h-10 text-slate-200"></i>
                                            <p class="text-sm text-slate-400 font-medium">Tidak ada notifikasi</p>
                                        </div>
                                    </template>

                                    <template x-for="n in notifications" :key="n.id">
                                        <div
                                            @click="markRead(n)"
                                            :class="n.is_read ? 'bg-white' : 'bg-blue-50/60'"
                                            class="px-5 py-4 hover:bg-slate-50 cursor-pointer transition group">
                                            <div class="flex items-start gap-3">
                                                <div :class="n.is_read ? 'bg-slate-100 text-slate-400' : 'bg-blue-100 text-blue-700'"
                                                     class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 mt-0.5 transition">
                                                    <i :data-lucide="n.icon" class="w-4 h-4"></i>
                                                </div>
                                                <div class="flex-grow min-w-0">
                                                    <p class="text-xs font-bold text-slate-800 leading-snug" x-text="n.title"></p>
                                                    <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed line-clamp-2" x-text="n.message"></p>
                                                    <div class="flex items-center justify-between mt-1.5">
                                                        <span class="text-[10px] text-slate-400 font-medium" x-text="n.created_at"></span>
                                                        <a x-show="n.order_id"
                                                           :href="'/customer/orders/' + n.order_id"
                                                           class="text-[10px] text-blue-700 font-bold hover:underline">
                                                            Lacak Pesanan &rarr;
                                                        </a>
                                                    </div>
                                                </div>
                                                <div x-show="!n.is_read"
                                                     class="w-2 h-2 rounded-full bg-blue-500 shrink-0 mt-1.5"></div>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Footer -->
                                <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50 text-center">
                                    <span class="text-[11px] text-slate-400">Notifikasi tersimpan selama 30 hari</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Toast Messages -->
                @if (session('success') || session('error'))
                    <div x-data="{ show: true }" 
                         x-show="show" 
                         x-init="setTimeout(() => show = false, 5000)" 
                         class="max-w-7xl w-full mx-auto px-6 md:px-8 mt-6"
                         style="display: none;">
                        @if (session('success'))
                            <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl p-4 flex justify-between items-center shadow-sm">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                                </div>
                                <button @click="show = false" class="text-green-500 hover:text-green-800">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl p-4 flex justify-between items-center shadow-sm">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600"></i>
                                    <span class="text-sm font-semibold">{{ session('error') }}</span>
                                </div>
                                <button @click="show = false" class="text-red-500 hover:text-red-800">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Page Content -->
                <main class="flex-grow">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <script>
            // Initialize Lucide Icons
            lucide.createIcons();

            function togglePasswordVisibility(inputId, btnEl) {
                const input = document.getElementById(inputId);
                const icon = btnEl.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.setAttribute('data-lucide', 'eye-off');
                } else {
                    input.type = 'password';
                    icon.setAttribute('data-lucide', 'eye');
                }
                lucide.createIcons();
            }

            // Notification Bell Alpine.js Component
            function notificationBell() {
                return {
                    open: false,
                    notifications: [],
                    unreadCount: 0,
                    pollInterval: null,

                    init() {
                        // Fetch badge count on load
                        this.fetchCount();
                        // Poll for unread count every 30 seconds
                        this.pollInterval = setInterval(() => {
                            this.fetchCount();
                        }, 30000);
                    },

                    fetchCount() {
                        fetch('/notifications', {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        })
                        .then(r => r.json())
                        .then(data => {
                            this.unreadCount = data.unread_count;
                            // Re-render Lucide after DOM changes
                            this.$nextTick(() => lucide.createIcons());
                        })
                        .catch(() => {});
                    },

                    fetchNotifications() {
                        fetch('/notifications', {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        })
                        .then(r => r.json())
                        .then(data => {
                            this.notifications = data.notifications;
                            this.unreadCount = data.unread_count;
                            this.$nextTick(() => lucide.createIcons());
                        })
                        .catch(() => {});
                    },

                    markRead(notif) {
                        if (!notif.is_read) {
                            fetch('/notifications/' + notif.id + '/read', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'X-Requested-With': 'XMLHttpRequest',
                                }
                            }).then(() => {
                                notif.is_read = true;
                                this.unreadCount = Math.max(0, this.unreadCount - 1);
                                this.$nextTick(() => lucide.createIcons());
                            });
                        }
                    },

                    markAllRead() {
                        fetch('/notifications/read-all', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest',
                            }
                        }).then(() => {
                            this.notifications.forEach(n => n.is_read = true);
                            this.unreadCount = 0;
                            this.$nextTick(() => lucide.createIcons());
                        });
                    }
                };
            }
        </script>
    </body>
</html>
