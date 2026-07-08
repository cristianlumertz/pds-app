@extends('layouts.admin')

@section('content')
    @php
        $checkoutUrl = (string) $payment->pagarme_checkout_url;
        $canOpenCheckout = str_starts_with($checkoutUrl, 'https://');
    @endphp

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <a href="{{ route('admin.payments.index') }}" class="text-sm font-black text-[#D42B2B] hover:underline">Voltar para pagamentos</a>
            <h1 class="mt-1 text-3xl font-black text-[#1A1A1A]">Pagamento #{{ $payment->id }}</h1>
            <p class="mt-1 text-sm text-[#767676]">Pedido #{{ $payment->order_id }} · {{ $payment->order?->user?->name ?? 'Cliente removido' }}</p>
        </div>
        <x-admin.status-badge :status="$payment->status" />
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <section class="rounded-lg border border-[#E0E0E0] bg-white p-5 shadow-sm xl:col-span-2">
            <h2 class="text-lg font-black">Dados do pagamento</h2>
            <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                <div><dt class="text-xs font-black uppercase text-[#767676]">Método</dt><dd class="mt-1 font-bold">{{ $payment->paymentMethodLabel() }}</dd></div>
                <div><dt class="text-xs font-black uppercase text-[#767676]">Valor</dt><dd class="mt-1 font-black text-[#1A3A6B]">R$ {{ number_format((float) $payment->amount, 2, ',', '.') }}</dd></div>
                <div><dt class="text-xs font-black uppercase text-[#767676]">Payment link ID</dt><dd class="mt-1 break-all font-bold">{{ $payment->pagarme_payment_link_id ?: '-' }}</dd></div>
                <div><dt class="text-xs font-black uppercase text-[#767676]">Checkout</dt><dd class="mt-1">@if($canOpenCheckout)<a href="{{ $checkoutUrl }}" target="_blank" rel="noopener noreferrer" class="font-black text-[#D42B2B] hover:underline">Abrir checkout Pagar.me</a>@else - @endif</dd></div>
                <div><dt class="text-xs font-black uppercase text-[#767676]">Pagar.me order</dt><dd class="mt-1 break-all">{{ $payment->pagarme_order_id ?: '-' }}</dd></div>
                <div><dt class="text-xs font-black uppercase text-[#767676]">Charge</dt><dd class="mt-1 break-all">{{ $payment->pagarme_charge_id ?: '-' }}</dd></div>
                <div><dt class="text-xs font-black uppercase text-[#767676]">Transaction</dt><dd class="mt-1 break-all">{{ $payment->pagarme_transaction_id ?: '-' }}</dd></div>
                <div><dt class="text-xs font-black uppercase text-[#767676]">Pago em</dt><dd class="mt-1">{{ $payment->paid_at?->format('d/m/Y H:i') ?? '-' }}</dd></div>
                <div><dt class="text-xs font-black uppercase text-[#767676]">Cancelado em</dt><dd class="mt-1">{{ $payment->cancelled_at?->format('d/m/Y H:i') ?? '-' }}</dd></div>
                <div><dt class="text-xs font-black uppercase text-[#767676]">Reembolsado em</dt><dd class="mt-1">{{ $payment->refunded_at?->format('d/m/Y H:i') ?? '-' }}</dd></div>
                <div><dt class="text-xs font-black uppercase text-[#767676]">Boleto</dt><dd class="mt-1 break-all">{{ $payment->boleto_url ?: '-' }}</dd></div>
                <div><dt class="text-xs font-black uppercase text-[#767676]">Código boleto</dt><dd class="mt-1 break-all">{{ $payment->boleto_barcode ?: '-' }}</dd></div>
            </dl>

            @if ($payment->pix_qr_code)
                <div class="mt-5 rounded border border-[#E0E0E0] bg-[#F8FAFC] p-4">
                    <p class="text-xs font-black uppercase text-[#767676]">PIX QR Code</p>
                    <p class="mt-2 break-all text-sm">{{ $payment->pix_qr_code }}</p>
                </div>
            @endif
        </section>

        <aside class="rounded-lg border border-[#E0E0E0] bg-white p-5 shadow-sm">
            <h2 class="text-lg font-black">Pedido vinculado</h2>
            <dl class="mt-5 space-y-3 text-sm">
                <div class="flex justify-between gap-3"><dt class="text-[#767676]">Pedido</dt><dd class="font-black">#{{ $payment->order_id }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-[#767676]">Cliente</dt><dd class="font-bold text-right">{{ $payment->order?->user?->name ?? '-' }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-[#767676]">Total</dt><dd class="font-black text-[#1A3A6B]">R$ {{ number_format((float) $payment->order?->total_amount, 2, ',', '.') }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-[#767676]">Pedido</dt><dd><x-admin.status-badge :status="$payment->order?->status" /></dd></div>
                <div class="flex justify-between gap-3"><dt class="text-[#767676]">Pagamento</dt><dd><x-admin.status-badge :status="$payment->order?->payment_status" /></dd></div>
            </dl>
            @if ($payment->order)
                <a href="{{ route('admin.pedidos.show', $payment->order) }}" class="mt-5 inline-flex w-full justify-center rounded bg-[#1A3A6B] px-4 py-2 text-sm font-black text-white">Abrir pedido</a>
            @endif
        </aside>
    </div>

    <section class="mt-6 rounded-lg border border-[#E0E0E0] bg-white p-5 shadow-sm">
        <h2 class="text-lg font-black">Eventos recebidos</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[720px] text-left text-sm">
                <thead class="bg-[#F3F5F8] text-xs uppercase text-[#767676]">
                    <tr><th class="px-4 py-3">Evento</th><th class="px-4 py-3">ID Pagar.me</th><th class="px-4 py-3">Processado</th><th class="px-4 py-3">Recebido</th></tr>
                </thead>
                <tbody class="divide-y divide-[#E0E0E0]">
                    @forelse ($payment->events as $event)
                        <tr>
                            <td class="px-4 py-3 font-black">{{ $event->event_type }}</td>
                            <td class="px-4 py-3">{{ $event->pagarme_event_id ?: '-' }}</td>
                            <td class="px-4 py-3">{{ $event->processed_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $event->created_at?->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8"><x-admin.empty-state title="Nenhum evento recebido para este pagamento." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
