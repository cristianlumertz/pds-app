@extends('layouts.app')

@section('content')
    @php
        $cartItems = $cart?->items ?? collect();
        $cartTotal = $cart ? (float) $cart->total_price : (float) $cartItems->sum(fn ($item) => $item->getSubtotal());
    @endphp

    <div class="min-h-screen bg-[#F1EFE8] py-10">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="mb-10 rounded-3xl bg-white p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="flex flex-1 items-center">
                        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-[#1D9E75] text-sm font-bold text-white">
                            ✓
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-bold text-[#1D9E75]">Endereço</p>
                            <p class="text-xs text-[#3D3D3A]/60">Concluído</p>
                        </div>
                    </div>

                    <div class="mx-4 h-1 flex-1 rounded-full bg-[#185FA5]"></div>

                    <div class="flex flex-1 items-center">
                        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-[#185FA5] text-sm font-bold text-white">
                            2
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-bold text-[#185FA5]">Pagamento</p>
                            <p class="text-xs text-[#3D3D3A]/60">Ativo</p>
                        </div>
                    </div>

                    <div class="mx-4 h-1 flex-1 rounded-full bg-[#3D3D3A]/15"></div>

                    <div class="flex flex-1 items-center">
                        <div class="flex h-11 w-11 items-center justify-center rounded-full border border-[#3D3D3A]/20 bg-white text-sm font-bold text-[#3D3D3A]/60">
                            3
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-bold text-[#3D3D3A]">Revisão</p>
                            <p class="text-xs text-[#3D3D3A]/60">Finalização</p>
                        </div>
                    </div>
                </div>
            </div>

            @if (session('status'))
                <div class="mb-6 rounded-3xl border border-[#1D9E75]/20 bg-[#1D9E75]/10 px-5 py-4 text-sm font-semibold text-[#1D9E75]">
                    {{ session('status') }}
                </div>
            @endif

            <div
                x-data="{ tab: '{{ old('payment_method', session('checkout.payment_method', 'pix')) }}' }"
                class="rounded-3xl bg-white p-6 shadow-sm"
            >
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-[#3D3D3A]">Pagamento seguro pela Pagar.me</h1>
                    <p class="mt-1 text-sm text-[#3D3D3A]/70">
                        A escolha abaixo é apenas uma preferência. Na próxima etapa, você será redirecionado para o checkout hospedado da Pagar.me.
                    </p>
                </div>

                <form action="{{ url('/checkout/step2') }}" method="POST">
                    @csrf

                    <input type="hidden" name="payment_method" :value="tab">

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <button
                            type="button"
                            @click="tab = 'pix'"
                            :class="tab === 'pix' ? 'border-[#1D9E75] bg-[#1D9E75]/10 text-[#1D9E75]' : 'border-[#3D3D3A]/15 bg-white text-[#3D3D3A]'"
                            class="rounded border px-4 py-5 text-left transition"
                        >
                            <span class="block text-sm font-black uppercase">PIX</span>
                            <span class="mt-2 block text-xs font-semibold text-[#3D3D3A]/70">Disponível no checkout hospedado.</span>
                        </button>

                        <button
                            type="button"
                            @click="tab = 'boleto'"
                            :class="tab === 'boleto' ? 'border-[#BA7517] bg-[#BA7517]/10 text-[#BA7517]' : 'border-[#3D3D3A]/15 bg-white text-[#3D3D3A]'"
                            class="rounded border px-4 py-5 text-left transition"
                        >
                            <span class="block text-sm font-black uppercase">Boleto</span>
                            <span class="mt-2 block text-xs font-semibold text-[#3D3D3A]/70">Gerado pela Pagar.me após redirecionamento.</span>
                        </button>

                        <button
                            type="button"
                            @click="tab = 'cartao'"
                            :class="tab === 'cartao' ? 'border-[#185FA5] bg-[#185FA5]/10 text-[#185FA5]' : 'border-[#3D3D3A]/15 bg-white text-[#3D3D3A]'"
                            class="rounded border px-4 py-5 text-left transition"
                        >
                            <span class="block text-sm font-black uppercase">Cartão</span>
                            <span class="mt-2 block text-xs font-semibold text-[#3D3D3A]/70">Dados do cartão serão informados somente na Pagar.me.</span>
                        </button>
                    </div>

                    <div class="mt-6 rounded-3xl border border-[#185FA5]/15 bg-[#185FA5]/10 p-5">
                        <p class="text-sm font-bold text-[#185FA5]">Nenhum dado de cartão é coletado pela Construcerto.</p>
                        <p class="mt-2 text-sm text-[#3D3D3A]/70">
                            Ao confirmar o pedido, a Pagar.me exibirá Pix, boleto e cartão conforme os métodos habilitados no link de pagamento.
                        </p>
                        <p class="mt-3 text-sm font-black text-[#3D3D3A]">
                            Total do carrinho: R$ {{ number_format($cartTotal, 2, ',', '.') }}
                        </p>
                    </div>

                    @error('payment_method')
                        <p class="mt-4 text-sm font-semibold text-[#993C1D]">{{ $message }}</p>
                    @enderror

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                        <a
                            href="{{ route('checkout.step1') }}"
                            class="inline-flex items-center justify-center rounded-full border border-[#185FA5] px-6 py-3 text-sm font-bold text-[#185FA5] transition hover:bg-[#185FA5] hover:text-white"
                        >
                            Voltar para endereço
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-full bg-[#185FA5] px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#144f8a] focus:outline-none focus:ring-2 focus:ring-[#185FA5] focus:ring-offset-2 sm:ml-auto"
                        >
                            Continuar para revisão
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
