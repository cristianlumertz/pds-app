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
        $featuredProducts = Product::query()
            ->with('category')
            ->where('is_active', true)
            ->latest()
            ->take(8)
            ->get();

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('store.home', compact('featuredProducts', 'categories'));
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

        $relatedProducts = Product::query()
            ->where('is_active', true)
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('store.products.show', compact('product', 'relatedProducts'));
    }
}
