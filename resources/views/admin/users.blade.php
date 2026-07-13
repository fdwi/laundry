<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-slate-800 leading-tight flex items-center gap-2">
            <i data-lucide="users" class="text-blue-600 w-6 h-6"></i>
            {{ __('Kelola Pelanggan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Alert Banner -->
            @if(session('success'))
                <div class="bg-blue-50 border border-blue-200 text-blue-900 px-4 py-3 rounded-xl flex items-center space-x-2" role="alert">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium text-sm">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Search and Filter Card -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Daftar Pelanggan Terdaftar</h3>
                    <p class="text-xs text-slate-400">Total pelanggan yang terdaftar di aplikasi L-Dry.</p>
                </div>
                
                <!-- Search Form -->
                <form method="GET" action="{{ route('admin.users.index') }}" class="w-full md:w-auto flex items-center gap-2">
                    <div class="relative w-full md:w-64">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                            <i data-lucide="search" class="w-4 h-4"></i>
                        </span>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, email, atau telepon..." 
                               class="w-full pl-9 pr-4 py-2 text-xs border border-slate-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm transition" />
                    </div>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold px-4 py-2 rounded-xl text-xs transition whitespace-nowrap">
                        Cari
                    </button>
                    @if($search)
                        <a href="{{ route('admin.users.index') }}" class="border border-slate-200 text-slate-500 hover:bg-slate-50 font-bold px-4 py-2 rounded-xl text-xs transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Customers Table/Card Card -->
            <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                <th class="px-6 py-4">ID Pelanggan</th>
                                <th class="px-6 py-4">Nama Pelanggan</th>
                                <th class="px-6 py-4">Alamat Email</th>
                                <th class="px-6 py-4">WhatsApp / HP</th>
                                <th class="px-6 py-4 text-center">Total Order</th>
                                <th class="px-6 py-4">Tanggal Daftar</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($users as $user)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-4 font-bold text-slate-400 text-xs">#CST-{{ $user->id }}</td>
                                    <td class="px-6 py-4 font-bold text-slate-800">{{ $user->name }}</td>
                                    <td class="px-6 py-4 text-slate-600 font-medium">{{ $user->email }}</td>
                                    <td class="px-6 py-4 text-slate-500">
                                        @if($user->phone)
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->phone) }}" target="_blank" class="hover:text-blue-600 flex items-center gap-1">
                                                <i data-lucide="phone" class="w-3.5 h-3.5 text-slate-400"></i>
                                                {{ $user->phone }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-slate-800">{{ $user->orders_count }} order</td>
                                    <td class="px-6 py-4 text-slate-400 text-xs">{{ $user->created_at->format('d M Y') }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold {{ $user->is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                            {{ $user->is_active ? 'Aktif' : 'Non-aktif' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <form method="POST" action="{{ route('admin.users.toggle-status', $user->id) }}">
                                            @csrf
                                            <button type="submit" class="font-bold text-xs bg-slate-50 border border-slate-200 hover:bg-slate-100 px-3 py-1.5 rounded-lg transition {{ $user->is_active ? 'text-red-600 hover:text-red-700' : 'text-green-600 hover:text-green-700' }}">
                                                {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-slate-400 italic">Belum ada pelanggan terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="block md:hidden divide-y divide-slate-100">
                    @forelse($users as $user)
                        <div class="p-5 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-400 text-xs">#CST-{{ $user->id }}</span>
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $user->is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                    {{ $user->is_active ? 'Aktif' : 'Non-aktif' }}
                                </span>
                            </div>
                            
                            <div>
                                <h4 class="font-bold text-slate-800 text-base leading-tight">{{ $user->name }}</h4>
                                <p class="text-xs text-slate-600 font-medium mt-1">{{ $user->email }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-3 pt-3 text-xs border-t border-slate-50">
                                <div>
                                    <span class="text-slate-400 block text-[9px] uppercase tracking-wider font-bold">WhatsApp / HP</span>
                                    @if($user->phone)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->phone) }}" target="_blank" class="text-blue-600 font-bold hover:underline flex items-center gap-1 mt-1">
                                            <i data-lucide="phone" class="w-3 h-3"></i>
                                            {{ $user->phone }}
                                        </a>
                                    @else
                                        <span class="text-slate-500 font-medium mt-1 block">-</span>
                                    @endif
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[9px] uppercase tracking-wider font-bold">Total Order</span>
                                    <span class="text-slate-850 font-bold block mt-1">{{ $user->orders_count }} order</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-3 border-t border-slate-50">
                                <div class="text-[11px] text-slate-400 font-medium">
                                    Daftar: {{ $user->created_at->format('d M Y') }}
                                </div>
                                <form method="POST" action="{{ route('admin.users.toggle-status', $user->id) }}">
                                    @csrf
                                    <button type="submit" class="font-bold text-xs bg-slate-50 border border-slate-200 hover:bg-slate-100 px-3.5 py-2 rounded-xl transition {{ $user->is_active ? 'text-red-600 hover:text-red-700' : 'text-green-600 hover:text-green-700' }}">
                                        {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center text-slate-400 italic">Belum ada pelanggan terdaftar.</div>
                    @endforelse
                </div>
            </div>

            <!-- Pagination Links -->
            @if($users->hasPages())
                <div class="bg-white border border-slate-100 rounded-3xl p-4 shadow-sm">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
