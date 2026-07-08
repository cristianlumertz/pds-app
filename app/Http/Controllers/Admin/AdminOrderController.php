<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\StockService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminOrderController extends Controller
{
    public function __construct(
        private readonly StockService $stockService
    ) {
    }

    public function index(Request $request): View
    {
        $orders = Order::query()
            ->with(['user', 'address'])
            ->when($request->query('status'), function ($query, string $status) {
                $query->where('status', $status);
            })
            ->when($request->query('payment_status'), function ($query, string $status) {
                $query->where('payment_status', $status);
            })
            ->when($request->query('payment_method'), function ($query, string $method) {
                $query->where('payment_method', $method);
            })
            ->when($request->filled('customer'), function ($query) use ($request): void {
                $search = trim((string) $request->query('customer'));

                $query->whereHas('user', function ($builder) use ($search): void {
                    $builder
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('cpf', 'like', "%{$search}%");
                });
            })
            ->when($request->date('from'), fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($request->date('to'), fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->when($request->filled('min_total'), fn ($query) => $query->where('total_amount', '>=', (float) $request->query('min_total')))
            ->when($request->filled('max_total'), fn ($query) => $query->where('total_amount', '<=', (float) $request->query('max_total')))
            ->latest()
            ->paginate(15);

        $stats = Order::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    public function show(Order $order): View
    {
        $order->load([
            'items.product',
            'user',
            'address',
            'orderCoupons.coupon',
            'payments',
            'paymentEvents',
            'stockMovements.product',
            'stockMovements.user',
        ]);

        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', 'in:pending,processing,shipped,delivered,cancelled'],
            'tracking_number' => ['nullable', 'string', 'max:100', 'required_if:status,shipped'],
        ], [
            'status.in' => 'Status inválido.',
            'tracking_number.required_if' => 'O código de rastreamento é obrigatório para pedidos enviados.',
            'tracking_number.string' => 'O código de rastreamento deve ser um texto.',
            'tracking_number.max' => 'O código de rastreamento deve ter no máximo 100 caracteres.',
        ]);

        DB::transaction(function () use ($order, $validated, $request): void {
            $previousStatus = (string) $order->status;
            $nextStatus = $validated['status'] ?? null;

            if (! empty($nextStatus)) {
                $order->status = $nextStatus;
            }

            if (! empty($validated['tracking_number'])) {
                $order->tracking_number = $validated['tracking_number'];
            }

            if ($nextStatus === Order::STATUS_CANCELLED && $previousStatus !== Order::STATUS_CANCELLED) {
                $this->stockService->restoreOrderStock(
                    $order,
                    $request->user(),
                    'Estoque restaurado por cancelamento de pedido'
                );
            }

            $order->save();
        });

        return redirect()
            ->back()
            ->with('status', 'Pedido atualizado com sucesso.');
    }
}
