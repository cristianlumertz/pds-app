<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminStockController extends Controller
{
    public function __construct(
        private readonly StockService $stockService
    ) {
    }

    public function index(Request $request): View
    {
        $products = Product::query()
            ->with(['category'])
            ->withMax('stockMovements as last_stock_movement_at', 'created_at')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $search = trim((string) $request->query('q'));

                $query->where(function ($builder) use ($search): void {
                    $builder
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($request->integer('category_id'), fn ($query, int $categoryId) => $query->where('category_id', $categoryId))
            ->when($request->query('stock') === 'low', fn ($query) => $query->whereBetween('stock', [1, 5]))
            ->when($request->query('stock') === 'out', fn ($query) => $query->where('stock', 0))
            ->orderBy('stock')
            ->paginate(20);

        $categories = Category::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $stats = [
            'total' => Product::query()->count(),
            'normal' => Product::query()->where('stock', '>', 5)->count(),
            'low' => Product::query()->whereBetween('stock', [1, 5])->count(),
            'out' => Product::query()->where('stock', 0)->count(),
        ];

        return view('admin.stock.index', compact('products', 'categories', 'stats'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'operation' => ['required', Rule::in(['entrada', 'saida', 'ajuste'])],
            'quantity' => ['nullable', 'required_if:operation,entrada,saida', 'integer', 'min:1'],
            'target_stock' => ['nullable', 'required_if:operation,ajuste', 'integer', 'min:0'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $reason = $data['reason'] ?? 'Ajuste manual no painel admin';

        match ($data['operation']) {
            StockMovement::TYPE_ENTRY => $this->stockService->increaseStock(
                $product,
                (int) $request->input('quantity'),
                null,
                $request->user(),
                $reason
            ),
            StockMovement::TYPE_EXIT => $this->stockService->decreaseStock(
                $product,
                (int) $request->input('quantity'),
                null,
                $request->user(),
                $reason
            ),
            default => $this->stockService->adjustStock(
                $product,
                (int) $request->input('target_stock'),
                $request->user(),
                $reason
            ),
        };

        return redirect()
            ->route('admin.stock.index')
            ->with('status', 'Estoque atualizado com movimentacao registrada.');
    }
}
