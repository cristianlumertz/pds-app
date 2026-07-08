<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function __invoke(Request $request): View
    {
        $from = $request->date('from')?->startOfDay();
        $to = $request->date('to')?->endOfDay();

        $paidOrders = Order::query()
            ->where('payment_status', Order::PAYMENT_STATUS_PAID)
            ->when($from, fn ($query) => $query->where('created_at', '>=', $from))
            ->when($to, fn ($query) => $query->where('created_at', '<=', $to));

        $sales = [
            'orders' => (clone $paidOrders)->count(),
            'revenue' => (clone $paidOrders)->sum('total_amount'),
            'average_ticket' => (float) ((clone $paidOrders)->avg('total_amount') ?? 0),
        ];

        $ordersByStatus = Order::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $paymentsByStatus = Payment::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $topProducts = Product::query()
            ->select('products.id', 'products.name', 'products.sku')
            ->selectRaw('COALESCE(SUM(order_items.quantity), 0) as sold_quantity')
            ->leftJoin('order_items', 'order_items.product_id', '=', 'products.id')
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('sold_quantity')
            ->take(8)
            ->get();

        $topCoupons = Coupon::query()
            ->withCount('orders')
            ->orderByDesc('orders_count')
            ->take(8)
            ->get();

        $topCustomers = User::query()
            ->withCount('orders')
            ->withSum(['orders as paid_orders_sum' => fn ($query) => $query->where('payment_status', Order::PAYMENT_STATUS_PAID)], 'total_amount')
            ->orderByDesc('orders_count')
            ->take(8)
            ->get();

        $lowStockProducts = Product::query()
            ->with('category')
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->take(10)
            ->get();

        return view('admin.reports.index', compact(
            'sales',
            'ordersByStatus',
            'paymentsByStatus',
            'topProducts',
            'topCoupons',
            'topCustomers',
            'lowStockProducts'
        ));
    }
}
