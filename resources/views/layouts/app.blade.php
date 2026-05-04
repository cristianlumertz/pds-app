<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ShopLaravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#F1EFE8] text-[#3D3D3A] antialiased">
        @php
            $user = auth()->user();
            $cartCount = $user ? (int) ($user->carts()->latest('id')->value('item_count') ?? 0) : 0;
            $logoFile = public_path('images/logo-construcerto.png');
            $productsRoute = $user && $user->is_admin ? route('admin.products.index') : route('store.products');
        @endphp

        <header class="border-b border-slate-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex w-full max-w-7xl items-center gap-3 px-4 py-3 sm:px-6 lg:px-8">
                <a href="{{ route('store.home') }}" class="shrink-0">
                    @if(file_exists($logoFile))
                        <img src="{{ asset('images/logo-construcerto.png') }}" alt="ConstruCerto" class="h-11 w-auto">
                    @else
                        <span class="text-lg font-black tracking-tight text-[#185FA5]">ConstruCerto</span>
                    @endif
                </a>

                <form method="GET" action="{{ route('store.products') }}" class="hidden flex-1 md:block">
                    <label for="top-search" class="sr-only">Buscar produtos</label>
                    <input
                        id="top-search"
                        name="q"
                        value="{{ request('q') }}"
                        data-header-product-search
                        placeholder="Buscar cimento, furadeira, tinta..."
                        class="w-full rounded-full border border-slate-300 bg-[#F8F6EF] px-4 py-2 text-sm outline-none transition focus:border-[#185FA5] focus:ring-2 focus:ring-[#185FA5]/20"
                    >
                </form>

                <div class="ml-auto flex items-center gap-2">
                    <a href="{{ $productsRoute }}" class="rounded-full border border-slate-300 px-3 py-1.5 text-sm font-semibold text-slate-700 hover:border-slate-500">
                        Produtos
                    </a>
                    <a href="{{ route('categories.index') }}" class="rounded-full border border-slate-300 px-3 py-1.5 text-sm font-semibold text-slate-700 hover:border-slate-500">
                        Categorias
                    </a>
                    <span class="inline-flex items-center gap-2 rounded-full bg-[#1D9E75]/10 px-3 py-1.5 text-xs font-bold text-[#1D9E75]">
                        Carrinho
                        <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-[#1D9E75] px-1.5 text-[11px] text-white">
                            {{ $cartCount }}
                        </span>
                    </span>

                    @auth
                        <a href="{{ auth()->user()->is_admin ? route('admin.dashboard') : route('user.dashboard') }}" class="rounded-full bg-[#185FA5] px-3 py-1.5 text-sm font-semibold text-white hover:bg-[#174f88]">
                            Perfil
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-full border border-[#993C1D] px-3 py-1.5 text-sm font-semibold text-[#993C1D] hover:bg-[#993C1D]/10">
                                Sair
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="rounded-full bg-[#185FA5] px-3 py-1.5 text-sm font-semibold text-white hover:bg-[#174f88]">
                            Entrar
                        </a>
                    @endauth
                </div>
            </div>

            <div class="border-t border-slate-100 px-4 py-2 md:hidden">
                <form method="GET" action="{{ route('store.products') }}">
                    <label for="top-search-mobile" class="sr-only">Buscar produtos</label>
                    <input
                        id="top-search-mobile"
                        name="q"
                        value="{{ request('q') }}"
                        data-header-product-search
                        placeholder="Buscar produtos..."
                        class="w-full rounded-full border border-slate-300 bg-[#F8F6EF] px-4 py-2 text-sm outline-none transition focus:border-[#185FA5] focus:ring-2 focus:ring-[#185FA5]/20"
                    >
                </form>
            </div>
        </header>

        <main class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            @if(session('status'))
                <div class="mb-4 rounded-xl border border-[#1D9E75]/20 bg-[#1D9E75]/10 px-4 py-3 text-sm font-semibold text-[#1D9E75]">
                    {{ session('status') }}
                </div>
            @endif

            @isset($header)
                <div class="mb-4 rounded-xl bg-white p-4 shadow-sm">
                    {{ $header }}
                </div>
            @endisset

            @hasSection('content')
                @yield('content')
            @elseif (isset($slot))
                {{ $slot }}
            @endif
        </main>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const inputs = document.querySelectorAll('[data-header-product-search]');
                if (!inputs.length) {
                    return;
                }

                let debounceTimer;

                const dispatchSearch = (value) => {
                    if (!window.Livewire || typeof window.Livewire.dispatch !== 'function') {
                        return;
                    }

                    window.Livewire.dispatch('header-search:updated', { term: value });
                };

                inputs.forEach((input) => {
                    input.addEventListener('input', (event) => {
                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(() => {
                            dispatchSearch(event.target.value);
                        }, 250);
                    });
                });
            });
        </script>
    </body>
</html>
