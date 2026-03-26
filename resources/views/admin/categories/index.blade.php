@extends('layouts.app')

@section('content')
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-black text-slate-900">Categorias</h1>
                <p class="mt-1 text-sm text-slate-600">Gerencie as categorias do catalogo.</p>
            </div>
            <a href="{{ route('admin.categories.create') }}" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                Nova categoria
            </a>
        </div>

        <div class="mt-5 overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-2 py-2">Nome</th>
                        <th class="px-2 py-2">Slug</th>
                        <th class="px-2 py-2">Produtos</th>
                        <th class="px-2 py-2">Status</th>
                        <th class="px-2 py-2 text-right">Acoes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr class="border-b border-slate-100">
                            <td class="px-2 py-2 font-semibold text-slate-800">{{ $category->name }}</td>
                            <td class="px-2 py-2 text-slate-600">{{ $category->slug }}</td>
                            <td class="px-2 py-2 text-slate-600">{{ $category->products_count }}</td>
                            <td class="px-2 py-2">
                                <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $category->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $category->is_active ? 'Ativa' : 'Inativa' }}
                                </span>
                            </td>
                            <td class="px-2 py-2">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="rounded-full border border-slate-300 px-3 py-1 text-xs font-semibold text-slate-700 hover:border-slate-500">
                                        Editar
                                    </a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-full border border-rose-300 px-3 py-1 text-xs font-semibold text-rose-700 hover:border-rose-500">
                                            Excluir
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-2 py-4 text-sm text-slate-500">Nenhuma categoria cadastrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">
            {{ $categories->links() }}
        </div>
    </section>
@endsection
