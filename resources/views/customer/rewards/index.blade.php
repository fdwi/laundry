<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Hadiah & Poin Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8" x-data="{ tab: 'catalog' }">
            <!-- Alert Banner -->
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-900 px-4 py-3 rounded-2xl flex items-center justify-between shadow-sm" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-semibold text-sm">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-green-500 hover:text-green-800">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-900 px-4 py-3 rounded-2xl flex items-center justify-between shadow-sm" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="font-semibold text-sm">{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-red-500 hover:text-red-800">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            @endif

            <!-- Points Card Banner & Quick Stats -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-stretch">
                <!-- Loyalty Card (Premium design with gradients and glassmorphism) -->
                <div class="lg:col-span-2 bg-gradient-to-br from-blue-700 via-indigo-900 to-slate-900 text-white rounded-3xl p-8 shadow-xl relative overflow-hidden flex flex-col justify-between min-h-[200px] border border-blue-800/40">
                    <div class="absolute right-0 top-0 -mt-6 -mr-6 w-36 h-36 bg-white/10 rounded-full blur-2xl"></div>
                    <div class="absolute left-1/3 bottom-0 -mb-10 w-44 h-44 bg-amber-500/15 rounded-full blur-3xl"></div>
                    
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <span class="text-xs font-bold tracking-widest text-slate-400 uppercase">L-DRY LOYALTY CLUB</span>
                            <h3 class="text-lg font-bold text-slate-200 mt-0.5">{{ Auth::user()->name }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl flex items-center justify-center text-amber-500 shadow-inner">
                            <i data-lucide="award" class="w-6 h-6 animate-pulse"></i>
                        </div>
                    </div>

                    <div class="mt-8 relative z-10 flex items-baseline gap-2">
                        <span class="text-5xl font-black tracking-tight text-amber-400">{{ Auth::user()->points ?? 0 }}</span>
                        <span class="text-sm font-semibold tracking-wider text-slate-400 uppercase">Poin Aktif</span>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-700/40 relative z-10 flex justify-between items-center text-xs text-slate-400">
                        <span>Status Keanggotaan: <strong class="text-slate-200">Regular Member</strong></span>
                        <span class="flex items-center gap-1"><i data-lucide="shield-check" class="w-3.5 h-3.5 text-blue-400"></i> Terverifikasi</span>
                    </div>
                </div>

                <!-- Fast Info box -->
                <div class="bg-white border border-slate-100 rounded-3xl p-8 flex flex-col justify-between shadow-sm">
                    <div>
                        <h4 class="font-extrabold text-slate-800 text-lg flex items-center gap-2">
                            <i data-lucide="help-circle" class="w-5 h-5 text-blue-600"></i> Bagaimana cara kerja?
                        </h4>
                        <p class="text-xs text-slate-500 mt-3 leading-relaxed">
                            Kami selalu menjaga ketepatan waktu. Namun, jika pengerjaan pesanan laundry Anda meleset atau terlambat diserahkan dari estimasi awal, kami akan otomatis mengirimkan poin dispensasi sebagai kompensasi.
                        </p>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                            Poin yang terkumpul dapat ditukarkan langsung melalui halaman ini menjadi voucher potongan harga menarik atau hadiah gratis dari kami.
                        </p>
                    </div>
                    <div class="pt-4 border-t border-slate-100 mt-6 flex justify-between items-center text-xs text-slate-400">
                        <span>Poin per Keterlambatan:</span>
                        <span class="font-extrabold text-slate-800">{{ \App\Models\Setting::getValue('late_delivery_points', '50') }} Poin</span>
                    </div>
                </div>
            </div>

            <!-- Tab Navigation Buttons -->
            <div class="flex border-b border-slate-200 gap-6">
                <button @click="tab = 'catalog'" :class="tab === 'catalog' ? 'border-blue-600 text-blue-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800 font-semibold'" class="pb-4 border-b-2 text-sm transition">
                    Katalog Hadiah
                </button>
                <button @click="tab = 'my-vouchers'" :class="tab === 'my-vouchers' ? 'border-blue-600 text-blue-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800 font-semibold'" class="pb-4 border-b-2 text-sm transition relative">
                    Voucher & Hadiah Saya
                    @if($redemptions->where('status', 'active')->count() > 0)
                        <span class="absolute top-0 -right-3.5 bg-red-500 text-white text-[9px] w-4.5 h-4.5 rounded-full flex items-center justify-center font-bold">{{ $redemptions->where('status', 'active')->count() }}</span>
                    @endif
                </button>
                <button @click="tab = 'history'" :class="tab === 'history' ? 'border-blue-600 text-blue-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800 font-semibold'" class="pb-4 border-b-2 text-sm transition">
                    Riwayat Poin
                </button>
            </div>

            <!-- TAB 1: CATALOG OF REWARDS -->
            <div x-show="tab === 'catalog'" class="space-y-6">
                @if($rewards->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($rewards as $reward)
                            <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm flex flex-col justify-between group hover:shadow-md hover:border-slate-200 transition duration-300">
                                <div class="p-6">
                                    <!-- Icon badge -->
                                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mb-4 group-hover:scale-110 transition duration-300">
                                        @if(Str::contains(strtolower($reward->name), 'voucher') || Str::contains(strtolower($reward->name), 'diskon'))
                                            <i data-lucide="ticket" class="w-6 h-6"></i>
                                        @else
                                            <i data-lucide="gift" class="w-6 h-6"></i>
                                        @endif
                                    </div>
                                    <h4 class="font-extrabold text-slate-800 text-base leading-snug">{{ $reward->name }}</h4>
                                    <p class="text-xs text-slate-400 mt-2 leading-relaxed min-h-[40px]">{{ $reward->description }}</p>
                                </div>

                                <div class="px-6 pb-6 pt-4 border-t border-slate-50 flex items-center justify-between bg-slate-50/20">
                                    <div class="flex flex-col">
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Biaya Penukaran</span>
                                        <span class="text-sm font-extrabold text-amber-600">{{ $reward->points_cost }} Poin</span>
                                    </div>
                                    
                                    @if((Auth::user()->points ?? 0) >= $reward->points_cost)
                                        <form method="POST" action="{{ route('customer.rewards.redeem', $reward->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menukarkan {{ $reward->points_cost }} poin untuk {{ $reward->name }}?')">
                                            @csrf
                                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-md transition duration-200">
                                                Tukarkan
                                            </button>
                                        </form>
                                    @else
                                        <button disabled class="bg-slate-100 text-slate-400 font-bold text-xs px-4 py-2 rounded-xl cursor-not-allowed border border-slate-200/50">
                                            Poin Kurang
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white border border-slate-100 rounded-3xl p-12 text-center text-slate-400 italic">
                        Belum ada item hadiah yang terdaftar saat ini. Hubungi outlet untuk informasi lebih lanjut.
                    </div>
                @endif
            </div>

            <!-- TAB 2: MY VOUCHERS -->
            <div x-show="tab === 'my-vouchers'" class="space-y-6" style="display: none;">
                @if($redemptions->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($redemptions as $redemption)
                            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex items-start gap-4 relative overflow-hidden">
                                <!-- Status Line indicator -->
                                <div class="absolute left-0 top-0 bottom-0 w-1.5 
                                    @if($redemption->status === 'active') bg-emerald-500 
                                    @elseif($redemption->status === 'used' || $redemption->status === 'completed') bg-slate-300 
                                    @else bg-red-400 @endif">
                                </div>

                                <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                                    <i data-lucide="ticket" class="w-6 h-6"></i>
                                </div>

                                <div class="flex-grow min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <h4 class="font-extrabold text-slate-800 text-sm truncate">{{ $redemption->reward->name }}</h4>
                                        <span class="inline-flex px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider 
                                            @if($redemption->status === 'active') bg-emerald-50 text-emerald-700 border border-emerald-100 
                                            @elseif($redemption->status === 'used') bg-blue-50 text-blue-700 border border-blue-100 
                                            @elseif($redemption->status === 'completed') bg-slate-100 text-slate-700 border border-slate-200 
                                            @else bg-red-50 text-red-700 border border-red-100 @endif">
                                            @if($redemption->status === 'active') Aktif
                                            @elseif($redemption->status === 'used') Digunakan
                                            @elseif($redemption->status === 'completed') Selesai
                                            @else Dibatalkan @endif
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-1">Ditukarkan pada {{ $redemption->created_at->format('d M Y, H:i') }} ({{ $redemption->points_spent }} Pts)</p>
                                    
                                    <div class="mt-4 p-3 bg-slate-50 border border-slate-100 rounded-xl flex items-center justify-between">
                                        <div class="flex flex-col">
                                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-none">KODE VOUCHER</span>
                                            <span class="text-sm font-extrabold text-slate-800 mt-1 select-all font-mono">{{ $redemption->code }}</span>
                                        </div>
                                        @if($redemption->status === 'active')
                                            <button 
                                                onclick="navigator.clipboard.writeText('{{ $redemption->code }}'); alert('Kode voucher berhasil disalin!');" 
                                                class="text-blue-700 hover:text-blue-800 hover:bg-blue-50/60 p-2 rounded-xl transition"
                                                title="Salin Kode">
                                                <i data-lucide="copy" class="w-4 h-4"></i>
                                            </button>
                                        @endif
                                    </div>
                                    
                                    @if($redemption->status === 'active')
                                        <span class="block text-[10px] text-slate-400 italic mt-2.5">Tunjukkan kode voucher di atas kepada kurir atau kasir saat pembayaran laundry untuk klaim potongan harga.</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white border border-slate-100 rounded-3xl p-12 text-center text-slate-400 italic">
                        Anda belum menukarkan voucher apapun.
                    </div>
                @endif
            </div>

            <!-- TAB 3: POINTS HISTORY -->
            <div x-show="tab === 'history'" class="space-y-6" style="display: none;">
                <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="text-base font-bold text-slate-800">Riwayat Mutasi Poin</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                    <th class="px-6 py-4">Tanggal</th>
                                    <th class="px-6 py-4">Deskripsi</th>
                                    <th class="px-6 py-4">Tipe</th>
                                    <th class="px-6 py-4 text-right">Jumlah Poin</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                @forelse($transactions as $t)
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="px-6 py-4 text-slate-500">{{ $t->created_at->format('d M Y H:i') }}</td>
                                        <td class="px-6 py-4 font-semibold text-slate-700">{{ $t->description }}</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider 
                                                @if($t->type === 'earn_late') bg-green-50 text-green-700 border border-green-100
                                                @elseif($t->type === 'refund') bg-emerald-50 text-emerald-700 border border-emerald-100
                                                @else bg-amber-50 text-amber-700 border border-amber-100 @endif">
                                                @if($t->type === 'earn_late') Kompensasi Terlambat
                                                @elseif($t->type === 'refund') Pengembalian/Refund
                                                @else Penukaran Hadiah @endif
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right font-extrabold 
                                            @if($t->amount > 0) text-green-600 @else text-red-500 @endif">
                                            {{ $t->amount > 0 ? '+' . $t->amount : $t->amount }} Pts
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">Belum ada riwayat transaksi poin.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($transactions->hasPages())
                        <div class="px-6 py-4 border-t border-slate-100">
                            {{ $transactions->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
