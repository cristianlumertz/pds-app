<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Construcerto') }} Admin</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#F3F5F8] font-sans text-[#1A1A1A] antialiased" x-data="{ adminSidebarOpen: false }">
        @php
            $navItems = [
                ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'match' => 'admin.dashboard', 'icon' => 'D'],
                ['label' => 'Produtos', 'route' => 'admin.products.index', 'match' => 'admin.products.*', 'icon' => 'P'],
                ['label' => 'Categorias', 'route' => 'admin.categories.index', 'match' => 'admin.categories.*', 'icon' => 'C'],
                ['label' => 'Pedidos', 'route' => 'admin.pedidos.index', 'match' => 'admin.pedidos.*', 'icon' => '#'],
                ['label' => 'Pagamentos', 'route' => 'admin.payments.index', 'match' => 'admin.payments.*', 'icon' => '$'],
                ['label' => 'Estoque', 'route' => 'admin.stock.index', 'match' => 'admin.stock.*', 'icon' => 'E'],
                ['label' => 'Movimentações', 'route' => 'admin.stock-movements.index', 'match' => 'admin.stock-movements.*', 'icon' => 'M'],
                ['label' => 'Cupons', 'route' => 'admin.coupons.index', 'match' => 'admin.coupons.*', 'icon' => '%'],
                ['label' => 'Clientes', 'route' => 'admin.users.index', 'match' => 'admin.users.*', 'icon' => 'U'],
                ['label' => 'Relatórios', 'route' => 'admin.reports.index', 'match' => 'admin.reports.*', 'icon' => 'R'],
            ];
        @endphp

        <div class="min-h-screen lg:flex">
            <div
                class="fixed inset-0 z-40 bg-black/40 lg:hidden"
                x-show="adminSidebarOpen"
                x-transition.opacity
                x-cloak
                x-on:click="adminSidebarOpen = false"
            ></div>

            <aside
                class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col bg-[#1A3A6B] text-white shadow-2xl transition-transform duration-200 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0"
                x-bind:class="{ 'translate-x-0': adminSidebarOpen }"
            >
                <div class="flex h-20 items-center justify-between border-b border-white/10 px-6">
                    <a href="{{ route('admin.dashboard') }}" class="block">
                        <span class="block text-lg font-black tracking-wide">Construcerto</span>
                        <span class="text-xs font-semibold uppercase text-white/60">Painel administrativo</span>
                    </a>
                    <button type="button" class="rounded p-2 text-white/70 hover:bg-white/10 lg:hidden" x-on:click="adminSidebarOpen = false">
                        <span class="sr-only">Fechar menu</span>
                        X
                    </button>
                </div>

                <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-5">
                    @foreach ($navItems as $item)
                        @php
                            $active = request()->routeIs($item['match']);
                        @endphp
                        <a
                            href="{{ route($item['route']) }}"
                            class="flex items-center gap-3 rounded px-3 py-2.5 text-sm font-bold transition {{ $active ? 'bg-white text-[#1A3A6B]' : 'text-white/80 hover:bg-white/10 hover:text-white' }}"
                            @if($active) aria-current="page" @endif
                        >
                            <span class="flex h-7 w-7 items-center justify-center rounded {{ $active ? 'bg-[#D42B2B] text-white' : 'bg-white/10 text-white' }} text-xs font-black">
                                {{ $item['icon'] }}
                            </span>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                <div class="border-t border-white/10 p-4">
                    <a href="{{ route('store.home') }}" class="flex items-center justify-center rounded border border-white/20 px-4 py-2 text-sm font-bold text-white/90 hover:bg-white/10">
                        Voltar para loja
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="flex w-full items-center justify-center rounded bg-[#D42B2B] px-4 py-2 text-sm font-black text-white hover:bg-[#B02020]">
                            Sair
                        </button>
                    </form>
                </div>
            </aside>

            <div class="min-w-0 flex-1">
                <header class="sticky top-0 z-30 border-b border-[#E0E0E0] bg-white/95 backdrop-blur">
                    <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
                        <button type="button" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm font-bold text-[#1A3A6B] lg:hidden" x-on:click="adminSidebarOpen = true">
                            Menu
                        </button>
                        <div class="hidden lg:block">
                            <p class="text-xs font-bold uppercase text-[#767676]">Admin</p>
                            <p class="text-sm font-black text-[#1A3A6B]">{{ auth()->user()?->name }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="rounded-full bg-[#F3F5F8] px-3 py-1.5 text-xs font-bold text-[#767676]">{{ now()->format('d/m/Y') }}</span>
                            <a href="{{ route('admin.dashboard') }}" class="rounded bg-[#D42B2B] px-3 py-2 text-sm font-black text-white hover:bg-[#B02020]">
                                Painel
                            </a>
                        </div>
                    </div>
                </header>

                <main class="px-4 py-6 sm:px-6 lg:px-8">
                    @if (session('status'))
                        <div class="mb-5 rounded border border-[#1D9E75]/25 bg-[#1D9E75]/10 px-4 py-3 text-sm font-bold text-[#16765A]">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-5 rounded border border-[#D42B2B]/25 bg-[#D42B2B]/10 px-4 py-3 text-sm font-bold text-[#B02020]">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
