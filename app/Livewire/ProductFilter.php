<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class ProductFilter extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    #[Validate('nullable|string|max:100')]
    public string $term = '';

    #[Url(as: 'category', except: '')]
    #[Validate('nullable|string|max:255')]
    public string $category = '';

    #[Url(as: 'min_price', except: '')]
    #[Validate('nullable|numeric|min:0')]
    public string $minPrice = '';

    #[Url(as: 'max_price', except: '')]
    #[Validate('nullable|numeric|min:0')]
    public string $maxPrice = '';

    #[Url(as: 'in_stock', except: false)]
    #[Validate('boolean')]
    public bool $inStock = false;

    #[Url(as: 'on_sale', except: false)]
    #[Validate('boolean')]
    public bool $onSale = false;

    #[Url(as: 'sort', except: 'relevance')]
    #[Validate('in:relevance,price_asc,price_desc,newest')]
    public string $sort = 'relevance';

    public int $perPage = 9;

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'term.string' => 'O termo de busca deve ser um texto válido.',
            'term.max' => 'O termo de busca deve ter no máximo 100 caracteres.',
            'category.string' => 'Selecione uma categoria válida.',
            'category.max' => 'Selecione uma categoria válida.',
            'minPrice.numeric' => 'O preço mínimo deve ser numérico.',
            'minPrice.min' => 'O preço mínimo não pode ser negativo.',
            'maxPrice.numeric' => 'O preço máximo deve ser numérico.',
            'maxPrice.min' => 'O preço máximo não pode ser negativo.',
            'inStock.boolean' => 'O filtro de estoque é inválido.',
            'onSale.boolean' => 'O filtro de promoção é inválido.',
            'sort.in' => 'Selecione uma ordenação válida.',
        ];
    }

    public function updated(string $property): void
    {
        if ($property === 'sort') {
            $this->validateOnly('sort');
            $this->resetPage();
        }
    }

    public function applyFilters(): void
    {
        $this->validate();

        if (
            $this->minPrice !== ''
            && $this->maxPrice !== ''
            && (float) $this->minPrice > (float) $this->maxPrice
        ) {
            $this->addError('maxPrice', 'O preço máximo deve ser maior ou igual ao preço mínimo.');

            return;
        }

        $this->resetValidation();
        $this->resetPage();
    }

    #[On('header-search:updated')]
    public function updateFromHeader(string $term = ''): void
    {
        $this->term = trim($term);
        $this->resetPage();

        if ($this->term !== '') {
            $this->validateOnly('term');
        } else {
            $this->resetValidation('term');
        }
    }

    #[On('product-filter:clear')]
    public function clearFilters(): void
    {
        $this->reset('term', 'category', 'minPrice', 'maxPrice', 'inStock', 'onSale', 'sort');
        $this->sort = 'relevance';
        $this->resetValidation();
        $this->resetPage();
    }

    #[On('product-filter:refresh')]
    public function refreshProducts(): void
    {
        // Livewire re-render triggered by event.
    }

    public function render(): View
    {
        $query = Product::query()
            ->with(['category', 'productImages'])
            ->where('is_active', true);

        if ($this->term !== '') {
            $search = trim($this->term);
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('name', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%')
                    ->orWhere('sku', 'like', '%'.$search.'%');
            });
        }

        if ($this->category !== '') {
            $query->whereHas('category', function ($builder): void {
                $builder->where('slug', $this->category);
            });
        }

        if ($this->minPrice !== '') {
            $query->where('price', '>=', (float) $this->minPrice);
        }

        if ($this->maxPrice !== '') {
            $query->where('price', '<=', (float) $this->maxPrice);
        }

        if ($this->inStock) {
            $query->where('stock', '>', 0);
        }

        if ($this->onSale) {
            if (Schema::hasColumn('products', 'is_promotional')) {
                $query->where('is_promotional', true);
            } elseif (Schema::hasColumn('products', 'old_price')) {
                $query->whereNotNull('old_price')
                    ->whereColumn('old_price', '>', 'price');
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        match ($this->sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'newest' => $query->latest(),
            default => $query->orderBy('name'),
        };

        $products = $query->paginate($this->perPage);

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return view('livewire.product-filter', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
