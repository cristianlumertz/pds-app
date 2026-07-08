@extends('layouts.admin')

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
        $status = $statuses[$order->status] ?? ['label' => ucfirst((string) $order->status), 'color' => '#3D3D3A'];
        $zipCode = preg_replace('/\D/', '', (string) $order->address?->zip_code);
        $formattedZipCode = strlen($zipCode) === 8 ? preg_replace('/(\d{5})(\d{3})/', '$1-$2', $zipCode) : $order->address?->zip_code;
    @endphp

    <div class="min-h-screen bg-[#F1EFE8] py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <a href="{{ route('admin.pedidos.index') }}" class="text-sm font-bold text-[#185FA5] hover:underline">Voltar para pedidos</a>
                    <h1 class="mt-2 text-3xl font-black text-[#3D3D3A]">Pedido #{{ $order->id }}</h1>
                </div>
                <span class="inline-flex w-fit rounded-full px-4 py-2 text-sm font-bold text-white" style="background-color: {{ $status['color'] }}">{{ $status['label'] }}</span>
            </div>

            @if (session('status'))
                <div class="mb-6 rounded-3xl border border-[#1D9E75]/20 bg-[#1D9E75]/10 px-5 py-4 text-sm font-bold text-[#1D9E75]">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-3xl border border-[#993C1D]/20 bg-[#993C1D]/10 px-5 py-4 text-sm font-bold text-[#993C1D]">{{ $errors->first() }}</div>
            @endif

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <section class="rounded-3xl bg-white p-6 shadow-sm">
                        <h2 class="text-xl font-black text-[#3D3D3A]">Dados gerais</h2>
                        <div class="mt-5 grid gap-4 sm:grid-cols-3">
                            <div><p class="text-xs font-semibold uppercase text-[#3D3D3A]/60">Cliente</p><p class="mt-1 font-bold">{{ $order->user?->name ?? 'Cliente removido' }}</p></div>
                            <div><p class="text-xs font-semibold uppercase text-[#3D3D3A]/60">Pedido</p><p class="mt-1 font-bold">{{ $status['label'] }}</p></div>
                            <div><p class="text-xs font-semibold uppercase text-[#3D3D3A]/60">Pagamento</p><p class="mt-1 font-bold">{{ $paymentStatuses[$order->payment_status] ?? ucfirst((string) $order->payment_status) }}</p></div>
                            <div><p class="text-xs font-semibold uppercase text-[#3D3D3A]/60">Método</p><p class="mt-1 font-bold">{{ $order->paymentMethodLabel() }}</p></div>
                            <div><p class="text-xs font-semibold uppercase text-[#3D3D3A]/60">Criado em</p><p class="mt-1 font-bold">{{ $order->created_at?->format('d/m/Y H:i') }}</p></div>
                            <div><p class="text-xs font-semibold uppercase text-[#3D3D3A]/60">Rastreio</p><p class="mt-1 font-bold">{{ $order->tracking_number ?: 'Não informado' }}</p></div>
                        </div>
                    </section>

                    <section class="rounded-3xl bg-white p-6 shadow-sm">
                        <h2 class="text-xl font-black text-[#3D3D3A]">Endereço</h2>
                        <div class="mt-4 space-y-1 text-sm text-[#3D3D3A]/75">
                            <p class="font-bold text-[#3D3D3A]">{{ $order->address?->street }}, {{ $order->address?->number }}</p>
                            @if ($order->address?->complement)<p>{{ $order->address->complement }}</p>@endif
                            <p>{{ $order->address?->city }}/{{ $order->address?->state }}</p>
                            <p>CEP {{ $formattedZipCode }}</p>
                        </div>
                    </section>

                    <section class="rounded-3xl bg-white p-6 shadow-sm">
                        <h2 class="text-xl font-black text-[#3D3D3A]">Itens</h2>
                        <div class="mt-5 overflow-x-auto">
                            <table class="w-full min-w-[720px] text-left text-sm">
                                <thead class="text-xs uppercase text-[#3D3D3A]/60">
                                    <tr class="border-b border-[#3D3D3A]/10">
                                        <th class="pb-3">Produto</th><th class="pb-3">SKU</th><th class="pb-3 text-center">Qtd</th><th class="pb-3 text-right">Preço</th><th class="pb-3 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#3D3D3A]/10">
                                    @foreach ($order->items as $item)
                                        @php
                                            $name = $item->product_name ?: ($item->product?->name ?? 'Produto removido');
                                            $sku = $item->product_sku ?: ($item->product?->sku ?? '-');
                                        @endphp
                                        <tr>
                                            <td class="py-4 font-bold text-[#3D3D3A]">{{ $name }}</td>
                                            <td class="py-4 text-[#3D3D3A]/70">{{ $sku }}</td>
                                            <td class="py-4 text-center">{{ $item->quantity }}</td>
                                            <td class="py-4 text-right">R$ {{ number_format((float) $item->price, 2, ',', '.') }}</td>
                                            <td class="py-4 text-right font-black">R$ {{ number_format($item->getSubtotal(), 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <section class="rounded-3xl bg-white p-6 shadow-sm">
                        <h2 class="text-xl font-black text-[#3D3D3A]">Cupons</h2>
                        <div class="mt-4 space-y-2">
                            @forelse ($order->orderCoupons as $orderCoupon)
                                <div class="flex items-center justify-between rounded-2xl bg-[#F1EFE8] px-4 py-3 text-sm">
                                    <span class="font-bold">{{ $orderCoupon->coupon?->code ?? 'Cupom removido' }}</span>
                                    <span class="font-black text-[#1D9E75]">- R$ {{ number_format((float) $orderCoupon->discount_amount, 2, ',', '.') }}</span>
                                </div>
                            @empty
                                <p class="text-sm text-[#3D3D3A]/60">Nenhum cupom aplicado.</p>
                            @endforelse
                        </div>
                    </section>

                    <section class="rounded-3xl bg-white p-6 shadow-sm">
                        <h2 class="text-xl font-black text-[#3D3D3A]">Pagamentos</h2>
                        <div class="mt-5 overflow-x-auto">
                            <table class="w-full min-w-[980px] text-left text-sm">
                                <thead class="text-xs uppercase text-[#3D3D3A]/60">
                                    <tr class="border-b border-[#3D3D3A]/10">
                                        <th class="pb-3">Método</th><th class="pb-3">Status</th><th class="pb-3 text-right">Valor</th><th class="pb-3">Link</th><th class="pb-3">Order</th><th class="pb-3">Charge</th><th class="pb-3">Transaction</th><th class="pb-3">Datas</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#3D3D3A]/10">
                                    @forelse ($order->payments as $payment)
                                        @php
                                            $paymentCheckoutUrl = (string) $payment->pagarme_checkout_url;
                                            $canOpenPaymentCheckout = str_starts_with($paymentCheckoutUrl, 'https://');
                                        @endphp
                                        <tr>
                                            <td class="py-4">{{ $payment->paymentMethodLabel() }}</td>
                                            <td class="py-4 font-bold">{{ $payment->status }}</td>
                                            <td class="py-4 text-right">R$ {{ number_format((float) $payment->amount, 2, ',', '.') }}</td>
                                            <td class="py-4 text-xs">{{ $payment->pagarme_payment_link_id ?: '-' }} @if($canOpenPaymentCheckout)<a class="ml-2 text-[#185FA5] underline" href="{{ $paymentCheckoutUrl }}" target="_blank" rel="noopener noreferrer">abrir</a>@endif</td>
                                            <td class="py-4 text-xs">{{ $payment->pagarme_order_id ?: '-' }}</td>
                                            <td class="py-4 text-xs">{{ $payment->pagarme_charge_id ?: '-' }}</td>
                                            <td class="py-4 text-xs">{{ $payment->pagarme_transaction_id ?: '-' }}</td>
                                            <td class="py-4 text-xs">Pago: {{ $payment->paid_at?->format('d/m/Y H:i') ?? '-' }}<br>Cancelado: {{ $payment->cancelled_at?->format('d/m/Y H:i') ?? '-' }}<br>Reembolso: {{ $payment->refunded_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="8" class="py-4 text-sm text-[#3D3D3A]/60">Nenhum pagamento registrado.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <section class="rounded-3xl bg-white p-6 shadow-sm">
                        <h2 class="text-xl font-black text-[#3D3D3A]">Eventos de pagamento</h2>
                        <div class="mt-5 overflow-x-auto">
                            <table class="w-full min-w-[720px] text-left text-sm">
                                <thead class="text-xs uppercase text-[#3D3D3A]/60"><tr class="border-b border-[#3D3D3A]/10"><th class="pb-3">Evento</th><th class="pb-3">ID Pagar.me</th><th class="pb-3">Processado</th><th class="pb-3">Recebido</th></tr></thead>
                                <tbody class="divide-y divide-[#3D3D3A]/10">
                                    @forelse ($order->paymentEvents as $event)
                                        <tr>
                                            <td class="py-4 font-bold">{{ $event->event_type }}</td>
                                            <td class="py-4 text-xs">{{ $event->pagarme_event_id ?: '-' }}</td>
                                            <td class="py-4">{{ $event->processed_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                            <td class="py-4">{{ $event->created_at?->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="py-4 text-sm text-[#3D3D3A]/60">Nenhum evento registrado.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <section class="rounded-3xl bg-white p-6 shadow-sm">
                        <h2 class="text-xl font-black text-[#3D3D3A]">Movimentos de estoque</h2>
                        <div class="mt-5 overflow-x-auto">
                            <table class="w-full min-w-[720px] text-left text-sm">
                                <thead class="text-xs uppercase text-[#3D3D3A]/60"><tr class="border-b border-[#3D3D3A]/10"><th class="pb-3">Produto</th><th class="pb-3">Tipo</th><th class="pb-3 text-right">Qtd</th><th class="pb-3">Motivo</th><th class="pb-3">Data</th></tr></thead>
                                <tbody class="divide-y divide-[#3D3D3A]/10">
                                    @forelse ($order->stockMovements as $movement)
                                        <tr>
                                            <td class="py-4 font-bold">{{ $movement->product?->name ?? 'Produto removido' }}</td>
                                            <td class="py-4">{{ $movement->type }}</td>
                                            <td class="py-4 text-right">{{ $movement->quantity }}</td>
                                            <td class="py-4">{{ $movement->reason ?: '-' }}</td>
                                            <td class="py-4">{{ $movement->created_at?->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="py-4 text-sm text-[#3D3D3A]/60">Nenhum movimento registrado.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>

                <aside class="lg:col-span-1">
                    <section class="sticky top-6 space-y-6">
                        <div class="rounded-3xl bg-white p-6 shadow-sm">
                            <h2 class="text-xl font-black text-[#3D3D3A]">Resumo financeiro</h2>
                            <dl class="mt-5 space-y-3 text-sm">
                                <div class="flex justify-between"><dt>Subtotal</dt><dd class="font-bold">R$ {{ number_format((float) $order->subtotal_amount, 2, ',', '.') }}</dd></div>
                                <div class="flex justify-between text-[#1D9E75]"><dt>Desconto</dt><dd class="font-bold">- R$ {{ number_format((float) $order->discount_amount, 2, ',', '.') }}</dd></div>
                                <div class="flex justify-between"><dt>Frete</dt><dd class="font-bold">R$ {{ number_format((float) $order->shipping_amount, 2, ',', '.') }}</dd></div>
                                <div class="border-t pt-4 flex justify-between text-lg"><dt class="font-black">Total</dt><dd class="font-black text-[#185FA5]">R$ {{ number_format((float) $order->total_amount, 2, ',', '.') }}</dd></div>
                            </dl>
                            <div class="mt-5 text-sm text-[#3D3D3A]/75">
                                <p><strong>Frete:</strong> {{ $order->shipping_method ?: '-' }}</p>
                                <p><strong>Status frete:</strong> {{ $order->shipping_status ?: '-' }}</p>
                                <p><strong>Previsão:</strong> {{ $order->delivery_estimate ?: '-' }}</p>
                            </div>
                        </div>

                        <div class="rounded-3xl bg-white p-6 shadow-sm" x-data="{ status: '{{ old('status', $order->status) }}' }">
                            <h2 class="text-xl font-black text-[#3D3D3A]">Atualizar pedido</h2>
                            <form action="{{ route('admin.pedidos.update', $order) }}" method="POST" class="mt-5 space-y-5">
                                @csrf
                                @method('PUT')
                                <div>
                                    <label for="status" class="mb-2 block text-sm font-bold">Status</label>
                                    <select id="status" name="status" x-model="status" class="w-full rounded border border-[#3D3D3A]/20 px-4 py-3 text-sm">
                                        @foreach ($statuses as $value => $data)
                                            <option value="{{ $value }}" @selected(old('status', $order->status) === $value)>{{ $data['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div x-show="status === 'shipped'" x-cloak>
                                    <label for="tracking_number" class="mb-2 block text-sm font-bold">Código de rastreamento</label>
                                    <input id="tracking_number" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}" maxlength="100" class="w-full rounded border border-[#3D3D3A]/20 px-4 py-3 text-sm">
                                </div>
                                <button type="submit" class="inline-flex w-full justify-center rounded-full bg-[#1D9E75] px-6 py-3 text-sm font-bold text-white">Atualizar pedido</button>
                            </form>
                        </div>
                    </section>
                </aside>
            </div>
        </div>
    </div>
@endsection
