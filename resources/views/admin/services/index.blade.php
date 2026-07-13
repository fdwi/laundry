<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Kelola Layanan Laundry') }}
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

            <!-- Create Service Button -->
            <div class="flex justify-end">
                <a href="{{ route('admin.services.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold px-5 py-2.5 rounded-xl shadow-md hover:shadow-lg transition text-xs flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
                    <span>Tambah Layanan Baru</span>
                </a>
            </div>

            <!-- Services Table Card -->
            <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                <th class="px-6 py-4">Nama Layanan</th>
                                <th class="px-6 py-4">Slug</th>
                                <th class="px-6 py-4">Deskripsi</th>
                                <th class="px-6 py-4">Satuan</th>
                                <th class="px-6 py-4">Tarif</th>
                                <th class="px-6 py-4">Durasi Selesai</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($services as $service)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-4 font-bold text-slate-800">{{ $service->name }}</td>
                                    <td class="px-6 py-4 text-slate-400 font-mono text-xs">{{ $service->slug }}</td>
                                    <td class="px-6 py-4 text-slate-500 max-w-xs truncate" title="{{ $service->description }}">
                                        {{ $service->description }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-slate-600 uppercase">{{ $service->unit }}</td>
                                    <td class="px-6 py-4 font-bold text-slate-800">
                                        Rp {{ number_format($service->unit === 'kg' ? $service->price_per_kg : $service->price_per_pcs, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 font-medium">{{ $service->duration_hours }} Jam</td>
                                    <td class="px-6 py-4 text-center">
                                        @if($service->is_active)
                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-green-50 text-green-700">Aktif</span>
                                        @else
                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-500">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right flex items-center justify-end space-x-2">
                                        <a href="{{ route('admin.services.edit', $service->id) }}" class="text-blue-700 hover:text-blue-800 font-bold text-xs bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition">Edit</a>
                                        
                                        <form action="{{ route('admin.services.toggle', $service->id) }}" method="POST">
                                            @csrf
                                            @if($service->is_active)
                                                <button type="submit" class="text-amber-600 hover:text-amber-700 font-bold text-xs bg-amber-50 hover:bg-amber-100 px-3 py-1.5 rounded-lg transition">Nonaktifkan</button>
                                            @else
                                                <button type="submit" class="text-green-600 hover:text-green-700 font-bold text-xs bg-green-50 hover:bg-green-100 px-3 py-1.5 rounded-lg transition">Aktifkan</button>
                                            @endif
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-slate-400 italic">Belum ada layanan laundry terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
