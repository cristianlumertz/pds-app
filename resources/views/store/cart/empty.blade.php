@extends('layouts.app')

@section('content')
    <nav class="flex items-center gap-2 text-xs text-[#767676]" aria-label="Breadcrumb">
        <a href="{{ route('store.home') }}" class="transition hover:text-[#2B5FAA] hover:underline">Home</a>
        <span aria-hidden="true">›</span>
        <span class="font-semibold text-[#444444]">Carrinho</span>
    </nav>

    <section class="mx-auto my-8 max-w-lg rounded-xl border border-[#E0E0E0] bg-white px-6 py-10 text-center shadow-sm sm:px-10">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="mx-auto h-16 w-16 text-[#E0E0E0]" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
        </svg>

        <h1 class="mt-6 text-xl font-bold text-[#1A1A1A]">Seu carrinho está vazio</h1>
        <p class="mt-2 text-sm leading-6 text-[#767676]">
            Adicione produtos do nosso catálogo para começar.
        </p>

        <a
            href="{{ route('store.products') }}"
            class="mt-7 inline-flex items-center justify-center rounded bg-[#D42B2B] px-8 py-3 text-sm font-bold text-white transition hover:bg-[#B02020] focus:outline-none focus:ring-2 focus:ring-[#D42B2B] focus:ring-offset-2"
        >
            Explorar produtos
        </a>
    </section>
@endsection
