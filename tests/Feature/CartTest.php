<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::post('/teste/carrinho/adicionar', function (Request $request, CartService $service) {
            $validated = $request->validate([
                'product_id' => ['required', 'integer', 'exists:products,id'],
                'quantity' => ['required', 'integer', 'min:1'],
            ], [
                'product_id.required' => 'Produto é obrigatório.',
                'product_id.exists' => 'Produto não encontrado.',
                'quantity.required' => 'Quantidade é obrigatória.',
                'quantity.min' => 'Quantidade mínima é 1.',
            ]);

            $user = $request->user();
            abort_unless($user, 401);

            $cart = $user->carts()->firstOrCreate([
                'user_id' => $user->id,
            ], [
                'total_price' => 0,
                'item_count' => 0,
            ]);

            $product = Product::query()->findOrFail((int) $validated['product_id']);

            try {
                $item = $service->addItem($cart, $product, (int) $validated['quantity']);

                return response()->json([
                    'message' => 'Item adicionado com sucesso.',
                    'cart_item_id' => $item->id,
                ], 200);
            } catch (ValidationException $exception) {
                return response()->json([
                    'message' => 'Falha de validação ao adicionar item.',
                    'errors' => $exception->errors(),
                ], 422);
            }
        })->middleware('auth');
    }

    public function test_adiciona_item_no_carrinho_sem_reduzir_estoque(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'price' => 199.90,
            'stock' => 10,
            'is_active' => true,
        ]);
        $cart = Cart::factory()->create(['user_id' => $user->id]);

        $service = app(CartService::class);
        $item = $service->addItem($cart, $product, 3);

        $this->assertInstanceOf(CartItem::class, $item);
        $this->assertSame(3, (int) $item->quantity);
        $this->assertEquals(199.90, (float) $item->price);

        $cart->refresh();
        $product->refresh();

        $this->assertSame(10, (int) $product->stock);
        $this->assertSame(3, (int) $cart->item_count);
        $this->assertEquals(599.70, (float) $cart->total_price);
    }

    public function test_falha_ao_adicionar_item_quando_o_estoque_e_insuficiente(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'stock' => 1,
            'is_active' => true,
        ]);
        $cart = Cart::factory()->create(['user_id' => $user->id]);

        $service = app(CartService::class);

        $this->expectException(ValidationException::class);
        $service->addItem($cart, $product, 2);
    }

    public function test_remove_item_do_carrinho_nao_altera_estoque_e_recalcula_totais(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'price' => 50.00,
            'stock' => 20,
            'is_active' => true,
        ]);
        $cart = Cart::factory()->create(['user_id' => $user->id]);

        $service = app(CartService::class);
        $service->addItem($cart, $product, 4);
        $service->removeItem($cart, $product->id);

        $product->refresh();
        $cart->refresh();

        $this->assertSame(20, (int) $product->stock);
        $this->assertSame(0, (int) $cart->item_count);
        $this->assertEquals(0.0, (float) $cart->total_price);
        $this->assertSame(0, $cart->items()->count());
    }

    public function test_atualiza_quantidade_no_carrinho_sem_alterar_estoque(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'price' => 25.00,
            'stock' => 10,
            'is_active' => true,
        ]);
        $cart = Cart::factory()->create(['user_id' => $user->id]);

        $service = app(CartService::class);
        $service->addItem($cart, $product, 2);
        $item = $service->addItem($cart, $product, 3);

        $product->refresh();
        $cart->refresh();

        $this->assertSame(10, (int) $product->stock);
        $this->assertSame(5, (int) $item->quantity);
        $this->assertSame(5, (int) $cart->item_count);
        $this->assertEquals(125.0, (float) $cart->total_price);
    }

    public function test_limpa_carrinho_sem_alterar_estoque(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'price' => 40.00,
            'stock' => 10,
            'is_active' => true,
        ]);
        $cart = Cart::factory()->create(['user_id' => $user->id]);

        $service = app(CartService::class);
        $service->addItem($cart, $product, 2);
        $service->clear($cart);

        $product->refresh();
        $cart->refresh();

        $this->assertSame(10, (int) $product->stock);
        $this->assertSame(0, (int) $cart->item_count);
        $this->assertEquals(0.0, (float) $cart->total_price);
        $this->assertSame(0, $cart->items()->count());
    }

    public function test_calcula_total_corretamente_com_multiplos_itens(): void
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $productA = Product::factory()->create(['price' => 10, 'stock' => 10, 'is_active' => true]);
        $productB = Product::factory()->create(['price' => 25, 'stock' => 10, 'is_active' => true]);

        $service = app(CartService::class);
        $service->addItem($cart, $productA, 2); // 20
        $service->addItem($cart, $productB, 3); // 75

        $total = $service->calculateTotal($cart);
        $cart->refresh();

        $this->assertEquals(95.0, $total);
        $this->assertSame(5, (int) $cart->item_count);
        $this->assertEquals(95.0, (float) $cart->total_price);
    }

    public function test_decrease_stock_legado_reduz_estoque_apenas_quando_chamado_diretamente(): void
    {
        $product = Product::factory()->create([
            'stock' => 2,
            'is_active' => true,
        ]);

        $ok = $product->decreaseStock(1);
        $product->refresh();
        $fail = $product->decreaseStock(5);
        $product->refresh();

        $this->assertTrue($ok);
        $this->assertFalse($fail);
        $this->assertSame(1, (int) $product->stock);
    }

    public function test_retorna_200_ao_adicionar_item_via_rota_http(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'stock' => 10,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->postJson('/teste/carrinho/adicionar', [
                'product_id' => $product->id,
                'quantity' => 2,
            ])
            ->assertOk()
            ->assertJsonPath('message', 'Item adicionado com sucesso.');

        $product->refresh();
        $this->assertSame(10, (int) $product->stock);
    }

    public function test_retorna_422_quando_quantidade_enviada_e_invalida(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'stock' => 10,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->postJson('/teste/carrinho/adicionar', [
                'product_id' => $product->id,
                'quantity' => 0,
            ])
            ->assertStatus(422);
    }
}
