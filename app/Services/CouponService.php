<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderCoupon;
use Illuminate\Validation\ValidationException;

class CouponService
{
    public function findByCode(?string $code): ?Coupon
    {
        $code = $this->normalizeCode($code);

        if ($code === null) {
            return null;
        }

        return Coupon::query()
            ->where('code', $code)
            ->first();
    }

    /**
     * @throws ValidationException
     */
    public function validateCoupon(Coupon $coupon, float $subtotal): void
    {
        if (! $coupon->isActive()) {
            throw ValidationException::withMessages([
                'coupon' => 'Este cupom não está ativo.',
            ]);
        }

        if (! $coupon->hasStarted()) {
            throw ValidationException::withMessages([
                'coupon' => 'Este cupom ainda não está disponível.',
            ]);
        }

        if ($coupon->isExpired()) {
            throw ValidationException::withMessages([
                'coupon' => 'Este cupom está expirado.',
            ]);
        }

        if ($coupon->hasUsageLimitReached()) {
            throw ValidationException::withMessages([
                'coupon' => 'Este cupom atingiu o limite de uso.',
            ]);
        }

        if ($coupon->min_order_amount !== null && $subtotal < (float) $coupon->min_order_amount) {
            throw ValidationException::withMessages([
                'coupon' => 'O subtotal do pedido não atinge o valor mínimo deste cupom.',
            ]);
        }
    }

    /**
     * @throws ValidationException
     */
    public function calculateDiscount(Coupon $coupon, float $subtotal, float $shippingAmount = 0): float
    {
        $subtotal = max(0, round($subtotal, 2));
        $shippingAmount = max(0, round($shippingAmount, 2));

        $this->validateCoupon($coupon, $subtotal);

        $discount = match ((string) $coupon->discount_type) {
            Coupon::TYPE_PERCENTAGE => round($subtotal * max(0, (float) $coupon->discount_value) / 100, 2),
            Coupon::TYPE_FIXED => min($subtotal, round(max(0, (float) $coupon->discount_value), 2)),
            Coupon::TYPE_FREE_SHIPPING => $shippingAmount,
            default => 0.0,
        };

        return min($discount, round($subtotal + $shippingAmount, 2));
    }

    public function applyCouponToOrder(Order $order, Coupon $coupon, float $discountAmount): OrderCoupon
    {
        return OrderCoupon::query()->create([
            'order_id' => $order->id,
            'coupon_id' => $coupon->id,
            'discount_amount' => max(0, round($discountAmount, 2)),
        ]);
    }

    public function normalizeCode(?string $code): ?string
    {
        $code = strtoupper(trim((string) $code));

        return $code === '' ? null : $code;
    }
}
