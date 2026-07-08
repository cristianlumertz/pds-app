@props([
    'label',
    'value',
    'hint' => null,
    'tone' => 'blue',
])

@php
    $tones = [
        'blue' => 'bg-[#1A3A6B]/10 text-[#1A3A6B]',
        'red' => 'bg-[#D42B2B]/10 text-[#B02020]',
        'green' => 'bg-[#1D9E75]/10 text-[#16765A]',
        'yellow' => 'bg-[#FFF3CD] text-[#856404]',
        'gray' => 'bg-[#F3F5F8] text-[#3D3D3A]',
    ];
@endphp

<article class="rounded-lg border border-[#E0E0E0] bg-white p-5 shadow-sm">
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-xs font-black uppercase tracking-wide text-[#767676]">{{ $label }}</p>
            <p class="mt-2 text-2xl font-black text-[#1A1A1A]">{{ $value }}</p>
        </div>
        <span class="flex h-10 w-10 items-center justify-center rounded {{ $tones[$tone] ?? $tones['blue'] }} text-sm font-black">
            {{ strtoupper(substr((string) $label, 0, 1)) }}
        </span>
    </div>
    @if ($hint)
        <p class="mt-3 text-xs font-semibold text-[#767676]">{{ $hint }}</p>
    @endif
</article>
