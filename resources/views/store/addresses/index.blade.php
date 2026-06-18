@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-[#F1EFE8] py-10">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-[#3D3D3A]">Meus Endereços</h1>
                    <p class="mt-2 text-sm text-[#3D3D3A]/70">Gerencie seus endereços de entrega.</p>
                </div>

                <button
                    type="button"
                    id="toggle-address-form"
                    class="inline-flex items-center justify-center rounded-full bg-[#185FA5] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#144f8a] focus:outline-none focus:ring-2 focus:ring-[#185FA5] focus:ring-offset-2"
                >
                    Adicionar endereço
                </button>
            </div>

            @if (session('status'))
                <div class="mb-6 rounded-3xl border border-[#1D9E75]/20 bg-[#1D9E75]/10 px-5 py-4 text-sm font-medium text-[#1D9E75]">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-3xl border border-[#993C1D]/20 bg-[#993C1D]/10 px-5 py-4 text-sm text-[#993C1D]">
                    <p class="font-semibold">Confira os dados informados:</p>
                    <ul class="mt-2 list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div
                id="address-form-wrapper"
                class="{{ $errors->any() ? '' : 'hidden' }} mb-8 rounded-3xl bg-white p-6 shadow-sm"
            >
                <form action="{{ route('addresses.store') }}" method="POST" class="space-y-6">
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
                                    class="rounded bg-[#1D9E75] px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#168463] focus:outline-none focus:ring-2 focus:ring-[#1D9E75] focus:ring-offset-2"
                                >
                                    Buscar
                                </button>
                            </div>
                            @error('zip_code')
                                <p class="mt-2 text-sm text-[#993C1D]">{{ $message }}</p>
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
                                <p class="mt-2 text-sm text-[#993C1D]">{{ $message }}</p>
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
                                <p class="mt-2 text-sm text-[#993C1D]">{{ $message }}</p>
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
                                <p class="mt-2 text-sm text-[#993C1D]">{{ $message }}</p>
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
                                <p class="mt-2 text-sm text-[#993C1D]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="state" class="mb-2 block text-sm font-semibold text-[#3D3D3A]">Estado (UF)</label>
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
                                <p class="mt-2 text-sm text-[#993C1D]">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
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

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-full bg-[#1D9E75] px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#168463] focus:outline-none focus:ring-2 focus:ring-[#1D9E75] focus:ring-offset-2"
                        >
                            Salvar endereço
                        </button>
                    </div>
                </form>
            </div>

            @if ($addresses->isEmpty())
                <div class="rounded-3xl bg-white p-8 text-center shadow-sm">
                    <h2 class="text-xl font-semibold text-[#3D3D3A]">Nenhum endereço cadastrado</h2>
                    <p class="mt-2 text-sm text-[#3D3D3A]/70">Adicione um endereço para agilizar suas compras.</p>
                </div>
            @else
                <div class="grid gap-5 md:grid-cols-2">
                    @foreach ($addresses as $address)
                        <article class="rounded-3xl bg-white p-6 shadow-sm">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h2 class="text-lg font-semibold text-[#3D3D3A]">
                                        {{ $address->street }}, {{ $address->number }}
                                    </h2>
                                    @if ($address->complement)
                                        <p class="mt-1 text-sm text-[#3D3D3A]/70">{{ $address->complement }}</p>
                                    @endif
                                </div>

                                @if ($address->is_default)
                                    <span class="rounded-full bg-[#1D9E75]/10 px-3 py-1 text-xs font-semibold text-[#1D9E75]">
                                        Padrão
                                    </span>
                                @endif
                            </div>

                            <div class="mt-4 space-y-1 text-sm text-[#3D3D3A]/75">
                                <p>{{ $address->city }}/{{ $address->state }}</p>
                                <p>CEP {{ preg_replace('/(\d{5})(\d{3})/', '$1-$2', preg_replace('/\D/', '', (string) $address->zip_code)) }}</p>
                            </div>

                            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                                @unless ($address->is_default)
                                    <form action="{{ route('addresses.update', $address) }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <input type="hidden" name="street" value="{{ $address->street }}">
                                        <input type="hidden" name="number" value="{{ $address->number }}">
                                        <input type="hidden" name="complement" value="{{ $address->complement }}">
                                        <input type="hidden" name="city" value="{{ $address->city }}">
                                        <input type="hidden" name="state" value="{{ $address->state }}">
                                        <input type="hidden" name="zip_code" value="{{ $address->zip_code }}">
                                        <input type="hidden" name="country" value="{{ $address->country }}">
                                        <input type="hidden" name="is_default" value="1">

                                        <button
                                            type="submit"
                                            class="inline-flex w-full items-center justify-center rounded-full border border-[#1D9E75] px-4 py-2 text-sm font-semibold text-[#1D9E75] transition hover:bg-[#1D9E75] hover:text-white sm:w-auto"
                                        >
                                            Definir como padrão
                                        </button>
                                    </form>
                                @endunless

                                <form action="{{ route('addresses.destroy', $address) }}" method="POST">
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="inline-flex w-full items-center justify-center rounded-full border border-[#993C1D] px-4 py-2 text-sm font-semibold text-[#993C1D] transition hover:bg-[#993C1D] hover:text-white sm:w-auto"
                                        onclick="return confirm('Deseja excluir este endereço?')"
                                    >
                                        Excluir
                                    </button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleButton = document.getElementById('toggle-address-form');
            const formWrapper = document.getElementById('address-form-wrapper');
            const zipCodeInput = document.getElementById('zip_code');
            const searchButton = document.getElementById('search-zip-code');
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

                searchButton.disabled = true;
                searchButton.textContent = 'Buscando...';

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
                    searchButton.disabled = false;
                    searchButton.textContent = 'Buscar';
                }
            };

            toggleButton.addEventListener('click', () => {
                formWrapper.classList.toggle('hidden');

                if (! formWrapper.classList.contains('hidden')) {
                    zipCodeInput.focus();
                }
            });

            zipCodeInput.addEventListener('input', (event) => {
                event.target.value = formatZipCode(event.target.value);
            });

            stateInput.addEventListener('input', (event) => {
                event.target.value = event.target.value.toUpperCase().slice(0, 2);
            });

            searchButton.addEventListener('click', searchZipCode);

            zipCodeInput.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    searchZipCode();
                }
            });
        });
    </script>
@endsection
