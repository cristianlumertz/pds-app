<?php

namespace App\Livewire;

use App\Models\Cart;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CartSummary extends Component
{
    #[Validate('nullable|string|max:30')]
    public string $couponCode = '';

    public float $subtotal = 0.0;

    public float $shippingCost = 0.0;

    public float $discountAmount = 0.0;

    public float $totalAmount = 0.0;

    public string $feedback = '';

    protected function messages(): array
    {
        return [
            'couponCode.string' => 'O cupom deve ser um texto válido.',
            'couponCode.max' => 'O cupom deve ter no máximo 30 caracteres.',
        ];
    }

    public function mount(): void
    {
        $this->recalculate();
    }

    public function updatedCouponCode(): void
    {
        $this->validateOnly('couponCode');
        $this->feedback = '';
        $this->recalculate();
    }

    public function applyCoupon(): void
    {
        $this->validateOnly('couponCode');
        $this->feedback = '';
        $this->recalculate();
    }

    #[On('cart:updated')]
    #[On('cart:item-added')]
    #[On('cart:item-removed')]
    #[On('cart:cleared')]
    public function refreshSummary(): void
    {
        $this->recalculate();
    }

    private function recalculate(): void
    {
        $cart = $this->currentCart();

        if (! $cart) {
            $this->subtotal = 0;
            $this->shippingCost = 0;
            $this->discountAmount = 0;
            $this->totalAmount = 0;

            return;
        }

        $cart->calculateTotal();

        $this->subtotal = (float) $cart->total_price;
        $this->shippingCost = $this->subtotal >= 300 ? 0.0 : 24.90;
        $this->discountAmount = $this->resolveDiscount($this->subtotal, $this->couponCode);

        $rawTotal = $this->subtotal + $this->shippingCost - $this->discountAmount;
        $this->totalAmount = max(0, round($rawTotal, 2));
    }

    private function resolveDiscount(float $subtotal, string $couponCode): float
    {
        $coupon = strtoupper(trim($couponCode));

        if ($coupon === '') {
            return 0;
        }

        if ($coupon === 'OBRA10') {
            $this->feedback = 'Cupom OBRA10 aplicado: 10% de desconto.';

            return round($subtotal * 0.10, 2);
        }

        if ($coupon === 'FRETEGRATIS') {
            $this->feedback = 'Cupom FRETEGRATIS aplicado: frete zerado.';
            $this->shippingCost = 0.0;

            return 0;
        }

        $this->addError('couponCode', 'Cupom inválido ou expirado.');

        return 0;
    }

    private function currentCart(): ?Cart
    {
        $user = Auth::user();

        if (! $user) {
            return null;
        }

        return $user->carts()->latest('id')->first();
    }

    public function render(): View
    {
        return view('livewire.cart-summary');
    }
}
