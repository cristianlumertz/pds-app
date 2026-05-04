<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->with('category')
            ->latest()
            ->paginate(12);

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug'],
            'description' => ['nullable', 'string'],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image_url' => ['nullable', 'url', 'max:255'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'image', 'max:4096'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $slug = $data['slug'] ?? Str::slug($data['name']);

        DB::transaction(function () use ($data, $request, $slug): void {
            $product = Product::query()->create([
                'category_id' => (int) $data['category_id'],
                'name' => $data['name'],
                'slug' => $this->ensureUniqueSlug($slug),
                'description' => $data['description'] ?? null,
                'sku' => $data['sku'],
                'price' => $data['price'],
                'stock' => (int) $data['stock'],
                'image_url' => $data['image_url'] ?? null,
                'is_active' => $request->boolean('is_active'),
            ]);

            $this->storeUploadedImages($product, $request);
        });

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Produto criado com sucesso.');
    }

    public function edit(Product $product): View
    {
        $product->load('productImages');

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'slug')->ignore($product->id),
            ],
            'description' => ['nullable', 'string'],
            'sku' => [
                'required',
                'string',
                'max:100',
                Rule::unique('products', 'sku')->ignore($product->id),
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image_url' => ['nullable', 'url', 'max:255'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'image', 'max:4096'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $slug = $data['slug'] ?? Str::slug($data['name']);

        DB::transaction(function () use ($data, $request, $slug, $product): void {
            $product->update([
                'category_id' => (int) $data['category_id'],
                'name' => $data['name'],
                'slug' => $this->ensureUniqueSlug($slug, $product->id),
                'description' => $data['description'] ?? null,
                'sku' => $data['sku'],
                'price' => $data['price'],
                'stock' => (int) $data['stock'],
                'image_url' => $data['image_url'] ?? null,
                'is_active' => $request->boolean('is_active'),
            ]);

            $this->storeUploadedImages($product, $request);
        });

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Produto atualizado com sucesso.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->load('productImages');

        foreach ($product->productImages as $image) {
            if (str_starts_with($image->url, '/storage/')) {
                $relativePath = ltrim(str_replace('/storage/', '', $image->url), '/');
                Storage::disk('public')->delete($relativePath);
            }
        }

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Produto removido com sucesso.');
    }

    private function storeUploadedImages(Product $product, Request $request): void
    {
        $files = $request->file('images', []);
        if ($files === []) {
            return;
        }

        $nextOrder = ((int) ProductImage::query()
            ->where('product_id', $product->id)
            ->max('order')) + 1;

        foreach ($files as $index => $file) {
            if (! $file) {
                continue;
            }

            $storedPath = $file->store('products', 'public');
            $publicUrl = Storage::url($storedPath);

            $payload = [
                'product_id' => $product->id,
                'url' => $publicUrl,
                'alt_text' => $product->name.' - imagem '.($nextOrder + $index),
                'order' => $nextOrder + $index,
            ];

            if (Schema::hasColumn('product_images', 'image_url')) {
                $payload['image_url'] = $publicUrl;
            }

            ProductImage::query()->create($payload);
        }

        $firstImageUrl = ProductImage::query()
            ->where('product_id', $product->id)
            ->orderBy('order')
            ->value('url');

        if ($firstImageUrl && ! $product->image_url) {
            $product->forceFill(['image_url' => $firstImageUrl])->save();
        }
    }

    private function ensureUniqueSlug(string $slug, ?int $exceptId = null): string
    {
        $base = Str::slug($slug) ?: 'produto';
        $candidate = $base;
        $counter = 1;

        while (
            Product::query()
                ->when($exceptId, fn ($query) => $query->whereKeyNot($exceptId))
                ->where('slug', $candidate)
                ->exists()
        ) {
            $candidate = "{$base}-{$counter}";
            $counter++;
        }

        return $candidate;
    }
}
