<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CartPage extends Component
{
    /**
     * @var array<int, int>
     */
    #[Validate('array')]
    public array $quantities = [];

    public string $feedback = '';

    protected function messages(): array
    {
        return [
            'quantities.array' => 'Os itens do carrinho são inválidos.',
            'quantities.*.integer' => 'A quantidade deve ser um número inteiro.',
            'quantities.*.min' => 'A quantidade mínima é 1.',
            'quantities.*.max' => 'A quantidade máxima por item é 99.',
        ];
    }

    public function mount(): void
    {
        $this->syncFromDatabase();
    }

    #[On('cart:updated')]
    #[On('cart:item-added')]
    public function syncFromDatabase(): void
    {
        $cart = $this->currentCart();

        if (! $cart) {
            $this->quantities = [];

            return;
        }

        $this->quantities = $cart->items()
            ->pluck('quantity', 'id')
            ->map(fn ($quantity): int => (int) $quantity)
            ->toArray();
    }

    public function increase(int $itemId): void
    {
        $this->changeByDelta($itemId, 1);
    }

    public function decrease(int $itemId): void
    {
        $this->changeByDelta($itemId, -1);
    }

    public function updateQuantity(int $itemId): void
    {
        $value = $this->quantities[$itemId] ?? null;

        if (! is_numeric($value)) {
            $this->addError("quantities.$itemId", 'A quantidade deve ser um número válido.');

            return;
        }

        $newQuantity = (int) $value;

        if ($newQuantity < 1) {
            $this->addError("quantities.$itemId", 'A quantidade mínima é 1.');

            return;
        }

        if ($newQuantity > 99) {
            $this->addError("quantities.$itemId", 'A quantidade máxima por item é 99.');

            return;
        }

        $cart = $this->currentCart();
        if (! $cart) {
            $this->addError('cart', 'Carrinho não encontrado para este usuário.');

            return;
        }

        try {
            DB::transaction(function () use ($cart, $itemId, $newQuantity): void {
                /** @var CartItem|null $item */
                $item = CartItem::query()
                    ->where('cart_id', $cart->id)
                    ->whereKey($itemId)
                    ->lockForUpdate()
                    ->first();

                if (! $item) {
                    throw ValidationException::withMessages([
                        'cart' => 'Item do carrinho não encontrado.',
                    ]);
                }

                /** @var Product|null $product */
                $product = Product::query()
                    ->whereKey($item->product_id)
                    ->lockForUpdate()
                    ->first();

                if (! $product) {
                    throw ValidationException::withMessages([
                        'cart' => 'Produto do item não foi encontrado.',
                    ]);
                }

                if (! $product->is_active) {
                    throw ValidationException::withMessages([
                        "quantities.$itemId" => 'Este produto não está mais disponível.',
                    ]);
                }

                app(StockService::class)->validateAvailableStock($product, $newQuantity);

                $item->quantity = $newQuantity;
                $item->price = (float) $product->price;
                $item->save();

                $cart->calculateTotal();
            });
        } catch (ValidationException $exception) {
            foreach ($exception->errors() as $field => $messages) {
                $this->addError($field, $messages[0] ?? 'Erro de validação.');
            }

            return;
        }

        $this->feedback = 'Quantidade atualizada com sucesso.';
        $this->resetValidation("quantities.$itemId");
        $this->dispatch('cart:updated');
        $this->syncFromDatabase();
    }

    public function removeItem(int $itemId): void
    {
        $cart = $this->currentCart();
        if (! $cart) {
            $this->addError('cart', 'Carrinho não encontrado para este usuário.');

            return;
        }

        try {
            DB::transaction(function () use ($cart, $itemId): void {
                /** @var CartItem|null $item */
                $item = CartItem::query()
                    ->where('cart_id', $cart->id)
                    ->whereKey($itemId)
                    ->lockForUpdate()
                    ->first();

                if (! $item) {
                    throw ValidationException::withMessages([
                        'cart' => 'Item do carrinho não encontrado.',
                    ]);
                }

                $item->delete();
                $cart->calculateTotal();
            });
        } catch (ValidationException $exception) {
            foreach ($exception->errors() as $field => $messages) {
                $this->addError($field, $messages[0] ?? 'Erro de validação.');
            }

            return;
        }

        unset($this->quantities[$itemId]);
        $this->feedback = 'Item removido do carrinho.';
        $this->dispatch('cart:updated');
        $this->syncFromDatabase();
    }

    private function changeByDelta(int $itemId, int $delta): void
    {
        $current = (int) ($this->quantities[$itemId] ?? 1);
        $newValue = $current + $delta;

        if ($newValue < 1) {
            $this->removeItem($itemId);

            return;
        }

        $this->quantities[$itemId] = $newValue;
        $this->updateQuantity($itemId);
    }

    private function currentCart(): ?Cart
    {
        $user = Auth::user();

        if (! $user) {
            return null;
        }

        return $user->carts()
            ->with(['items.product.productImages'])
            ->latest('id')
            ->first();
    }

    public function render(): View
    {
        $cart = $this->currentCart();
        $items = $cart?->items ?? collect();

        return view('livewire.cart-page', [
            'cart' => $cart,
            'items' => $items,
        ]);
    }
}
