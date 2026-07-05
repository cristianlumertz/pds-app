@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-[#F1EFE8] py-12">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <section class="rounded-3xl bg-white p-8 text-center shadow-sm sm:p-10">
                <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-[#BA7517]/10">
                    <svg
                        class="h-14 w-14 text-[#BA7517]"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.5"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 17h.01" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" />
                    </svg>
                </div>

                <h1 class="mt-6 text-3xl font-black text-[#3D3D3A]">Não foi possível iniciar o pagamento</h1>
                <p class="mt-3 text-base text-[#3D3D3A]/70">
                    Seu pedido #{{ $order->id }} foi criado e está aguardando pagamento.
                </p>

                @if ($errors->any())
                    <div class="mt-6 rounded-3xl border border-[#993C1D]/20 bg-[#993C1D]/10 px-5 py-4 text-sm font-bold text-[#993C1D]">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="mt-8 rounded-3xl bg-[#F1EFE8] p-6 text-left">
                    <h2 class="text-lg font-bold text-[#3D3D3A]">Resumo do pedido</h2>

                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-[#3D3D3A]/60">Número</p>
                            <p class="mt-1 text-base font-bold text-[#3D3D3A]">#{{ $order->id }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-[#3D3D3A]/60">Status do pagamento</p>
                            <p class="mt-1 text-base font-bold text-[#BA7517]">Aguardando pagamento</p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-[#3D3D3A]/60">Preferência escolhida</p>
                            <p class="mt-1 text-base font-bold text-[#3D3D3A]">{{ ucfirst($order->payment_method) }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-[#3D3D3A]/60">Total</p>
                            <p class="mt-1 text-base font-bold text-[#185FA5]">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                    <form action="{{ route('checkout.payment.retry', $order) }}" method="POST">
                        @csrf

                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center rounded-full bg-[#1D9E75] px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#168463] focus:outline-none focus:ring-2 focus:ring-[#1D9E75] focus:ring-offset-2 sm:w-auto"
                        >
                            Tentar pagar novamente
                        </button>
                    </form>

                    <a
                        href="{{ route('orders.index') }}"
                        class="inline-flex items-center justify-center rounded-full border border-[#185FA5] px-6 py-3 text-sm font-bold text-[#185FA5] transition hover:bg-[#185FA5] hover:text-white"
                    >
                        Ver meus pedidos
                    </a>
                </div>
            </section>
        </div>
    </div>
@endsection
