@props(['product'])

@php
    $imageUrl = $product->primaryImageUrl();
@endphp

<article class="product-carousel-card flex min-h-[390px] shrink-0 snap-start flex-col overflow-hidden rounded border border-[#E0E0E0] bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
    <a href="{{ route('store.products.show', $product->slug) }}" class="flex h-48 items-center justify-center bg-white p-4">
        @if ($imageUrl)
            <img
                src="{{ $imageUrl }}"
                alt="{{ $product->name }}"
                loading="lazy"
                class="h-full w-full object-contain"
            >
        @else
            <div class="flex h-full w-full items-center justify-center rounded bg-[#F1EFE8] text-[#C5D4EC]">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-16 w-16" aria-hidden="true">
                    <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                    <circle cx="9" cy="9" r="2"></circle>
                    <path d="m21 15-5-5L5 21"></path>
                </svg>
            </div>
        @endif
    </a>

    <div class="flex flex-1 flex-col px-4 pb-4 pt-3">
        <h3 class="line-clamp-2 min-h-12 text-sm font-semibold leading-6 text-[#1A1A1A]">
            <a href="{{ route('store.products.show', $product->slug) }}" class="transition hover:text-[#1A3A6B]">
                {{ $product->name }}
            </a>
        </h3>

        <p class="mt-3 text-2xl font-black text-[#1A3A6B]">
            R$ {{ number_format((float) $product->price, 2, ',', '.') }}
        </p>

        @if ((float) $product->price >= 299)
            <p class="mt-1 text-xs font-bold text-[#1D9E75]">Frete grátis</p>
        @else
            <p class="mt-1 text-xs font-semibold text-[#767676]">Entrega em Capão da Canoa/RS</p>
        @endif

        <div class="mt-auto pt-4">
            @if ($product->isInStock())
                <livewire:add-to-cart
                    :product-id="$product->id"
                    :compact="true"
                    button-label="COMPRAR"
                    :key="'home-carousel-'.$product->id"
                />
            @else
                <button type="button" disabled class="w-full cursor-not-allowed rounded bg-[#E0E0E0] px-4 py-3 text-sm font-black text-[#767676]">
                    ESGOTADO
                </button>
            @endif
        </div>
    </div>
</article>
