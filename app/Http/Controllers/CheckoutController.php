<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateBoleto;
use App\Jobs\SendOrderConfirmationEmail;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cartService
    ) {
        $this->middleware('auth');
    }

    public function step1(): View|RedirectResponse
    {
        $cart = $this->getCart();

        if (! $cart || $cart->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('status', 'Seu carrinho está vazio.');
        }

        $addresses = auth()->user()
            ->addresses()
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        return view('store.checkout.step1', compact('addresses', 'cart'));
    }

    public function saveStep1(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'address_id' => ['required', 'exists:addresses,id'],
        ], [
            'address_id.required' => 'Selecione um endereço para continuar.',
            'address_id.exists' => 'O endereço selecionado é inválido.',
        ]);

        $address = Address::query()->findOrFail($validated['address_id']);

        abort_if($address->user_id !== auth()->id(), 403, 'Você não tem permissão para usar este endereço.');

        session(['checkout.address_id' => $address->id]);

        return redirect()->route('checkout.step2');
    }

    public function step2(): View|RedirectResponse
    {
        if (! session()->has('checkout.address_id')) {
            return redirect()
                ->route('checkout.step1')
                ->with('status', 'Selecione um endereço para continuar.');
        }

        $cart = $this->getCart();

        if (! $cart || $cart->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('status', 'Seu carrinho está vazio.');
        }

        return view('store.checkout.step2', compact('cart'));
    }

    public function saveStep2(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'payment_method' => ['required', 'in:cartao,boleto,pix'],
        ], [
            'payment_method.required' => 'Selecione uma forma de pagamento.',
            'payment_method.in' => 'Forma de pagamento inválida.',
        ]);

        session(['checkout.payment_method' => $validated['payment_method']]);

        return redirect()->route('checkout.step3');
    }

    public function step3(): View|RedirectResponse
    {
        if (! session()->has('checkout.address_id')) {
            return redirect()
                ->route('checkout.step1')
                ->with('status', 'Selecione um endereço para continuar.');
        }

        if (! session()->has('checkout.payment_method')) {
            return redirect()
                ->route('checkout.step2')
                ->with('status', 'Selecione uma forma de pagamento.');
        }

        $cart = $this->getCart();

        if (! $cart || $cart->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('status', 'Seu carrinho está vazio.');
        }

        $address = Address::query()
            ->where('user_id', auth()->id())
            ->findOrFail(session('checkout.address_id'));

        $items = $cart->items;

        return view('store.checkout.step3', compact('cart', 'address', 'items'));
    }

    public function store(Request $request): RedirectResponse
    {
        if (! session()->has('checkout.address_id')) {
            return redirect()
                ->route('checkout.step1')
                ->with('status', 'Selecione um endereço para continuar.');
        }

        if (! session()->has('checkout.payment_method')) {
            return redirect()
                ->route('checkout.step2')
                ->with('status', 'Selecione uma forma de pagamento.');
        }

        $cart = $this->getCart();

        if (! $cart || $cart->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('status', 'Seu carrinho está vazio.');
        }

        $address = Address::query()
            ->where('user_id', auth()->id())
            ->findOrFail(session('checkout.address_id'));

        $paymentMethod = (string) session('checkout.payment_method');

        try {
            $order = DB::transaction(function () use ($cart, $address, $paymentMethod): Order {
                $lockedCart = Cart::query()
                    ->whereKey($cart->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $items = $lockedCart->items()
                    ->with('product')
                    ->lockForUpdate()
                    ->get();

                if ($items->isEmpty()) {
                    throw ValidationException::withMessages([
                        'cart' => 'Seu carrinho está vazio.',
                    ]);
                }

                $order = Order::query()->create([
                    'user_id' => auth()->id(),
                    'address_id' => $address->id,
                    'status' => Order::STATUS_PENDING,
                    'payment_method' => $paymentMethod,
                    'total_amount' => $lockedCart->total_price,
                ]);

                foreach ($items as $item) {
                    $quantity = (int) $item->quantity;

                    $product = Product::query()
                        ->whereKey($item->product_id)
                        ->lockForUpdate()
                        ->first();

                    if (! $product || ! $product->is_active) {
                        throw ValidationException::withMessages([
                            'stock' => 'Um dos produtos do carrinho não está disponível.',
                        ]);
                    }

                    if (! $product->isInStock($quantity)) {
                        throw ValidationException::withMessages([
                            'stock' => "Estoque insuficiente para o produto {$product->name}.",
                        ]);
                    }

                    $order->items()->create([
                        'product_id' => $item->product_id,
                        'quantity' => $quantity,
                        'price' => $item->price,
                    ]);

                    if (! $product->decreaseStock($quantity)) {
                        throw ValidationException::withMessages([
                            'stock' => "Estoque insuficiente para o produto {$product->name}.",
                        ]);
                    }
                }

                $this->cartService->clear($lockedCart);

                return $order;
            });
        } catch (ValidationException $exception) {
            return redirect()
                ->back()
                ->withErrors($exception->errors())
                ->withInput();
        } catch (Throwable) {
            return redirect()
                ->back()
                ->withErrors(['checkout' => 'Não foi possível finalizar o pedido. Tente novamente.'])
                ->withInput();
        }

        session()->forget('checkout');

        SendOrderConfirmationEmail::dispatch($order);

        if ($order->payment_method === 'boleto') {
            GenerateBoleto::dispatch($order)->delay(now()->addSeconds(5));
        }

        return redirect()->route('checkout.sucesso', $order);
    }

    public function sucesso(Order $order): View
    {
        abort_if($order->user_id !== auth()->id(), 403, 'Você não tem permissão para visualizar este pedido.');

        return view('store.checkout.sucesso', compact('order'));
    }

    private function getCart(): ?Cart
    {
        return auth()->user()
            ->carts()
            ->with('items.product')
            ->latest()
            ->first();
    }
}
