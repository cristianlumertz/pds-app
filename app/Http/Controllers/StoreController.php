<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function home(): View
    {
        $baseProductQuery = fn () => Product::query()
            ->with(['category', 'productImages'])
            ->where('is_active', true);

        $novidades = $baseProductQuery()
            ->latest()
            ->take(12)
            ->get();

        $ofertas = $baseProductQuery()
            ->orderBy('price')
            ->latest()
            ->take(12)
            ->get();

        $categories = Category::query()
            ->where('is_active', true)
            ->withCount(['products' => fn ($query) => $query->where('is_active', true)])
            ->orderBy('name')
            ->get();

        $categorySections = collect([
            'ferramentas' => 'Ferramentas',
            'eletrica' => 'Elétrica',
            'hidraulica' => 'Hidráulica',
            'tintas-e-acabamentos' => 'Tintas',
            'materiais-basicos' => 'Materiais Básicos',
        ])->map(function (string $title, string $slug) use ($baseProductQuery): ?array {
            $products = $baseProductQuery()
                ->whereHas('category', fn ($query) => $query->where('slug', $slug))
                ->latest()
                ->take(12)
                ->get();

            if ($products->isEmpty()) {
                return null;
            }

            return [
                'title' => $title,
                'slug' => $slug,
                'products' => $products,
            ];
        })->filter()->values();

        return view('store.home', compact('novidades', 'ofertas', 'categorySections', 'categories'));
    }

    public function products(Request $request): View
    {
        $query = Product::query()
            ->with('category')
            ->where('is_active', true);

        if ($request->filled('q')) {
            $search = (string) $request->string('q');
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $category = (string) $request->string('category');
            $query->whereHas('category', function ($builder) use ($category): void {
                $builder->where('slug', $category);
            });
        }

        $products = $query
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('store.products.index', compact('products', 'categories'));
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);

        $product->load(['category', 'productImages']);

        $relatedProducts = Product::query()
            ->with(['category', 'productImages'])
            ->where('is_active', true)
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('store.products.show', compact('product', 'relatedProducts'));
    }
}
