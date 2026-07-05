@extends('layouts.app')

@section('content')
    @php
        $paymentInstructions = [
            'boleto' => 'Seu pagamento será confirmado pela Pagar.me após a compensação do boleto.',
            'pix' => 'Seu pagamento será confirmado pela Pagar.me após a conclusão do PIX.',
            'cartao' => 'Seu pagamento será confirmado pela Pagar.me após a aprovação do cartão.',
        ];

        $instruction = $paymentInstructions[$order->payment_method] ?? 'Seu pedido está sendo processado.';
        $paymentStatusLabels = [
            'pending' => 'Pagamento pendente',
            'paid' => 'Pago',
            'failed' => 'Pagamento falhou',
            'cancelled' => 'Pagamento cancelado',
            'expired' => 'Pagamento expirado',
            'refunded' => 'Reembolsado',
        ];
    @endphp

    <div class="min-h-screen bg-[#F1EFE8] py-12">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <section class="rounded-3xl bg-white p-8 text-center shadow-sm sm:p-10">
                <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-[#1D9E75]/10">
                    <svg
                        class="h-14 w-14 text-[#1D9E75]"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.5"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75 9 17.25 19.5 6.75" />
                    </svg>
                </div>

                <h1 class="mt-6 text-3xl font-black text-[#3D3D3A]">Pedido realizado com sucesso!</h1>
                <p class="mt-3 text-base text-[#3D3D3A]/70">
                    Seu pedido #{{ $order->id }} foi recebido e está sendo processado.
                </p>

                <div class="mt-5">
                    <span class="inline-flex rounded-full bg-[#BA7517]/10 px-4 py-2 text-sm font-bold text-[#BA7517]">
                        Aguardando confirmação
                    </span>
                </div>

                <div class="mt-8 rounded-3xl bg-[#F1EFE8] p-6 text-left">
                    <h2 class="text-lg font-bold text-[#3D3D3A]">Informações do pedido</h2>

                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-[#3D3D3A]/60">Número</p>
                            <p class="mt-1 text-base font-bold text-[#3D3D3A]">#{{ $order->id }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-[#3D3D3A]/60">Data</p>
                            <p class="mt-1 text-base font-bold text-[#3D3D3A]">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-[#3D3D3A]/60">Forma de pagamento</p>
                            <p class="mt-1 text-base font-bold text-[#3D3D3A]">{{ ucfirst($order->payment_method) }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-[#3D3D3A]/60">Status do pagamento</p>
                            <p class="mt-1 text-base font-bold text-[#3D3D3A]">{{ $paymentStatusLabels[$order->payment_status] ?? ucfirst((string) $order->payment_status) }}</p>
                        </div>
                    </div>

                    <dl class="mt-5 space-y-2 border-t border-[#3D3D3A]/10 pt-5 text-sm">
                        <div class="flex justify-between"><dt>Subtotal</dt><dd class="font-bold">R$ {{ number_format((float) $order->subtotal_amount, 2, ',', '.') }}</dd></div>
                        <div class="flex justify-between text-[#1D9E75]"><dt>Desconto</dt><dd class="font-bold">- R$ {{ number_format((float) $order->discount_amount, 2, ',', '.') }}</dd></div>
                        <div class="flex justify-between"><dt>Frete</dt><dd class="font-bold">R$ {{ number_format((float) $order->shipping_amount, 2, ',', '.') }}</dd></div>
                        <div class="flex justify-between border-t border-[#3D3D3A]/10 pt-3 text-base"><dt class="font-black">Total</dt><dd class="font-black text-[#185FA5]">R$ {{ number_format((float) $order->total_amount, 2, ',', '.') }}</dd></div>
                    </dl>
                </div>

                <div class="mt-6 rounded-3xl border border-[#185FA5]/15 bg-[#185FA5]/10 px-5 py-4 text-sm font-semibold text-[#185FA5]">
                    {{ $instruction }}
                </div>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                    <a
                        href="{{ route('orders.index') }}"
                        class="inline-flex items-center justify-center rounded-full bg-[#185FA5] px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#144f8a] focus:outline-none focus:ring-2 focus:ring-[#185FA5] focus:ring-offset-2"
                    >
                        Ver meus pedidos
                    </a>

                    <a
                        href="{{ route('store.products') }}"
                        class="inline-flex items-center justify-center rounded-full border border-[#185FA5] px-6 py-3 text-sm font-bold text-[#185FA5] transition hover:bg-[#185FA5] hover:text-white"
                    >
                        Continuar comprando
                    </a>
                </div>
            </section>
        </div>
    </div>
@endsection
