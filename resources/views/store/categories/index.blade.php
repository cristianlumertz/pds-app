@extends('layouts.app')

@section('content')
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-black text-slate-900">Categorias</h1>
        <p class="mt-1 text-sm text-slate-600">Explore o catalogo por tipo de material.</p>

        <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($categories as $category)
                <a href="{{ route('categories.show', $category) }}" class="rounded-2xl border border-slate-200 bg-slate-50 p-4 transition hover:-translate-y-0.5 hover:shadow-sm">
                    <p class="text-sm font-black text-slate-900">{{ $category->name }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ $category->products_count }} produtos ativos</p>
                </a>
            @empty
                <p class="rounded-xl border border-dashed border-slate-300 p-4 text-sm text-slate-500">Nenhuma categoria ativa encontrada.</p>
            @endforelse
        </div>
    </section>
@endsection
