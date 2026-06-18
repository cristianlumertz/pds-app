@extends('layouts.app')

@section('content')
    <section class="mx-auto w-full max-w-5xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-[#185FA5]">Checkout - Etapa 2</p>
            <h1 class="mt-1 text-2xl font-black text-slate-900">Escolha a forma de pagamento</h1>
            <p class="mt-1 text-sm text-slate-600">Selecione Cartão, Boleto ou PIX para continuar o pedido.</p>
        </div>

        @if(session('status'))
            <div class="mb-4 rounded-xl border border-[#1D9E75]/30 bg-[#1D9E75]/10 px-4 py-3 text-sm font-semibold text-[#1D9E75]">
                {{ session('status') }}
            </div>
        @endif

        <div
            x-data="{ tab: '{{ old('payment_method', 'card') }}' }"
            class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
        >
            <div class="grid grid-cols-1 gap-2 rounded-xl bg-slate-100 p-1 sm:grid-cols-3">
                <button type="button" @click="tab = 'card'" :class="tab === 'card' ? 'bg-white text-[#185FA5] shadow-sm' : 'text-slate-600'" class="rounded-lg px-3 py-2 text-sm font-bold transition">
                    Cartão
                </button>
                <button type="button" @click="tab = 'boleto'" :class="tab === 'boleto' ? 'bg-white text-[#185FA5] shadow-sm' : 'text-slate-600'" class="rounded-lg px-3 py-2 text-sm font-bold transition">
                    Boleto
                </button>
                <button type="button" @click="tab = 'pix'" :class="tab === 'pix' ? 'bg-white text-[#185FA5] shadow-sm' : 'text-slate-600'" class="rounded-lg px-3 py-2 text-sm font-bold transition">
                    PIX
                </button>
            </div>

            <form method="POST" action="{{ route('checkout.payment.store') }}" class="mt-5 space-y-4">
                @csrf

                <input type="hidden" name="payment_method" :value="tab">

                <div x-show="tab === 'card'" x-cloak class="space-y-4">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-sm font-semibold text-slate-700">Dados do cartão</p>

                        <div class="mt-3">
                            <x-input-label for="card_holder" value="Nome no cartão" />
                            <x-text-input id="card_holder" name="card_holder" type="text" class="mt-1 block w-full" value="{{ old('card_holder') }}" placeholder="Nome completo" />
                            @error('card_holder')
                                <p class="mt-1 text-sm font-semibold text-[#993C1D]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-3">
                            <x-input-label for="card_number" value="Número do cartão" />
                            <x-text-input id="card_number" name="card_number" type="text" class="mt-1 block w-full" value="{{ old('card_number') }}" placeholder="0000 0000 0000 0000" />
                            @error('card_number')
                                <p class="mt-1 text-sm font-semibold text-[#993C1D]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-3 grid gap-3 sm:grid-cols-2">
                            <div>
                                <x-input-label for="card_expiry" value="Validade (MM/AA)" />
                                <x-text-input id="card_expiry" name="card_expiry" type="text" class="mt-1 block w-full" value="{{ old('card_expiry') }}" placeholder="12/30" />
                                @error('card_expiry')
                                    <p class="mt-1 text-sm font-semibold text-[#993C1D]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <x-input-label for="card_cvv" value="CVV" />
                                <x-text-input id="card_cvv" name="card_cvv" type="password" class="mt-1 block w-full" value="{{ old('card_cvv') }}" placeholder="123" />
                                @error('card_cvv')
                                    <p class="mt-1 text-sm font-semibold text-[#993C1D]">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="tab === 'boleto'" x-cloak class="space-y-4">
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                        <p class="text-sm font-semibold text-amber-900">Pagamento via boleto</p>
                        <p class="mt-1 text-sm text-amber-800">
                            O boleto será gerado após a confirmação do pedido e enviado para seu e-mail.
                        </p>
                    </div>
                </div>

                <div x-show="tab === 'pix'" x-cloak class="space-y-4">
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                        <p class="text-sm font-semibold text-emerald-900">Pagamento via PIX</p>
                        <p class="mt-1 text-sm text-emerald-800">
                            Você receberá um QR Code e a chave PIX para pagamento imediato após finalizar.
                        </p>
                    </div>
                </div>

                @error('payment_method')
                    <p class="text-sm font-semibold text-[#993C1D]">{{ $message }}</p>
                @enderror

                <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:items-center">
                    <a href="{{ route('checkout.step1') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-700 transition hover:border-slate-500">
                        Voltar para endereços
                    </a>

                    <x-primary-button class="justify-center sm:ms-auto">
                        Continuar para revisão
                    </x-primary-button>
                </div>
            </form>
        </div>
    </section>
@endsection
