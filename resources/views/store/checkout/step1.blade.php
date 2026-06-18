@extends('layouts.app')

@section('content')
    @php
        $addresses = $addresses ?? collect();
        $defaultAddress = $defaultAddress ?? $addresses->firstWhere('is_default', true);
        $selectedAddressId = old('address_id', session('checkout.address_id', optional($defaultAddress)->id));
        $cart = $cart ?? auth()->user()?->carts()->with('items.product')->latest()->first();
        $cartItems = $cart?->items ?? collect();
        $cartTotal = $cart ? (float) $cart->total_price : (float) $cartItems->sum(fn ($item) => $item->getSubtotal());
    @endphp

    <div class="min-h-screen bg-[#F1EFE8] py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-10 rounded-3xl bg-white p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="flex flex-1 items-center">
                        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-[#185FA5] text-sm font-bold text-white">
                            1
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-bold text-[#185FA5]">Endereço</p>
                            <p class="text-xs text-[#3D3D3A]/60">Ativo</p>
                        </div>
                    </div>

                    <div class="mx-4 h-1 flex-1 rounded-full bg-[#3D3D3A]/15"></div>

                    <div class="flex flex-1 items-center">
                        <div class="flex h-11 w-11 items-center justify-center rounded-full border border-[#3D3D3A]/20 bg-white text-sm font-bold text-[#3D3D3A]/60">
                            2
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-bold text-[#3D3D3A]">Pagamento</p>
                            <p class="text-xs text-[#3D3D3A]/60">Próximo</p>
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

            <div class="grid gap-8 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <section class="rounded-3xl bg-white p-6 shadow-sm">
                        <div class="mb-6">
                            <h1 class="text-2xl font-bold text-[#3D3D3A]">Endereço de entrega</h1>
                            <p class="mt-1 text-sm text-[#3D3D3A]/70">Escolha onde seu pedido será entregue.</p>
                        </div>

                        <form id="checkout-address-form" action="{{ url('/checkout/step1') }}" method="POST">
                            @csrf

                            <div class="space-y-4">
                                @forelse ($addresses as $address)
                                    <label class="block cursor-pointer rounded-3xl border border-[#3D3D3A]/10 bg-white p-5 shadow-sm transition hover:border-[#185FA5]/50">
                                        <div class="flex items-start gap-4">
                                            <input
                                                type="radio"
                                                name="address_id"
                                                value="{{ $address->id }}"
                                                class="mt-1 border-[#3D3D3A]/30 text-[#185FA5] focus:ring-[#185FA5]"
                                                @checked((string) $selectedAddressId === (string) $address->id)
                                            >

                                            <div class="flex-1">
                                                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                                    <h2 class="text-base font-bold text-[#3D3D3A]">
                                                        {{ $address->street }}, {{ $address->number }}
                                                    </h2>

                                                    @if ($address->is_default)
                                                        <span class="inline-flex w-fit rounded-full bg-[#1D9E75]/10 px-3 py-1 text-xs font-bold text-[#1D9E75]">
                                                            Padrão
                                                        </span>
                                                    @endif
                                                </div>

                                                @if ($address->complement)
                                                    <p class="mt-1 text-sm text-[#3D3D3A]/70">{{ $address->complement }}</p>
                                                @endif

                                                <div class="mt-3 space-y-1 text-sm text-[#3D3D3A]/75">
                                                    <p>{{ $address->city }}/{{ $address->state }}</p>
                                                    <p>CEP {{ preg_replace('/(\d{5})(\d{3})/', '$1-$2', preg_replace('/\D/', '', (string) $address->zip_code)) }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                @empty
                                    <div class="rounded-3xl border border-dashed border-[#3D3D3A]/20 bg-[#F1EFE8]/60 p-6 text-sm text-[#3D3D3A]/70">
                                        Você ainda não possui endereços cadastrados.
                                    </div>
                                @endforelse
                            </div>

                            @error('address_id')
                                <p class="mt-4 text-sm font-semibold text-[#993C1D]">{{ $message }}</p>
                            @enderror

                            <p id="address-selection-error" class="mt-4 hidden text-sm font-semibold text-[#993C1D]">
                                Selecione um endereço para continuar.
                            </p>

                            <div class="mt-6 flex justify-end">
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center rounded-full bg-[#185FA5] px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#144f8a] focus:outline-none focus:ring-2 focus:ring-[#185FA5] focus:ring-offset-2"
                                >
                                    Continuar para pagamento
                                </button>
                            </div>
                        </form>
                    </section>

                    <section class="rounded-3xl bg-white p-6 shadow-sm">
                        <button
                            type="button"
                            id="toggle-new-address"
                            class="flex w-full items-center justify-between text-left text-lg font-bold text-[#185FA5]"
                        >
                            <span>+ Usar novo endereço</span>
                            <span id="new-address-indicator" class="text-2xl leading-none">+</span>
                        </button>

                        <div id="new-address-wrapper" class="{{ $errors->has('zip_code') || $errors->has('street') || $errors->has('number') || $errors->has('complement') || $errors->has('city') || $errors->has('state') ? '' : 'hidden' }} mt-6">
                            <form action="{{ route('addresses.store') }}" method="POST" class="space-y-5">
                                @csrf

                                <input type="hidden" name="country" value="Brasil">

                                <div class="grid gap-5 md:grid-cols-3">
                                    <div>
                                        <label for="zip_code" class="mb-2 block text-sm font-semibold text-[#3D3D3A]">CEP</label>
                                        <div class="flex gap-2">
                                            <input
                                                type="text"
                                                id="zip_code"
                                                name="zip_code"
                                                value="{{ old('zip_code') }}"
                                                maxlength="9"
                                                inputmode="numeric"
                                                autocomplete="postal-code"
                                                placeholder="00000-000"
                                                class="w-full rounded border border-[#3D3D3A]/20 px-4 py-3 text-sm text-[#3D3D3A] shadow-sm focus:border-[#185FA5] focus:outline-none focus:ring-2 focus:ring-[#185FA5]/20"
                                                required
                                            >

                                            <button
                                                type="button"
                                                id="search-zip-code"
                                                class="rounded bg-[#1D9E75] px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#168463] focus:outline-none focus:ring-2 focus:ring-[#1D9E75] focus:ring-offset-2"
                                            >
                                                Buscar
                                            </button>
                                        </div>
                                        @error('zip_code')
                                            <p class="mt-2 text-sm font-semibold text-[#993C1D]">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="md:col-span-2">
                                        <label for="street" class="mb-2 block text-sm font-semibold text-[#3D3D3A]">Rua</label>
                                        <input
                                            type="text"
                                            id="street"
                                            name="street"
                                            value="{{ old('street') }}"
                                            autocomplete="address-line1"
                                            class="w-full rounded border border-[#3D3D3A]/20 px-4 py-3 text-sm text-[#3D3D3A] shadow-sm focus:border-[#185FA5] focus:outline-none focus:ring-2 focus:ring-[#185FA5]/20"
                                            required
                                        >
                                        @error('street')
                                            <p class="mt-2 text-sm font-semibold text-[#993C1D]">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid gap-5 md:grid-cols-4">
                                    <div>
                                        <label for="number" class="mb-2 block text-sm font-semibold text-[#3D3D3A]">Número</label>
                                        <input
                                            type="text"
                                            id="number"
                                            name="number"
                                            value="{{ old('number') }}"
                                            autocomplete="address-line2"
                                            class="w-full rounded border border-[#3D3D3A]/20 px-4 py-3 text-sm text-[#3D3D3A] shadow-sm focus:border-[#185FA5] focus:outline-none focus:ring-2 focus:ring-[#185FA5]/20"
                                            required
                                        >
                                        @error('number')
                                            <p class="mt-2 text-sm font-semibold text-[#993C1D]">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="complement" class="mb-2 block text-sm font-semibold text-[#3D3D3A]">Complemento</label>
                                        <input
                                            type="text"
                                            id="complement"
                                            name="complement"
                                            value="{{ old('complement') }}"
                                            class="w-full rounded border border-[#3D3D3A]/20 px-4 py-3 text-sm text-[#3D3D3A] shadow-sm focus:border-[#185FA5] focus:outline-none focus:ring-2 focus:ring-[#185FA5]/20"
                                        >
                                        @error('complement')
                                            <p class="mt-2 text-sm font-semibold text-[#993C1D]">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="city" class="mb-2 block text-sm font-semibold text-[#3D3D3A]">Cidade</label>
                                        <input
                                            type="text"
                                            id="city"
                                            name="city"
                                            value="{{ old('city') }}"
                                            autocomplete="address-level2"
                                            class="w-full rounded border border-[#3D3D3A]/20 px-4 py-3 text-sm text-[#3D3D3A] shadow-sm focus:border-[#185FA5] focus:outline-none focus:ring-2 focus:ring-[#185FA5]/20"
                                            required
                                        >
                                        @error('city')
                                            <p class="mt-2 text-sm font-semibold text-[#993C1D]">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="state" class="mb-2 block text-sm font-semibold text-[#3D3D3A]">Estado</label>
                                        <input
                                            type="text"
                                            id="state"
                                            name="state"
                                            value="{{ old('state') }}"
                                            maxlength="2"
                                            autocomplete="address-level1"
                                            class="w-full rounded border border-[#3D3D3A]/20 px-4 py-3 text-sm uppercase text-[#3D3D3A] shadow-sm focus:border-[#185FA5] focus:outline-none focus:ring-2 focus:ring-[#185FA5]/20"
                                            required
                                        >
                                        @error('state')
                                            <p class="mt-2 text-sm font-semibold text-[#993C1D]">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <label class="inline-flex items-center gap-3 text-sm font-medium text-[#3D3D3A]">
                                    <input
                                        type="checkbox"
                                        name="is_default"
                                        value="1"
                                        @checked(old('is_default'))
                                        class="rounded border-[#3D3D3A]/30 text-[#1D9E75] focus:ring-[#1D9E75]"
                                    >
                                    Endereço padrão
                                </label>

                                <div class="flex justify-end">
                                    <button
                                        type="submit"
                                        class="inline-flex items-center justify-center rounded-full bg-[#1D9E75] px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#168463] focus:outline-none focus:ring-2 focus:ring-[#1D9E75] focus:ring-offset-2"
                                    >
                                        Salvar endereço
                                    </button>
                                </div>
                            </form>
                        </div>
                    </section>
                </div>

                <aside class="lg:col-span-1">
                    <div class="sticky top-6 rounded-3xl bg-white p-6 shadow-sm">
                        <h2 class="text-xl font-bold text-[#3D3D3A]">Resumo do carrinho</h2>

                        <div class="mt-5 space-y-4">
                            @forelse ($cartItems as $item)
                                <div class="flex items-start justify-between gap-4 border-b border-[#3D3D3A]/10 pb-4">
                                    <div>
                                        <p class="text-sm font-bold text-[#3D3D3A]">{{ $item->product?->name ?? 'Produto removido' }}</p>
                                        <p class="mt-1 text-xs text-[#3D3D3A]/60">Qtd: {{ $item->quantity }}</p>
                                    </div>

                                    <p class="whitespace-nowrap text-sm font-bold text-[#3D3D3A]">
                                        R$ {{ number_format($item->getSubtotal(), 2, ',', '.') }}
                                    </p>
                                </div>
                            @empty
                                <div class="rounded-3xl border border-dashed border-[#3D3D3A]/20 bg-[#F1EFE8]/60 p-4 text-sm text-[#3D3D3A]/70">
                                    Seu carrinho está vazio.
                                </div>
                            @endforelse
                        </div>

                        <div class="mt-6 flex items-center justify-between border-t border-[#3D3D3A]/10 pt-5">
                            <span class="text-base font-bold text-[#3D3D3A]">Total</span>
                            <span class="text-xl font-black text-[#185FA5]">R$ {{ number_format($cartTotal, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const checkoutAddressForm = document.getElementById('checkout-address-form');
            const addressSelectionError = document.getElementById('address-selection-error');
            const toggleNewAddressButton = document.getElementById('toggle-new-address');
            const newAddressWrapper = document.getElementById('new-address-wrapper');
            const newAddressIndicator = document.getElementById('new-address-indicator');
            const zipCodeInput = document.getElementById('zip_code');
            const searchZipCodeButton = document.getElementById('search-zip-code');
            const streetInput = document.getElementById('street');
            const cityInput = document.getElementById('city');
            const stateInput = document.getElementById('state');

            const onlyDigits = (value) => value.replace(/\D/g, '');

            const formatZipCode = (value) => {
                const digits = onlyDigits(value).slice(0, 8);

                if (digits.length <= 5) {
                    return digits;
                }

                return `${digits.slice(0, 5)}-${digits.slice(5)}`;
            };

            const searchZipCode = async () => {
                const zipCode = onlyDigits(zipCodeInput.value);

                if (zipCode.length !== 8) {
                    alert('Informe um CEP válido com 8 dígitos.');
                    zipCodeInput.focus();
                    return;
                }

                searchZipCodeButton.disabled = true;
                searchZipCodeButton.textContent = 'Buscando...';

                try {
                    const response = await fetch(`https://viacep.com.br/ws/${zipCode}/json/`);
                    const data = await response.json();

                    if (data.erro) {
                        alert('CEP não encontrado.');
                        return;
                    }

                    streetInput.value = data.logradouro || streetInput.value;
                    cityInput.value = data.localidade || cityInput.value;
                    stateInput.value = data.uf || stateInput.value;
                } catch (error) {
                    alert('Não foi possível buscar o CEP. Tente novamente.');
                } finally {
                    searchZipCodeButton.disabled = false;
                    searchZipCodeButton.textContent = 'Buscar';
                }
            };

            checkoutAddressForm.addEventListener('submit', (event) => {
                const selectedAddress = checkoutAddressForm.querySelector('input[name="address_id"]:checked');

                if (! selectedAddress) {
                    event.preventDefault();
                    addressSelectionError.classList.remove('hidden');
                }
            });

            checkoutAddressForm.querySelectorAll('input[name="address_id"]').forEach((radio) => {
                radio.addEventListener('change', () => {
                    addressSelectionError.classList.add('hidden');
                });
            });

            toggleNewAddressButton.addEventListener('click', () => {
                newAddressWrapper.classList.toggle('hidden');
                newAddressIndicator.textContent = newAddressWrapper.classList.contains('hidden') ? '+' : '-';

                if (! newAddressWrapper.classList.contains('hidden')) {
                    zipCodeInput.focus();
                }
            });

            zipCodeInput.addEventListener('input', (event) => {
                event.target.value = formatZipCode(event.target.value);
            });

            stateInput.addEventListener('input', (event) => {
                event.target.value = event.target.value.toUpperCase().slice(0, 2);
            });

            searchZipCodeButton.addEventListener('click', searchZipCode);

            zipCodeInput.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    searchZipCode();
                }
            });
        });
    </script>
@endsection
