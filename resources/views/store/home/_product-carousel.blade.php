@props([
    'title',
    'products',
    'viewAllUrl' => null,
])

@php
    $carouselId = 'carousel-'.\Illuminate\Support\Str::slug($title);
@endphp

@if ($products->isNotEmpty())
    <section class="mt-10" data-product-carousel id="{{ $carouselId }}">
        <div class="mb-4 flex items-end justify-between gap-4">
            <h2 class="text-2xl font-black text-[#1A3A6B]">{{ $title }}</h2>

            @if ($viewAllUrl)
                <a href="{{ $viewAllUrl }}" class="shrink-0 text-sm font-bold text-[#D42B2B] transition hover:text-[#B02020] hover:underline">
                    Ver todos
                </a>
            @endif
        </div>

        <div class="relative">
            @if ($products->count() > 5)
                <button
                    type="button"
                    class="carousel-control carousel-prev absolute -left-3 top-1/2 z-10 hidden h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-[#C5D4EC] bg-white text-[#1A3A6B] shadow-sm transition hover:border-[#1A3A6B] hover:bg-[#E8EFF8] lg:flex"
                    aria-label="Produtos anteriores em {{ $title }}"
                    data-carousel-prev
                >
                    ‹
                </button>
            @endif

            <div
                class="product-carousel-track flex snap-x snap-mandatory gap-4 overflow-x-auto scroll-smooth pb-2"
                data-carousel-track
            >
                @foreach ($products as $product)
                    @include('store.home._carousel-card', ['product' => $product])
                @endforeach
            </div>

            @if ($products->count() > 5)
                <button
                    type="button"
                    class="carousel-control carousel-next absolute -right-3 top-1/2 z-10 hidden h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-[#C5D4EC] bg-white text-[#1A3A6B] shadow-sm transition hover:border-[#1A3A6B] hover:bg-[#E8EFF8] lg:flex"
                    aria-label="Próximos produtos em {{ $title }}"
                    data-carousel-next
                >
                    ›
                </button>
            @endif
        </div>

        @if ($products->count() > 5)
            <div class="mt-4 flex justify-center gap-2" data-carousel-dots aria-hidden="true"></div>
        @endif
    </section>
@endif
