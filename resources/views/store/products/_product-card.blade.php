@props(['product'])

@php
    $imageUrl = $product->primaryImageUrl();
    $hasOldPrice = isset($product->old_price) && (float) $product->old_price > (float) $product->price;
    $hasPromotion = $hasOldPrice || (isset($product->is_promotional) && (bool) $product->is_promotional);
@endphp

<article class="relative flex h-full min-w-0 flex-col overflow-hidden rounded-lg border border-[#E0E0E0] bg-white transition duration-150 hover:-translate-y-0.5 hover:shadow-md">
    @if ($hasPromotion)
        <span class="absolute left-0 top-0 z-10 rounded-br bg-[#D42B2B] px-2 py-[3px] text-[11px] font-bold text-white">
            -15%
        </span>
    @endif

    <a href="{{ route('store.products.show', $product->slug) }}" class="block overflow-hidden bg-[#F5F5F5]">
        @if ($imageUrl)
            <img
                src="{{ $imageUrl }}"
                alt="{{ $product->name }}"
                loading="lazy"
                class="aspect-square w-full object-cover transition duration-300 hover:scale-[1.03]"
            >
        @else
            <div class="flex aspect-square items-center justify-center bg-[#F5F5F5] text-[#C5D4EC]">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-12 w-12" aria-hidden="true">
                    <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                    <circle cx="9" cy="9" r="2"></circle>
                    <path d="m21 15-5-5L5 21"></path>
                </svg>
            </div>
        @endif
    </a>

    <div class="flex flex-1 flex-col p-3">
        <p class="text-[11px] font-semibold uppercase tracking-[0.05em] text-[#767676]">
            {{ $product->category->name ?? 'Sem categoria' }}
        </p>

        <h3 class="mt-1 line-clamp-2 min-h-10 text-sm font-semibold leading-5 text-[#1A1A1A]">
            <a href="{{ route('store.products.show', $product->slug) }}" class="transition hover:text-[#2B5FAA]">
                {{ $product->name }}
            </a>
        </h3>

        <p class="mt-3 text-lg font-bold text-[#D42B2B]">
            R$ {{ number_format((float) $product->price, 2, ',', '.') }}
        </p>

        @if ((float) $product->price >= 299)
            <span class="mt-1 text-[11px] font-semibold text-[#198754]">✓ Frete grátis</span>
        @endif

        <div class="mt-2">
            @if ($product->isInStock())
                <span class="text-[11px] font-semibold text-[#198754]">Em estoque</span>
            @else
                <span class="text-[11px] font-semibold text-[#D42B2B]">Esgotado</span>
            @endif
        </div>
    </div>

    @if ($product->isInStock())
        <livewire:add-to-cart :product-id="$product->id" :compact="true" :key="'card-'.$product->id" />
    @else
        <button type="button" disabled class="w-full cursor-not-allowed rounded-none bg-[#E0E0E0] px-4 py-2.5 text-sm font-bold text-[#767676]">
            Produto esgotado
        </button>
    @endif
</article>
