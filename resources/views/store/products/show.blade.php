@extends('layouts.app')

@section('content')
    <section class="grid gap-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm lg:grid-cols-2">
        <div class="aspect-square rounded-2xl bg-gradient-to-br from-amber-100 to-orange-100"></div>

        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $product->category->name ?? 'Sem categoria' }}</p>
            <h1 class="mt-2 text-3xl font-black text-slate-900">{{ $product->name }}</h1>
            <p class="mt-3 text-sm leading-relaxed text-slate-600">
                {{ $product->description ?: 'Produto sem descricao cadastrada.' }}
            </p>

            <div class="mt-6 flex flex-wrap items-center gap-4">
                <p class="text-3xl font-black text-amber-700">R$ {{ number_format((float) $product->price, 2, ',', '.') }}</p>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                    Estoque: {{ $product->stock }}
                </span>
            </div>

            <p class="mt-4 text-xs text-slate-500">SKU: {{ $product->sku }}</p>
            <a href="{{ route('store.products') }}" class="mt-6 inline-flex rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-500 hover:text-slate-900">
                Voltar ao catalogo
            </a>
        </div>
    </section>

    <section class="mt-8">
        <h2 class="text-xl font-black text-slate-900">Relacionados</h2>
        <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @forelse($relatedProducts as $item)
                <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="aspect-square rounded-xl bg-gradient-to-br from-amber-100 to-orange-100"></div>
                    <h3 class="mt-3 text-sm font-bold text-slate-900">{{ $item->name }}</h3>
                    <p class="mt-1 text-sm font-black text-amber-700">R$ {{ number_format((float) $item->price, 2, ',', '.') }}</p>
                    <a href="{{ route('store.products.show', $item) }}" class="mt-3 inline-flex text-sm font-semibold text-slate-700 hover:text-slate-900">
                        Ver produto
                    </a>
                </article>
            @empty
                <p class="rounded-2xl border border-dashed border-slate-300 bg-white p-6 text-sm text-slate-500 sm:col-span-2 lg:col-span-4">
                    Sem outros produtos relacionados por categoria.
                </p>
            @endforelse
        </div>
    </section>
@endsection
