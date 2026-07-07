<?php

namespace Tests\Feature;

use App\Exceptions\PagarmePaymentException;
use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Services\PagarmeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class PagarmeCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.url' => 'http://localhost']);
        URL::forceRootUrl('http://localhost');
    }

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
            ])
            ->post('/checkout/confirmar');

        $response->assertRedirect('https://checkout.pagar.me/test/plink_test_123');

        $order = Order::query()->firstOrFail();

        $this->assertSame(Order::STATUS_PENDING, $order->status);
        $this->assertSame(Order::PAYMENT_STATUS_PENDING, $order->payment_status);
        $this->assertSame(Order::PAYMENT_METHOD_PAGARME_CHECKOUT, $order->payment_method);
        $this->assertSame('plink_test_123', $order->pagarme_payment_link_id);
        $this->assertSame('https://checkout.pagar.me/test/plink_test_123', $order->pagarme_checkout_url);

        Http::assertSent(fn ($request): bool => $request->hasHeader('User-Agent', 'pagarme-skill-generated/1.0')
            && $request->hasHeader('Authorization', 'Basic '.base64_encode('sk_test_123:'))
            && $request['type'] === 'order'
            && $request['order_code'] === (string) $order->id
            && $request['max_paid_sessions'] === 1
            && $request['payment_settings']['accepted_payment_methods'] === ['credit_card', 'pix', 'boleto']
            && $request['payment_settings']['credit_card_settings']['operation_type'] === 'auth_and_capture'
            && $request['payment_settings']['credit_card_settings']['installments'][0]['number'] === 1
            && $request['payment_settings']['credit_card_settings']['installments'][0]['total'] === 27090
            && $request['payment_settings']['pix_settings']['expires_in'] === 3600
            && $request['payment_settings']['boleto_settings']['due_in'] === 3
            && $request['cart_settings']['shipping_cost'] === 2990
            && $request['cart_settings']['items'][0]['amount'] === 12050
            && $request['cart_settings']['items'][0]['default_quantity'] === 2
            && ! isset($request['flow_settings'])
            && $request['customer_settings']['customer']['email'] === $user->email
            && $request['customer_settings']['customer']['document_type'] === 'CPF'
        );
    }

    public function test_checkout_sends_configured_public_success_url_to_pagarme(): void
    {
        Queue::fake();
        $this->configurePagarme([
            'services.pagarme.success_url' => 'https://checkout-return.example.com/pedido/{order_id}/sucesso',
        ]);

        Http::fake([
            'https://sdx-api.pagar.me/core/v5/paymentlinks' => Http::response([
                'id' => 'plink_success_url_123',
                'url' => 'https://checkout.pagar.me/test/plink_success_url_123',
            ], 201),
        ]);

        [$user, $address] = $this->createCheckoutCart();

        $this->actingAs($user)
            ->withSession([
                'checkout.address_id' => $address->id,
            ])
            ->post('/checkout/confirmar')
            ->assertRedirect('https://checkout.pagar.me/test/plink_success_url_123');

        $order = Order::query()->firstOrFail();

        Http::assertSent(fn ($request): bool => $request['flow_settings']['success_url'] === 'https://checkout-return.example.com/pedido/'.$order->id.'/sucesso'
            && $request['payment_settings']['credit_card_settings']['installments'][0]['total'] === 27090
        );
    }

    public function test_pagarme_payload_can_disable_credit_card_method(): void
    {
        Queue::fake();
        $this->configurePagarme([
            'services.pagarme.enable_credit_card' => false,
        ]);

        Http::fake([
            'https://sdx-api.pagar.me/core/v5/paymentlinks' => Http::response([
                'id' => 'plink_no_card_123',
                'url' => 'https://checkout.pagar.me/test/plink_no_card_123',
            ], 201),
        ]);

        [$user, $address] = $this->createCheckoutCart();

        $this->actingAs($user)
            ->withSession([
                'checkout.address_id' => $address->id,
            ])
            ->post('/checkout/confirmar')
            ->assertRedirect('https://checkout.pagar.me/test/plink_no_card_123');

        Http::assertSent(fn ($request): bool => $request['payment_settings']['accepted_payment_methods'] === ['pix', 'boleto']
            && ! isset($request['payment_settings']['credit_card_settings'])
            && isset($request['payment_settings']['pix_settings'])
            && isset($request['payment_settings']['boleto_settings'])
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
            ])
            ->post('/checkout/confirmar');

        $order = Order::query()->firstOrFail();

        $response->assertRedirect(route('checkout.payment-unavailable', $order));

        $this->assertSame(Order::STATUS_PENDING, $order->status);
        $this->assertSame(Order::PAYMENT_STATUS_PENDING, $order->payment_status);
        $this->assertNull($order->pagarme_payment_link_id);
        $this->assertNull($order->pagarme_checkout_url);

        $payment = $order->payments()->firstOrFail();

        $this->assertSame(Payment::STATUS_PENDING, $payment->status);
        $this->assertEquals((float) $order->total_amount, (float) $payment->amount);
        $this->assertNull($payment->pagarme_payment_link_id);
        $this->assertNull($payment->pagarme_checkout_url);

        $this->actingAs($user)
            ->get("/checkout/pagamento/indisponivel/{$order->id}")
            ->assertOk()
            ->assertSee('Não foi possível iniciar o pagamento')
            ->assertSee('Tentar pagar novamente');
    }

    public function test_retry_payment_creates_new_pagarme_link_and_redirects_to_hosted_checkout(): void
    {
        Queue::fake();
        $this->configurePagarme();

        Http::fake([
            'https://sdx-api.pagar.me/core/v5/paymentlinks' => Http::response([
                'id' => 'plink_retry_123',
                'url' => 'https://checkout.pagar.me/test/plink_retry_123',
            ], 201),
        ]);

        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'address_id' => $address->id,
            'status' => Order::STATUS_PENDING,
            'payment_status' => Order::PAYMENT_STATUS_FAILED,
            'payment_method' => Order::PAYMENT_METHOD_PAGARME_CHECKOUT,
            'total_amount' => 99.90,
        ]);
        $product = Product::factory()->create([
            'name' => 'Areia média',
            'price' => 99.90,
            'stock' => 10,
            'is_active' => true,
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'quantity' => 1,
            'price' => 99.90,
        ]);
        $payment = $order->payments()->create([
            'payment_method' => Order::PAYMENT_METHOD_PAGARME_CHECKOUT,
            'status' => Payment::STATUS_FAILED,
            'amount' => 99.90,
        ]);

        $this->actingAs($user)
            ->post(route('checkout.payment.retry', $order))
            ->assertRedirect('https://checkout.pagar.me/test/plink_retry_123');

        $order->refresh();
        $payment->refresh();

        $this->assertSame(Order::PAYMENT_STATUS_PENDING, $order->payment_status);
        $this->assertSame(Payment::STATUS_PENDING, $payment->status);
        $this->assertSame('plink_retry_123', $order->pagarme_payment_link_id);
        $this->assertSame('https://checkout.pagar.me/test/plink_retry_123', $order->pagarme_checkout_url);
        $this->assertSame('plink_retry_123', $payment->pagarme_payment_link_id);
        $this->assertSame('https://checkout.pagar.me/test/plink_retry_123', $payment->pagarme_checkout_url);
    }

    public function test_pagarme_service_rejects_public_key_before_http_request(): void
    {
        config([
            'services.pagarme.secret_key' => 'pk_test_123',
            'services.pagarme.base_url' => 'https://sdx-api.pagar.me/core/v5',
        ]);

        Http::fake();

        $order = Order::factory()->create();

        try {
            app(PagarmeService::class)->createPaymentLink($order);
            $this->fail('A chave pública pk_test deveria ser rejeitada antes da requisição HTTP.');
        } catch (PagarmePaymentException $exception) {
            $this->assertStringContainsString('PAGARME_SECRET_KEY deve ser uma secret key', $exception->getMessage());
        }

        Http::assertNothingSent();
    }

    public function test_pagarme_order_paid_webhook_marks_order_as_paid_and_processing(): void
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_PENDING,
            'payment_status' => Order::PAYMENT_STATUS_PENDING,
            'pagarme_payment_link_id' => 'plink_paid_123',
            'pagarme_checkout_url' => 'https://checkout.pagar.me/test/plink_paid_123',
        ]);

        $response = $this->postJson('/webhooks/pagarme', [
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

    /**
     * @param array<string, mixed> $overrides
     */
    private function configurePagarme(array $overrides = []): void
    {
        config(array_merge([
            'services.pagarme.secret_key' => 'sk_test_123',
            'services.pagarme.base_url' => 'https://sdx-api.pagar.me/core/v5',
            'services.pagarme.success_url' => null,
            'services.pagarme.cancel_url' => null,
            'services.pagarme.enable_credit_card' => true,
        ], $overrides));
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
