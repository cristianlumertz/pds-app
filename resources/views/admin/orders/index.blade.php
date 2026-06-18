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

        $currentStatus = request('status', '');
    @endphp

    <div class="min-h-screen bg-[#F1EFE8] py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-3xl font-black text-[#3D3D3A]">Pedidos</h1>
                    <p class="mt-2 text-sm text-[#3D3D3A]/70">Acompanhe e atualize os pedidos da loja.</p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                @foreach ($statuses as $status => $data)
                    <article class="rounded-3xl bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-bold text-[#3D3D3A]/65">{{ $data['label'] }}</p>
                                <p class="mt-2 text-3xl font-black text-[#3D3D3A]">{{ (int) ($stats[$status] ?? 0) }}</p>
                            </div>

                            <span class="h-10 w-10 rounded-full" style="background-color: {{ $data['color'] }}"></span>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-6 flex gap-2 overflow-x-auto rounded-3xl bg-white p-2 shadow-sm">
                <a
                    href="{{ route('admin.pedidos.index') }}"
                    class="whitespace-nowrap rounded-full px-4 py-2 text-sm font-bold transition {{ $currentStatus === '' ? 'bg-[#185FA5] text-white' : 'text-[#3D3D3A] hover:bg-[#F1EFE8]' }}"
                >
                    Todos
                </a>

                @foreach ($statuses as $status => $data)
                    <a
                        href="{{ route('admin.pedidos.index', ['status' => $status]) }}"
                        class="whitespace-nowrap rounded-full px-4 py-2 text-sm font-bold transition {{ $currentStatus === $status ? 'bg-[#185FA5] text-white' : 'text-[#3D3D3A] hover:bg-[#F1EFE8]' }}"
                    >
                        {{ $data['label'] }}
                    </a>
                @endforeach
            </div>

            <section class="mt-6 overflow-hidden rounded-3xl bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[920px] text-left">
                        <thead class="bg-[#F1EFE8] text-xs uppercase tracking-wide text-[#3D3D3A]/65">
                            <tr>
                                <th class="px-5 py-4 font-bold">#</th>
                                <th class="px-5 py-4 font-bold">Cliente</th>
                                <th class="px-5 py-4 font-bold">Status</th>
                                <th class="px-5 py-4 text-right font-bold">Total</th>
                                <th class="px-5 py-4 font-bold">Pagamento</th>
                                <th class="px-5 py-4 font-bold">Data</th>
                                <th class="px-5 py-4 text-right font-bold">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#3D3D3A]/10">
                            @forelse ($orders as $order)
                                @php
                                    $status = $statuses[$order->status] ?? ['label' => ucfirst((string) $order->status), 'color' => '#3D3D3A'];
                                @endphp

                                <tr>
                                    <td class="px-5 py-4 text-sm font-black text-[#3D3D3A]">#{{ $order->id }}</td>
                                    <td class="px-5 py-4">
                                        <p class="text-sm font-bold text-[#3D3D3A]">{{ $order->user?->name ?? 'Cliente removido' }}</p>
                                        <p class="mt-1 text-xs text-[#3D3D3A]/60">{{ $order->user?->email }}</p>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span
                                            class="inline-flex rounded-full px-3 py-1 text-xs font-bold text-white"
                                            style="background-color: {{ $status['color'] }}"
                                        >
                                            {{ $status['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-right text-sm font-black text-[#185FA5]">
                                        R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-4 text-sm font-semibold text-[#3D3D3A]">{{ ucfirst($order->payment_method) }}</td>
                                    <td class="px-5 py-4 text-sm text-[#3D3D3A]/70">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-5 py-4 text-right">
                                        <a
                                            href="{{ route('admin.pedidos.show', $order) }}"
                                            class="inline-flex items-center justify-center rounded-full bg-[#185FA5] px-4 py-2 text-sm font-bold text-white transition hover:bg-[#144f8a]"
                                        >
                                            Ver detalhes
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-12 text-center text-sm font-semibold text-[#3D3D3A]/70">
                                        Nenhum pedido encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="mt-6">
                {{ $orders->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection
