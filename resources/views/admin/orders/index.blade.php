@extends('layouts.admin')

@section('content')
    @php
        $statuses = [
            'pending' => 'Pendente',
            'processing' => 'Processando',
            'shipped' => 'Enviado',
            'delivered' => 'Entregue',
            'cancelled' => 'Cancelado',
        ];
        $paymentStatuses = [
            'pending' => 'Pendente',
            'paid' => 'Pago',
            'failed' => 'Falhou',
            'cancelled' => 'Cancelado',
            'expired' => 'Expirado',
            'refunded' => 'Reembolsado',
        ];
        $paymentMethods = ['pagarme_checkout' => 'Checkout Pagar.me', 'pix' => 'PIX', 'cartao' => 'Cartão', 'boleto' => 'Boleto'];
    @endphp

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-black uppercase text-[#D42B2B]">Operação</p>
            <h1 class="text-3xl font-black text-[#1A1A1A]">Pedidos</h1>
            <p class="mt-1 text-sm text-[#767676]">Acompanhe pedidos, pagamentos, descontos, frete e rastreamento.</p>
        </div>
    </div>

    <section class="mb-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
        @foreach ($statuses as $value => $label)
            <x-admin.metric-card :label="$label" :value="(int) ($stats[$value] ?? 0)" :tone="$value === 'cancelled' ? 'red' : ($value === 'delivered' ? 'green' : 'blue')" />
        @endforeach
    </section>

    <form method="GET" action="{{ route('admin.pedidos.index') }}" class="mb-5 grid gap-3 rounded-lg border border-[#E0E0E0] bg-white p-4 shadow-sm lg:grid-cols-6">
        <select name="status" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
            <option value="">Status do pedido</option>
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="payment_status" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
            <option value="">Status pagamento</option>
            @foreach ($paymentStatuses as $value => $label)
                <option value="{{ $value }}" @selected(request('payment_status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="payment_method" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
            <option value="">Método</option>
            @foreach ($paymentMethods as $value => $label)
                <option value="{{ $value }}" @selected(request('payment_method') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <input name="customer" value="{{ request('customer') }}" placeholder="Cliente" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
        <input type="date" name="from" value="{{ request('from') }}" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
        <input type="date" name="to" value="{{ request('to') }}" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
        <input name="min_total" value="{{ request('min_total') }}" placeholder="Valor mínimo" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
        <input name="max_total" value="{{ request('max_total') }}" placeholder="Valor máximo" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
        <div class="flex gap-2 lg:col-span-2">
            <button type="submit" class="rounded bg-[#1A3A6B] px-4 py-2 text-sm font-black text-white">Filtrar</button>
            <a href="{{ route('admin.pedidos.index') }}" class="rounded border border-[#E0E0E0] px-4 py-2 text-sm font-black text-[#3D3D3A]">Limpar</a>
        </div>
    </form>

    <section class="overflow-hidden rounded-lg border border-[#E0E0E0] bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1320px] text-left text-sm">
                <thead class="bg-[#F3F5F8] text-xs uppercase text-[#767676]">
                    <tr>
                        <th class="px-4 py-3">Pedido</th>
                        <th class="px-4 py-3">Cliente</th>
                        <th class="px-4 py-3">Data</th>
                        <th class="px-4 py-3">Pedido</th>
                        <th class="px-4 py-3">Pagamento</th>
                        <th class="px-4 py-3">Método</th>
                        <th class="px-4 py-3 text-right">Subtotal</th>
                        <th class="px-4 py-3 text-right">Desconto</th>
                        <th class="px-4 py-3 text-right">Frete</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E0E0E0]">
                    @forelse ($orders as $order)
                        <tr>
                            <td class="px-4 py-3 font-black">#{{ $order->id }}</td>
                            <td class="px-4 py-3">
                                <p class="font-bold">{{ $order->user?->name ?? 'Cliente removido' }}</p>
                                <p class="text-xs text-[#767676]">{{ $order->user?->email }}</p>
                            </td>
                            <td class="px-4 py-3 text-[#767676]">{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3"><x-admin.status-badge :status="$order->status" /></td>
                            <td class="px-4 py-3"><x-admin.status-badge :status="$order->payment_status" /></td>
                            <td class="px-4 py-3 font-bold">{{ $order->paymentMethodLabel() }}</td>
                            <td class="px-4 py-3 text-right">R$ {{ number_format((float) $order->subtotal_amount, 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-[#16765A]">- R$ {{ number_format((float) $order->discount_amount, 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right">R$ {{ number_format((float) $order->shipping_amount, 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-black text-[#1A3A6B]">R$ {{ number_format((float) $order->total_amount, 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.pedidos.show', $order) }}" class="rounded bg-[#1A3A6B] px-3 py-1.5 text-xs font-black text-white">Detalhes</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="11" class="px-4 py-8"><x-admin.empty-state title="Nenhum pedido encontrado." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <x-admin.pagination :items="$orders" />
@endsection
