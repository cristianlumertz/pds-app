@extends('layouts.app')

@section('content')
    @php
        $paymentInstructions = [
            'boleto' => 'Seu boleto será gerado e enviado por e-mail em instantes.',
            'pix' => 'Acesse seu e-mail para obter o QR Code do PIX.',
            'cartao' => 'Seu pagamento está sendo processado.',
        ];

        $instruction = $paymentInstructions[$order->payment_method] ?? 'Seu pedido está sendo processado.';
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
                            <p class="text-xs font-semibold uppercase tracking-wide text-[#3D3D3A]/60">Total</p>
                            <p class="mt-1 text-base font-bold text-[#185FA5]">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</p>
                        </div>
                    </div>
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
