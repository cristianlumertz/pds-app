@extends('layouts.app')

@section('content')
    @php
        $statusLabels = [
            'pending' => ['label' => 'Aguardando pagamento', 'color' => '#BA7517'],
            'processing' => ['label' => 'Em processamento', 'color' => '#185FA5'],
            'shipped' => ['label' => 'Enviado', 'color' => '#534AB7'],
            'delivered' => ['label' => 'Entregue', 'color' => '#3B6D11'],
            'cancelled' => ['label' => 'Cancelado', 'color' => '#993C1D'],
        ];

        $filters = [
            '' => 'Todos',
            'pending' => 'Pendente',
            'processing' => 'Processando',
            'shipped' => 'Enviado',
            'delivered' => 'Entregue',
            'cancelled' => 'Cancelado',
        ];
        $paymentStatusLabels = [
            'pending' => 'Pagamento pendente',
            'paid' => 'Pago',
            'failed' => 'Pagamento falhou',
            'cancelled' => 'Pagamento cancelado',
            'expired' => 'Pagamento expirado',
            'refunded' => 'Reembolsado',
        ];

        $currentStatus = request('status', '');
    @endphp

    <div class="min-h-screen bg-[#F1EFE8] py-10">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-4 lg:px-8">
            <aside class="lg:col-span-1">
                <nav class="rounded-3xl bg-white p-4 shadow-sm">
                    <a
                        href="{{ route('user.dashboard') }}"
                        class="block rounded-full px-4 py-3 text-sm font-bold text-[#3D3D3A] transition hover:bg-[#F1EFE8]"
                    >
                        Minha conta
                    </a>

                    <a
                        href="{{ route('orders.index') }}"
                        class="mt-2 block rounded-full bg-[#185FA5] px-4 py-3 text-sm font-bold text-white"
                    >
                        Meus Pedidos
                    </a>

                    <a
                        href="{{ route('addresses.index') }}"
                        class="mt-2 block rounded-full px-4 py-3 text-sm font-bold text-[#3D3D3A] transition hover:bg-[#F1EFE8]"
                    >
                        Meus Endereços
                    </a>

                    <a
                        href="{{ route('profile.edit') }}"
                        class="mt-2 block rounded-full px-4 py-3 text-sm font-bold text-[#3D3D3A] transition hover:bg-[#F1EFE8]"
                    >
                        Dados pessoais
                    </a>
                </nav>
            </aside>

            <main class="lg:col-span-3">
                <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h1 class="text-3xl font-black text-[#3D3D3A]">Meus Pedidos</h1>
                        <p class="mt-2 text-sm text-[#3D3D3A]/70">Acompanhe suas compras e o status de cada pedido.</p>
                    </div>
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

                <div class="mb-6 flex gap-2 overflow-x-auto rounded-3xl bg-white p-2 shadow-sm">
                    @foreach ($filters as $status => $label)
                        <a
                            href="{{ $status === '' ? route('orders.index') : route('orders.index', ['status' => $status]) }}"
                            class="whitespace-nowrap rounded-full px-4 py-2 text-sm font-bold transition {{ $currentStatus === $status ? 'bg-[#185FA5] text-white' : 'text-[#3D3D3A] hover:bg-[#F1EFE8]' }}"
                        >
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                @if ($orders->isEmpty())
                    <section class="rounded-3xl bg-white p-10 text-center shadow-sm">
                        <div class="mx-auto flex h-28 w-28 items-center justify-center rounded-full bg-[#185FA5]/10">
                            <svg
                                class="h-14 w-14 text-[#185FA5]"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18l-2 13H5L3 7Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7a4 4 0 0 1 8 0" />
                            </svg>
                        </div>

                        <h2 class="mt-6 text-2xl font-black text-[#3D3D3A]">Você ainda não fez nenhum pedido</h2>
                        <p class="mt-2 text-sm text-[#3D3D3A]/70">Explore nossos materiais de construção e encontre o que precisa para sua obra.</p>

                        <a
                            href="{{ route('store.products') }}"
                            class="mt-6 inline-flex items-center justify-center rounded-full bg-[#185FA5] px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#144f8a]"
                        >
                            Explorar produtos
                        </a>
                    </section>
                @else
                    <div class="space-y-5">
                        @foreach ($orders as $order)
                            @php
                                $status = $statusLabels[$order->status] ?? ['label' => ucfirst((string) $order->status), 'color' => '#3D3D3A'];
                                $images = $order->items->take(3)->map(function ($item) {
                                    return $item->product?->primaryImageUrl() ?? $item->product?->image_url;
                                })->filter();
                            @endphp

                            <article class="rounded-3xl bg-white p-6 shadow-sm">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <div class="flex flex-wrap items-center gap-3">
                                            <h2 class="text-xl font-black text-[#3D3D3A]">#{{ $order->id }}</h2>
                                            <span class="text-sm text-[#3D3D3A]/60">{{ $order->created_at->format('d/m/Y') }}</span>
                                        </div>

                                        <span
                                            class="mt-3 inline-flex rounded-full px-3 py-1 text-xs font-bold text-white"
                                            style="background-color: {{ $status['color'] }}"
                                        >
                                            {{ $status['label'] }}
                                        </span>
                                        <p class="mt-2 text-sm font-semibold text-[#3D3D3A]/70">
                                            {{ $paymentStatusLabels[$order->payment_status] ?? ucfirst((string) $order->payment_status) }}
                                        </p>
                                    </div>

                                    <div class="text-left sm:text-right">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-[#3D3D3A]/60">Total</p>
                                        <p class="mt-1 text-xl font-black text-[#185FA5]">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</p>
                                    </div>
                                </div>

                                <div class="mt-5 flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                                    <div class="flex items-center gap-2">
                                        @forelse ($images as $image)
                                            <img
                                                src="{{ $image }}"
                                                alt="Produto do pedido #{{ $order->id }}"
                                                class="h-10 w-10 rounded object-cover ring-2 ring-white"
                                            >
                                        @empty
                                            <div class="flex h-10 w-10 items-center justify-center rounded bg-[#F1EFE8] text-xs font-bold text-[#3D3D3A]/60">
                                                Item
                                            </div>
                                        @endforelse

                                        @if ($order->items->count() > 3)
                                            <span class="flex h-10 w-10 items-center justify-center rounded bg-[#F1EFE8] text-xs font-bold text-[#3D3D3A]">
                                                +{{ $order->items->count() - 3 }}
                                            </span>
                                        @endif

                                        <span class="ml-2 text-sm font-semibold text-[#3D3D3A]/70">
                                            {{ $order->items->sum('quantity') }} item(ns)
                                        </span>
                                    </div>

                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                        <a
                                            href="{{ route('orders.show', $order) }}"
                                            class="inline-flex items-center justify-center rounded-full bg-[#185FA5] px-4 py-2 text-sm font-bold text-white transition hover:bg-[#144f8a]"
                                        >
                                            Ver detalhes
                                        </a>

                                        @if ($order->canBeCancelled())
                                            <form action="{{ route('orders.cancel', $order) }}" method="POST">
                                                @csrf

                                                <button
                                                    type="submit"
                                                    onclick="return confirm('Deseja cancelar este pedido?')"
                                                    class="inline-flex w-full items-center justify-center rounded-full border border-[#993C1D] px-4 py-2 text-sm font-bold text-[#993C1D] transition hover:bg-[#993C1D] hover:text-white"
                                                >
                                                    Cancelar pedido
                                                </button>
                                            </form>
                                        @endif

                                        @if ($order->status === 'shipped' && $order->tracking_number)
                                            <span class="inline-flex items-center justify-center rounded-full border border-[#534AB7] px-4 py-2 text-sm font-bold text-[#534AB7]">
                                                Rastrear: {{ $order->tracking_number }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $orders->withQueryString()->links() }}
                    </div>
                @endif
            </main>
        </div>
    </div>
@endsection
