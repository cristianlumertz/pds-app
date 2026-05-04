<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Contracts\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->withCount([
                'products' => fn ($query) => $query->where('is_active', true),
            ])
            ->orderBy('name')
            ->get();

        return view('store.categories.index', compact('categories'));
    }

    public function show(Category $category): View
    {
        abort_unless($category->is_active, 404);

        $products = $category->products()
            ->where('is_active', true)
            ->with(['category', 'productImages'])
            ->orderBy('name')
            ->paginate(12);

        return view('store.categories.show', [
            'category' => $category,
            'products' => $products,
        ]);
    }
}
