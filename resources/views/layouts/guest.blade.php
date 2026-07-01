<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ConstruCerto') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#F5F5F5] font-sans text-[#444444] antialiased">
        @php
            $logoAsset = file_exists(public_path('images/logo-construcerto.png'))
                ? 'images/logo-construcerto.png'
                : (file_exists(public_path('images/logo.jpeg')) ? 'images/logo.jpeg' : null);
        @endphp

        <main class="mx-auto flex min-h-screen w-full max-w-md flex-col justify-center px-4 py-12">
            <a href="{{ route('store.home') }}" class="mx-auto inline-flex items-center justify-center" aria-label="Voltar para a página inicial da ConstruCerto">
                @if ($logoAsset)
                    <img src="{{ asset($logoAsset) }}" alt="ConstruCerto Materiais" class="h-16 w-auto max-w-[240px] object-contain">
                @else
                    <span class="text-2xl font-bold tracking-tight">
                        <span class="text-[#D42B2B]">Constru</span><span class="text-[#1A3A6B]">Certo</span>
                    </span>
                @endif
            </a>

            <section class="mt-7 rounded-xl border border-[#E0E0E0] bg-white p-6 shadow-sm sm:p-8">
                {{ $slot }}
            </section>

            <p class="mt-6 text-center text-xs text-[#767676]">© 2025 ConstruCerto</p>
        </main>
    </body>
</html>
