<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'sku',
        'price',
        'stock',
        'is_active',
        'image_url',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function productImages(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    public function primaryImageUrl(): ?string
    {
        $image = $this->relationLoaded('productImages')
            ? $this->productImages->first()
            : $this->productImages()->first();

        return $image?->url ?? $this->image_url;
    }

    public function getDiscountedPrice(float $discountPercent = 10): float
    {
        $discountPercent = max(0, min(100, $discountPercent));
        $price = (float) $this->price;

        return round($price * (1 - ($discountPercent / 100)), 2);
    }

    public function isInStock(int $quantity = 1): bool
    {
        return (int) $this->stock >= max(1, $quantity);
    }

    public function decreaseStock(int $quantity = 1): bool
    {
        $quantity = max(1, $quantity);

        try {
            app(\App\Services\StockService::class)->decreaseStock(
                $this,
                $quantity,
                null,
                null,
                'Baixa de estoque via método legado do produto'
            );

            $this->refresh();

            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
