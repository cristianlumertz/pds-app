<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\StockMovement;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        $now = CarbonImmutable::now();
        $monthStart = $now->startOfMonth();
        $salesStart = $now->subDays(13)->startOfDay();

        $stats = [
            'revenue_total' => Order::query()->where('payment_status', Order::PAYMENT_STATUS_PAID)->sum('total_amount'),
            'revenue_month' => Order::query()
                ->where('payment_status', Order::PAYMENT_STATUS_PAID)
                ->where('created_at', '>=', $monthStart)
                ->sum('total_amount'),
            'orders_month' => Order::query()
                ->where('created_at', '>=', $monthStart)
                ->count(),
            'pending_payments_count' => Payment::query()->where('status', Payment::STATUS_PENDING)->count(),
            'low_stock_products_count' => Product::query()->where('stock', '>', 0)->where('stock', '<=', 5)->count(),
            'out_of_stock_products_count' => Product::query()->where('stock', 0)->count(),
        ];

        $salesByDate = Order::query()
            ->where('payment_status', Order::PAYMENT_STATUS_PAID)
            ->whereBetween('created_at', [$salesStart, $now->endOfDay()])
            ->get(['created_at', 'total_amount'])
            ->groupBy(fn (Order $order): string => $order->created_at?->format('Y-m-d') ?? '')
            ->map(fn ($orders): float => (float) $orders->sum('total_amount'));

        $salesSeries = collect(range(0, 13))
            ->map(function (int $day) use ($salesStart, $salesByDate): array {
                $date = $salesStart->addDays($day);

                return [
                    'label' => $date->format('d/m'),
                    'value' => (float) ($salesByDate[$date->format('Y-m-d')] ?? 0),
                ];
            });

        $orderStatuses = [
            Order::STATUS_PENDING => 'Pendente',
            Order::STATUS_PROCESSING => 'Processando',
            Order::STATUS_SHIPPED => 'Enviado',
            Order::STATUS_DELIVERED => 'Entregue',
            Order::STATUS_CANCELLED => 'Cancelado',
        ];

        $ordersByStatusRaw = Order::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $ordersByStatus = collect($orderStatuses)
            ->map(fn (string $label, string $status): array => [
                'status' => $status,
                'label' => $label,
                'total' => (int) ($ordersByStatusRaw[$status] ?? 0),
            ])
            ->values();

        $paymentStatuses = [
            Payment::STATUS_PENDING => 'Pendente',
            Payment::STATUS_PAID => 'Pago',
            Payment::STATUS_FAILED => 'Falhou',
            Payment::STATUS_CANCELLED => 'Cancelado',
            Payment::STATUS_EXPIRED => 'Expirado',
            Payment::STATUS_REFUNDED => 'Reembolsado',
        ];

        $paymentsByStatusRaw = Payment::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $paymentsByStatus = collect($paymentStatuses)
            ->map(fn (string $label, string $status): array => [
                'status' => $status,
                'label' => $label,
                'total' => (int) ($paymentsByStatusRaw[$status] ?? 0),
            ])
            ->values();

        $stockDistribution = collect([
            ['label' => 'Normal', 'status' => 'paid', 'total' => Product::query()->where('stock', '>', 5)->count()],
            ['label' => 'Estoque baixo', 'status' => 'pending', 'total' => $stats['low_stock_products_count']],
            ['label' => 'Sem estoque', 'status' => 'failed', 'total' => $stats['out_of_stock_products_count']],
        ]);

        $topProducts = OrderItem::query()
            ->select('product_id', 'product_name', 'product_sku')
            ->selectRaw('SUM(quantity) as sold_quantity')
            ->groupBy('product_id', 'product_name', 'product_sku')
            ->orderByDesc('sold_quantity')
            ->take(10)
            ->get();

        $topCoupons = Coupon::query()
            ->where('used_count', '>', 0)
            ->orderByDesc('used_count')
            ->take(5)
            ->get(['id', 'code', 'description', 'used_count']);

        $latestOrders = Order::query()
            ->with('user')
            ->latest()
            ->take(6)
            ->get();

        $latestPayments = Payment::query()
            ->with(['order.user'])
            ->latest()
            ->take(6)
            ->get();

        $latestMovements = StockMovement::query()
            ->with(['product', 'user', 'order'])
            ->latest()
            ->take(6)
            ->get();

        $lowStockProducts = Product::query()
            ->with('category')
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->take(6)
            ->get();

        $alerts = collect([
            $stats['pending_payments_count'] > 0 ? "{$stats['pending_payments_count']} pagamentos pendentes aguardando acompanhamento." : null,
            $stats['low_stock_products_count'] > 0 ? "{$stats['low_stock_products_count']} produtos com estoque baixo." : null,
            $stats['out_of_stock_products_count'] > 0 ? "{$stats['out_of_stock_products_count']} produtos sem estoque." : null,
        ])->filter()->values();

        return view('admin.dashboard', compact(
            'stats',
            'salesSeries',
            'ordersByStatus',
            'paymentsByStatus',
            'stockDistribution',
            'topProducts',
            'topCoupons',
            'latestOrders',
            'latestPayments',
            'latestMovements',
            'lowStockProducts',
            'alerts'
        ));
    }
}
