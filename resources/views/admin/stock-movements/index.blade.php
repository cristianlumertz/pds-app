@extends('layouts.app')

@section('content')
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-black text-slate-900">Histórico de estoque</h1>
                <p class="mt-1 text-sm text-slate-600">Movimentações registradas pelo StockService.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.stock-movements.index') }}" class="mt-5 grid gap-3 rounded-2xl bg-slate-50 p-4 md:grid-cols-5">
            <select name="product_id" class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
                <option value="">Produto</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" @selected((int) request('product_id') === $product->id)>{{ $product->name }} {{ $product->sku ? '('.$product->sku.')' : '' }}</option>
                @endforeach
            </select>
            <select name="type" class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
                <option value="">Tipo</option>
                @foreach ($types as $value => $label)
                    <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <input type="date" name="from" value="{{ request('from') }}" class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
            <input type="date" name="to" value="{{ request('to') }}" class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
            <div class="flex gap-2">
                <button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Filtrar</button>
                <a href="{{ route('admin.stock-movements.index') }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Limpar</a>
            </div>
        </form>

        <div class="mt-5 overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-2 py-2">Produto</th>
                        <th class="px-2 py-2">Tipo</th>
                        <th class="px-2 py-2 text-right">Quantidade</th>
                        <th class="px-2 py-2">Motivo</th>
                        <th class="px-2 py-2">Usuário</th>
                        <th class="px-2 py-2">Pedido</th>
                        <th class="px-2 py-2">Data</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($movements as $movement)
                        <tr class="border-b border-slate-100">
                            <td class="px-2 py-3 font-semibold text-slate-800">{{ $movement->product?->name ?? 'Produto removido' }}</td>
                            <td class="px-2 py-3 text-slate-700">{{ $types[$movement->type] ?? $movement->type }}</td>
                            <td class="px-2 py-3 text-right font-bold text-slate-800">{{ $movement->quantity }}</td>
                            <td class="px-2 py-3 text-slate-600">{{ $movement->reason ?: '-' }}</td>
                            <td class="px-2 py-3 text-slate-600">{{ $movement->user?->name ?? '-' }}</td>
                            <td class="px-2 py-3">
                                @if ($movement->order)
                                    <a href="{{ route('admin.pedidos.show', $movement->order) }}" class="font-semibold text-[#185FA5] hover:underline">#{{ $movement->order_id }}</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-2 py-3 text-slate-600">{{ $movement->created_at?->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-2 py-4 text-sm text-slate-500">Nenhuma movimentação encontrada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">{{ $movements->withQueryString()->links() }}</div>
    </section>
@endsection
