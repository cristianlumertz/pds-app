@extends('layouts.app')

@section('content')
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-black text-slate-900">Minha conta</h1>
        <p class="mt-1 text-sm text-slate-600">Bem-vindo, {{ auth()->user()->name }}.</p>

        <div class="mt-6 grid gap-4 sm:grid-cols-2">
            <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total de pedidos</p>
                <p class="mt-2 text-3xl font-black text-slate-900">{{ $stats['orders_count'] }}</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Carrinhos abertos</p>
                <p class="mt-2 text-3xl font-black text-slate-900">{{ $stats['open_carts_count'] }}</p>
            </article>
        </div>
    </section>

    <section class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-black text-slate-900">Ultimos pedidos</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-2 py-2">#</th>
                        <th class="px-2 py-2">Status</th>
                        <th class="px-2 py-2">Total</th>
                        <th class="px-2 py-2">Data</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($latestOrders as $order)
                        <tr class="border-b border-slate-100">
                            <td class="px-2 py-2 font-semibold text-slate-700">#{{ $order->id }}</td>
                            <td class="px-2 py-2 text-slate-600">{{ $order->status }}</td>
                            <td class="px-2 py-2 text-slate-700">R$ {{ number_format((float) $order->total_amount, 2, ',', '.') }}</td>
                            <td class="px-2 py-2 text-slate-500">{{ optional($order->created_at)->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-2 py-4 text-sm text-slate-500">Ainda sem pedidos cadastrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
