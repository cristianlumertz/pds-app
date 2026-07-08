@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-black uppercase text-[#D42B2B]">Operação</p>
            <h1 class="text-3xl font-black text-[#1A1A1A]">Movimentações de estoque</h1>
            <p class="mt-1 text-sm text-[#767676]">Histórico de entradas, saídas, ajustes, reservas e cancelamentos.</p>
        </div>
        <a href="{{ route('admin.stock.index') }}" class="rounded border border-[#C5D4EC] bg-white px-4 py-2 text-sm font-black text-[#1A3A6B]">Voltar ao estoque</a>
    </div>

    <form method="GET" action="{{ route('admin.stock-movements.index') }}" class="mb-5 grid gap-3 rounded-lg border border-[#E0E0E0] bg-white p-4 shadow-sm lg:grid-cols-7">
        <select name="product_id" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
            <option value="">Produto</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}" @selected((string) request('product_id') === (string) $product->id)>{{ $product->name }} {{ $product->sku ? '('.$product->sku.')' : '' }}</option>
            @endforeach
        </select>
        <select name="type" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
            <option value="">Tipo</option>
            @foreach ($types as $value => $label)
                <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="user_id" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
            <option value="">Usuário</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected((string) request('user_id') === (string) $user->id)>{{ $user->name }}</option>
            @endforeach
        </select>
        <input name="order_id" value="{{ request('order_id') }}" placeholder="Pedido #" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
        <input type="date" name="from" value="{{ request('from') }}" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
        <input type="date" name="to" value="{{ request('to') }}" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
        <div class="flex gap-2">
            <button class="rounded bg-[#1A3A6B] px-4 py-2 text-sm font-black text-white">Filtrar</button>
            <a href="{{ route('admin.stock-movements.index') }}" class="rounded border border-[#E0E0E0] px-4 py-2 text-sm font-black text-[#3D3D3A]">Limpar</a>
        </div>
    </form>

    <section class="overflow-hidden rounded-lg border border-[#E0E0E0] bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1120px] text-left text-sm">
                <thead class="bg-[#F3F5F8] text-xs uppercase text-[#767676]">
                    <tr>
                        <th class="px-4 py-3">Data</th>
                        <th class="px-4 py-3">Produto</th>
                        <th class="px-4 py-3">Tipo</th>
                        <th class="px-4 py-3 text-right">Quantidade</th>
                        <th class="px-4 py-3">Usuário</th>
                        <th class="px-4 py-3">Pedido</th>
                        <th class="px-4 py-3">Motivo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E0E0E0]">
                    @forelse ($movements as $movement)
                        <tr>
                            <td class="px-4 py-3 text-[#767676]">{{ $movement->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">
                                <p class="font-black">{{ $movement->product?->name ?? 'Produto removido' }}</p>
                                <p class="text-xs text-[#767676]">{{ $movement->product?->sku }}</p>
                            </td>
                            <td class="px-4 py-3"><x-admin.status-badge :status="$movement->type" :label="$types[$movement->type] ?? $movement->type" /></td>
                            <td class="px-4 py-3 text-right font-black">{{ $movement->quantity }}</td>
                            <td class="px-4 py-3">{{ $movement->user?->name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @if ($movement->order)
                                    <a href="{{ route('admin.pedidos.show', $movement->order) }}" class="font-black text-[#1A3A6B] hover:underline">#{{ $movement->order_id }}</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $movement->reason ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8"><x-admin.empty-state title="Nenhuma movimentação encontrada." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <x-admin.pagination :items="$movements" />
@endsection
