<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use RuntimeException;

class AddToCart extends Component
{
    public int $productId;
    public bool $compact = false;

    #[Validate('required|integer|min:1|max:99')]
    public int $quantity = 1;

    public string $feedback = '';

    public function mount(int $productId, int $initialQuantity = 1, bool $compact = false): void
    {
        $this->productId = $productId;
        $this->quantity = max(1, min(99, $initialQuantity));
        $this->compact = $compact;
    }

    protected function messages(): array
    {
        return [
            'quantity.required' => 'Informe a quantidade desejada.',
            'quantity.integer' => 'A quantidade deve ser um número inteiro.',
            'quantity.min' => 'A quantidade mínima é 1.',
            'quantity.max' => 'A quantidade máxima por operação é 99.',
        ];
    }

    public function updatedQuantity(): void
    {
        $this->validateOnly('quantity');
        $this->feedback = '';
    }

    public function add(CartService $cartService): void
    {
        $this->validate();
        $this->feedback = '';

        if (! Auth::check()) {
            $this->redirectRoute('login');

            return;
        }

        $user = Auth::user();
        if (! $user) {
            throw new RuntimeException('Usuário autenticado não encontrado.');
        }

        $product = Product::query()
            ->whereKey($this->productId)
            ->where('is_active', true)
            ->first();

        if (! $product) {
            $this->addError('product', 'Produto não encontrado ou indisponível.');

            return;
        }

        /** @var Cart $cart */
        $cart = $user->carts()->firstOrCreate(
            [],
            [
                'total_price' => 0,
                'item_count' => 0,
            ]
        );

        try {
            $cartService->addItem($cart, $product, $this->quantity);
            $this->feedback = 'Produto adicionado ao carrinho com sucesso.';

            $this->dispatch('cart:updated');
            $this->dispatch('cart:item-added', productId: $this->productId, quantity: $this->quantity);
        } catch (ValidationException $exception) {
            foreach ($exception->errors() as $field => $messages) {
                $this->addError($field, $messages[0] ?? 'Erro de validação.');
            }
        } catch (\Throwable $exception) {
            $this->addError('cart', 'Não foi possível adicionar o item ao carrinho. Tente novamente.');
        }
    }

    #[On('cart:set-quantity')]
    public function setQuantity(int $quantity): void
    {
        $this->quantity = max(1, min(99, $quantity));
        $this->resetValidation('quantity');
    }

    public function render(): View
    {
        return view('livewire.add-to-cart');
    }
}
