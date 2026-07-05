<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockService
{
    /**
     * @throws ValidationException
     */
    public function validateAvailableStock(Product $product, int $quantity): void
    {
        $this->validatePositiveQuantity($quantity);

        if ((int) $product->stock < $quantity) {
            throw ValidationException::withMessages([
                'stock' => "Estoque insuficiente para o produto {$product->name}.",
            ]);
        }
    }

    /**
     * @throws ValidationException
     */
    public function decreaseStock(
        Product $product,
        int $quantity,
        ?Order $order = null,
        ?User $user = null,
        ?string $reason = null
    ): StockMovement {
        $this->validatePositiveQuantity($quantity);

        return DB::transaction(function () use ($product, $quantity, $order, $user, $reason): StockMovement {
            /** @var Product $lockedProduct */
            $lockedProduct = Product::query()
                ->whereKey($product->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->validateAvailableStock($lockedProduct, $quantity);

            $lockedProduct->decrement('stock', $quantity);

            return $this->recordMovement(
                $lockedProduct,
                StockMovement::TYPE_EXIT,
                $quantity,
                $order,
                $user,
                $reason
            );
        });
    }

    /**
     * @throws ValidationException
     */
    public function increaseStock(
        Product $product,
        int $quantity,
        ?Order $order = null,
        ?User $user = null,
        ?string $reason = null
    ): StockMovement {
        $this->validatePositiveQuantity($quantity);

        return DB::transaction(function () use ($product, $quantity, $order, $user, $reason): StockMovement {
            /** @var Product $lockedProduct */
            $lockedProduct = Product::query()
                ->whereKey($product->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedProduct->increment('stock', $quantity);

            return $this->recordMovement(
                $lockedProduct,
                StockMovement::TYPE_ENTRY,
                $quantity,
                $order,
                $user,
                $reason
            );
        });
    }

    /**
     * @throws ValidationException
     */
    public function adjustStock(
        Product $product,
        int $newQuantity,
        ?User $user = null,
        ?string $reason = null
    ): ?StockMovement {
        if ($newQuantity < 0) {
            throw ValidationException::withMessages([
                'stock' => 'O estoque não pode ser negativo.',
            ]);
        }

        return DB::transaction(function () use ($product, $newQuantity, $user, $reason): ?StockMovement {
            /** @var Product $lockedProduct */
            $lockedProduct = Product::query()
                ->whereKey($product->id)
                ->lockForUpdate()
                ->firstOrFail();

            $oldQuantity = (int) $lockedProduct->stock;
            $diff = $newQuantity - $oldQuantity;

            if ($diff === 0) {
                return null;
            }

            $lockedProduct->forceFill([
                'stock' => $newQuantity,
            ])->save();

            return $this->recordMovement(
                $lockedProduct,
                StockMovement::TYPE_ADJUSTMENT,
                $diff,
                null,
                $user,
                $reason
            );
        });
    }

    public function restoreOrderStock(Order $order, ?User $user = null, ?string $reason = null): void
    {
        DB::transaction(function () use ($order, $user, $reason): void {
            $alreadyRestored = StockMovement::query()
                ->where('order_id', $order->id)
                ->where('type', StockMovement::TYPE_CANCELLATION)
                ->exists();

            if ($alreadyRestored) {
                return;
            }

            $order->loadMissing('items');

            foreach ($order->items as $item) {
                /** @var Product|null $product */
                $product = Product::query()
                    ->whereKey($item->product_id)
                    ->lockForUpdate()
                    ->first();

                if (! $product) {
                    continue;
                }

                $quantity = (int) $item->quantity;
                $this->validatePositiveQuantity($quantity);

                $product->increment('stock', $quantity);

                $this->recordMovement(
                    $product,
                    StockMovement::TYPE_CANCELLATION,
                    $quantity,
                    $order,
                    $user,
                    $reason
                );
            }
        });
    }

    /**
     * @throws ValidationException
     */
    private function validatePositiveQuantity(int $quantity): void
    {
        if ($quantity < 1) {
            throw ValidationException::withMessages([
                'quantity' => 'A quantidade deve ser maior que zero.',
            ]);
        }
    }

    private function recordMovement(
        Product $product,
        string $type,
        int $quantity,
        ?Order $order,
        ?User $user,
        ?string $reason
    ): StockMovement {
        return StockMovement::query()->create([
            'product_id' => $product->id,
            'user_id' => $user?->id,
            'order_id' => $order?->id,
            'type' => $type,
            'quantity' => $quantity,
            'reason' => $reason,
        ]);
    }
}
