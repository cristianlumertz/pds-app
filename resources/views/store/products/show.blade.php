@extends('layouts.app')

@section('content')
    @php
        $gallery = $product->productImages;
        $featuredImage = $gallery->first()?->url ?? $product->image_url;
        $featuredAlt = $gallery->first()?->alt_text ?: $product->name;
        $description = $product->description ?: 'Produto sem descrição cadastrada.';
        $hasLongDescription = mb_strlen($description) > 180;
    @endphp

    <nav class="flex flex-wrap items-center gap-2 text-xs text-[#767676]" aria-label="Breadcrumb">
        <a href="{{ route('store.home') }}" class="transition hover:text-[#2B5FAA] hover:underline">Home</a>
        <span aria-hidden="true">›</span>
        @if ($product->category)
            <a href="{{ route('categories.show', $product->category) }}" class="transition hover:text-[#2B5FAA] hover:underline">
                {{ $product->category->name }}
            </a>
            <span aria-hidden="true">›</span>
        @endif
        <span class="line-clamp-1 font-semibold text-[#444444]">{{ $product->name }}</span>
    </nav>

    <section class="mt-5 grid gap-8 lg:grid-cols-2 lg:gap-10">
        <div
            x-data="{
                selectedImage: @js($featuredImage),
                selectedAlt: @js($featuredAlt)
            }"
        >
            <div class="overflow-hidden rounded-lg border border-[#E0E0E0] bg-[#F5F5F5]">
                @if ($featuredImage)
                    <img
                        x-bind:src="selectedImage"
                        x-bind:alt="selectedAlt"
                        src="{{ $featuredImage }}"
                        alt="{{ $featuredAlt }}"
                        class="aspect-square w-full object-contain"
                    >
                @else
                    <div class="flex aspect-square items-center justify-center text-[#C5D4EC]">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-20 w-20" aria-hidden="true">
                            <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                            <circle cx="9" cy="9" r="2"></circle>
                            <path d="m21 15-5-5L5 21"></path>
                        </svg>
                    </div>
                @endif
            </div>

            @if ($gallery->count() > 1)
                <div class="mt-4 flex flex-wrap gap-2" aria-label="Galeria de imagens do produto">
                    @foreach ($gallery as $image)
                        @php
                            $imageAlt = $image->alt_text ?: $product->name;
                        @endphp
                        <button
                            type="button"
                            x-on:click="selectedImage = @js($image->url); selectedAlt = @js($imageAlt)"
                            x-bind:class="selectedImage === @js($image->url) ? 'border-[#D42B2B]' : 'border-transparent'"
                            class="h-[60px] w-[60px] overflow-hidden rounded border-2 bg-[#F5F5F5] transition hover:border-[#D42B2B] focus:outline-none focus:ring-2 focus:ring-[#D42B2B] focus:ring-offset-2"
                            aria-label="Ver imagem {{ $loop->iteration }} de {{ $gallery->count() }}"
                        >
                            <img src="{{ $image->url }}" alt="{{ $imageAlt }}" loading="lazy" class="h-full w-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="rounded-lg border border-[#E0E0E0] bg-white p-5 sm:p-7">
            @if ($product->category)
                <a href="{{ route('categories.show', $product->category) }}" class="text-[11px] font-semibold uppercase tracking-[0.05em] text-[#767676] transition hover:text-[#2B5FAA] hover:underline">
                    {{ $product->category->name }}
                </a>
            @else
                <span class="text-[11px] font-semibold uppercase tracking-[0.05em] text-[#767676]">Sem categoria</span>
            @endif

            <h1 class="mt-2 text-2xl font-bold leading-tight text-[#1A1A1A]">{{ $product->name }}</h1>
            <p class="mt-2 text-xs text-[#767676]">Cód: {{ $product->sku }}</p>

            <p class="mt-6 text-[32px] font-bold leading-none text-[#D42B2B]">
                R$ {{ number_format((float) $product->price, 2, ',', '.') }}
            </p>

            @if ((float) $product->price >= 299)
                <p class="mt-3 text-sm font-semibold text-[#198754]">✓ Frete grátis</p>
            @endif

            <div class="mt-4">
                @if ($product->isInStock())
                    <span class="inline-flex rounded-full bg-[#198754]/10 px-3 py-1.5 text-xs font-semibold text-[#198754]">
                        Em estoque ({{ $product->stock }} un)
                    </span>
                @else
                    <span class="inline-flex rounded-full bg-[#FAE8E8] px-3 py-1.5 text-xs font-semibold text-[#D42B2B]">
                        Esgotado
                    </span>
                @endif
            </div>

            <hr class="my-6 border-[#E0E0E0]">

            <div x-data="{ expanded: false }">
                <h2 class="text-sm font-bold text-[#1A3A6B]">Descrição</h2>
                <p
                    class="mt-2 text-sm leading-[1.6] text-[#444444]"
                    @if ($hasLongDescription)
                        x-bind:class="expanded ? '' : 'line-clamp-3'"
                    @endif
                >
                    {{ $description }}
                </p>

                @if ($hasLongDescription)
                    <button
                        type="button"
                        x-on:click="expanded = ! expanded"
                        class="mt-2 text-xs font-semibold text-[#2B5FAA] hover:underline"
                        x-text="expanded ? 'Ver menos' : 'Ver mais'"
                    >
                        Ver mais
                    </button>
                @endif
            </div>

            <div class="mt-6">
                @if ($product->isInStock())
                    <livewire:add-to-cart :product-id="$product->id" />
                @else
                    <button type="button" disabled class="w-full cursor-not-allowed rounded bg-[#E0E0E0] px-4 py-3.5 text-sm font-bold text-[#767676]">
                        Produto esgotado
                    </button>
                @endif
            </div>

            <div class="mt-6 grid gap-3 border-t border-[#E0E0E0] pt-5 text-xs font-semibold text-[#444444] sm:grid-cols-3">
                <span>🔒 Compra segura</span>
                <span>🔄 Troca em 30 dias</span>
                <span>🚚 Entrega em Capão da Canoa/RS</span>
            </div>
        </div>
    </section>

    <section class="mt-10">
        <h2 class="text-xl font-bold text-[#1A3A6B]">Você também pode gostar</h2>

        <div class="mt-4 grid grid-cols-2 gap-4 lg:grid-cols-4">
            @forelse ($relatedProducts as $relatedProduct)
                @include('store.products._product-card', ['product' => $relatedProduct])
            @empty
                <p class="col-span-2 rounded-lg border border-dashed border-[#E0E0E0] bg-white p-6 text-sm text-[#767676] lg:col-span-4">
                    Nenhum produto relacionado disponível no momento.
                </p>
            @endforelse
        </div>
    </section>
@endsection
