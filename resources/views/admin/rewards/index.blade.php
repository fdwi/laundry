<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Kelola Katalog Hadiah') }}
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

            <div class="flex justify-between items-center bg-white border border-slate-100 p-6 rounded-3xl shadow-sm">
                <div>
                    <h3 class="font-extrabold text-slate-800 text-base">Katalog Hadiah Loyalty</h3>
                    <p class="text-xs text-slate-400 mt-1">Daftar item hadiah/voucher yang dapat ditukarkan customer dengan poin loyalty mereka.</p>
                </div>
                <a href="{{ route('admin.rewards.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-5 py-3 rounded-2xl shadow-md transition duration-200 flex items-center gap-1.5">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Tambah Hadiah Baru</span>
                </a>
            </div>

            <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                <th class="px-6 py-4">Nama Hadiah</th>
                                <th class="px-6 py-4">Deskripsi</th>
                                <th class="px-6 py-4 text-center">Biaya Poin</th>
                                <th class="px-6 py-4 text-center">Stok</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($rewards as $reward)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-4 font-bold text-slate-800 leading-snug">{{ $reward->name }}</td>
                                    <td class="px-6 py-4 text-slate-500 max-w-sm leading-relaxed">{{ $reward->description }}</td>
                                    <td class="px-6 py-4 text-center font-extrabold text-amber-600">{{ $reward->points_cost }} Pts</td>
                                    <td class="px-6 py-4 text-center font-semibold text-slate-700">{{ $reward->stock }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider 
                                            @if($reward->is_active) bg-green-50 text-green-700 border border-green-100 
                                            @else bg-red-50 text-red-700 border border-red-100 @endif">
                                            {{ $reward->is_active ? 'Aktif' : 'Non-Aktif' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                        <a href="{{ route('admin.rewards.edit', $reward->id) }}" class="text-blue-700 hover:text-blue-800 font-bold text-xs bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition inline-flex items-center gap-1">
                                            <i data-lucide="edit-2" class="w-3.5 h-3.5"></i> Edit
                                        </a>
                                        
                                        <form method="POST" action="{{ route('admin.rewards.destroy', $reward->id) }}" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus hadiah ini? Data penukaran yang sudah berjalan akan tetap dipertahankan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-bold text-xs bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition inline-flex items-center gap-1">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">Belum ada item hadiah yang terdaftar. Silakan tambah baru.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($rewards->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/20">
                        {{ $rewards->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
