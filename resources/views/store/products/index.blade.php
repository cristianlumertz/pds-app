@extends('layouts.app')

@section('content')
    <nav class="flex items-center gap-2 text-xs text-[#767676]" aria-label="Breadcrumb">
        <a href="{{ route('store.home') }}" class="transition hover:text-[#2B5FAA] hover:underline">Home</a>
        <span aria-hidden="true">›</span>
        <span class="font-semibold text-[#444444]">Produtos</span>
    </nav>

    <header class="mt-4">
        <h1 class="text-2xl font-bold text-[#1A1A1A]">Catálogo de Produtos</h1>
        <p class="mt-1 text-[13px] text-[#767676]">
            {{ $products->total() ?? '' }} produtos encontrados
        </p>
    </header>

    <div class="mt-6">
        <livewire:product-filter />
    </div>
@endsection
