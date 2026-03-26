@extends('layouts.app')

@section('content')
    <section class="relative overflow-hidden rounded-3xl border border-amber-200 bg-white p-8 shadow-sm sm:p-12">
        <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-amber-200/50 blur-2xl"></div>
        <div class="absolute -bottom-16 -left-16 h-52 w-52 rounded-full bg-orange-200/50 blur-2xl"></div>

        <div class="relative grid gap-8 lg:grid-cols-2 lg:items-center">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-amber-700">E-commerce em Laravel</p>
                <h1 class="mt-3 text-3xl font-black leading-tight text-slate-900 sm:text-5xl">
                    Painel de vendas para clientes e administradores
                </h1>
                <p class="mt-4 max-w-xl text-sm text-slate-600 sm:text-base">
                    Explore o catalogo, acesse sua conta e gerencie produtos e categorias no painel admin.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('store.products') }}" class="rounded-full bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700">
                        Ver produtos
                    </a>
                    @guest
                        <a href="{{ route('register') }}" class="rounded-full border border-amber-300 px-5 py-2.5 text-sm font-semibold text-amber-800 transition hover:bg-amber-100">
                            Criar conta
                        </a>
                    @endguest
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Categorias ativas</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    @forelse($categories as $category)
                        <a href="{{ route('store.products', ['category' => $category->slug]) }}" class="rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 ring-1 ring-slate-200 transition hover:bg-amber-50 hover:text-amber-800">
                            {{ $category->name }}
                        </a>
                    @empty
                        <span class="text-sm text-slate-500">Nenhuma categoria cadastrada ainda.</span>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <section class="mt-8">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-xl font-black text-slate-900">Produtos em destaque</h2>
            <a href="{{ route('store.products') }}" class="text-sm font-semibold text-amber-700 hover:text-amber-800">
                Ver catalogo completo
            </a>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @forelse($featuredProducts as $product)
                <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="aspect-square rounded-xl bg-gradient-to-br from-amber-100 to-orange-100"></div>
                    <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $product->category->name ?? 'Sem categoria' }}</p>
                    <h3 class="mt-1 line-clamp-2 text-sm font-bold text-slate-900">{{ $product->name }}</h3>
                    <p class="mt-2 text-lg font-black text-amber-700">R$ {{ number_format((float) $product->price, 2, ',', '.') }}</p>
                    <a href="{{ route('store.products.show', $product) }}" class="mt-3 inline-flex text-sm font-semibold text-slate-700 hover:text-slate-900">
                        Ver detalhes
                    </a>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-sm text-slate-500 sm:col-span-2 lg:col-span-4">
                    Sem produtos ativos no momento. Cadastre itens no painel admin para preencher o catalogo.
                </div>
            @endforelse
        </div>
    </section>
@endsection
