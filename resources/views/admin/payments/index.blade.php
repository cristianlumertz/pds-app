@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-black uppercase text-[#D42B2B]">Financeiro</p>
            <h1 class="text-3xl font-black text-[#1A1A1A]">Pagamentos</h1>
            <p class="mt-1 text-sm text-[#767676]">Acompanhe cobranças, links Pagar.me e eventos recebidos.</p>
        </div>
    </div>

    <section class="mb-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-6">
        @foreach ($statuses as $status => $label)
            <x-admin.metric-card :label="$label" :value="(int) ($stats[$status] ?? 0)" :tone="$status === 'paid' ? 'green' : ($status === 'pending' ? 'yellow' : ($status === 'failed' ? 'red' : 'gray'))" />
        @endforeach
    </section>

    <form method="GET" action="{{ route('admin.payments.index') }}" class="mb-5 grid gap-3 rounded-lg border border-[#E0E0E0] bg-white p-4 shadow-sm lg:grid-cols-6">
        <select name="status" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
            <option value="">Status</option>
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="method" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
            <option value="">Método</option>
            @foreach ($methods as $value => $label)
                <option value="{{ $value }}" @selected(request('method') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <input name="order_id" value="{{ request('order_id') }}" placeholder="Pedido #" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
        <input name="customer" value="{{ request('customer') }}" placeholder="Cliente" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
        <input type="date" name="from" value="{{ request('from') }}" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
        <input type="date" name="to" value="{{ request('to') }}" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
        <div class="flex gap-2 lg:col-span-2">
            <button type="submit" class="rounded bg-[#1A3A6B] px-4 py-2 text-sm font-black text-white">Filtrar</button>
            <a href="{{ route('admin.payments.index') }}" class="rounded border border-[#E0E0E0] px-4 py-2 text-sm font-black text-[#3D3D3A]">Limpar</a>
        </div>
    </form>

    <section class="overflow-hidden rounded-lg border border-[#E0E0E0] bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1180px] text-left text-sm">
                <thead class="bg-[#F3F5F8] text-xs uppercase text-[#767676]">
                    <tr>
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">Pedido</th>
                        <th class="px-4 py-3">Cliente</th>
                        <th class="px-4 py-3">Método</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Valor</th>
                        <th class="px-4 py-3">Pagar.me</th>
                        <th class="px-4 py-3">Criado</th>
                        <th class="px-4 py-3">Pago</th>
                        <th class="px-4 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E0E0E0]">
                    @forelse ($payments as $payment)
                        @php
                            $checkoutUrl = (string) $payment->pagarme_checkout_url;
                            $canOpenCheckout = str_starts_with($checkoutUrl, 'https://');
                        @endphp
                        <tr>
                            <td class="px-4 py-3 font-black">#{{ $payment->id }}</td>
                            <td class="px-4 py-3"><a href="{{ route('admin.pedidos.show', $payment->order) }}" class="font-black text-[#1A3A6B] hover:underline">#{{ $payment->order_id }}</a></td>
                            <td class="px-4 py-3">{{ $payment->order?->user?->name ?? 'Cliente removido' }}</td>
                            <td class="px-4 py-3 font-bold">{{ $payment->paymentMethodLabel() }}</td>
                            <td class="px-4 py-3"><x-admin.status-badge :status="$payment->status" /></td>
                            <td class="px-4 py-3 text-right font-black">R$ {{ number_format((float) $payment->amount, 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-xs">
                                {{ $payment->pagarme_payment_link_id ?: '-' }}
                                @if ($canOpenCheckout)
                                    <a href="{{ $checkoutUrl }}" target="_blank" rel="noopener noreferrer" class="ml-2 font-black text-[#D42B2B] hover:underline">abrir</a>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-[#767676]">{{ $payment->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-[#767676]">{{ $payment->paid_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-3 text-right"><a href="{{ route('admin.payments.show', $payment) }}" class="rounded bg-[#1A3A6B] px-3 py-1.5 text-xs font-black text-white">Detalhes</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="px-4 py-8"><x-admin.empty-state title="Nenhum pagamento encontrado." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <x-admin.pagination :items="$payments" />
@endsection
