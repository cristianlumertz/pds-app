<?php

namespace App\Http\Controllers;

use App\Exceptions\PagarmePaymentException;
use App\Jobs\SendOrderConfirmationEmail;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Services\CartService;
use App\Services\CouponService;
use App\Services\PagarmeService;
use App\Services\StockService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly CouponService $couponService,
        private readonly PagarmeService $pagarmeService,
        private readonly StockService $stockService
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

        return redirect()->route('checkout.step3');
    }

    public function step2(): RedirectResponse
    {
        return redirect()->route('checkout.step3');
    }

    public function saveStep2(): RedirectResponse
    {
        return redirect()->route('checkout.step3');
    }

    public function step3(): View|RedirectResponse
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

        $address = Address::query()
            ->where('user_id', auth()->id())
            ->findOrFail(session('checkout.address_id'));

        $items = $cart->items;

        try {
            $summary = $this->calculateOrderTotals($items, session('checkout.coupon_code'));
        } catch (ValidationException $exception) {
            session()->forget('checkout.coupon_code');
            $summary = $this->calculateOrderTotals($items, null);
            session()->flash('status', $exception->errors()['coupon'][0] ?? 'O cupom aplicado não é mais válido.');
        }

        return view('store.checkout.step3', compact('cart', 'address', 'items', 'summary'));
    }

    public function store(Request $request): RedirectResponse
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

        $address = Address::query()
            ->where('user_id', auth()->id())
            ->findOrFail(session('checkout.address_id'));

        $paymentMethod = Order::PAYMENT_METHOD_PAGARME_CHECKOUT;
        $couponCode = session('checkout.coupon_code');
        $user = $request->user();

        try {
            $order = DB::transaction(function () use ($cart, $address, $paymentMethod, $couponCode, $user): Order {
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

                $totals = $this->calculateOrderTotals($items, null);
                $coupon = $this->resolveLockedCoupon($couponCode, $totals['subtotal_amount']);

                if ($coupon) {
                    $totals = $this->calculateOrderTotals($items, $coupon);
                }

                $order = Order::query()->create([
                    'user_id' => auth()->id(),
                    'address_id' => $address->id,
                    'status' => Order::STATUS_PENDING,
                    'subtotal_amount' => $totals['subtotal_amount'],
                    'discount_amount' => $totals['discount_amount'],
                    'shipping_amount' => $totals['shipping_amount'],
                    'total_amount' => $totals['total_amount'],
                    'shipping_method' => 'standard',
                    'shipping_status' => 'pending',
                    'delivery_estimate' => '3 a 7 dias úteis',
                    'payment_method' => $paymentMethod,
                    'payment_status' => Order::PAYMENT_STATUS_PENDING,
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

                    $this->stockService->validateAvailableStock($product, $quantity);

                    $order->items()->create([
                        'product_id' => $item->product_id,
                        'product_name' => $product->name,
                        'product_sku' => $product->sku,
                        'quantity' => $quantity,
                        'price' => $item->price,
                    ]);

                    $this->stockService->decreaseStock(
                        $product,
                        $quantity,
                        $order,
                        $user,
                        'Saída por pedido finalizado'
                    );
                }

                $order->payments()->create([
                    'payment_method' => $paymentMethod,
                    'status' => $order->payment_status,
                    'amount' => $order->total_amount,
                    'pagarme_payment_link_id' => $order->pagarme_payment_link_id,
                    'pagarme_checkout_url' => $order->pagarme_checkout_url,
                ]);

                if ($coupon) {
                    $this->couponService->applyCouponToOrder(
                        $order,
                        $coupon,
                        (float) $order->discount_amount
                    );

                    $coupon->increment('used_count');
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

        return $this->redirectToPagarmeCheckout($order);
    }

    public function sucesso(Order $order): View
    {
        abort_if($order->user_id !== auth()->id(), 403, 'Você não tem permissão para visualizar este pedido.');

        return view('store.checkout.sucesso', compact('order'));
    }

    public function paymentUnavailable(Order $order): View
    {
        abort_if($order->user_id !== auth()->id(), 403, 'Você não tem permissão para visualizar este pedido.');

        return view('store.checkout.payment-unavailable', compact('order'));
    }

    public function retryPayment(Order $order): RedirectResponse
    {
        abort_if($order->user_id !== auth()->id(), 403, 'Você não tem permissão para pagar este pedido.');

        if ($order->isPaid()) {
            return redirect()
                ->route('orders.show', $order)
                ->with('status', 'Este pedido já está pago.');
        }

        if (! in_array((string) $order->payment_status, [
            Order::PAYMENT_STATUS_PENDING,
            Order::PAYMENT_STATUS_FAILED,
            Order::PAYMENT_STATUS_EXPIRED,
        ], true)) {
            return redirect()
                ->route('orders.show', $order)
                ->withErrors(['payment' => 'Este pedido não está disponível para nova tentativa de pagamento.']);
        }

        return $this->redirectToPagarmeCheckout($order);
    }

    private function redirectToPagarmeCheckout(Order $order): RedirectResponse
    {
        $payment = $this->ensurePaymentForOrder($order);
        $this->markPaymentAttemptAsPending($order, $payment);

        try {
            $paymentLink = $this->pagarmeService->createPaymentLink($order);
            $checkoutUrl = $paymentLink['url'] ?? null;

            if (! is_string($checkoutUrl) || trim($checkoutUrl) === '') {
                throw new PagarmePaymentException('A Pagar.me não retornou uma URL de checkout.');
            }

            $paymentLinkId = $paymentLink['id'] ?? null;

            DB::transaction(function () use ($order, $payment, $paymentLinkId, $checkoutUrl): void {
                $pagarmePaymentLinkId = is_scalar($paymentLinkId) ? (string) $paymentLinkId : null;

                $order->forceFill([
                    'pagarme_payment_link_id' => $pagarmePaymentLinkId,
                    'pagarme_checkout_url' => $checkoutUrl,
                ])->save();

                $payment->forceFill([
                    'payment_method' => $order->payment_method,
                    'status' => $order->payment_status,
                    'amount' => $order->total_amount,
                    'pagarme_payment_link_id' => $pagarmePaymentLinkId,
                    'pagarme_checkout_url' => $checkoutUrl,
                ])->save();
            });

            return redirect()->away($checkoutUrl);
        } catch (Throwable $exception) {
            $context = [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
            ];

            if ($exception instanceof PagarmePaymentException) {
                $context = array_merge($context, $exception->context());
            }

            Log::error('Não foi possível iniciar o checkout hospedado da Pagar.me.', $context);

            return redirect()
                ->route('checkout.payment-unavailable', $order)
                ->withErrors([
                    'payment' => 'Não foi possível iniciar o pagamento. Tente novamente.',
            ]);
        }
    }

    private function markPaymentAttemptAsPending(Order $order, Payment $payment): void
    {
        if (! in_array((string) $order->payment_status, [
            Order::PAYMENT_STATUS_FAILED,
            Order::PAYMENT_STATUS_EXPIRED,
        ], true)) {
            return;
        }

        DB::transaction(function () use ($order, $payment): void {
            $order->forceFill([
                'payment_status' => Order::PAYMENT_STATUS_PENDING,
            ])->save();

            $payment->forceFill([
                'status' => Payment::STATUS_PENDING,
                'amount' => $order->total_amount,
            ])->save();
        });
    }

    /**
     * @param \Illuminate\Support\Collection<int, \App\Models\CartItem> $items
     *
     * @return array{subtotal_amount: float, discount_amount: float, shipping_amount: float, total_amount: float, coupon_code: ?string}
     */
    private function calculateOrderTotals($items, Coupon|string|null $couponOrCode = null): array
    {
        $subtotal = round((float) $items->sum(fn ($item): float => round(((int) $item->quantity) * ((float) $item->price), 2)), 2);
        $shipping = $subtotal > 299 ? 0.0 : 29.90;
        $discount = 0.0;
        $couponCode = null;

        if ($couponOrCode instanceof Coupon) {
            $coupon = $couponOrCode;
        } else {
            $normalizedCode = $this->couponService->normalizeCode(is_string($couponOrCode) ? $couponOrCode : null);
            $coupon = $this->couponService->findByCode($normalizedCode);

            if ($normalizedCode !== null && ! $coupon) {
                throw ValidationException::withMessages([
                    'coupon' => 'Cupom inválido ou expirado.',
                ]);
            }
        }

        if ($coupon) {
            $discount = $this->couponService->calculateDiscount($coupon, $subtotal, $shipping);
            $couponCode = (string) $coupon->code;
        }

        $total = max(0, round($subtotal - $discount + $shipping, 2));

        return [
            'subtotal_amount' => $subtotal,
            'discount_amount' => $discount,
            'shipping_amount' => $shipping,
            'total_amount' => $total,
            'coupon_code' => $couponCode,
        ];
    }

    private function resolveLockedCoupon(mixed $couponCode, float $subtotal): ?Coupon
    {
        $normalizedCode = $this->couponService->normalizeCode(is_string($couponCode) ? $couponCode : null);

        if ($normalizedCode === null) {
            return null;
        }

        /** @var Coupon|null $coupon */
        $coupon = Coupon::query()
            ->where('code', $normalizedCode)
            ->lockForUpdate()
            ->first();

        if (! $coupon) {
            throw ValidationException::withMessages([
                'coupon' => 'Cupom inválido ou expirado.',
            ]);
        }

        $this->couponService->validateCoupon($coupon, $subtotal);

        return $coupon;
    }

    private function ensurePaymentForOrder(Order $order): Payment
    {
        $payment = $order->payments()
            ->latest('id')
            ->first();

        if ($payment) {
            $payment->forceFill([
                'payment_method' => $order->payment_method,
                'status' => $order->payment_status,
                'amount' => $order->total_amount,
            ])->save();

            return $payment;
        }

        return $order->payments()->create([
            'payment_method' => $order->payment_method,
            'status' => $order->payment_status,
            'amount' => $order->total_amount,
            'pagarme_payment_link_id' => $order->pagarme_payment_link_id,
            'pagarme_checkout_url' => $order->pagarme_checkout_url,
        ]);
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
