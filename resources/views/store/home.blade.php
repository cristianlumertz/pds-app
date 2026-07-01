@extends('layouts.app')

@section('content')
    <section class="overflow-hidden rounded-xl bg-gradient-to-br from-[#1A3A6B] to-[#2B5FAA] px-6 py-8 sm:px-8 sm:py-10 lg:px-12 lg:py-12">
        <div class="grid gap-10 lg:grid-cols-2 lg:items-center">
            <div>
                <span class="inline-flex rounded-full bg-white/15 px-3 py-1.5 text-[11px] font-semibold text-white">
                    ✓ Capão da Canoa / RS
                </span>

                <h1 class="mt-5 max-w-2xl text-[32px] font-bold leading-tight text-white lg:text-[40px]">
                    Tudo para sua obra, do fundamento ao acabamento.
                </h1>

                <p class="mt-4 max-w-xl text-sm leading-6 text-white/80 sm:text-base">
                    Ferramentas, cimento, tintas e muito mais com os melhores preços da região.
                </p>

                <div class="mt-7 flex flex-wrap gap-3">
                    <a
                        href="{{ route('store.products') }}"
                        class="rounded bg-[#D42B2B] px-6 py-3 text-sm font-bold text-white transition hover:bg-[#B02020] focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-[#1A3A6B]"
                    >
                        Ver produtos
                    </a>
                    <a
                        href="{{ route('categories.index') }}"
                        class="rounded border border-white px-6 py-3 text-sm font-bold text-white transition hover:bg-white hover:text-[#1A3A6B] focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-[#1A3A6B]"
                    >
                        Ver categorias
                    </a>
                </div>

                <div class="mt-7 flex flex-wrap gap-2 text-[11px] font-semibold text-white sm:text-xs">
                    <span class="rounded-full bg-white/10 px-3 py-2">📦 Frete grátis acima R$299</span>
                    <span class="rounded-full bg-white/10 px-3 py-2">🚚 Entrega em Capão da Canoa/RS</span>
                    <span class="rounded-full bg-white/10 px-3 py-2">🔒 Compra segura</span>
                </div>
            </div>

            <div class="hidden grid-cols-2 gap-3 lg:grid">
                <a href="{{ route('store.products', ['category' => 'ferramentas']) }}" class="rounded-lg border border-white/20 bg-white/10 p-5 text-white transition hover:-translate-y-0.5 hover:bg-white/15">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7" aria-hidden="true">
                        <path d="M14.7 6.3a4 4 0 0 0-5-5L7.5 3.5l3 3 2.2-2.2a4 4 0 0 0 2 5L6.4 17.6a2 2 0 1 0 2.8 2.8l8.3-8.3a4 4 0 0 0 5-5l-2.2 2.2-3-3 2.2-2.2"></path>
                    </svg>
                    <span class="mt-4 block text-sm font-bold">Ferramentas</span>
                </a>

                <a href="{{ route('store.products', ['category' => 'eletrica']) }}" class="rounded-lg border border-white/20 bg-white/10 p-5 text-white transition hover:-translate-y-0.5 hover:bg-white/15">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7" aria-hidden="true">
                        <path d="m13 2-9 12h8l-1 8 9-12h-8l1-8Z"></path>
                    </svg>
                    <span class="mt-4 block text-sm font-bold">Elétrica</span>
                </a>

                <a href="{{ route('store.products', ['category' => 'hidraulica']) }}" class="rounded-lg border border-white/20 bg-white/10 p-5 text-white transition hover:-translate-y-0.5 hover:bg-white/15">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7" aria-hidden="true">
                        <path d="M12 2s6 6.2 6 12a6 6 0 0 1-12 0c0-5.8 6-12 6-12Z"></path>
                        <path d="M9 15a3 3 0 0 0 3 3"></path>
                    </svg>
                    <span class="mt-4 block text-sm font-bold">Hidráulica</span>
                </a>

                <a href="{{ route('store.products', ['category' => 'tintas-e-acabamentos']) }}" class="rounded-lg border border-white/20 bg-white/10 p-5 text-white transition hover:-translate-y-0.5 hover:bg-white/15">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7" aria-hidden="true">
                        <path d="M14.5 4.5 19 9l-9.5 9.5a3.2 3.2 0 0 1-4.5 0 3.2 3.2 0 0 1 0-4.5l9.5-9.5Z"></path>
                        <path d="m12 7 5 5"></path>
                        <path d="M19 16s2 2.2 2 3.5a2 2 0 0 1-4 0c0-1.3 2-3.5 2-3.5Z"></path>
                    </svg>
                    <span class="mt-4 block text-sm font-bold">Tintas</span>
                </a>
            </div>
        </div>
    </section>

    <section class="mt-4 rounded bg-[#D42B2B] px-4 py-2.5 text-center text-sm font-bold text-white">
        🔥 Frete GRÁTIS em compras acima de R$ 299,00 — Aproveite!
    </section>

    <section class="mt-9">
        <h2 class="text-[22px] font-bold text-[#1A1A1A]">Compre por categoria</h2>

        <div class="mt-4 grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-4">
            @forelse ($categories as $category)
                @php
                    $categoryIcons = [
                        'ferramentas' => '🔨',
                        'eletrica' => '⚡',
                        'hidraulica' => '💧',
                        'materiais-basicos' => '🧱',
                        'tintas-e-acabamentos' => '🎨',
                        'seguranca' => '⛑️',
                        'fixacao' => '🔩',
                    ];
                    $categoryIcon = $categoryIcons[$category->slug] ?? '🏗️';
                @endphp

                <a
                    href="{{ route('categories.show', $category) }}"
                    class="group rounded-lg border border-[#C5D4EC] bg-[#E8EFF8] px-3 py-5 text-center transition duration-150 hover:border-[#D42B2B] hover:bg-[#D42B2B]"
                >
                    <span class="block text-4xl leading-none" aria-hidden="true">{{ $categoryIcon }}</span>
                    <span class="mt-3 block text-[13px] font-semibold text-[#1A3A6B] transition duration-150 group-hover:text-white">
                        {{ $category->name }}
                    </span>
                    <span class="mt-1 block text-[11px] text-[#767676] transition duration-150 group-hover:text-white/80">
                        {{ $category->products->count() }} produtos
                    </span>
                </a>
            @empty
                <div class="col-span-2 rounded-lg border border-dashed border-[#C5D4EC] bg-white p-6 text-sm text-[#767676] md:col-span-3 lg:col-span-4">
                    Nenhuma categoria ativa encontrada.
                </div>
            @endforelse
        </div>
    </section>

    <section class="mt-10">
        <div class="mb-4 flex items-center justify-between gap-4">
            <h2 class="text-[22px] font-bold text-[#1A1A1A]">Produtos em destaque</h2>
            <a href="{{ route('store.products') }}" class="shrink-0 text-sm font-bold text-[#D42B2B] transition hover:text-[#B02020] hover:underline">
                Ver todos →
            </a>
        </div>

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            @forelse ($featuredProducts as $product)
                @include('store.products._product-card', ['product' => $product])
            @empty
                <div class="col-span-2 rounded-lg border border-dashed border-[#E0E0E0] bg-white p-8 text-sm text-[#767676] lg:col-span-4">
                    Nenhum produto em destaque disponível no momento.
                </div>
            @endforelse
        </div>
    </section>
@endsection
