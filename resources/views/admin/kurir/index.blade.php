<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Kelola Akun Kurir') }}
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

            <!-- Create Kurir Button -->
            <div class="flex justify-end">
                <a href="{{ route('admin.kurir.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold px-5 py-2.5 rounded-xl shadow-md hover:shadow-lg transition text-xs flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                    <span>Daftarkan Kurir Baru</span>
                </a>
            </div>

            <!-- Kurir Table Card -->
            <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                <th class="px-6 py-4">Nama Lengkap</th>
                                <th class="px-6 py-4">Alamat Email</th>
                                <th class="px-6 py-4">Nomor Kontak</th>
                                <th class="px-6 py-4">Alamat Rumah</th>
                                <th class="px-6 py-4 text-center">Total Pengerjaan</th>
                                <th class="px-6 py-4 text-center">Status Login</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($kurirMembers as $kurir)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-4 font-bold text-slate-800">{{ $kurir->name }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $kurir->email }}</td>
                                    <td class="px-6 py-4 font-semibold text-slate-500">{{ $kurir->phone }}</td>
                                    <td class="px-6 py-4 text-slate-500 max-w-xs truncate" title="{{ $kurir->address }}">
                                        {{ $kurir->address }}
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-blue-700">
                                        {{ $kurir->kurir_orders_count }} Order
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($kurir->is_active)
                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-green-50 text-green-700">Aktif</span>
                                        @else
                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700">Ditangguhkan</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right flex items-center justify-end space-x-2">
                                        <a href="{{ route('admin.kurir.orders', $kurir->id) }}" class="text-blue-600 hover:text-blue-700 font-bold text-xs bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition">Kinerja</a>
                                        <a href="{{ route('admin.kurir.edit', $kurir->id) }}" class="text-blue-700 hover:text-blue-800 font-bold text-xs bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-400 italic">Belum ada kurir terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
