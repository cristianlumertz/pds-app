@props([
    'title' => 'Nenhum registro encontrado.',
    'description' => null,
])

<div class="rounded-lg border border-dashed border-[#C5D4EC] bg-[#F8FAFC] p-8 text-center">
    <p class="text-sm font-black text-[#1A3A6B]">{{ $title }}</p>
    @if ($description)
        <p class="mt-1 text-sm text-[#767676]">{{ $description }}</p>
    @endif
</div>
