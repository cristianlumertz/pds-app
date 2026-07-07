@extends('layouts.app')

@section('content')
    @php
        $statusLabels = [
            'pending' => 'Aguardando',
            'processing' => 'Em processamento',
            'shipped' => 'Enviado',
            'delivered' => 'Entregue',
            'cancelled' => 'Cancelado',
        ];
        $paymentStatusLabels = [
            'pending' => 'Pagamento pendente',
            'paid' => 'Pago',
            'failed' => 'Pagamento falhou',
            'cancelled' => 'Pagamento cancelado',
            'expired' => 'Pagamento expirado',
            'refunded' => 'Reembolsado',
        ];
        $canRetryPayment = in_array((string) $order->payment_status, ['pending', 'failed', 'expired'], true)
            && (string) $order->status !== 'cancelled';
        $pagarmeCheckoutUrl = (string) $order->pagarme_checkout_url;
        $canOpenPagarmeCheckout = str_starts_with($pagarmeCheckoutUrl, 'https://');
        $zipCode = preg_replace('/\D/', '', (string) $order->address?->zip_code);
        $formattedZipCode = strlen($zipCode) === 8 ? preg_replace('/(\d{5})(\d{3})/', '$1-$2', $zipCode) : $order->address?->zip_code;
    @endphp

    <div class="min-h-screen bg-[#F1EFE8] py-10">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <a href="{{ route('orders.index') }}" class="text-sm font-bold text-[#185FA5] hover:underline">Voltar para meus pedidos</a>
            <div class="mt-4 rounded-3xl bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h1 class="text-3xl font-black text-[#3D3D3A]">Pedido #{{ $order->id }}</h1>
                        <p class="mt-2 text-sm text-[#3D3D3A]/70">Realizado em {{ $order->created_at?->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="text-left sm:text-right">
                        <p class="text-sm font-bold text-[#3D3D3A]">{{ $statusLabels[$order->status] ?? ucfirst((string) $order->status) }}</p>
                        <p class="mt-1 text-sm font-bold text-[#185FA5]">{{ $paymentStatusLabels[$order->payment_status] ?? ucfirst((string) $order->payment_status) }}</p>
                    </div>
                </div>

                @if ($canRetryPayment)
                    <div class="mt-5 flex flex-wrap gap-2 rounded-2xl bg-[#FFF3CD] p-4 text-sm font-semibold text-[#856404]">
                        <span>Pagamento ainda não confirmado.</span>
                        @if ($canOpenPagarmeCheckout)
                            <a href="{{ $pagarmeCheckoutUrl }}" class="underline" target="_blank" rel="noopener noreferrer">Abrir checkout</a>
                        @endif
                        <form action="{{ route('checkout.payment.retry', $order) }}" method="POST">
                            @csrf
                            <button type="submit" class="font-bold underline">Tentar novamente</button>
                        </form>
                    </div>
                @endif
            </div>

            <div class="mt-6 grid gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <section class="rounded-3xl bg-white p-6 shadow-sm">
                        <h2 class="text-xl font-black text-[#3D3D3A]">Itens</h2>
                        <div class="mt-5 divide-y divide-[#3D3D3A]/10">
                            @foreach ($order->items as $item)
                                @php
                                    $name = $item->product_name ?: ($item->product?->name ?? 'Produto removido');
                                    $sku = $item->product_sku ?: ($item->product?->sku ?? null);
                                @endphp
                                <div class="flex gap-4 py-4">
                                    @if ($item->product?->primaryImageUrl())
                                        <img src="{{ $item->product->primaryImageUrl() }}" alt="{{ $name }}" class="h-16 w-16 rounded object-cover">
                                    @else
                                        <div class="h-16 w-16 rounded bg-[#F1EFE8]"></div>
                                    @endif
                                    <div class="min-w-0 flex-1">
                                        <p class="font-bold text-[#3D3D3A]">{{ $name }}</p>
                                        @if ($sku)<p class="mt-1 text-xs text-[#3D3D3A]/60">SKU: {{ $sku }}</p>@endif
                                        <p class="mt-2 text-sm text-[#3D3D3A]/70">{{ $item->quantity }} x R$ {{ number_format((float) $item->price, 2, ',', '.') }}</p>
                                    </div>
                                    <p class="font-black text-[#3D3D3A]">R$ {{ number_format($item->getSubtotal(), 2, ',', '.') }}</p>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    <section class="rounded-3xl bg-white p-6 shadow-sm">
                        <h2 class="text-xl font-black text-[#3D3D3A]">Entrega</h2>
                        <div class="mt-4 space-y-1 text-sm text-[#3D3D3A]/75">
                            <p class="font-bold text-[#3D3D3A]">{{ $order->address?->street }}, {{ $order->address?->number }}</p>
                            @if ($order->address?->complement)<p>{{ $order->address->complement }}</p>@endif
                            <p>{{ $order->address?->city }}/{{ $order->address?->state }}</p>
                            <p>CEP {{ $formattedZipCode }}</p>
                            <p class="pt-2"><strong>Previsão:</strong> {{ $order->delivery_estimate ?: 'A confirmar' }}</p>
                            @if ($order->tracking_number)<p><strong>Rastreio:</strong> {{ $order->tracking_number }}</p>@endif
                        </div>
                    </section>
                </div>

                <aside class="lg:col-span-1">
                    <section class="rounded-3xl bg-white p-6 shadow-sm">
                        <h2 class="text-xl font-black text-[#3D3D3A]">Resumo</h2>
                        <dl class="mt-5 space-y-3 text-sm">
                            <div class="flex justify-between"><dt>Subtotal</dt><dd class="font-bold">R$ {{ number_format((float) $order->subtotal_amount, 2, ',', '.') }}</dd></div>
                            <div class="flex justify-between text-[#1D9E75]"><dt>Desconto</dt><dd class="font-bold">- R$ {{ number_format((float) $order->discount_amount, 2, ',', '.') }}</dd></div>
                            <div class="flex justify-between"><dt>Frete</dt><dd class="font-bold">R$ {{ number_format((float) $order->shipping_amount, 2, ',', '.') }}</dd></div>
                            <div class="border-t pt-4 flex justify-between text-lg"><dt class="font-black">Total</dt><dd class="font-black text-[#185FA5]">R$ {{ number_format((float) $order->total_amount, 2, ',', '.') }}</dd></div>
                        </dl>
                        <div class="mt-5 space-y-1 text-sm text-[#3D3D3A]/75">
                            <p><strong>Pagamento:</strong> {{ $order->paymentMethodLabel() }}</p>
                            <p><strong>Status:</strong> {{ $paymentStatusLabels[$order->payment_status] ?? ucfirst((string) $order->payment_status) }}</p>
                        </div>
                    </section>
                </aside>
            </div>
        </div>
    </div>
@endsection
