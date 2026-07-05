<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PagarmeCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_creates_pending_order_and_redirects_to_pagarme_url(): void
    {
        Queue::fake();
        $this->configurePagarme();

        Http::fake([
            'https://sdx-api.pagar.me/core/v5/paymentlinks' => Http::response([
                'id' => 'plink_test_123',
                'url' => 'https://checkout.pagar.me/test/plink_test_123',
            ], 201),
        ]);

        [$user, $address] = $this->createCheckoutCart();

        $response = $this->actingAs($user)
            ->withSession([
                'checkout.address_id' => $address->id,
                'checkout.payment_method' => 'pix',
            ])
            ->post(route('checkout.store'));

        $response->assertRedirect('https://checkout.pagar.me/test/plink_test_123');

        $order = Order::query()->firstOrFail();

        $this->assertSame(Order::STATUS_PENDING, $order->status);
        $this->assertSame(Order::PAYMENT_STATUS_PENDING, $order->payment_status);
        $this->assertSame('plink_test_123', $order->pagarme_payment_link_id);
        $this->assertSame('https://checkout.pagar.me/test/plink_test_123', $order->pagarme_checkout_url);

        Http::assertSent(fn ($request): bool => $request->hasHeader('User-Agent', 'pagarme-skill-generated/1.0')
            && $request->hasHeader('Authorization', 'Basic '.base64_encode('sk_test_123:'))
            && $request['type'] === 'order'
            && $request['payment_settings']['accepted_payment_methods'] === ['credit_card', 'pix', 'boleto']
            && $request['cart_settings']['items'][0]['amount'] === 12050
        );
    }

    public function test_checkout_keeps_order_when_pagarme_link_creation_fails(): void
    {
        Queue::fake();
        $this->configurePagarme();

        Http::fake([
            'https://sdx-api.pagar.me/core/v5/paymentlinks' => Http::response([
                'message' => 'Erro sandbox',
            ], 500),
        ]);

        [$user, $address] = $this->createCheckoutCart();

        $response = $this->actingAs($user)
            ->withSession([
                'checkout.address_id' => $address->id,
                'checkout.payment_method' => 'boleto',
            ])
            ->post(route('checkout.store'));

        $order = Order::query()->firstOrFail();

        $response->assertRedirect(route('checkout.payment-unavailable', $order));

        $this->assertSame(Order::STATUS_PENDING, $order->status);
        $this->assertSame(Order::PAYMENT_STATUS_PENDING, $order->payment_status);
        $this->assertNull($order->pagarme_payment_link_id);
        $this->assertNull($order->pagarme_checkout_url);

        $this->actingAs($user)
            ->get(route('checkout.payment-unavailable', $order))
            ->assertOk()
            ->assertSee('Não foi possível iniciar o pagamento')
            ->assertSee('Tentar pagar novamente');
    }

    public function test_pagarme_order_paid_webhook_marks_order_as_paid_and_processing(): void
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING,
            'payment_status' => Order::PAYMENT_STATUS_PENDING,
            'pagarme_payment_link_id' => 'plink_paid_123',
            'pagarme_checkout_url' => 'https://checkout.pagar.me/test/plink_paid_123',
        ]);

        $response = $this->postJson(route('webhooks.pagarme'), [
            'type' => 'order.paid',
            'data' => [
                'payment_link' => [
                    'id' => 'plink_paid_123',
                ],
            ],
        ]);

        $response->assertOk()->assertJson(['status' => 'ok']);

        $order->refresh();

        $this->assertSame(Order::PAYMENT_STATUS_PAID, $order->payment_status);
        $this->assertSame(Order::STATUS_PROCESSING, $order->status);
    }

    private function configurePagarme(): void
    {
        config([
            'services.pagarme.secret_key' => 'sk_test_123',
            'services.pagarme.base_url' => 'https://sdx-api.pagar.me/core/v5',
        ]);
    }

    /**
     * @return array{0: User, 1: Address}
     */
    private function createCheckoutCart(): array
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create([
            'name' => 'Cimento CP II',
            'description' => 'Saco de cimento para teste',
            'price' => 120.50,
            'stock' => 10,
            'is_active' => true,
        ]);

        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'total_price' => 241.00,
            'item_count' => 2,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 120.50,
        ]);

        return [$user, $address];
    }
}
