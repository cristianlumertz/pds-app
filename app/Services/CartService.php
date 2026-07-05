<?php
// app/Services/CartService.php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class CartService
{
    public function __construct(
        private readonly StockService $stockService
    ) {
    }

    /**
     
     * @throws ValidationException
     */
    public function addItem(Cart $cart, Product $product, int $quantity = 1): CartItem
    {
        if ($quantity < 1) {
            throw ValidationException::withMessages([
                'quantity' => 'A quantidade deve ser no mínimo 1.',
            ]);
        }

        if (! $product->is_active) {
            throw ValidationException::withMessages([
                'product' => 'Este produto está inativo e não pode ser adicionado ao carrinho.',
            ]);
        }

        try {
            /** @var CartItem $item */
            $item = DB::transaction(function () use ($cart, $product, $quantity): CartItem {
                /** @var Product $lockedProduct */
                $lockedProduct = Product::query()
                    ->whereKey($product->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (! $lockedProduct->is_active) {
                    throw ValidationException::withMessages([
                        'product' => 'Este produto está inativo e não pode ser adicionado ao carrinho.',
                    ]);
                }

                $item = $cart->items()->firstOrNew([
                    'product_id' => $lockedProduct->id,
                ]);

                $newQuantity = ((int) $item->quantity) + $quantity;
                $this->stockService->validateAvailableStock($lockedProduct, $newQuantity);

                $item->quantity = $newQuantity;
                $item->price = (float) $lockedProduct->price;
                $item->save();

                $this->calculateTotal($cart);

                return $item;
            });

            return $item;
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            throw new RuntimeException('Erro ao adicionar item no carrinho. Tente novamente.', 0, $exception);
        }
    }
    /**
     * @throws ValidationException
     */
    public function removeItem(Cart $cart, int $productId): void
    {
        if ($productId < 1) {
            throw ValidationException::withMessages([
                'product_id' => 'Produto inválido para remoção do carrinho.',
            ]);
        }

        try {
            DB::transaction(function () use ($cart, $productId): void {
                /** @var CartItem|null $item */
                $item = $cart->items()
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                if (! $item) {
                    throw ValidationException::withMessages([
                        'cart_item' => 'O item informado não foi encontrado no carrinho.',
                    ]);
                }

                $item->delete();
                $this->calculateTotal($cart);
            });
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            throw new RuntimeException('Erro ao remover item do carrinho. Tente novamente.', 0, $exception);
        }
    }

    public function calculateTotal(Cart $cart): float
    {
        try {
            $totals = $cart->items()
                ->selectRaw('COALESCE(SUM(quantity * price), 0) AS total_price, COALESCE(SUM(quantity), 0) AS item_count')
                ->first();

            $totalPrice = (float) ($totals->total_price ?? 0);
            $itemCount = (int) ($totals->item_count ?? 0);

            $cart->forceFill([
                'total_price' => $totalPrice,
                'item_count' => $itemCount,
            ])->save();

            return $totalPrice;
        } catch (Throwable $exception) {
            throw new RuntimeException('Erro ao calcular os totais do carrinho.', 0, $exception);
        }
    }

    public function isEmpty(Cart $cart): bool
    {
        try {
            return ! $cart->items()->exists();
        } catch (Throwable $exception) {
            throw new RuntimeException('Erro ao verificar o estado do carrinho.', 0, $exception);
        }
    }

    public function clear(Cart $cart): void
    {
        try {
            DB::transaction(function () use ($cart): void {
                $cart->items()->delete();
                $cart->forceFill([
                    'total_price' => 0,
                    'item_count' => 0,
                ])->save();
            });
        } catch (Throwable $exception) {
            throw new RuntimeException('Erro ao limpar o carrinho. Tente novamente.', 0, $exception);
        }
    }
}
