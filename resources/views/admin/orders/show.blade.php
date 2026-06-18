@extends('layouts.app')

@section('content')
    @php
        $statuses = [
            'pending' => ['label' => 'Pendente', 'color' => '#BA7517'],
            'processing' => ['label' => 'Processando', 'color' => '#185FA5'],
            'shipped' => ['label' => 'Enviado', 'color' => '#534AB7'],
            'delivered' => ['label' => 'Entregue', 'color' => '#3B6D11'],
            'cancelled' => ['label' => 'Cancelado', 'color' => '#993C1D'],
        ];

        $status = $statuses[$order->status] ?? ['label' => ucfirst((string) $order->status), 'color' => '#3D3D3A'];
        $cpf = preg_replace('/\D/', '', (string) $order->user?->cpf);
        $formattedCpf = strlen($cpf) === 11 ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf) : $order->user?->cpf;
        $zipCode = preg_replace('/\D/', '', (string) $order->address?->zip_code);
        $formattedZipCode = strlen($zipCode) === 8 ? preg_replace('/(\d{5})(\d{3})/', '$1-$2', $zipCode) : $order->address?->zip_code;
    @endphp

    <div class="min-h-screen bg-[#F1EFE8] py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <a href="{{ route('admin.pedidos.index') }}" class="text-sm font-bold text-[#185FA5] hover:underline">
                        Voltar para pedidos
                    </a>
                    <h1 class="mt-2 text-3xl font-black text-[#3D3D3A]">Pedido #{{ $order->id }}</h1>
                </div>

                <span
                    class="inline-flex w-fit rounded-full px-4 py-2 text-sm font-bold text-white"
                    style="background-color: {{ $status['color'] }}"
                >
                    {{ $status['label'] }}
                </span>
            </div>

            @if (session('status'))
                <div class="mb-6 rounded-3xl border border-[#1D9E75]/20 bg-[#1D9E75]/10 px-5 py-4 text-sm font-bold text-[#1D9E75]">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-3xl border border-[#993C1D]/20 bg-[#993C1D]/10 px-5 py-4 text-sm font-bold text-[#993C1D]">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <section class="rounded-3xl bg-white p-6 shadow-sm">
                        <h2 class="text-xl font-black text-[#3D3D3A]">Informações do pedido</h2>

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
                                <p class="text-xs font-semibold uppercase tracking-wide text-[#3D3D3A]/60">Status</p>
                                <p class="mt-1 text-base font-bold text-[#3D3D3A]">{{ $status['label'] }}</p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-[#3D3D3A]/60">Pagamento</p>
                                <p class="mt-1 text-base font-bold text-[#3D3D3A]">{{ ucfirst($order->payment_method) }}</p>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-3xl bg-white p-6 shadow-sm">
                        <h2 class="text-xl font-black text-[#3D3D3A]">Cliente</h2>

                        <div class="mt-5 grid gap-4 sm:grid-cols-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-[#3D3D3A]/60">Nome</p>
                                <p class="mt-1 text-base font-bold text-[#3D3D3A]">{{ $order->user?->name }}</p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-[#3D3D3A]/60">E-mail</p>
                                <p class="mt-1 text-base font-bold text-[#3D3D3A]">{{ $order->user?->email }}</p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-[#3D3D3A]/60">CPF</p>
                                <p class="mt-1 text-base font-bold text-[#3D3D3A]">{{ $formattedCpf }}</p>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-3xl bg-white p-6 shadow-sm">
                        <h2 class="text-xl font-black text-[#3D3D3A]">Endereço de entrega</h2>

                        <div class="mt-5 space-y-1 text-sm text-[#3D3D3A]/75">
                            <p class="font-bold text-[#3D3D3A]">{{ $order->address?->street }}, {{ $order->address?->number }}</p>
                            @if ($order->address?->complement)
                                <p>{{ $order->address->complement }}</p>
                            @endif
                            <p>{{ $order->address?->city }}/{{ $order->address?->state }}</p>
                            <p>CEP {{ $formattedZipCode }}</p>
                        </div>
                    </section>

                    <section class="rounded-3xl bg-white p-6 shadow-sm">
                        <h2 class="text-xl font-black text-[#3D3D3A]">Itens</h2>

                        <div class="mt-5 overflow-x-auto">
                            <table class="w-full min-w-[620px] text-left">
                                <thead class="text-xs uppercase tracking-wide text-[#3D3D3A]/60">
                                    <tr class="border-b border-[#3D3D3A]/10">
                                        <th class="pb-3 font-bold">Produto</th>
                                        <th class="pb-3 text-center font-bold">Qtd</th>
                                        <th class="pb-3 text-right font-bold">Preço unitário</th>
                                        <th class="pb-3 text-right font-bold">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#3D3D3A]/10">
                                    @foreach ($order->items as $item)
                                        <tr>
                                            <td class="py-4 text-sm font-bold text-[#3D3D3A]">{{ $item->product?->name ?? 'Produto removido' }}</td>
                                            <td class="py-4 text-center text-sm font-semibold text-[#3D3D3A]">{{ $item->quantity }}</td>
                                            <td class="py-4 text-right text-sm font-semibold text-[#3D3D3A]">R$ {{ number_format($item->price, 2, ',', '.') }}</td>
                                            <td class="py-4 text-right text-sm font-black text-[#3D3D3A]">R$ {{ number_format($item->getSubtotal(), 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <div class="rounded-3xl bg-[#F1EFE8] px-6 py-4">
                                <p class="text-sm text-[#3D3D3A]/70">Total geral</p>
                                <p class="mt-1 text-2xl font-black text-[#185FA5]">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</p>
                            </div>
                        </div>
                    </section>
                </div>

                <aside class="lg:col-span-1">
                    <section
                        x-data="{ status: '{{ old('status', $order->status) }}' }"
                        class="sticky top-6 rounded-3xl bg-white p-6 shadow-sm"
                    >
                        <h2 class="text-xl font-black text-[#3D3D3A]">Atualizar pedido</h2>

                        <form action="{{ route('admin.pedidos.update', $order) }}" method="POST" class="mt-5 space-y-5">
                            @csrf
                            @method('PUT')

                            <div>
                                <label for="status" class="mb-2 block text-sm font-bold text-[#3D3D3A]">Status</label>
                                <select
                                    id="status"
                                    name="status"
                                    x-model="status"
                                    class="w-full rounded border border-[#3D3D3A]/20 px-4 py-3 text-sm text-[#3D3D3A] shadow-sm focus:border-[#185FA5] focus:outline-none focus:ring-2 focus:ring-[#185FA5]/20"
                                >
                                    @foreach ($statuses as $value => $data)
                                        <option value="{{ $value }}" @selected(old('status', $order->status) === $value)>
                                            {{ $data['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div x-show="status === 'shipped'" x-cloak>
                                <label for="tracking_number" class="mb-2 block text-sm font-bold text-[#3D3D3A]">Código de rastreamento</label>
                                <input
                                    type="text"
                                    id="tracking_number"
                                    name="tracking_number"
                                    value="{{ old('tracking_number', $order->tracking_number) }}"
                                    maxlength="100"
                                    class="w-full rounded border border-[#3D3D3A]/20 px-4 py-3 text-sm text-[#3D3D3A] shadow-sm focus:border-[#185FA5] focus:outline-none focus:ring-2 focus:ring-[#185FA5]/20"
                                >
                            </div>

                            <button
                                type="submit"
                                class="inline-flex w-full items-center justify-center rounded-full bg-[#1D9E75] px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#168463] focus:outline-none focus:ring-2 focus:ring-[#1D9E75] focus:ring-offset-2"
                            >
                                Atualizar pedido
                            </button>
                        </form>
                    </section>
                </aside>
            </div>
        </div>
    </div>
@endsection
