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
                x-data="{ tab: '{{ old('payment_method', session('checkout.payment_method', 'cartao')) }}' }"
                class="rounded-3xl bg-white p-6 shadow-sm"
            >
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-[#3D3D3A]">Forma de pagamento</h1>
                    <p class="mt-1 text-sm text-[#3D3D3A]/70">Escolha como deseja pagar seu pedido.</p>
                </div>

                <div class="grid grid-cols-1 gap-2 rounded-3xl bg-[#F1EFE8] p-2 sm:grid-cols-3">
                    <button
                        type="button"
                        @click="tab = 'cartao'"
                        :class="tab === 'cartao' ? 'bg-white text-[#185FA5] shadow-sm' : 'text-[#3D3D3A]/70'"
                        class="rounded-full px-4 py-3 text-sm font-bold transition"
                    >
                        Cartão de crédito
                    </button>
                    <button
                        type="button"
                        @click="tab = 'boleto'"
                        :class="tab === 'boleto' ? 'bg-white text-[#185FA5] shadow-sm' : 'text-[#3D3D3A]/70'"
                        class="rounded-full px-4 py-3 text-sm font-bold transition"
                    >
                        Boleto
                    </button>
                    <button
                        type="button"
                        @click="tab = 'pix'"
                        :class="tab === 'pix' ? 'bg-white text-[#185FA5] shadow-sm' : 'text-[#3D3D3A]/70'"
                        class="rounded-full px-4 py-3 text-sm font-bold transition"
                    >
                        PIX
                    </button>
                </div>

                <form action="{{ url('/checkout/step2') }}" method="POST" class="mt-6">
                    @csrf

                    <input type="hidden" name="payment_method" :value="tab">

                    <div x-show="tab === 'cartao'" x-cloak class="grid gap-6 lg:grid-cols-2">
                        <div class="rounded-3xl bg-gradient-to-br from-[#185FA5] to-[#0f3d6d] p-6 text-white shadow-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold uppercase tracking-widest text-white/80">ShopLaravel</span>
                                <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-bold">Crédito</span>
                            </div>

                            <div class="mt-12">
                                <p id="card-preview-number" class="text-xl font-bold tracking-widest">**** **** **** ****</p>
                            </div>

                            <div class="mt-10 flex items-end justify-between gap-4">
                                <div>
                                    <p class="text-xs uppercase text-white/60">Nome</p>
                                    <p id="card-preview-name" class="mt-1 text-sm font-bold uppercase">NOME IMPRESSO</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase text-white/60">Validade</p>
                                    <p id="card-preview-expiry" class="mt-1 text-sm font-bold">MM/AA</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label for="card_number" class="mb-2 block text-sm font-semibold text-[#3D3D3A]">Número do cartão</label>
                                <input
                                    type="text"
                                    id="card_number"
                                    name="card_number"
                                    value="{{ old('card_number') }}"
                                    maxlength="19"
                                    inputmode="numeric"
                                    placeholder="0000 0000 0000 0000"
                                    class="w-full rounded border border-[#3D3D3A]/20 px-4 py-3 text-sm text-[#3D3D3A] shadow-sm focus:border-[#185FA5] focus:outline-none focus:ring-2 focus:ring-[#185FA5]/20"
                                >
                            </div>

                            <div>
                                <label for="card_holder" class="mb-2 block text-sm font-semibold text-[#3D3D3A]">Nome impresso</label>
                                <input
                                    type="text"
                                    id="card_holder"
                                    name="card_holder"
                                    value="{{ old('card_holder') }}"
                                    placeholder="Nome como está no cartão"
                                    class="w-full rounded border border-[#3D3D3A]/20 px-4 py-3 text-sm uppercase text-[#3D3D3A] shadow-sm focus:border-[#185FA5] focus:outline-none focus:ring-2 focus:ring-[#185FA5]/20"
                                >
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="card_expiry" class="mb-2 block text-sm font-semibold text-[#3D3D3A]">Validade</label>
                                    <input
                                        type="text"
                                        id="card_expiry"
                                        name="card_expiry"
                                        value="{{ old('card_expiry') }}"
                                        maxlength="5"
                                        inputmode="numeric"
                                        placeholder="MM/AA"
                                        class="w-full rounded border border-[#3D3D3A]/20 px-4 py-3 text-sm text-[#3D3D3A] shadow-sm focus:border-[#185FA5] focus:outline-none focus:ring-2 focus:ring-[#185FA5]/20"
                                    >
                                </div>

                                <div>
                                    <label for="card_cvv" class="mb-2 block text-sm font-semibold text-[#3D3D3A]">CVV</label>
                                    <input
                                        type="password"
                                        id="card_cvv"
                                        name="card_cvv"
                                        value="{{ old('card_cvv') }}"
                                        maxlength="4"
                                        inputmode="numeric"
                                        placeholder="123"
                                        class="w-full rounded border border-[#3D3D3A]/20 px-4 py-3 text-sm text-[#3D3D3A] shadow-sm focus:border-[#185FA5] focus:outline-none focus:ring-2 focus:ring-[#185FA5]/20"
                                    >
                                </div>
                            </div>

                            <div>
                                <label for="installments" class="mb-2 block text-sm font-semibold text-[#3D3D3A]">Parcelas</label>
                                <select
                                    id="installments"
                                    name="installments"
                                    class="w-full rounded border border-[#3D3D3A]/20 px-4 py-3 text-sm text-[#3D3D3A] shadow-sm focus:border-[#185FA5] focus:outline-none focus:ring-2 focus:ring-[#185FA5]/20"
                                >
                                    @for ($installment = 1; $installment <= 12; $installment++)
                                        <option value="{{ $installment }}" @selected(old('installments') == $installment)>
                                            {{ $installment }}x de R$ {{ number_format($cartTotal / $installment, 2, ',', '.') }} sem juros
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <div x-show="tab === 'boleto'" x-cloak class="rounded-3xl border border-[#BA7517]/20 bg-[#BA7517]/10 p-6">
                        <div class="flex flex-col gap-6 sm:flex-row sm:items-center">
                            <div class="flex h-20 w-20 items-center justify-center rounded-3xl bg-white text-4xl shadow-sm">
                                ▯
                            </div>

                            <div class="flex-1">
                                <p class="text-sm font-semibold uppercase tracking-wide text-[#BA7517]">Pagamento por boleto</p>
                                <p class="mt-2 text-3xl font-black text-[#3D3D3A]">R$ {{ number_format($cartTotal, 2, ',', '.') }}</p>
                                <p class="mt-3 text-sm text-[#3D3D3A]/75">O boleto será gerado após confirmar o pedido.</p>
                                <p class="mt-1 text-sm font-semibold text-[#3D3D3A]">Vencimento em 3 dias úteis.</p>
                            </div>
                        </div>
                    </div>

                    <div x-show="tab === 'pix'" x-cloak class="rounded-3xl border border-[#1D9E75]/20 bg-[#1D9E75]/10 p-6">
                        <div class="flex flex-col gap-6 md:flex-row md:items-center">
                            <div class="flex h-40 w-40 shrink-0 items-center justify-center rounded border-2 border-dashed border-[#1D9E75] bg-white text-center text-sm font-bold text-[#1D9E75]">
                                QR Code PIX
                            </div>

                            <div>
                                <p class="text-sm font-semibold uppercase tracking-wide text-[#1D9E75]">Pagamento por PIX</p>
                                <p class="mt-2 text-3xl font-black text-[#3D3D3A]">R$ {{ number_format($cartTotal, 2, ',', '.') }}</p>
                                <p class="mt-4 text-sm text-[#3D3D3A]/70">Chave PIX</p>
                                <p class="mt-1 rounded-full bg-white px-4 py-2 text-sm font-bold text-[#3D3D3A] shadow-sm">shoplaravel@pix.com</p>
                                <p class="mt-4 text-sm font-semibold text-[#3D3D3A]">
                                    Válido por <span id="pix-countdown">30:00</span>
                                </p>
                            </div>
                        </div>
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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const cardNumberInput = document.getElementById('card_number');
            const cardHolderInput = document.getElementById('card_holder');
            const cardExpiryInput = document.getElementById('card_expiry');
            const cardNumberPreview = document.getElementById('card-preview-number');
            const cardHolderPreview = document.getElementById('card-preview-name');
            const cardExpiryPreview = document.getElementById('card-preview-expiry');
            const pixCountdown = document.getElementById('pix-countdown');

            const onlyDigits = (value) => value.replace(/\D/g, '');

            const formatCardNumber = (value) => {
                return onlyDigits(value).slice(0, 16).replace(/(\d{4})(?=\d)/g, '$1 ');
            };

            const formatExpiry = (value) => {
                const digits = onlyDigits(value).slice(0, 4);

                if (digits.length <= 2) {
                    return digits;
                }

                return `${digits.slice(0, 2)}/${digits.slice(2)}`;
            };

            const updateCardPreview = () => {
                cardNumberPreview.textContent = cardNumberInput.value || '**** **** **** ****';
                cardHolderPreview.textContent = cardHolderInput.value.trim().toUpperCase() || 'NOME IMPRESSO';
                cardExpiryPreview.textContent = cardExpiryInput.value || 'MM/AA';
            };

            cardNumberInput.addEventListener('input', (event) => {
                event.target.value = formatCardNumber(event.target.value);
                updateCardPreview();
            });

            cardHolderInput.addEventListener('input', updateCardPreview);

            cardExpiryInput.addEventListener('input', (event) => {
                event.target.value = formatExpiry(event.target.value);
                updateCardPreview();
            });

            let remainingSeconds = 30 * 60;

            window.setInterval(() => {
                remainingSeconds = Math.max(0, remainingSeconds - 1);

                const minutes = String(Math.floor(remainingSeconds / 60)).padStart(2, '0');
                const seconds = String(remainingSeconds % 60).padStart(2, '0');

                pixCountdown.textContent = `${minutes}:${seconds}`;
            }, 1000);

            updateCardPreview();
        });
    </script>
@endsection
