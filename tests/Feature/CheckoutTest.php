<?php

use App\Jobs\SendOrderConfirmationEmail;
use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

function createCartWithItem(User $user, array $itemOverrides = []): array
{
    $product = Product::factory()->create([
        'price' => $itemOverrides['price'] ?? 149.90,
        'stock' => 10,
        'is_active' => true,
    ]);

    $quantity = $itemOverrides['quantity'] ?? 2;
    $price = $itemOverrides['price'] ?? (float) $product->price;
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

it('usuário pode acessar checkout com carrinho não vazio', function () {
    $user = User::factory()->create();
    Address::factory()->create(['user_id' => $user->id]);
    createCartWithItem($user);

    $this->actingAs($user)
        ->get(route('checkout.step1'))
        ->assertOk()
        ->assertViewIs('store.checkout.step1');
});

it('usuário sem carrinho é redirecionado da página de checkout', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('checkout.step1'))
        ->assertRedirect(route('cart.index'))
        ->assertSessionHas('status', 'Seu carrinho está vazio.');
});

it('step1 salva address_id na sessão e redireciona para step2', function () {
    $user = User::factory()->create();
    $address = Address::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->post(route('checkout.save-step1'), [
            'address_id' => $address->id,
        ])
        ->assertRedirect(route('checkout.step2'))
        ->assertSessionHas('checkout.address_id', $address->id);
});

it('step1 falha se address não pertence ao usuário', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $address = Address::factory()->create(['user_id' => $otherUser->id]);

    $this->actingAs($user)
        ->post(route('checkout.save-step1'), [
            'address_id' => $address->id,
        ])
        ->assertForbidden();
});

it('step2 salva payment_method na sessão e redireciona para step3', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('checkout.save-step2'), [
            'payment_method' => 'pix',
        ])
        ->assertRedirect(route('checkout.step3'))
        ->assertSessionHas('checkout.payment_method', 'pix');
});

it('confirmar pedido cria Order com status pending', function () {
    Queue::fake();

    $user = User::factory()->create();
    $address = Address::factory()->create(['user_id' => $user->id]);
    createCartWithItem($user, ['quantity' => 2, 'price' => 100.00]);

    $this->actingAs($user)
        ->withSession([
            'checkout.address_id' => $address->id,
            'checkout.payment_method' => 'pix',
        ])
        ->post(route('checkout.store'))
        ->assertRedirect();

    $order = Order::query()->first();

    expect($order)->not->toBeNull()
        ->and($order->user_id)->toBe($user->id)
        ->and($order->address_id)->toBe($address->id)
        ->and($order->status)->toBe(Order::STATUS_PENDING)
        ->and($order->payment_method)->toBe('pix')
        ->and((float) $order->total_amount)->toBe(200.00);
});

it('confirmar pedido cria OrderItems corretos', function () {
    Queue::fake();

    $user = User::factory()->create();
    $address = Address::factory()->create(['user_id' => $user->id]);
    [$cart, $cartItem, $product] = createCartWithItem($user, ['quantity' => 3, 'price' => 59.90]);

    $this->actingAs($user)
        ->withSession([
            'checkout.address_id' => $address->id,
            'checkout.payment_method' => 'cartao',
        ])
        ->post(route('checkout.store'));

    $order = Order::query()->with('items')->firstOrFail();
    $orderItem = $order->items->first();

    expect($orderItem)->not->toBeNull()
        ->and($orderItem->product_id)->toBe($product->id)
        ->and((int) $orderItem->quantity)->toBe((int) $cartItem->quantity)
        ->and((float) $orderItem->price)->toBe(59.90)
        ->and((float) $order->total_amount)->toBe((float) $cart->total_price);
});

it('confirmar pedido limpa o carrinho após criação', function () {
    Queue::fake();

    $user = User::factory()->create();
    $address = Address::factory()->create(['user_id' => $user->id]);
    [$cart] = createCartWithItem($user);

    $this->actingAs($user)
        ->withSession([
            'checkout.address_id' => $address->id,
            'checkout.payment_method' => 'pix',
        ])
        ->post(route('checkout.store'));

    $cart->refresh();

    expect($cart->items()->count())->toBe(0)
        ->and((int) $cart->item_count)->toBe(0)
        ->and((float) $cart->total_price)->toBe(0.0);
});

it('confirmar pedido dispara job SendOrderConfirmationEmail', function () {
    Queue::fake();

    $user = User::factory()->create();
    $address = Address::factory()->create(['user_id' => $user->id]);
    createCartWithItem($user);

    $this->actingAs($user)
        ->withSession([
            'checkout.address_id' => $address->id,
            'checkout.payment_method' => 'pix',
        ])
        ->post(route('checkout.store'));

    Queue::assertPushed(SendOrderConfirmationEmail::class, function (SendOrderConfirmationEmail $job) use ($user) {
        return $job->order->user_id === $user->id;
    });
});
