<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ConstruCerto') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,600,700&display=swap" rel="stylesheet" />

        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="flex min-h-screen flex-col overflow-x-hidden bg-[#F5F5F5] font-sans text-[#444444] antialiased">
        @php
            $logoAsset = file_exists(public_path('images/logo-construcerto.png'))
                ? 'images/logo-construcerto.png'
                : (file_exists(public_path('images/logo.jpeg')) ? 'images/logo.jpeg' : null);
            $ordersRoute = auth()->check() ? route('orders.index') : route('login');
            $categoryLinks = [
                ['label' => 'Ferramentas', 'slug' => 'ferramentas'],
                ['label' => 'Elétrica', 'slug' => 'eletrica'],
                ['label' => 'Hidráulica', 'slug' => 'hidraulica'],
                ['label' => 'Materiais Básicos', 'slug' => 'materiais-basicos'],
                ['label' => 'Tintas', 'slug' => 'tintas-e-acabamentos'],
                ['label' => 'Segurança', 'slug' => 'seguranca'],
                ['label' => 'Fixação', 'slug' => 'fixacao'],
            ];
        @endphp

        <header class="sticky top-0 z-40" x-data="{ open: false, departmentsOpen: false }">
            <div class="h-9 bg-[#1A3A6B] text-white">
                <div class="mx-auto flex h-full max-w-7xl items-center justify-between px-4 text-xs sm:px-6 lg:px-8">
                    <a href="tel:+5551999999999" class="font-semibold hover:underline">
                        Atendimento: (51) 9999-9999
                    </a>

                    <p class="hidden font-semibold sm:block">Entrega em Capão da Canoa/RS</p>
                </div>
            </div>

            <div class="bg-[#1A3A6B] shadow-sm">
                <div class="mx-auto flex h-16 max-w-7xl items-center gap-4 px-4 sm:px-6 lg:gap-6 lg:px-8">
                    <a href="{{ route('store.home') }}" class="flex shrink-0 items-center" aria-label="Página inicial da ConstruCerto">
                        @if ($logoAsset)
                            <img
                                src="{{ asset($logoAsset) }}"
                                alt="ConstruCerto Materiais"
                                class="h-11 w-auto max-w-[190px] object-contain"
                            >
                        @else
                            <span class="flex flex-col leading-none">
                                <span class="text-xl font-bold tracking-tight">
                                    <span class="text-[#D42B2B]">Constru</span><span class="text-white">Certo</span>
                                </span>
                                <span class="mt-1 text-[10px] font-semibold uppercase tracking-[0.24em] text-white">Materiais</span>
                            </span>
                        @endif
                    </a>

                    <form method="GET" action="{{ route('store.products') }}" class="hidden min-w-0 flex-1 md:flex">
                        <label for="top-search" class="sr-only">Buscar produtos</label>
                        <input
                            id="top-search"
                            name="q"
                            value="{{ request('q') }}"
                            data-header-product-search
                            placeholder="Buscar cimento, furadeira, tinta..."
                            class="min-w-0 flex-1 rounded-l border-0 bg-white px-4 py-2.5 text-sm text-[#1A1A1A] outline-none placeholder:text-[#767676] focus:ring-2 focus:ring-inset focus:ring-[#2B5FAA]"
                        >
                        <button
                            type="submit"
                            class="flex w-12 shrink-0 items-center justify-center rounded-r bg-[#D42B2B] text-white transition hover:bg-[#B02020] focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-[#1A3A6B]"
                            aria-label="Buscar"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.3-4.3"></path>
                            </svg>
                        </button>
                    </form>

                    <div class="ml-auto hidden shrink-0 items-center gap-3 md:flex">
                        <livewire:cart-icon />

                        @auth
                            <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 rounded px-2 py-2 text-sm font-semibold text-white transition hover:bg-white/10">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true">
                                    <path d="M20 21a8 8 0 0 0-16 0"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                Meu perfil
                            </a>

                            @if (auth()->user()->is_admin)
                                <a href="{{ route('admin.dashboard') }}" class="rounded border border-white/40 px-3 py-2 text-sm font-semibold text-white transition hover:bg-white/10">
                                    Admin
                                </a>
                            @endif

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="inline-flex items-center gap-1.5 rounded border border-white/40 px-3 py-2 text-sm font-semibold text-white transition hover:border-[#D42B2B] hover:bg-[#D42B2B] focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-[#1A3A6B]"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                        <path d="M10 17l5-5-5-5"></path>
                                        <path d="M15 12H3"></path>
                                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                                    </svg>
                                    Sair
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="rounded bg-[#D42B2B] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-[#B02020] focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-[#1A3A6B]">
                                Entrar
                            </a>
                        @endauth
                    </div>

                    <div class="ml-auto flex items-center gap-2 md:hidden">
                        <livewire:cart-icon />

                        <button
                            type="button"
                            class="inline-flex h-10 w-10 items-center justify-center rounded text-white transition hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white"
                            x-on:click="open = ! open"
                            x-bind:aria-expanded="open"
                            aria-controls="mobile-menu"
                            aria-label="Abrir menu"
                        >
                            <svg x-show="! open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="h-6 w-6" aria-hidden="true">
                                <path d="M4 6h16"></path>
                                <path d="M4 12h16"></path>
                                <path d="M4 18h16"></path>
                            </svg>
                            <svg x-show="open" x-cloak xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="h-6 w-6" aria-hidden="true">
                                <path d="m6 6 12 12"></path>
                                <path d="m18 6-12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <nav class="relative hidden h-9 bg-[#D42B2B] md:block" aria-label="Departamentos de produtos">
                <div class="mx-auto flex h-full max-w-7xl items-center px-4 sm:px-6 lg:px-8">
                    <div class="relative h-full" x-on:click.outside="departmentsOpen = false">
                        <button
                            type="button"
                            class="flex h-full items-center gap-2 whitespace-nowrap px-4 text-[13px] font-bold uppercase text-white transition hover:bg-black/15 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-[#D42B2B]"
                            x-on:click="departmentsOpen = ! departmentsOpen"
                            x-bind:aria-expanded="departmentsOpen"
                            aria-haspopup="true"
                            aria-controls="departments-menu"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                <path d="M4 6h16"></path>
                                <path d="M4 12h16"></path>
                                <path d="M4 18h16"></path>
                            </svg>
                            Todos departamentos
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 transition" x-bind:class="{ 'rotate-180': departmentsOpen }" aria-hidden="true">
                                <path d="m6 9 6 6 6-6"></path>
                            </svg>
                        </button>

                        <div
                            id="departments-menu"
                            x-show="departmentsOpen"
                            x-cloak
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="-translate-y-1 opacity-0"
                            x-transition:enter-end="translate-y-0 opacity-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="translate-y-0 opacity-100"
                            x-transition:leave-end="-translate-y-1 opacity-0"
                            class="absolute left-0 top-full z-50 mt-0 w-72 overflow-hidden rounded-b border border-[#E0E0E0] bg-white shadow-xl"
                        >
                            <a
                                href="{{ route('store.products') }}"
                                class="block border-b border-[#E0E0E0] px-4 py-3 text-sm font-bold text-[#1A3A6B] transition hover:bg-[#F5F5F5]"
                                x-on:click="departmentsOpen = false"
                            >
                                Todos os produtos
                            </a>

                            <div class="py-2">
                                @foreach ($categoryLinks as $categoryLink)
                                    <a
                                        href="{{ route('store.products', ['category' => $categoryLink['slug']]) }}"
                                        class="flex items-center justify-between px-4 py-2.5 text-sm font-semibold text-[#444444] transition hover:bg-[#F5F5F5] hover:text-[#1A3A6B]"
                                        x-on:click="departmentsOpen = false"
                                    >
                                        {{ $categoryLink['label'] }}
                                        <span class="text-[#D42B2B]" aria-hidden="true">›</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <div
                id="mobile-menu"
                x-show="open"
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="-translate-y-2 opacity-0"
                x-transition:enter-end="translate-y-0 opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="translate-y-0 opacity-100"
                x-transition:leave-end="-translate-y-2 opacity-0"
                x-on:click.outside="open = false"
                class="absolute inset-x-0 border-t border-white/15 bg-[#1A3A6B] shadow-xl md:hidden"
            >
                <div class="mx-auto max-w-7xl px-4 py-5">
                    <a href="{{ route('store.home') }}" class="inline-flex flex-col leading-none" x-on:click="open = false">
                        @if ($logoAsset)
                            <img src="{{ asset($logoAsset) }}" alt="ConstruCerto Materiais" class="h-10 w-auto max-w-[180px] object-contain">
                        @else
                            <span class="text-xl font-bold tracking-tight">
                                <span class="text-[#D42B2B]">Constru</span><span class="text-white">Certo</span>
                            </span>
                            <span class="mt-1 text-[10px] font-semibold uppercase tracking-[0.24em] text-white">Materiais</span>
                        @endif
                    </a>

                    <form method="GET" action="{{ route('store.products') }}" class="mt-4 flex">
                        <label for="top-search-mobile" class="sr-only">Buscar produtos</label>
                        <input
                            id="top-search-mobile"
                            name="q"
                            value="{{ request('q') }}"
                            data-header-product-search
                            placeholder="Buscar produtos..."
                            class="min-w-0 flex-1 rounded-l border-0 bg-white px-3 py-2.5 text-sm text-[#1A1A1A] outline-none placeholder:text-[#767676] focus:ring-2 focus:ring-inset focus:ring-[#2B5FAA]"
                        >
                        <button type="submit" class="flex w-11 items-center justify-center rounded-r bg-[#D42B2B] text-white hover:bg-[#B02020]" aria-label="Buscar">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.3-4.3"></path>
                            </svg>
                        </button>
                    </form>

                    <nav class="mt-5" aria-label="Departamentos no menu móvel">
                        <button
                            type="button"
                            class="flex w-full items-center justify-between rounded border border-white/20 px-3 py-2.5 text-left text-sm font-bold uppercase text-white transition hover:bg-white/10"
                            x-on:click="departmentsOpen = ! departmentsOpen"
                            x-bind:aria-expanded="departmentsOpen"
                            aria-controls="mobile-departments-menu"
                        >
                            Todos departamentos
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 transition" x-bind:class="{ 'rotate-180': departmentsOpen }" aria-hidden="true">
                                <path d="m6 9 6 6 6-6"></path>
                            </svg>
                        </button>

                        <div
                            id="mobile-departments-menu"
                            x-show="departmentsOpen"
                            x-cloak
                            x-transition
                            class="mt-2 grid grid-cols-2 gap-1"
                        >
                            <a
                                href="{{ route('store.products') }}"
                                class="rounded px-3 py-2.5 text-sm font-semibold text-white transition hover:bg-white/10"
                                x-on:click="open = false; departmentsOpen = false"
                            >
                                Todos os produtos
                            </a>

                            @foreach ($categoryLinks as $categoryLink)
                                <a
                                    href="{{ route('store.products', ['category' => $categoryLink['slug']]) }}"
                                    class="rounded px-3 py-2.5 text-sm font-semibold text-white transition hover:bg-white/10"
                                    x-on:click="open = false; departmentsOpen = false"
                                >
                                    {{ $categoryLink['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </nav>

                    <div class="mt-5 border-t border-white/15 pt-5">
                        @auth
                            <div class="grid gap-2">
                                <a href="{{ route('profile.edit') }}" class="rounded px-3 py-2.5 text-sm font-semibold text-white hover:bg-white/10" x-on:click="open = false">
                                    Meu perfil
                                </a>

                                <a href="{{ route('orders.index') }}" class="rounded px-3 py-2.5 text-sm font-semibold text-white hover:bg-white/10" x-on:click="open = false">
                                    Meus pedidos
                                </a>

                                @if (auth()->user()->is_admin)
                                    <a href="{{ route('admin.dashboard') }}" class="rounded px-3 py-2.5 text-sm font-semibold text-white hover:bg-white/10" x-on:click="open = false">
                                        Painel administrativo
                                    </a>
                                @endif

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full rounded border border-white/30 px-3 py-2.5 text-left text-sm font-semibold text-white hover:bg-white/10">
                                        Sair
                                    </button>
                                </form>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="block rounded bg-[#D42B2B] px-4 py-3 text-center text-sm font-bold text-white hover:bg-[#B02020]" x-on:click="open = false">
                                Entrar
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-6">
            @if (session('status'))
                <div class="mb-4 border-l-4 border-[#1A3A6B] bg-[#E8EFF8] px-4 py-3 text-sm font-semibold text-[#1A3A6B]" role="status">
                    {{ session('status') }}
                </div>
            @endif

            @isset($header)
                <div class="mb-4 rounded-lg border border-[#E0E0E0] bg-white p-4 shadow-sm">
                    {{ $header }}
                </div>
            @endisset

            @hasSection('content')
                @yield('content')
            @elseif (isset($slot))
                {{ $slot }}
            @endif
        </main>

        <footer class="bg-[#1A3A6B] pt-12 text-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-10 pb-12 sm:grid-cols-2 lg:grid-cols-4 lg:gap-8">
                    <div>
                        <a href="{{ route('store.home') }}" class="inline-block text-2xl font-bold tracking-tight text-white">
                            ConstruCerto
                        </a>

                        <p class="mt-4 max-w-xs text-sm leading-6 text-white/80">
                            Sua loja de materiais de construção em Capão da Canoa/RS
                        </p>

                        <address class="mt-4 space-y-2 text-sm not-italic text-white/80">
                            <p>
                                <a href="tel:+5551999999999" class="transition hover:text-white hover:underline">
                                    (51) 9999-9999
                                </a>
                            </p>
                            <p>
                                <a href="mailto:atendimento@construcerto.com.br" class="break-all transition hover:text-white hover:underline">
                                    atendimento@construcerto.com.br
                                </a>
                            </p>
                        </address>

                        <div class="mt-5 flex items-center gap-3" aria-label="Redes sociais">
                            <a href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer" class="flex h-9 w-9 items-center justify-center rounded border border-white/25 text-white transition hover:border-[#D42B2B] hover:bg-[#D42B2B]" aria-label="Instagram">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true">
                                    <rect width="18" height="18" x="3" y="3" rx="5"></rect>
                                    <circle cx="12" cy="12" r="4"></circle>
                                    <circle cx="17.5" cy="6.5" r="0.75" fill="currentColor" stroke="none"></circle>
                                </svg>
                            </a>
                            <a href="https://www.facebook.com/" target="_blank" rel="noopener noreferrer" class="flex h-9 w-9 items-center justify-center rounded border border-white/25 text-white transition hover:border-[#D42B2B] hover:bg-[#D42B2B]" aria-label="Facebook">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5" aria-hidden="true">
                                    <path d="M13.5 21v-8h2.8l.42-3.1H13.5V7.92c0-.9.25-1.51 1.6-1.51h1.71V3.64c-.3-.04-1.31-.13-2.5-.13-2.47 0-4.16 1.51-4.16 4.28V9.9H7.36V13h2.79v8h3.35Z"></path>
                                </svg>
                            </a>
                            <a href="https://wa.me/5551999999999" target="_blank" rel="noopener noreferrer" class="flex h-9 w-9 items-center justify-center rounded border border-white/25 text-white transition hover:border-[#D42B2B] hover:bg-[#D42B2B]" aria-label="WhatsApp">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true">
                                    <path d="M21 11.5a8.4 8.4 0 0 1-9 8.5 9.2 9.2 0 0 1-3.8-.9L3 21l1.8-5a8.6 8.6 0 1 1 16.2-4.5Z"></path>
                                    <path d="M8.3 8.1c.2-.5.5-.5.8-.5h.4c.2 0 .4 0 .5.4l.7 1.7c.1.3 0 .5-.1.7l-.6.7c-.2.2-.1.4 0 .6.6 1.1 1.5 2 2.6 2.6.2.1.4.2.6 0l.8-1c.2-.2.4-.3.7-.2l1.8.9c.3.1.4.3.4.5 0 .3-.2 1.5-1 2.1-.7.6-1.6.7-2.6.4-1-.3-2.3-.8-3.9-2.2-1.3-1.2-2.3-2.6-2.7-3.6-.5-1.1-.1-2.5.4-3.1Z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-white">Departamentos</h2>
                        <ul class="mt-4 space-y-2.5 text-sm text-white/80">
                            @foreach (array_slice($categoryLinks, 0, 6) as $categoryLink)
                                <li>
                                    <a href="{{ route('store.products', ['category' => $categoryLink['slug']]) }}" class="transition hover:text-white hover:underline">
                                        {{ $categoryLink['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-white">Atendimento</h2>
                        <ul class="mt-4 space-y-2.5 text-sm text-white/80">
                            <li>
                                <a href="{{ $ordersRoute }}" class="transition hover:text-white hover:underline">Meus Pedidos</a>
                            </li>
                            <li>
                                <a href="{{ route('store.home') }}#politica-de-troca" class="transition hover:text-white hover:underline">Política de Troca</a>
                            </li>
                            <li>
                                <a href="mailto:atendimento@construcerto.com.br" class="transition hover:text-white hover:underline">Fale Conosco</a>
                            </li>
                            <li>
                                <a href="{{ route('store.home') }}#duvidas-frequentes" class="transition hover:text-white hover:underline">Dúvidas Frequentes</a>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-white">Formas de pagamento</h2>
                        <p class="mt-4 text-sm text-white/80">Aceitamos:</p>

                        <div class="mt-3 flex flex-wrap gap-2" aria-label="Formas de pagamento aceitas">
                            <span class="inline-flex h-9 min-w-16 items-center justify-center rounded border border-white/30 bg-white px-2 text-xs font-bold italic text-[#1A3A6B]">VISA</span>
                            <span class="inline-flex h-9 min-w-16 items-center justify-center rounded border border-white/30 bg-white px-2 text-[11px] font-bold text-[#D42B2B]">MASTER</span>
                            <span class="inline-flex h-9 min-w-16 items-center justify-center rounded border border-white/30 bg-white px-2 text-xs font-bold text-[#198754]">PIX</span>
                            <span class="inline-flex h-9 min-w-16 items-center justify-center rounded border border-white/30 bg-white px-2 text-xs font-bold text-[#444444]">BOLETO</span>
                        </div>

                        <div class="mt-5 flex flex-wrap gap-2">
                            <span class="inline-flex items-center gap-1.5 rounded border border-white/40 px-3 py-2 text-xs font-semibold text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                    <path d="M20 13c0 5-3.5 7.5-8 9-4.5-1.5-8-4-8-9V5l8-3 8 3v8Z"></path>
                                    <path d="m9 12 2 2 4-4"></path>
                                </svg>
                                Compra Segura
                            </span>
                            <span class="inline-flex items-center gap-1.5 rounded border border-white/40 px-3 py-2 text-xs font-semibold text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                    <rect width="18" height="11" x="3" y="11" rx="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                                SSL
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-3 border-t border-white/15 py-4 text-xs text-white/70 sm:flex-row sm:items-center sm:justify-between">
                    <p>© 2025 ConstruCerto — Materiais de Construção. Todos os direitos reservados.</p>
                    <div class="flex flex-wrap items-center gap-x-5 gap-y-2">
                        <a href="{{ route('store.home') }}#politica-de-privacidade" class="transition hover:text-white hover:underline">Política de Privacidade</a>
                        <a href="{{ route('store.home') }}#termos-de-uso" class="transition hover:text-white hover:underline">Termos de Uso</a>
                    </div>
                </div>
            </div>
        </footer>

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
