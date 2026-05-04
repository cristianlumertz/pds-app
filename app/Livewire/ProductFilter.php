<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
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

    #[Validate('nullable|integer|exists:categories,id')]
    public string $categoryId = '';

    #[Validate('nullable|numeric|min:0')]
    public string $minPrice = '';

    #[Validate('nullable|numeric|min:0')]
    public string $maxPrice = '';

    #[Validate('nullable|in:all,1,0')]
    public string $isActive = 'all';

    public int $perPage = 8;

    protected function messages(): array
    {
        return [
            'term.string' => 'O termo de busca deve ser um texto válido.',
            'term.max' => 'O termo de busca deve ter no máximo 100 caracteres.',
            'categoryId.integer' => 'Selecione uma categoria válida.',
            'categoryId.exists' => 'A categoria selecionada não existe.',
            'minPrice.numeric' => 'O preço mínimo deve ser numérico.',
            'minPrice.min' => 'O preço mínimo não pode ser negativo.',
            'maxPrice.numeric' => 'O preço máximo deve ser numérico.',
            'maxPrice.min' => 'O preço máximo não pode ser negativo.',
            'isActive.in' => 'Selecione um status de produto válido.',
        ];
    }

    public function updated(string $property): void
    {
        $this->validateOnly($property);
        $this->resetPage();

        if (
            $this->minPrice !== ''
            && $this->maxPrice !== ''
            && (float) $this->minPrice > (float) $this->maxPrice
        ) {
            $this->addError('maxPrice', 'O preço máximo deve ser maior ou igual ao preço mínimo.');
        } else {
            $this->resetValidation('maxPrice');
        }
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
        $this->reset('categoryId', 'minPrice', 'maxPrice', 'isActive');
        $this->isActive = 'all';
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
            ->with('category')
            ->orderBy('name');

        if ($this->term !== '') {
            $query->where('name', 'like', '%'.$this->term.'%');
        }

        if ($this->categoryId !== '') {
            $query->where('category_id', (int) $this->categoryId);
        }

        if ($this->minPrice !== '') {
            $query->where('price', '>=', (float) $this->minPrice);
        }

        if ($this->maxPrice !== '') {
            $query->where('price', '<=', (float) $this->maxPrice);
        }

        if ($this->isActive !== 'all') {
            $query->where('is_active', $this->isActive === '1');
        }

        $products = $query->paginate($this->perPage);

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('livewire.product-filter', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
