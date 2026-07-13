<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Jadwal Penjemputan Cucian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Alert Banner -->
            @if(session('success'))
                <div class="bg-blue-50 border border-blue-200 text-blue-900 px-4 py-3 rounded-xl flex items-center space-x-2" role="alert">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium text-sm">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Table Card -->
            <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
                <div class="px-6 py-5 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-800 font-semibold">Daftar Jemputan Aktif</h3>
                    <p class="text-xs text-slate-400">Menampilkan cucian yang meminta metode pickup dan belum diambil/diterima di workshop.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                <th class="px-6 py-4">No. Order</th>
                                <th class="px-6 py-4">Tanggal & Jam Jemput</th>
                                <th class="px-6 py-4">Nama Pelanggan</th>
                                <th class="px-6 py-4">Nomor WhatsApp</th>
                                <th class="px-6 py-4">Alamat Penjemputan</th>
                                <th class="px-6 py-4">Layanan</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($pickups as $pickup)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-4 font-bold text-slate-800">{{ $pickup->order_number }}</td>
                                    <td class="px-6 py-4 font-semibold text-blue-700">
                                        {{ $pickup->pickup_datetime ? $pickup->pickup_datetime->format('d M Y H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 font-bold text-slate-800">{{ $pickup->customer->name }}</td>
                                    <td class="px-6 py-4 font-semibold text-slate-500">
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $pickup->customer->phone) }}" target="_blank" class="hover:text-blue-700 hover:underline flex items-center space-x-1.5">
                                            <span>{{ $pickup->customer->phone }}</span>
                                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-medium text-slate-500 leading-relaxed max-w-xs truncate" title="{{ $pickup->pickup_address }}">
                                        {{ $pickup->pickup_address }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 font-medium">{{ $pickup->service->name }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold {{ $pickup->status_badge_class }}">
                                            {{ $pickup->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <form action="{{ route('kurir.orders.pickup.mark', $pickup->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin pakaian ini telah dijemput dan tiba di workshop?')">
                                            @csrf
                                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold text-xs px-3.5 py-1.5 rounded-lg transition shadow-sm hover:shadow-md">
                                                Sudah Dijemput
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-slate-400 italic">Tidak ada jadwal penjemputan cucian aktif saat ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
