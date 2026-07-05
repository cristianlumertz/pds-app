<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;

    public const TYPE_FIXED = 'fixed';

    public const TYPE_PERCENTAGE = 'percentage';

    public const TYPE_FREE_SHIPPING = 'free_shipping';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'min_order_amount',
        'starts_at',
        'expires_at',
        'usage_limit',
        'used_count',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'usage_limit' => 'integer',
            'used_count' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function orderCoupons(): HasMany
    {
        return $this->hasMany(OrderCoupon::class);
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_coupons')
            ->withPivot('discount_amount')
            ->withTimestamps();
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function hasStarted(): bool
    {
        return $this->starts_at === null || ! $this->starts_at->isFuture();
    }

    public function hasUsageLimitReached(): bool
    {
        return $this->usage_limit !== null && (int) $this->used_count >= (int) $this->usage_limit;
    }

    public function isValidForAmount(float|int|string $amount): bool
    {
        $subtotal = (float) $amount;

        if (! $this->isActive() || ! $this->hasStarted() || $this->isExpired() || $this->hasUsageLimitReached()) {
            return false;
        }

        if ($this->min_order_amount !== null && $subtotal < (float) $this->min_order_amount) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float|int|string $subtotal, float|int|string $shippingAmount = 0): float
    {
        $subtotal = max(0, (float) $subtotal);
        $shippingAmount = max(0, (float) $shippingAmount);

        if (! $this->isValidForAmount($subtotal)) {
            return 0.0;
        }

        return match ((string) $this->discount_type) {
            self::TYPE_PERCENTAGE => round($subtotal * max(0, (float) $this->discount_value) / 100, 2),
            self::TYPE_FIXED => min($subtotal, round(max(0, (float) $this->discount_value), 2)),
            self::TYPE_FREE_SHIPPING => round($shippingAmount, 2),
            default => 0.0,
        };
    }
}
