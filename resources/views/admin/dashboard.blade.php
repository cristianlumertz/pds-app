@extends('layouts.app')

@section('content')
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-black text-slate-900">Painel admin</h1>
                <p class="mt-1 text-sm text-slate-600">Visao geral do sistema.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.categories.index') }}" class="rounded-full border border-slate-300 px-3 py-1.5 text-sm font-semibold text-slate-700 hover:border-slate-500">
                    Categorias
                </a>
                <a href="{{ route('admin.users.index') }}" class="rounded-full border border-slate-300 px-3 py-1.5 text-sm font-semibold text-slate-700 hover:border-slate-500">
                    Usuários
                </a>
                <a href="{{ route('admin.products.index') }}" class="rounded-full bg-slate-900 px-3 py-1.5 text-sm font-semibold text-white hover:bg-slate-700">
                    Produtos
                </a>
                <a href="{{ route('admin.coupons.index') }}" class="rounded-full border border-slate-300 px-3 py-1.5 text-sm font-semibold text-slate-700 hover:border-slate-500">
                    Cupons
                </a>
                <a href="{{ route('admin.stock-movements.index') }}" class="rounded-full border border-slate-300 px-3 py-1.5 text-sm font-semibold text-slate-700 hover:border-slate-500">
                    Estoque
                </a>
            </div>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Usuarios</p>
                <p class="mt-2 text-3xl font-black text-slate-900">{{ $stats['users_count'] }}</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Categorias</p>
                <p class="mt-2 text-3xl font-black text-slate-900">{{ $stats['categories_count'] }}</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Produtos</p>
                <p class="mt-2 text-3xl font-black text-slate-900">{{ $stats['products_count'] }}</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pedidos</p>
                <p class="mt-2 text-3xl font-black text-slate-900">{{ $stats['orders_count'] }}</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pedidos pagos</p>
                <p class="mt-2 text-3xl font-black text-slate-900">{{ $stats['paid_orders_count'] }}</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pagamentos pendentes</p>
                <p class="mt-2 text-3xl font-black text-slate-900">{{ $stats['pending_orders_count'] }}</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Receita paga</p>
                <p class="mt-2 text-2xl font-black text-slate-900">R$ {{ number_format((float) $stats['revenue_total'], 2, ',', '.') }}</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Cupons usados</p>
                <p class="mt-2 text-3xl font-black text-slate-900">{{ $stats['used_coupons_count'] }}</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Estoque baixo</p>
                <p class="mt-2 text-3xl font-black text-slate-900">{{ $stats['low_stock_products_count'] }}</p>
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
                        <th class="px-2 py-2">Cliente</th>
                        <th class="px-2 py-2">Status</th>
                        <th class="px-2 py-2">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($latestOrders as $order)
                        <tr class="border-b border-slate-100">
                            <td class="px-2 py-2 font-semibold text-slate-700">#{{ $order->id }}</td>
                            <td class="px-2 py-2 text-slate-600">{{ $order->user->name ?? 'N/A' }}</td>
                            <td class="px-2 py-2 text-slate-600">{{ $order->status }}</td>
                            <td class="px-2 py-2 text-slate-700">R$ {{ number_format((float) $order->total_amount, 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-2 py-4 text-sm text-slate-500">Sem pedidos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
