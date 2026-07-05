<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\StockService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(
        private readonly StockService $stockService
    ) {
    }

    public function index(Request $request): View
    {
        $orders = $request->user()
            ->orders()
            ->with(['items.product', 'address'])
            ->when($request->query('status'), function ($query, string $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10);

        return view('store.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        abort_if($order->user_id !== auth()->id(), 403);

        $order->load(['items.product', 'address', 'orderCoupons.coupon', 'payments']);

        return view('store.orders.show', compact('order'));
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        abort_if($order->user_id !== auth()->id(), 403);

        if (! $order->canBeCancelled()) {
            return redirect()
                ->back()
                ->withErrors(['order' => 'Este pedido não pode ser cancelado.']);
        }

        DB::transaction(function () use ($order, $request): void {
            $this->stockService->restoreOrderStock(
                $order,
                $request->user(),
                'Estoque restaurado por cancelamento de pedido'
            );

            $order->update([
                'status' => Order::STATUS_CANCELLED,
            ]);
        });

        return redirect()
            ->back()
            ->with('status', 'Pedido cancelado com sucesso.');
    }
}
