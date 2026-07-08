<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\StockService;
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
    public function __construct(
        private readonly StockService $stockService
    ) {
    }

    public function index(Request $request): View
    {
        $products = Product::query()
            ->with(['category', 'productImages'])
            ->when($request->filled('q'), function ($query) use ($request): void {
                $search = trim((string) $request->query('q'));

                $query->where(function ($builder) use ($search): void {
                    $builder
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($request->integer('category_id'), fn ($query, int $categoryId) => $query->where('category_id', $categoryId))
            ->when($request->query('status') === 'active', fn ($query) => $query->where('is_active', true))
            ->when($request->query('status') === 'inactive', fn ($query) => $query->where('is_active', false))
            ->when($request->query('stock') === 'low', fn ($query) => $query->whereBetween('stock', [1, 5]))
            ->when($request->query('stock') === 'out', fn ($query) => $query->where('stock', 0))
            ->when($request->filled('min_price'), fn ($query) => $query->where('price', '>=', (float) $request->query('min_price')))
            ->when($request->filled('max_price'), fn ($query) => $query->where('price', '<=', (float) $request->query('max_price')))
            ->latest()
            ->paginate(12);

        $categories = Category::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.products.index', compact('products', 'categories'));
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
            'image_url' => ['nullable', 'url', 'max:255', $this->secureExternalUrlRule()],
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
                'stock' => 0,
                'image_url' => $data['image_url'] ?? null,
                'is_active' => $request->boolean('is_active'),
            ]);

            if ((int) $data['stock'] > 0) {
                $this->stockService->increaseStock(
                    $product,
                    (int) $data['stock'],
                    null,
                    $request->user(),
                    'Entrada inicial de estoque no painel admin'
                );
            }

            $this->storeUploadedImages($product, $request);
        });

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Produto criado com sucesso.');
    }

    public function edit(Product $product): View
    {
        $product->load([
            'productImages',
            'stockMovements' => fn ($query) => $query->with(['user', 'order'])->latest()->take(10),
        ]);

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
            'image_url' => ['nullable', 'url', 'max:255', $this->secureExternalUrlRule()],
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
                'image_url' => $data['image_url'] ?? null,
                'is_active' => $request->boolean('is_active'),
            ]);

            $this->stockService->adjustStock(
                $product,
                (int) $data['stock'],
                $request->user(),
                'Ajuste de estoque no painel admin'
            );

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

    private function secureExternalUrlRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            if (! app()->environment('production') || ! is_string($value) || trim($value) === '') {
                return;
            }

            if (strtolower((string) parse_url($value, PHP_URL_SCHEME)) !== 'https') {
                $fail('Em produção, a URL da imagem precisa usar HTTPS.');
            }
        };
    }
}
