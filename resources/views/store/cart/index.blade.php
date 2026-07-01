@extends('layouts.app')

@section('content')
    <nav class="flex items-center gap-2 text-xs text-[#767676]" aria-label="Breadcrumb">
        <a href="{{ route('store.home') }}" class="transition hover:text-[#2B5FAA] hover:underline">Home</a>
        <span aria-hidden="true">›</span>
        <span class="font-semibold text-[#444444]">Carrinho</span>
    </nav>

    <header class="mt-4">
        <h1 class="text-2xl font-bold text-[#1A1A1A]">Meu Carrinho</h1>
        <p class="mt-1 text-[13px] text-[#767676]">
            {{ $cart->item_count }} {{ $cart->item_count === 1 ? 'item' : 'itens' }} no carrinho
        </p>
    </header>

    <section class="mt-6 grid gap-6 lg:grid-cols-3 lg:items-start">
        <div class="min-w-0 lg:col-span-2">
            <livewire:cart-page />
        </div>

        <aside class="min-w-0 lg:col-span-1">
            <livewire:cart-summary />
        </aside>
    </section>
@endsection
