@extends('layouts.admin')

@section('content')
    @php
        $salesMax = max(1, (float) $salesSeries->max('value'));
        $ordersMax = max(1, (int) $ordersByStatus->max('total'));
        $paymentsMax = max(1, (int) $paymentsByStatus->max('total'));
        $stockMax = max(1, (int) $stockDistribution->max('total'));
        $topProductsMax = max(1, (int) $topProducts->max('sold_quantity'));
        $topCouponsMax = max(1, (int) $topCoupons->max('used_count'));
    @endphp

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-black uppercase text-[#D42B2B]">Visão geral</p>
            <h1 class="text-3xl font-black text-[#1A1A1A]">Dashboard administrativo</h1>
            <p class="mt-1 text-sm text-[#767676]">Gráficos, rankings e resumos estratégicos da operação.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.reports.index') }}" class="rounded border border-[#C5D4EC] bg-white px-4 py-2 text-sm font-black text-[#1A3A6B] hover:border-[#1A3A6B]">Relatórios</a>
            <a href="{{ route('admin.pedidos.index') }}" class="rounded bg-[#D42B2B] px-4 py-2 text-sm font-black text-white hover:bg-[#B02020]">Ver pedidos</a>
        </div>
    </div>

    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.metric-card label="Receita do mês" value="R$ {{ number_format((float) $stats['revenue_month'], 2, ',', '.') }}" tone="green" />
        <x-admin.metric-card label="Pedidos do mês" :value="$stats['orders_month']" />
        <x-admin.metric-card label="Pagamentos pendentes" :value="$stats['pending_payments_count']" tone="yellow" />
        <x-admin.metric-card label="Estoque baixo" :value="$stats['low_stock_products_count']" tone="red" />
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-2">
        <article class="rounded-lg border border-[#E0E0E0] bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-black text-[#1A1A1A]">Vendas por período</h2>
                    <p class="mt-1 text-sm text-[#767676]">Receita diária dos últimos 14 dias em pedidos pagos.</p>
                </div>
                <span class="rounded bg-[#1D9E75]/10 px-3 py-1 text-xs font-black text-[#16765A]">
                    R$ {{ number_format((float) $stats['revenue_total'], 2, ',', '.') }} total
                </span>
            </div>

            <div class="mt-6 flex h-64 items-end gap-2 border-b border-l border-[#E0E0E0] px-2 pb-2">
                @foreach ($salesSeries as $point)
                    @php
                        $height = (float) $point['value'] > 0 ? max(8, ((float) $point['value'] / $salesMax) * 100) : 2;
                    @endphp
                    <div class="flex min-w-0 flex-1 flex-col items-center justify-end gap-2">
                        <div class="group relative flex h-full w-full items-end justify-center">
                            <div class="w-full max-w-8 rounded-t bg-[#1A3A6B] transition hover:bg-[#D42B2B]" style="height: {{ $height }}%"></div>
                            <span class="absolute bottom-full mb-2 hidden rounded bg-[#1A1A1A] px-2 py-1 text-xs font-bold text-white shadow group-hover:block">
                                R$ {{ number_format((float) $point['value'], 2, ',', '.') }}
                            </span>
                        </div>
                        <span class="text-[10px] font-bold text-[#767676]">{{ $point['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="rounded-lg border border-[#E0E0E0] bg-white p-5 shadow-sm">
            <h2 class="text-lg font-black text-[#1A1A1A]">Pedidos por status</h2>
            <p class="mt-1 text-sm text-[#767676]">Distribuição dos pedidos por etapa operacional.</p>
            <div class="mt-6 space-y-4">
                @foreach ($ordersByStatus as $item)
                    @php $width = ((int) $item['total'] / $ordersMax) * 100; @endphp
                    <div>
                        <div class="mb-1 flex items-center justify-between gap-3">
                            <x-admin.status-badge :status="$item['status']" :label="$item['label']" />
                            <span class="text-sm font-black text-[#1A1A1A]">{{ $item['total'] }}</span>
                        </div>
                        <div class="h-3 overflow-hidden rounded-full bg-[#F3F5F8]">
                            <div class="h-full rounded-full bg-[#1A3A6B]" style="width: {{ $width }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="rounded-lg border border-[#E0E0E0] bg-white p-5 shadow-sm">
            <h2 class="text-lg font-black text-[#1A1A1A]">Pagamentos por status</h2>
            <p class="mt-1 text-sm text-[#767676]">Visão financeira dos pagamentos registrados.</p>
            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                @foreach ($paymentsByStatus as $item)
                    @php $width = ((int) $item['total'] / $paymentsMax) * 100; @endphp
                    <div class="rounded border border-[#F0F0F0] p-3">
                        <div class="flex items-center justify-between gap-2">
                            <x-admin.status-badge :status="$item['status']" :label="$item['label']" />
                            <span class="font-black">{{ $item['total'] }}</span>
                        </div>
                        <div class="mt-3 h-2 overflow-hidden rounded-full bg-[#F3F5F8]">
                            <div class="h-full rounded-full bg-[#D42B2B]" style="width: {{ $width }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="rounded-lg border border-[#E0E0E0] bg-white p-5 shadow-sm">
            <h2 class="text-lg font-black text-[#1A1A1A]">Produtos por situação de estoque</h2>
            <p class="mt-1 text-sm text-[#767676]">Normal, baixo e sem estoque pelo saldo atual.</p>
            <div class="mt-6 space-y-4">
                @foreach ($stockDistribution as $item)
                    @php $width = ((int) $item['total'] / $stockMax) * 100; @endphp
                    <div>
                        <div class="mb-1 flex items-center justify-between">
                            <x-admin.status-badge :status="$item['status']" :label="$item['label']" />
                            <span class="text-sm font-black">{{ $item['total'] }}</span>
                        </div>
                        <div class="h-4 overflow-hidden rounded-full bg-[#F3F5F8]">
                            <div class="h-full rounded-full {{ $item['status'] === 'failed' ? 'bg-[#D42B2B]' : ($item['status'] === 'pending' ? 'bg-[#BA7517]' : 'bg-[#1D9E75]') }}" style="width: {{ $width }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="rounded-lg border border-[#E0E0E0] bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-black text-[#1A1A1A]">Produtos mais vendidos</h2>
                    <p class="mt-1 text-sm text-[#767676]">Ranking por quantidade em itens de pedido.</p>
                </div>
                <a href="{{ route('admin.reports.index') }}" class="text-sm font-black text-[#D42B2B] hover:underline">Ver relatório</a>
            </div>
            <div class="mt-5 space-y-3">
                @forelse ($topProducts as $item)
                    @php
                        $quantity = (int) $item->sold_quantity;
                        $width = ($quantity / $topProductsMax) * 100;
                        $name = $item->product_name ?: 'Produto #'.$item->product_id;
                    @endphp
                    <div>
                        <div class="mb-1 flex items-center justify-between gap-3">
                            <span class="truncate text-sm font-black">{{ $name }}</span>
                            <span class="text-sm font-black text-[#1A3A6B]">{{ $quantity }}</span>
                        </div>
                        <div class="h-3 overflow-hidden rounded-full bg-[#F3F5F8]">
                            <div class="h-full rounded-full bg-[#1A3A6B]" style="width: {{ $width }}%"></div>
                        </div>
                        <p class="mt-1 text-xs text-[#767676]">{{ $item->product_sku ?: 'SKU não informado' }}</p>
                    </div>
                @empty
                    <x-admin.empty-state title="Sem produtos vendidos ainda." />
                @endforelse
            </div>
        </article>

        <article class="rounded-lg border border-[#E0E0E0] bg-white p-5 shadow-sm">
            <h2 class="text-lg font-black text-[#1A1A1A]">Cupons mais usados</h2>
            <p class="mt-1 text-sm text-[#767676]">Top 5 por contador de uso.</p>
            <div class="mt-5 space-y-3">
                @forelse ($topCoupons as $coupon)
                    @php $width = ((int) $coupon->used_count / $topCouponsMax) * 100; @endphp
                    <div>
                        <div class="mb-1 flex items-center justify-between gap-3">
                            <span class="font-black text-[#1A3A6B]">{{ $coupon->code }}</span>
                            <span class="text-sm font-black text-[#D42B2B]">{{ $coupon->used_count }}</span>
                        </div>
                        <div class="h-3 overflow-hidden rounded-full bg-[#F3F5F8]">
                            <div class="h-full rounded-full bg-[#D42B2B]" style="width: {{ $width }}%"></div>
                        </div>
                        <p class="mt-1 truncate text-xs text-[#767676]">{{ $coupon->description ?: 'Sem descrição' }}</p>
                    </div>
                @empty
                    <x-admin.empty-state title="Nenhum cupom usado ainda." />
                @endforelse
            </div>
        </article>
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-2">
        <article class="rounded-lg border border-[#E0E0E0] bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-black text-[#1A1A1A]">Últimos pedidos</h2>
                <a href="{{ route('admin.pedidos.index') }}" class="text-sm font-black text-[#D42B2B] hover:underline">Ver todos</a>
            </div>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full min-w-[680px] text-left text-sm">
                    <thead class="border-b border-[#E0E0E0] text-xs uppercase text-[#767676]">
                        <tr><th class="py-3">Pedido</th><th class="py-3">Cliente</th><th class="py-3">Status</th><th class="py-3 text-right">Total</th><th class="py-3 text-right">Ação</th></tr>
                    </thead>
                    <tbody class="divide-y divide-[#E0E0E0]">
                        @forelse($latestOrders as $order)
                            <tr>
                                <td class="py-3 font-black">#{{ $order->id }}</td>
                                <td class="py-3">{{ $order->user?->name ?? 'Cliente removido' }}</td>
                                <td class="py-3"><x-admin.status-badge :status="$order->status" /></td>
                                <td class="py-3 text-right font-black text-[#1A3A6B]">R$ {{ number_format((float) $order->total_amount, 2, ',', '.') }}</td>
                                <td class="py-3 text-right"><a href="{{ route('admin.pedidos.show', $order) }}" class="font-black text-[#D42B2B] hover:underline">Abrir</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-6"><x-admin.empty-state title="Sem pedidos registrados." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <article class="rounded-lg border border-[#E0E0E0] bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-black text-[#1A1A1A]">Últimos pagamentos</h2>
                <a href="{{ route('admin.payments.index') }}" class="text-sm font-black text-[#D42B2B] hover:underline">Ver todos</a>
            </div>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full min-w-[680px] text-left text-sm">
                    <thead class="border-b border-[#E0E0E0] text-xs uppercase text-[#767676]">
                        <tr><th class="py-3">Pagamento</th><th class="py-3">Cliente</th><th class="py-3">Status</th><th class="py-3 text-right">Valor</th><th class="py-3 text-right">Ação</th></tr>
                    </thead>
                    <tbody class="divide-y divide-[#E0E0E0]">
                        @forelse($latestPayments as $payment)
                            <tr>
                                <td class="py-3 font-black">#{{ $payment->id }}</td>
                                <td class="py-3">{{ $payment->order?->user?->name ?? 'Cliente removido' }}</td>
                                <td class="py-3"><x-admin.status-badge :status="$payment->status" /></td>
                                <td class="py-3 text-right font-black text-[#1A3A6B]">R$ {{ number_format((float) $payment->amount, 2, ',', '.') }}</td>
                                <td class="py-3 text-right"><a href="{{ route('admin.payments.show', $payment) }}" class="font-black text-[#D42B2B] hover:underline">Abrir</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-6"><x-admin.empty-state title="Sem pagamentos registrados." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <article class="rounded-lg border border-[#E0E0E0] bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-black text-[#1A1A1A]">Últimas movimentações</h2>
                <a href="{{ route('admin.stock-movements.index') }}" class="text-sm font-black text-[#D42B2B] hover:underline">Ver todos</a>
            </div>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full min-w-[680px] text-left text-sm">
                    <thead class="border-b border-[#E0E0E0] text-xs uppercase text-[#767676]">
                        <tr><th class="py-3">Data</th><th class="py-3">Produto</th><th class="py-3">Tipo</th><th class="py-3 text-right">Qtd</th></tr>
                    </thead>
                    <tbody class="divide-y divide-[#E0E0E0]">
                        @forelse($latestMovements as $movement)
                            <tr>
                                <td class="py-3 text-[#767676]">{{ $movement->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="py-3 font-bold">{{ $movement->product?->name ?? 'Produto removido' }}</td>
                                <td class="py-3"><x-admin.status-badge :status="$movement->type" /></td>
                                <td class="py-3 text-right font-black">{{ $movement->quantity }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-6"><x-admin.empty-state title="Sem movimentações registradas." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <article class="rounded-lg border border-[#E0E0E0] bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-black text-[#1A1A1A]">Produtos com estoque baixo</h2>
                <a href="{{ route('admin.stock.index', ['stock' => 'low']) }}" class="text-sm font-black text-[#D42B2B] hover:underline">Ver todos</a>
            </div>
            <div class="mt-4 space-y-2">
                @forelse ($lowStockProducts as $product)
                    <a href="{{ route('admin.products.edit', $product) }}" class="flex items-center justify-between gap-3 rounded border border-[#F0F0F0] px-3 py-2 hover:border-[#C5D4EC]">
                        <span>
                            <span class="block text-sm font-black">{{ $product->name }}</span>
                            <span class="text-xs text-[#767676]">{{ $product->category?->name ?? 'Sem categoria' }} · {{ $product->sku }}</span>
                        </span>
                        <span class="text-sm font-black {{ (int) $product->stock === 0 ? 'text-[#B02020]' : 'text-[#856404]' }}">{{ $product->stock }}</span>
                    </a>
                @empty
                    <x-admin.empty-state title="Nenhum produto com estoque baixo." />
                @endforelse
            </div>
        </article>
    </section>

    <section class="mt-6 rounded-lg border border-[#FFF3CD] bg-[#FFF9E6] p-5 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-black text-[#856404]">Alertas administrativos</h2>
                <p class="mt-1 text-sm text-[#856404]/80">Pontos de atenção para a operação.</p>
            </div>
        </div>
        <div class="mt-4 grid gap-3 lg:grid-cols-3">
            @forelse ($alerts as $alert)
                <div class="rounded border border-[#FFF3CD] bg-white px-4 py-3 text-sm font-bold text-[#856404]">{{ $alert }}</div>
            @empty
                <div class="rounded border border-[#1D9E75]/20 bg-white px-4 py-3 text-sm font-bold text-[#16765A]">Nenhum alerta crítico no momento.</div>
            @endforelse
        </div>
    </section>
@endsection
