@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-black uppercase text-[#D42B2B]">Análise</p>
            <h1 class="text-3xl font-black text-[#1A1A1A]">Relatórios</h1>
            <p class="mt-1 text-sm text-[#767676]">Visões rápidas de vendas, pagamentos, estoque, cupons e clientes.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.reports.index') }}" class="mb-5 grid gap-3 rounded-lg border border-[#E0E0E0] bg-white p-4 shadow-sm md:grid-cols-4">
        <input type="date" name="from" value="{{ request('from') }}" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
        <input type="date" name="to" value="{{ request('to') }}" class="rounded border border-[#E0E0E0] px-3 py-2 text-sm">
        <button type="submit" class="rounded bg-[#1A3A6B] px-4 py-2 text-sm font-black text-white">Filtrar período</button>
        <a href="{{ route('admin.reports.index') }}" class="rounded border border-[#E0E0E0] px-4 py-2 text-center text-sm font-black text-[#3D3D3A]">Limpar</a>
    </form>

    <section class="grid gap-4 md:grid-cols-3">
        <x-admin.metric-card label="Pedidos pagos" :value="$sales['orders']" tone="green" />
        <x-admin.metric-card label="Receita no período" value="R$ {{ number_format((float) $sales['revenue'], 2, ',', '.') }}" tone="green" />
        <x-admin.metric-card label="Ticket médio" value="R$ {{ number_format((float) $sales['average_ticket'], 2, ',', '.') }}" />
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-2">
        <div class="rounded-lg border border-[#E0E0E0] bg-white p-5 shadow-sm">
            <h2 class="text-lg font-black">Pedidos por status</h2>
            <div class="mt-4 space-y-2">
                @forelse ($ordersByStatus as $status => $total)
                    <div class="flex items-center justify-between rounded border border-[#F0F0F0] px-3 py-2">
                        <x-admin.status-badge :status="$status" />
                        <span class="font-black">{{ $total }}</span>
                    </div>
                @empty
                    <x-admin.empty-state title="Sem pedidos." />
                @endforelse
            </div>
        </div>

        <div class="rounded-lg border border-[#E0E0E0] bg-white p-5 shadow-sm">
            <h2 class="text-lg font-black">Pagamentos por status</h2>
            <div class="mt-4 space-y-2">
                @forelse ($paymentsByStatus as $status => $total)
                    <div class="flex items-center justify-between rounded border border-[#F0F0F0] px-3 py-2">
                        <x-admin.status-badge :status="$status" />
                        <span class="font-black">{{ $total }}</span>
                    </div>
                @empty
                    <x-admin.empty-state title="Sem pagamentos." />
                @endforelse
            </div>
        </div>
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-2">
        <div class="rounded-lg border border-[#E0E0E0] bg-white p-5 shadow-sm">
            <h2 class="text-lg font-black">Produtos mais vendidos</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="text-xs uppercase text-[#767676]"><tr><th class="py-2">Produto</th><th class="py-2">SKU</th><th class="py-2 text-right">Qtd</th></tr></thead>
                    <tbody class="divide-y divide-[#E0E0E0]">
                        @foreach ($topProducts as $product)
                            <tr><td class="py-2 font-bold">{{ $product->name }}</td><td class="py-2">{{ $product->sku }}</td><td class="py-2 text-right font-black">{{ (int) $product->sold_quantity }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-lg border border-[#E0E0E0] bg-white p-5 shadow-sm">
            <h2 class="text-lg font-black">Produtos com estoque baixo</h2>
            <div class="mt-4 space-y-2">
                @forelse ($lowStockProducts as $product)
                    <a href="{{ route('admin.products.edit', $product) }}" class="flex justify-between rounded border border-[#F0F0F0] px-3 py-2 hover:border-[#C5D4EC]">
                        <span class="font-bold">{{ $product->name }}</span>
                        <span class="font-black text-[#B02020]">{{ $product->stock }}</span>
                    </a>
                @empty
                    <x-admin.empty-state title="Nenhum produto com estoque baixo." />
                @endforelse
            </div>
        </div>
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-2">
        <div class="rounded-lg border border-[#E0E0E0] bg-white p-5 shadow-sm">
            <h2 class="text-lg font-black">Cupons mais usados</h2>
            <div class="mt-4 space-y-2">
                @forelse ($topCoupons as $coupon)
                    <div class="flex justify-between rounded border border-[#F0F0F0] px-3 py-2">
                        <span class="font-black text-[#1A3A6B]">{{ $coupon->code }}</span>
                        <span>{{ $coupon->orders_count }} pedidos</span>
                    </div>
                @empty
                    <x-admin.empty-state title="Nenhum cupom usado." />
                @endforelse
            </div>
        </div>

        <div class="rounded-lg border border-[#E0E0E0] bg-white p-5 shadow-sm">
            <h2 class="text-lg font-black">Clientes com mais pedidos</h2>
            <div class="mt-4 space-y-2">
                @forelse ($topCustomers as $customer)
                    <a href="{{ route('admin.users.show', $customer) }}" class="flex justify-between rounded border border-[#F0F0F0] px-3 py-2 hover:border-[#C5D4EC]">
                        <span>
                            <span class="block font-bold">{{ $customer->name }}</span>
                            <span class="text-xs text-[#767676]">{{ $customer->orders_count }} pedidos</span>
                        </span>
                        <span class="font-black text-[#1A3A6B]">R$ {{ number_format((float) $customer->paid_orders_sum, 2, ',', '.') }}</span>
                    </a>
                @empty
                    <x-admin.empty-state title="Nenhum cliente com pedidos." />
                @endforelse
            </div>
        </div>
    </section>
@endsection
