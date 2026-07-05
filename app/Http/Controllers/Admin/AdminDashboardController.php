<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'users_count' => User::query()->count(),
            'products_count' => Product::query()->count(),
            'categories_count' => Category::query()->count(),
            'orders_count' => Order::query()->count(),
            'paid_orders_count' => Order::query()->where('payment_status', Order::PAYMENT_STATUS_PAID)->count(),
            'pending_orders_count' => Order::query()->where('payment_status', Order::PAYMENT_STATUS_PENDING)->count(),
            'revenue_total' => Order::query()->where('payment_status', Order::PAYMENT_STATUS_PAID)->sum('total_amount'),
            'used_coupons_count' => Coupon::query()->sum('used_count'),
            'low_stock_products_count' => Product::query()->where('stock', '<=', 5)->count(),
        ];

        $latestOrders = Order::query()
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'latestOrders'));
    }
}
