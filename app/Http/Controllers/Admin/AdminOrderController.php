<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->with(['user', 'address'])
            ->when($request->query('status'), function ($query, string $status) {
                $query->where('status', $status);
            })
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
        $order->load(['items.product', 'user', 'address']);

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

        if (! empty($validated['status'])) {
            $order->status = $validated['status'];
        }

        if (! empty($validated['tracking_number'])) {
            $order->tracking_number = $validated['tracking_number'];
        }

        $order->save();

        return redirect()
            ->back()
            ->with('status', 'Pedido atualizado com sucesso.');
    }
}
