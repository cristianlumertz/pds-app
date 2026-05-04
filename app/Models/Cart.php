<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Cart extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'total_price',
        'item_count',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
            'item_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function addItem(Product $product, int $quantity = 1): CartItem
    {
        $quantity = max(1, $quantity);

        $item = $this->items()->firstOrNew([
            'product_id' => $product->id,
        ]);

        $item->quantity = ((int) $item->quantity) + $quantity;
        $item->price = (float) $product->price;
        $item->save();

        $this->calculateTotal();

        return $item;
    }

    public function removeItem(int $productId): void
    {
        $this->items()->where('product_id', $productId)->delete();
        $this->calculateTotal();
    }

    public function calculateTotal(): void
    {
        $totals = $this->items()
            ->selectRaw('COALESCE(SUM(quantity * price), 0) AS total_price, COALESCE(SUM(quantity), 0) AS item_count')
            ->first();

        $this->forceFill([
            'total_price' => (float) ($totals->total_price ?? 0),
            'item_count' => (int) ($totals->item_count ?? 0),
        ])->save();
    }

    public function isEmpty(): bool
    {
        return ! $this->items()->exists();
    }

    public function clear(): void
    {
        DB::transaction(function (): void {
            $this->items()->delete();
            $this->forceFill([
                'total_price' => 0,
                'item_count' => 0,
            ])->save();
        });
    }
}
