@extends('layouts.app')

@section('content')
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-black text-slate-900">Produtos</h1>
                <p class="mt-1 text-sm text-slate-600">Controle de catalogo no painel admin.</p>
            </div>
            <a href="{{ route('admin.products.create') }}" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                Novo produto
            </a>
        </div>

        <div class="mt-5 overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-2 py-2">Nome</th>
                        <th class="px-2 py-2">Categoria</th>
                        <th class="px-2 py-2">SKU</th>
                        <th class="px-2 py-2">Preco</th>
                        <th class="px-2 py-2">Estoque</th>
                        <th class="px-2 py-2">Status</th>
                        <th class="px-2 py-2 text-right">Acoes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr class="border-b border-slate-100">
                            <td class="px-2 py-2 font-semibold text-slate-800">{{ $product->name }}</td>
                            <td class="px-2 py-2 text-slate-600">{{ $product->category->name ?? 'N/A' }}</td>
                            <td class="px-2 py-2 text-slate-600">{{ $product->sku }}</td>
                            <td class="px-2 py-2 text-slate-700">R$ {{ number_format((float) $product->price, 2, ',', '.') }}</td>
                            <td class="px-2 py-2 text-slate-700">{{ $product->stock }}</td>
                            <td class="px-2 py-2">
                                <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $product->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $product->is_active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="px-2 py-2">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="rounded-full border border-slate-300 px-3 py-1 text-xs font-semibold text-slate-700 hover:border-slate-500">
                                        Editar
                                    </a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}">
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
                            <td colspan="7" class="px-2 py-4 text-sm text-slate-500">Nenhum produto cadastrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">
            {{ $products->links() }}
        </div>
    </section>
@endsection
