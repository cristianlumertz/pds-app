<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AdminStockMovementController extends Controller
{
    public function index(Request $request): View
    {
        $movements = StockMovement::query()
            ->with(['product', 'user', 'order'])
            ->when($request->integer('product_id'), fn ($query, int $productId) => $query->where('product_id', $productId))
            ->when($request->query('type'), fn ($query, string $type) => $query->where('type', $type))
            ->when($request->date('from'), fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($request->date('to'), fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->latest()
            ->paginate(20);

        $products = Product::query()
            ->orderBy('name')
            ->get(['id', 'name', 'sku']);

        $types = [
            StockMovement::TYPE_ENTRY => 'Entrada',
            StockMovement::TYPE_EXIT => 'Saída',
            StockMovement::TYPE_ADJUSTMENT => 'Ajuste',
            StockMovement::TYPE_CANCELLATION => 'Cancelamento',
            StockMovement::TYPE_RESERVATION => 'Reserva',
            StockMovement::TYPE_RESERVATION_RELEASE => 'Liberação de reserva',
        ];

        return view('admin.stock-movements.index', compact('movements', 'products', 'types'));
    }
}
