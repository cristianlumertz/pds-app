<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Services\CouponService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
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
        $this->couponCode = (string) session('checkout.coupon_code', '');
        $this->recalculate();
    }

    public function updatedCouponCode(): void
    {
        $this->validateOnly('couponCode');
        $this->feedback = '';

        $normalizedCode = app(CouponService::class)->normalizeCode($this->couponCode);

        if ($normalizedCode !== session('checkout.coupon_code')) {
            session()->forget('checkout.coupon_code');
            $this->recalculate();
        }
    }

    public function applyCoupon(): void
    {
        $this->validateOnly('couponCode');
        $this->feedback = '';
        $this->resetErrorBag('couponCode');

        if (trim($this->couponCode) === '') {
            session()->forget('checkout.coupon_code');
            $this->feedback = 'Cupom removido.';
            $this->recalculate();

            return;
        }

        session(['checkout.coupon_code' => app(CouponService::class)->normalizeCode($this->couponCode)]);
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
        $this->shippingCost = $this->subtotal > 299 ? 0.0 : 29.90;
        $this->discountAmount = $this->resolveDiscount(
            $this->subtotal,
            $this->shippingCost,
            (string) session('checkout.coupon_code', '')
        );

        $rawTotal = $this->subtotal + $this->shippingCost - $this->discountAmount;
        $this->totalAmount = max(0, round($rawTotal, 2));
    }

    private function resolveDiscount(float $subtotal, float $shippingCost, string $couponCode): float
    {
        $code = app(CouponService::class)->normalizeCode($couponCode);

        if ($code === null) {
            session()->forget('checkout.coupon_code');
            return 0;
        }

        $couponService = app(CouponService::class);
        $coupon = $couponService->findByCode($code);

        if (! $coupon) {
            session()->forget('checkout.coupon_code');
            $this->addError('couponCode', 'Cupom inválido ou expirado.');

            return 0;
        }

        try {
            $discount = $couponService->calculateDiscount($coupon, $subtotal, $shippingCost);
        } catch (ValidationException $exception) {
            session()->forget('checkout.coupon_code');

            foreach ($exception->errors() as $messages) {
                $this->addError('couponCode', $messages[0] ?? 'Cupom inválido ou expirado.');
                break;
            }

            return 0;
        }

        $this->couponCode = $code;
        session(['checkout.coupon_code' => $code]);
        $this->feedback = "Cupom {$code} aplicado.";

        return $discount;
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
