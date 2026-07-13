<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Daftar Penukaran Poin (Klaim Hadiah)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Alert Banner -->
            @if(session('success'))
                <div class="bg-blue-50 border border-blue-200 text-blue-900 px-4 py-3 rounded-2xl flex items-center justify-between shadow-sm" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-semibold text-sm">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-blue-500 hover:text-blue-800">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-900 px-4 py-3 rounded-2xl flex items-center justify-between shadow-sm" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center space-x-2">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-red-500"></i>
                        <span class="font-semibold text-sm">{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-red-500 hover:text-red-800">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            @endif

            <!-- Search and Filter Bar -->
            <div class="bg-white border border-slate-100 p-6 rounded-3xl shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
                <form method="GET" action="{{ route('admin.redemptions.index') }}" class="w-full flex flex-col md:flex-row gap-3">
                    <!-- Search Input -->
                    <div class="flex-grow relative">
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}" 
                            placeholder="Cari kode, nama customer, atau nama hadiah..." 
                            class="w-full border-slate-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl pl-10 pr-4 py-2.5 text-sm transition"
                        />
                        <div class="absolute left-3 top-3.5 text-slate-400">
                            <i data-lucide="search" class="w-4 h-4"></i>
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="w-full md:w-48">
                        <select name="status" class="w-full border-slate-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl py-2.5 text-sm transition" onchange="this.form.submit()">
                            <option value="">-- Semua Status --</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif (Belum Digunakan)</option>
                            <option value="used" {{ request('status') === 'used' ? 'selected' : '' }}>Voucher Digunakan</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Fisik Diserahkan</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan / Refund</option>
                        </select>
                    </div>

                    <!-- Filter Button -->
                    <button type="submit" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs px-5 py-2.5 rounded-xl border border-slate-200 transition">
                        Filter
                    </button>
                    
                    @if(request()->anyFilled(['search', 'status']))
                        <a href="{{ route('admin.redemptions.index') }}" class="bg-red-50 hover:bg-red-100 text-red-600 font-bold text-xs px-5 py-2.5 rounded-xl border border-red-100 transition text-center flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Redemptions List Table -->
            <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                <th class="px-6 py-4">Kode Voucher</th>
                                <th class="px-6 py-4">Pelanggan</th>
                                <th class="px-6 py-4">Nama Hadiah</th>
                                <th class="px-6 py-4 text-center">Poin Ditukar</th>
                                <th class="px-6 py-4">Tanggal Klaim</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-right">Aksi Proses</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($redemptions as $item)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-4 font-mono font-bold text-slate-800 tracking-wider">{{ $item->code }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-slate-800">{{ $item->user->name }}</span>
                                            <span class="text-[10px] text-slate-400">{{ $item->user->email }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-slate-700 leading-snug">{{ $item->reward->name }}</td>
                                    <td class="px-6 py-4 text-center font-extrabold text-amber-600">{{ $item->points_spent }} Pts</td>
                                    <td class="px-6 py-4 text-slate-500">{{ $item->created_at->format('d M Y, H:i') }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider 
                                            @if($item->status === 'active') bg-emerald-50 text-emerald-700 border border-emerald-100 
                                            @elseif($item->status === 'used') bg-blue-50 text-blue-700 border border-blue-100 
                                            @elseif($item->status === 'completed') bg-slate-100 text-slate-700 border border-slate-200 
                                            @else bg-red-50 text-red-700 border border-red-100 @endif">
                                            @if($item->status === 'active') Aktif
                                            @elseif($item->status === 'used') Voucher Digunakan
                                            @elseif($item->status === 'completed') Fisik Diserahkan
                                            @else Dibatalkan / Refund @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        @if($item->status === 'active')
                                            <div class="inline-flex gap-2">
                                                <!-- Action 1: Use Voucher or Deliver Item -->
                                                <form method="POST" action="{{ route('admin.redemptions.process', $item->id) }}" class="inline-block">
                                                    @csrf
                                                    @if(Str::contains(strtolower($item->reward->name), 'voucher') || Str::contains(strtolower($item->reward->name), 'diskon'))
                                                        <input type="hidden" name="status" value="used" />
                                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-3 py-1.5 rounded-lg shadow-sm transition inline-flex items-center gap-1">
                                                            <i data-lucide="check" class="w-3.5 h-3.5"></i> Pakai Voucher
                                                        </button>
                                                    @else
                                                        <input type="hidden" name="status" value="completed" />
                                                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold text-xs px-3 py-1.5 rounded-lg shadow-sm transition inline-flex items-center gap-1">
                                                            <i data-lucide="gift" class="w-3.5 h-3.5"></i> Serahkan Barang
                                                        </button>
                                                    @endif
                                                </form>

                                                <!-- Action 2: Cancel / Refund -->
                                                <form method="POST" action="{{ route('admin.redemptions.process', $item->id) }}" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan klaim ini? Poin customer akan otomatis dikembalikan (refund) dan stok hadiah akan dikembalikan.')">
                                                    @csrf
                                                    <input type="hidden" name="status" value="cancelled" />
                                                    <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 font-bold text-xs px-3 py-1.5 rounded-lg border border-red-100 transition inline-flex items-center gap-1">
                                                        <i data-lucide="x-circle" class="w-3.5 h-3.5"></i> Batalkan & Refund
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-400 italic">Sudah diproses</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-400 italic">Tidak ada transaksi penukaran poin yang ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($redemptions->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/20">
                        {{ $redemptions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
