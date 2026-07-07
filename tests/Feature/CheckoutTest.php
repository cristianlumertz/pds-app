<?php

namespace Tests\Feature;

use App\Jobs\SendOrderConfirmationEmail;
use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.pagarme.secret_key' => 'sk_test_checkout',
            'services.pagarme.base_url' => 'https://sdx-api.pagar.me/core/v5',
        ]);

        Http::fake([
            'https://sdx-api.pagar.me/core/v5/paymentlinks' => Http::response([
                'id' => 'plink_checkout_test',
                'url' => 'https://checkout.pagar.me/test/plink_checkout_test',
            ], 201),
        ]);
    }

    public function test_usuario_pode_acessar_checkout_com_carrinho_nao_vazio(): void
    {
        $user = User::factory()->create();
        Address::factory()->create(['user_id' => $user->id]);
        $this->createCartWithItem($user);

        $this->actingAs($user)
            ->get(route('checkout.step1'))
            ->assertOk()
            ->assertViewIs('store.checkout.step1');
    }

    public function test_usuario_sem_carrinho_e_redirecionado_da_pagina_de_checkout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('checkout.step1'))
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('status', 'Seu carrinho está vazio.');
    }

    public function test_step1_salva_address_id_na_sessao_e_redireciona_para_revisao(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->post(route('checkout.save-step1'), [
                'address_id' => $address->id,
            ])
            ->assertRedirect(route('checkout.step3'))
            ->assertSessionHas('checkout.address_id', $address->id);
    }

    public function test_step1_falha_se_address_nao_pertence_ao_usuario(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user)
            ->post(route('checkout.save-step1'), [
                'address_id' => $address->id,
            ])
            ->assertForbidden();
    }

    public function test_rota_antiga_de_pagamento_redireciona_para_revisao_sem_salvar_metodo_local(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('checkout.save-step2'), [
                'payment_method' => 'pix',
            ])
            ->assertRedirect(route('checkout.step3'))
            ->assertSessionMissing('checkout.payment_method');
    }

    public function test_rota_antiga_de_pagamento_redireciona_para_revisao(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        $this->createCartWithItem($user);

        $this->actingAs($user)
            ->withSession(['checkout.address_id' => $address->id])
            ->get(route('checkout.step2'))
            ->assertRedirect(route('checkout.step3'));
    }

    public function test_revisao_nao_exibe_formulario_local_de_cartao(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        $this->createCartWithItem($user);

        $this->actingAs($user)
            ->withSession(['checkout.address_id' => $address->id])
            ->get(route('checkout.step3'))
            ->assertOk()
            ->assertSee('Pagamento seguro pela Pagar.me')
            ->assertSee('Lá você poderá escolher Pix, boleto ou cartão')
            ->assertDontSee('card_number')
            ->assertDontSee('card_holder')
            ->assertDontSee('card_expiry')
            ->assertDontSee('card_cvv')
            ->assertDontSee('Número do cartão')
            ->assertDontSee('CVV')
            ->assertDontSee('Parcelas');
    }

    public function test_confirmar_pedido_cria_order_payment_items_totais_baixa_estoque_movimento_saida_e_salva_link(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        [$cart, $cartItem, $product] = $this->createCartWithItem($user, [
            'quantity' => 2,
            'price' => 100.00,
            'stock' => 10,
            'name' => 'Cimento CP II',
            'sku' => 'CIM-001',
        ]);

        $this->actingAs($user)
            ->withSession([
                'checkout.address_id' => $address->id,
            ])
            ->post('/checkout/confirmar')
            ->assertRedirect('https://checkout.pagar.me/test/plink_checkout_test');

        $order = Order::query()->with(['items', 'payments', 'stockMovements'])->firstOrFail();
        $orderItem = $order->items->first();
        $payment = $order->payments->first();

        $this->assertSame($user->id, $order->user_id);
        $this->assertSame($address->id, $order->address_id);
        $this->assertSame(Order::STATUS_PENDING, $order->status);
        $this->assertSame(Order::PAYMENT_STATUS_PENDING, $order->payment_status);
        $this->assertSame(Order::PAYMENT_METHOD_PAGARME_CHECKOUT, $order->payment_method);
        $this->assertEquals(200.00, (float) $order->subtotal_amount);
        $this->assertEquals(0.00, (float) $order->discount_amount);
        $this->assertEquals(29.90, (float) $order->shipping_amount);
        $this->assertEquals(229.90, (float) $order->total_amount);

        $this->assertNotNull($orderItem);
        $this->assertSame($product->id, $orderItem->product_id);
        $this->assertSame('Cimento CP II', $orderItem->product_name);
        $this->assertSame('CIM-001', $orderItem->product_sku);
        $this->assertSame((int) $cartItem->quantity, (int) $orderItem->quantity);
        $this->assertEquals(100.00, (float) $orderItem->price);

        $this->assertNotNull($payment);
        $this->assertSame(Order::PAYMENT_METHOD_PAGARME_CHECKOUT, $payment->payment_method);
        $this->assertSame(Payment::STATUS_PENDING, $payment->status);
        $this->assertEquals((float) $order->total_amount, (float) $payment->amount);
        $this->assertSame('plink_checkout_test', $order->pagarme_payment_link_id);
        $this->assertSame('https://checkout.pagar.me/test/plink_checkout_test', $order->pagarme_checkout_url);
        $this->assertSame('plink_checkout_test', $payment->pagarme_payment_link_id);
        $this->assertSame('https://checkout.pagar.me/test/plink_checkout_test', $payment->pagarme_checkout_url);

        $product->refresh();
        $cart->refresh();

        $this->assertSame(8, (int) $product->stock);
        $this->assertSame(0, (int) $cart->item_count);
        $this->assertEquals(0.0, (float) $cart->total_price);
        $this->assertSame(0, $cart->items()->count());

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'user_id' => $user->id,
            'order_id' => $order->id,
            'type' => StockMovement::TYPE_EXIT,
            'quantity' => 2,
            'reason' => 'Saída por pedido finalizado',
        ]);
    }

    public function test_confirmar_pedido_dispara_job_send_order_confirmation_email(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        $this->createCartWithItem($user);

        $this->actingAs($user)
            ->withSession([
                'checkout.address_id' => $address->id,
            ])
            ->post('/checkout/confirmar');

        Queue::assertPushed(SendOrderConfirmationEmail::class, function (SendOrderConfirmationEmail $job) use ($user): bool {
            return $job->order->user_id === $user->id;
        });
    }

    public function test_cancelar_pedido_restaura_estoque_cria_movimento_e_nao_restaura_duas_vezes(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        [, , $product] = $this->createCartWithItem($user, [
            'quantity' => 2,
            'price' => 100.00,
            'stock' => 10,
        ]);

        $this->actingAs($user)
            ->withSession([
                'checkout.address_id' => $address->id,
            ])
            ->post('/checkout/confirmar');

        $order = Order::query()->firstOrFail();
        $product->refresh();

        $this->assertSame(8, (int) $product->stock);

        $this->actingAs($user)
            ->post(route('orders.cancel', $order))
            ->assertRedirect();

        $product->refresh();
        $order->refresh();

        $this->assertSame(Order::STATUS_CANCELLED, $order->status);
        $this->assertSame(10, (int) $product->stock);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'order_id' => $order->id,
            'type' => StockMovement::TYPE_CANCELLATION,
            'quantity' => 2,
            'reason' => 'Estoque restaurado por cancelamento de pedido',
        ]);

        $this->actingAs($user)
            ->post(route('orders.cancel', $order))
            ->assertRedirect();

        $product->refresh();

        $this->assertSame(10, (int) $product->stock);
        $this->assertSame(1, StockMovement::query()
            ->where('order_id', $order->id)
            ->where('type', StockMovement::TYPE_CANCELLATION)
            ->count());
    }

    public function test_cupom_obra10_aplica_desconto_percentual(): void
    {
        Queue::fake();

        $coupon = $this->createCoupon('OBRA10', Coupon::TYPE_PERCENTAGE, 10);
        [$order, $payment] = $this->checkoutWithCoupon($coupon->code, 100.00);

        $coupon->refresh();

        $this->assertEquals(20.00, (float) $order->discount_amount);
        $this->assertEquals(209.90, (float) $order->total_amount);
        $this->assertEquals((float) $order->total_amount, (float) $payment->amount);
        $this->assertSame(1, (int) $coupon->used_count);
        $this->assertDatabaseHas('order_coupons', [
            'order_id' => $order->id,
            'coupon_id' => $coupon->id,
            'discount_amount' => 20.00,
        ]);
    }

    public function test_cupom_obra5_aplica_desconto_fixo(): void
    {
        Queue::fake();

        $coupon = $this->createCoupon('OBRA5', Coupon::TYPE_FIXED, 5);
        [$order, $payment] = $this->checkoutWithCoupon($coupon->code, 100.00);

        $coupon->refresh();

        $this->assertEquals(5.00, (float) $order->discount_amount);
        $this->assertEquals(224.90, (float) $order->total_amount);
        $this->assertEquals((float) $order->total_amount, (float) $payment->amount);
        $this->assertSame(1, (int) $coupon->used_count);
    }

    public function test_cupom_fretegratis_aplica_desconto_de_frete(): void
    {
        Queue::fake();

        $coupon = $this->createCoupon('FRETEGRATIS', Coupon::TYPE_FREE_SHIPPING, 0);
        [$order, $payment] = $this->checkoutWithCoupon($coupon->code, 100.00);

        $coupon->refresh();

        $this->assertEquals(29.90, (float) $order->discount_amount);
        $this->assertEquals(200.00, (float) $order->total_amount);
        $this->assertEquals((float) $order->total_amount, (float) $payment->amount);
        $this->assertSame(1, (int) $coupon->used_count);
    }

    public function test_cupom_invalido_nao_cria_pedido_com_desconto_falso(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        $this->createCartWithItem($user, [
            'quantity' => 2,
            'price' => 100.00,
        ]);

        $this->actingAs($user)
            ->withSession([
                'checkout.address_id' => $address->id,
                'checkout.coupon_code' => 'NAOEXISTE',
            ])
            ->post('/checkout/confirmar')
            ->assertSessionHasErrors('coupon');

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_coupons', 0);
        $this->assertDatabaseCount('payments', 0);
    }

    /**
     * @return array{0: Cart, 1: CartItem, 2: Product}
     */
    private function createCartWithItem(User $user, array $overrides = []): array
    {
        $product = Product::factory()->create([
            'name' => $overrides['name'] ?? 'Produto de teste',
            'sku' => $overrides['sku'] ?? 'SKU-TESTE',
            'price' => $overrides['price'] ?? 149.90,
            'stock' => $overrides['stock'] ?? 10,
            'is_active' => $overrides['is_active'] ?? true,
        ]);

        $quantity = $overrides['quantity'] ?? 2;
        $price = $overrides['price'] ?? (float) $product->price;
        $total = $quantity * $price;

        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'total_price' => $total,
            'item_count' => $quantity,
        ]);

        $item = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price' => $price,
        ]);

        return [$cart, $item, $product];
    }

    private function createCoupon(string $code, string $type, float $value): Coupon
    {
        return Coupon::query()->create([
            'code' => $code,
            'description' => "Cupom {$code}",
            'discount_type' => $type,
            'discount_value' => $value,
            'is_active' => true,
        ]);
    }

    /**
     * @return array{0: Order, 1: Payment}
     */
    private function checkoutWithCoupon(string $couponCode, float $unitPrice): array
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        $this->createCartWithItem($user, [
            'quantity' => 2,
            'price' => $unitPrice,
            'stock' => 10,
        ]);

        $this->actingAs($user)
            ->withSession([
                'checkout.address_id' => $address->id,
                'checkout.coupon_code' => $couponCode,
            ])
            ->post('/checkout/confirmar')
            ->assertRedirect('https://checkout.pagar.me/test/plink_checkout_test');

        $order = Order::query()->with('payments')->firstOrFail();
        $payment = $order->payments->first();

        $this->assertNotNull($payment);

        return [$order, $payment];
    }
}
