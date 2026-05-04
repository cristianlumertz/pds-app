<?php

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class ProductSearch extends Component
{
    use WithPagination;

    #[Validate('nullable|string|min:2|max:100')]
    public string $term = '';

    public int $perPage = 8;

    protected function messages(): array
    {
        return [
            'term.min' => 'Digite pelo menos 2 caracteres para buscar.',
            'term.max' => 'A busca deve ter no máximo 100 caracteres.',
            'term.string' => 'A busca deve ser um texto válido.',
        ];
    }

    public function updatedTerm(): void
    {
        $this->resetPage();

        if ($this->term !== '') {
            $this->validateOnly('term');
        } else {
            $this->resetValidation('term');
        }
    }

    #[On('product-search:clear')]
    public function clearSearch(): void
    {
        $this->reset('term');
        $this->resetValidation();
        $this->resetPage();
    }

    #[On('product-search:refresh')]
    public function refreshProducts(): void
    {
        // Livewire re-render triggered by event.
    }

    public function render(): View
    {
        $products = Product::query()
            ->where('is_active', true)
            ->when($this->term !== '', function ($query): void {
                $query->where('name', 'like', '%'.$this->term.'%');
            })
            ->orderBy('name')
            ->paginate($this->perPage);

        return view('livewire.product-search', [
            'products' => $products,
        ]);
    }
}
