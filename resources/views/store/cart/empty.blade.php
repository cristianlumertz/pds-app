@extends('layouts.app')

@section('content')
    <section class="mx-auto w-full max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-10">
            <div class="mx-auto max-w-2xl text-center">
                <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-2xl bg-slate-100">
                    <svg viewBox="0 0 64 64" class="h-12 w-12 text-slate-500" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M8 12h7l5.5 26.5a4 4 0 0 0 3.9 3.2h20.8a4 4 0 0 0 3.9-3l4.2-17.7H18.3" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="26" cy="50" r="3.5" fill="currentColor"/>
                        <circle cx="45" cy="50" r="3.5" fill="currentColor"/>
                        <path d="M22 6l30 30" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                </div>

                <h1 class="mt-5 text-2xl font-black text-slate-900 sm:text-3xl">Seu carrinho está vazio</h1>
                <p class="mt-2 text-sm text-slate-600 sm:text-base">
                    Ainda não há itens no carrinho. Explore o catálogo e adicione materiais para continuar sua compra.
                </p>

                <div class="mt-6 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <a href="{{ route('store.products') }}" class="inline-flex w-full items-center justify-center rounded-xl bg-[#185FA5] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#174f88] sm:w-auto">
                        Ver produtos
                    </a>
                    <a href="{{ route('categories.index') }}" class="inline-flex w-full items-center justify-center rounded-xl border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 transition hover:border-slate-500 sm:w-auto">
                        Explorar categorias
                    </a>
                </div>
            </div>

            <div class="mx-auto mt-8 max-w-xl rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:p-5">
                <form method="GET" action="{{ route('store.products') }}" class="space-y-3">
                    @csrf
                    <div>
                        <x-input-label for="q" value="Busca rápida de produtos" />
                        <x-text-input
                            id="q"
                            name="q"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="Ex.: Cimento CP-II, Furadeira, Trena"
                            :value="old('q')"
                        />
                    </div>

                    <x-primary-button class="w-full justify-center sm:w-auto">
                        Buscar no catálogo
                    </x-primary-button>
                </form>
            </div>
        </div>
    </section>
@endsection
