@extends('layouts.app')

@section('content')
    @php
        $statuses = [
            'pending' => ['label' => 'Pendente', 'color' => '#BA7517'],
            'processing' => ['label' => 'Processando', 'color' => '#185FA5'],
            'shipped' => ['label' => 'Enviado', 'color' => '#534AB7'],
            'delivered' => ['label' => 'Entregue', 'color' => '#3B6D11'],
            'cancelled' => ['label' => 'Cancelado', 'color' => '#993C1D'],
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
        $currentStatus = request('status', '');
    @endphp

    <div class="min-h-screen bg-[#F1EFE8] py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-3xl font-black text-[#3D3D3A]">Pedidos</h1>
                    <p class="mt-2 text-sm text-[#3D3D3A]/70">Acompanhe pedidos, pagamentos, descontos e frete.</p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                @foreach ($statuses as $status => $data)
                    <article class="rounded-3xl bg-white p-5 shadow-sm">
                        <p class="text-sm font-bold text-[#3D3D3A]/65">{{ $data['label'] }}</p>
                        <p class="mt-2 text-3xl font-black text-[#3D3D3A]">{{ (int) ($stats[$status] ?? 0) }}</p>
                    </article>
                @endforeach
            </div>

            <form method="GET" action="{{ route('admin.pedidos.index') }}" class="mt-6 grid gap-3 rounded-3xl bg-white p-4 shadow-sm md:grid-cols-4">
                <select name="status" class="rounded border border-[#3D3D3A]/20 px-3 py-2 text-sm">
                    <option value="">Status do pedido</option>
                    @foreach ($statuses as $value => $data)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $data['label'] }}</option>
                    @endforeach
                </select>
                <select name="payment_status" class="rounded border border-[#3D3D3A]/20 px-3 py-2 text-sm">
                    <option value="">Status do pagamento</option>
                    @foreach ($paymentStatuses as $value => $label)
                        <option value="{{ $value }}" @selected(request('payment_status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="payment_method" class="rounded border border-[#3D3D3A]/20 px-3 py-2 text-sm">
                    <option value="">Método</option>
                    @foreach ($paymentMethods as $value => $label)
                        <option value="{{ $value }}" @selected(request('payment_method') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <button class="rounded bg-[#185FA5] px-4 py-2 text-sm font-bold text-white" type="submit">Filtrar</button>
                    <a href="{{ route('admin.pedidos.index') }}" class="rounded border border-[#3D3D3A]/20 px-4 py-2 text-sm font-bold text-[#3D3D3A]">Limpar</a>
                </div>
            </form>

            <section class="mt-6 overflow-hidden rounded-3xl bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1280px] text-left">
                        <thead class="bg-[#F1EFE8] text-xs uppercase tracking-wide text-[#3D3D3A]/65">
                            <tr>
                                <th class="px-4 py-4 font-bold">Pedido</th>
                                <th class="px-4 py-4 font-bold">Cliente</th>
                                <th class="px-4 py-4 font-bold">Pedido</th>
                                <th class="px-4 py-4 font-bold">Pagamento</th>
                                <th class="px-4 py-4 text-right font-bold">Subtotal</th>
                                <th class="px-4 py-4 text-right font-bold">Desconto</th>
                                <th class="px-4 py-4 text-right font-bold">Frete</th>
                                <th class="px-4 py-4 text-right font-bold">Total</th>
                                <th class="px-4 py-4 font-bold">Método</th>
                                <th class="px-4 py-4 font-bold">Data</th>
                                <th class="px-4 py-4 text-right font-bold">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#3D3D3A]/10">
                            @forelse ($orders as $order)
                                @php
                                    $status = $statuses[$order->status] ?? ['label' => ucfirst((string) $order->status), 'color' => '#3D3D3A'];
                                    $paymentLabel = $paymentStatuses[$order->payment_status] ?? ucfirst((string) $order->payment_status);
                                @endphp
                                <tr>
                                    <td class="px-4 py-4 text-sm font-black text-[#3D3D3A]">#{{ $order->id }}</td>
                                    <td class="px-4 py-4">
                                        <p class="text-sm font-bold text-[#3D3D3A]">{{ $order->user?->name ?? 'Cliente removido' }}</p>
                                        <p class="mt-1 text-xs text-[#3D3D3A]/60">{{ $order->user?->email }}</p>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold text-white" style="background-color: {{ $status['color'] }}">{{ $status['label'] }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-sm font-bold text-[#3D3D3A]">{{ $paymentLabel }}</td>
                                    <td class="px-4 py-4 text-right text-sm">R$ {{ number_format((float) $order->subtotal_amount, 2, ',', '.') }}</td>
                                    <td class="px-4 py-4 text-right text-sm text-[#1D9E75]">- R$ {{ number_format((float) $order->discount_amount, 2, ',', '.') }}</td>
                                    <td class="px-4 py-4 text-right text-sm">R$ {{ number_format((float) $order->shipping_amount, 2, ',', '.') }}</td>
                                    <td class="px-4 py-4 text-right text-sm font-black text-[#185FA5]">R$ {{ number_format((float) $order->total_amount, 2, ',', '.') }}</td>
                                    <td class="px-4 py-4 text-sm font-semibold">{{ $order->paymentMethodLabel() }}</td>
                                    <td class="px-4 py-4 text-sm text-[#3D3D3A]/70">{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-4 text-right">
                                        <a href="{{ route('admin.pedidos.show', $order) }}" class="inline-flex rounded-full bg-[#185FA5] px-4 py-2 text-sm font-bold text-white">Ver detalhes</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="px-5 py-12 text-center text-sm font-semibold text-[#3D3D3A]/70">Nenhum pedido encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="mt-6">
                {{ $orders->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection
