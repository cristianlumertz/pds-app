<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'PDS Shop' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[radial-gradient(circle_at_top,_#fef3c7,_#fffbeb_35%,_#ffffff_70%)] text-slate-900">
    <header class="border-b border-amber-200/70 bg-white/70 backdrop-blur">
        <div class="mx-auto flex w-full max-w-6xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('store.home') }}" class="text-lg font-black tracking-tight text-amber-700">
                PDS Shop
            </a>

            <nav class="flex items-center gap-2 text-sm font-medium sm:gap-4">
                <a href="{{ route('store.home') }}" class="rounded-full px-3 py-1.5 text-slate-700 transition hover:bg-amber-100 hover:text-amber-900">
                    Inicio
                </a>
                <a href="{{ route('store.products') }}" class="rounded-full px-3 py-1.5 text-slate-700 transition hover:bg-amber-100 hover:text-amber-900">
                    Catalogo
                </a>

                @auth
                    <a href="{{ route('user.dashboard') }}" class="rounded-full px-3 py-1.5 text-slate-700 transition hover:bg-amber-100 hover:text-amber-900">
                        Minha conta
                    </a>

                    @if(auth()->user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}" class="rounded-full bg-slate-900 px-3 py-1.5 text-white transition hover:bg-slate-700">
                            Admin
                        </a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-full border border-slate-300 px-3 py-1.5 text-slate-700 transition hover:border-slate-400 hover:text-slate-950">
                            Sair
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-full px-3 py-1.5 text-slate-700 transition hover:bg-amber-100 hover:text-amber-900">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="rounded-full bg-amber-500 px-3 py-1.5 text-white transition hover:bg-amber-600">
                        Cadastro
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="mx-auto w-full max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        @if(session('status'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        {{ $slot ?? '' }}
        @yield('content')
    </main>
</body>
</html>
