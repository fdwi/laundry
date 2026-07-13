@props(['active', 'icon'])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold bg-blue-500 text-white shadow-md shadow-blue-600/20 transition duration-200'
            : 'flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-400 hover:bg-slate-800/70 hover:text-slate-100 transition duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if(isset($icon))
        <i data-lucide="{{ $icon }}" class="w-4 h-4"></i>
    @endif
    <span>{{ $slot }}</span>
</a>
