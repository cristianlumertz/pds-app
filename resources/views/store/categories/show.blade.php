@extends('layouts.app')

@section('content')
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <a href="{{ route('categories.index') }}" class="text-sm font-semibold text-[#185FA5] hover:underline">Voltar para categorias</a>
        <h1 class="mt-2 text-2xl font-black text-slate-900">{{ $category->name }}</h1>
        <p class="mt-1 text-sm text-slate-600">{{ $category->description }}</p>
    </section>

    <section class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @forelse($products as $product)
            @php
                $imageUrl = $product->primaryImageUrl();
            @endphp
            <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <a href="{{ route('store.products.show', $product) }}" class="block overflow-hidden rounded-xl">
                    @if($imageUrl)
                        <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="aspect-square w-full object-cover">
                    @else
                        <div class="aspect-square rounded-xl bg-gradient-to-br from-amber-100 to-orange-100"></div>
                    @endif
                </a>
                <h3 class="mt-3 line-clamp-2 text-sm font-bold text-slate-900">{{ $product->name }}</h3>
                <p class="mt-2 text-lg font-black text-amber-700">R$ {{ number_format((float) $product->price, 2, ',', '.') }}</p>
                <a href="{{ route('store.products.show', $product) }}" class="mt-3 inline-flex text-sm font-semibold text-slate-700 hover:text-slate-900">Ver detalhes</a>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-sm text-slate-500 sm:col-span-2 lg:col-span-4">
                Nenhum produto ativo nesta categoria.
            </div>
        @endforelse
    </section>

    <div class="mt-6">
        {{ $products->links() }}
    </div>
@endsection
