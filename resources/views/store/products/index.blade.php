@extends('layouts.app')

@section('content')
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-black text-slate-900">Catalogo de produtos</h1>
                <p class="mt-1 text-sm text-slate-600">Busque por nome, SKU ou filtre por categoria.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('store.products') }}" class="mt-5 grid gap-3 md:grid-cols-3">
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Buscar produto..."
                class="rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none ring-amber-200 transition focus:ring-2"
            >
            <select name="category" class="rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none ring-amber-200 transition focus:ring-2">
                <option value="">Todas as categorias</option>
                @foreach($categories as $category)
                    <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700">
                Filtrar
            </button>
        </form>
    </section>

    <section class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @forelse($products as $product)
            @php
                $imageUrl = $product->primaryImageUrl();
                $discountedPrice = $product->getDiscountedPrice();
            @endphp
            <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <a href="{{ route('store.products.show', $product) }}" class="block overflow-hidden rounded-xl">
                    @if($imageUrl)
                        <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="aspect-square w-full object-cover transition duration-300 hover:scale-[1.02]">
                    @else
                        <div class="aspect-square rounded-xl bg-gradient-to-br from-amber-100 to-orange-100"></div>
                    @endif
                </a>
                <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $product->category->name ?? 'Sem categoria' }}</p>
                <h3 class="mt-1 line-clamp-2 text-sm font-bold text-slate-900">{{ $product->name }}</h3>
                <p class="mt-2 text-xs font-semibold uppercase tracking-wide text-slate-500 line-through">
                    R$ {{ number_format((float) $product->price, 2, ',', '.') }}
                </p>
                <p class="text-lg font-black text-amber-700">R$ {{ number_format($discountedPrice, 2, ',', '.') }}</p>
                <p class="mt-2 text-xs font-semibold {{ $product->isInStock() ? 'text-[#3B6D11]' : 'text-[#993C1D]' }}">
                    {{ $product->isInStock() ? 'Disponivel em estoque' : 'Produto indisponivel' }}
                </p>
                <a href="{{ route('store.products.show', $product) }}" class="mt-3 inline-flex text-sm font-semibold text-slate-700 hover:text-slate-900">Ver detalhes</a>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-sm text-slate-500 sm:col-span-2 lg:col-span-4">
                Nenhum produto encontrado para os filtros selecionados.
            </div>
        @endforelse
    </section>

    <div class="mt-6">
        {{ $products->links() }}
    </div>
@endsection
