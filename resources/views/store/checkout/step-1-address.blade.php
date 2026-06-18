@extends('layouts.app')

@section('content')
    <section class="mx-auto w-full max-w-5xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-[#185FA5]">Checkout - Etapa 1</p>
            <h1 class="mt-1 text-2xl font-black text-slate-900">Selecionar endereço de entrega</h1>
            <p class="mt-1 text-sm text-slate-600">Escolha um endereço existente ou adicione um novo endereço para continuar.</p>
        </div>

        @if(session('status'))
            <div class="mb-4 rounded-xl border border-[#1D9E75]/30 bg-[#1D9E75]/10 px-4 py-3 text-sm font-semibold text-[#1D9E75]">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-black text-slate-900">Endereços cadastrados</h2>
                <p class="mt-1 text-sm text-slate-600">Marque um endereço para usar nesta compra.</p>

                <form method="POST" action="{{ route('checkout.address.select') }}" class="mt-4 space-y-3">
                    @csrf

                    @forelse($addresses as $address)
                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-3 transition hover:border-slate-300">
                            <input
                                type="radio"
                                name="address_id"
                                value="{{ $address->id }}"
                                class="mt-1 border-slate-300 text-[#185FA5] focus:ring-[#185FA5]"
                                @checked(old('address_id', optional($defaultAddress)->id) == $address->id)
                            >
                            <span class="text-sm text-slate-700">
                                <strong class="block text-slate-900">{{ $address->street }}, {{ $address->number }}</strong>
                                {{ $address->city }} - {{ $address->state }}<br>
                                CEP: {{ $address->zip_code }}<br>
                                {{ $address->country }}
                                @if($address->is_default)
                                    <span class="mt-1 inline-flex rounded-full bg-[#185FA5]/10 px-2 py-0.5 text-xs font-bold text-[#185FA5]">Padrão</span>
                                @endif
                            </span>
                        </label>
                    @empty
                        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-600">
                            Você ainda não possui endereços cadastrados.
                        </div>
                    @endforelse

                    @error('address_id')
                        <p class="text-sm font-semibold text-[#993C1D]">{{ $message }}</p>
                    @enderror

                    <x-primary-button class="mt-2">
                        Usar endereço selecionado
                    </x-primary-button>
                </form>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-black text-slate-900">Adicionar novo endereço</h2>
                <p class="mt-1 text-sm text-slate-600">Preencha os dados abaixo para cadastrar um novo endereço.</p>

                <form method="POST" action="{{ route('addresses.store') }}" class="mt-4 space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="zip_code" value="CEP" />
                        <x-text-input id="zip_code" name="zip_code" type="text" class="mt-1 block w-full" value="{{ old('zip_code') }}" placeholder="00000000" />
                        @error('zip_code')
                            <p class="mt-1 text-sm font-semibold text-[#993C1D]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <x-input-label for="street" value="Rua" />
                        <x-text-input id="street" name="street" type="text" class="mt-1 block w-full" value="{{ old('street') }}" placeholder="Rua das Construções" />
                        @error('street')
                            <p class="mt-1 text-sm font-semibold text-[#993C1D]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="number" value="Número" />
                            <x-text-input id="number" name="number" type="text" class="mt-1 block w-full" value="{{ old('number') }}" placeholder="123" />
                            @error('number')
                                <p class="mt-1 text-sm font-semibold text-[#993C1D]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-input-label for="city" value="Cidade" />
                            <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" value="{{ old('city') }}" placeholder="Torres" />
                            @error('city')
                                <p class="mt-1 text-sm font-semibold text-[#993C1D]">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="state" value="Estado (UF)" />
                            <x-text-input id="state" name="state" type="text" class="mt-1 block w-full" value="{{ old('state') }}" placeholder="RS" />
                            @error('state')
                                <p class="mt-1 text-sm font-semibold text-[#993C1D]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-input-label for="country" value="País" />
                            <x-text-input id="country" name="country" type="text" class="mt-1 block w-full" value="{{ old('country', 'Brasil') }}" />
                            @error('country')
                                <p class="mt-1 text-sm font-semibold text-[#993C1D]">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <x-input-label for="complement" value="Complemento (opcional)" />
                        <x-text-input id="complement" name="complement" type="text" class="mt-1 block w-full" value="{{ old('complement') }}" placeholder="Apto, bloco, referência..." />
                        @error('complement')
                            <p class="mt-1 text-sm font-semibold text-[#993C1D]">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="is_default" value="1" class="rounded border-slate-300 text-[#185FA5] focus:ring-[#185FA5]" @checked(old('is_default'))>
                        Definir como endereço padrão
                    </label>

                    <x-primary-button>
                        Salvar novo endereço
                    </x-primary-button>
                </form>
            </div>
        </div>
    </section>
@endsection
