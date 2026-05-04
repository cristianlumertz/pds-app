@extends('layouts.app')

@section('content')
    <section class="mb-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-black text-slate-900">Catalogo de produtos</h1>
        <p class="mt-1 text-sm text-slate-600">Busca e filtros reativos sem recarregar a pagina.</p>
    </section>

    <livewire:product-filter />
@endsection
