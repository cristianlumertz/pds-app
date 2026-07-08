@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-black uppercase text-[#D42B2B]">Operação</p>
            <h1 class="text-3xl font-black text-[#1A1A1A]">Estoque</h1>
            <p class="mt-1 text-sm text-[#767676]">Ajustes manuais sempre registram movimentações via StockService.</p>
        </div>
        <a href="{{ route('admin.stock-movements.index') }}" class="rounded border border-[#C5D4EC] bg-white px-4 py-2 text-sm font-black text-[#1A3A6B]">Histórico de movimentações</a>
    </div>

    <section class="mb-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.metric-card label="Produtos" :value="$stats['total']" />
        <x-admin.metric-card label="Normal" :value="$stats['normal']" tone="green" />
        <x-admin.metric-card label="Baixo" :value="$stats['low']" tone="yellow" />
        <x-admin.metric-card label="Zerado" :value="$stats['out']" tone="red" />
    </section>

    <form method="GET" action="{{ route('admin.stock.index') }}" class="mb-5 grid gap-3 rounded-lg border border-[#E0E0E0] bg-white p-4 shadow-sm md:grid-cols-5">
        <input name="q" value="{{ request('q') }}" placeholder="Produto ou SKU" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm md:col-span-2">
        <select name="category_id" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
            <option value="">Categoria</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <select name="stock" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
            <option value="">Status estoque</option>
            <option value="low" @selected(request('stock') === 'low')>Baixo</option>
            <option value="out" @selected(request('stock') === 'out')>Zerado</option>
        </select>
        <div class="flex gap-2">
            <button type="submit" class="rounded bg-[#1A3A6B] px-4 py-2 text-sm font-black text-white">Filtrar</button>
            <a href="{{ route('admin.stock.index') }}" class="rounded border border-[#E0E0E0] px-4 py-2 text-sm font-black text-[#3D3D3A]">Limpar</a>
        </div>
    </form>

    <section class="overflow-hidden rounded-lg border border-[#E0E0E0] bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1180px] text-left text-sm">
                <thead class="bg-[#F3F5F8] text-xs uppercase text-[#767676]">
                    <tr>
                        <th class="px-4 py-3">Produto</th>
                        <th class="px-4 py-3">SKU</th>
                        <th class="px-4 py-3">Categoria</th>
                        <th class="px-4 py-3 text-right">Atual</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Última movimentação</th>
                        <th class="px-4 py-3">Ajustar estoque</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E0E0E0]">
                    @forelse ($products as $product)
                        @php
                            $stockStatus = (int) $product->stock === 0 ? 'failed' : ((int) $product->stock <= 5 ? 'pending' : 'paid');
                            $stockLabel = (int) $product->stock === 0 ? 'Zerado' : ((int) $product->stock <= 5 ? 'Baixo' : 'Normal');
                        @endphp
                        <tr>
                            <td class="px-4 py-3 font-black">{{ $product->name }}</td>
                            <td class="px-4 py-3 font-bold">{{ $product->sku }}</td>
                            <td class="px-4 py-3">{{ $product->category?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-right text-lg font-black text-[#1A3A6B]">{{ $product->stock }}</td>
                            <td class="px-4 py-3"><x-admin.status-badge :status="$stockStatus" :label="$stockLabel" /></td>
                            <td class="px-4 py-3 text-[#767676]">{{ $product->last_stock_movement_at ? \Illuminate\Support\Carbon::parse($product->last_stock_movement_at)->format('d/m/Y H:i') : '-' }}</td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('admin.stock.update', $product) }}" class="grid min-w-[420px] grid-cols-5 gap-2">
                                    @csrf
                                    <select name="operation" class="rounded border border-[#E0E0E0] px-2 py-2 text-xs">
                                        <option value="entrada">Entrada</option>
                                        <option value="saida">Saída</option>
                                        <option value="ajuste">Ajuste</option>
                                    </select>
                                    <input name="quantity" type="number" min="1" placeholder="Qtd" class="rounded border border-[#E0E0E0] px-2 py-2 text-xs">
                                    <input name="target_stock" type="number" min="0" placeholder="Novo saldo" class="rounded border border-[#E0E0E0] px-2 py-2 text-xs">
                                    <input name="reason" placeholder="Motivo" class="rounded border border-[#E0E0E0] px-2 py-2 text-xs">
                                    <button type="submit" class="rounded bg-[#1D9E75] px-3 py-2 text-xs font-black text-white">Salvar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8"><x-admin.empty-state title="Nenhum produto encontrado." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <x-admin.pagination :items="$products" />
@endsection
