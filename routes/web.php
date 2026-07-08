<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminCouponController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminStockController;
use App\Http\Controllers\Admin\AdminStockMovementController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PagarmeWebhookController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StoreController::class, 'home'])->name('store.home');
Route::get('/produtos', [ProductController::class, 'index'])->name('store.products');
Route::get('/produtos/{product:slug}', [ProductController::class, 'show'])->name('store.products.show');
Route::get('/categorias', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categorias/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/newsletter/cancelar', [NewsletterController::class, 'unsubscribe'])
    ->name('newsletter.unsubscribe');
Route::post('/newsletter/cancelar', [NewsletterController::class, 'confirmUnsubscribe'])
    ->name('newsletter.confirm-unsubscribe');

Route::post('/webhooks/pagarme', PagarmeWebhookController::class)
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->name('webhooks.pagarme');

Route::get('/dashboard', function () {
    return redirect()->route(auth()->user()->is_admin ? 'admin.dashboard' : 'user.dashboard');
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::middleware('verified')->group(function () {
        Route::get('/minha-conta', UserDashboardController::class)->name('user.dashboard');
        Route::get('/carrinho', [CartController::class, 'index'])->name('cart.index');

        // Endereços do cliente
        Route::get('/meus-enderecos', [AddressController::class, 'index'])->name('addresses.index');
        Route::post('/meus-enderecos', [AddressController::class, 'store'])->name('addresses.store');
        Route::put('/meus-enderecos/{address}', [AddressController::class, 'update'])->name('addresses.update');
        Route::delete('/meus-enderecos/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');

        // Checkout
        Route::get('/checkout', [CheckoutController::class, 'step1'])->name('checkout.step1');
        Route::post('/checkout/step1', [CheckoutController::class, 'saveStep1'])->name('checkout.save-step1');
        Route::get('/checkout/pagamento', [CheckoutController::class, 'step2'])->name('checkout.step2');
        Route::post('/checkout/step2', [CheckoutController::class, 'saveStep2'])->name('checkout.save-step2');
        Route::get('/checkout/revisao', [CheckoutController::class, 'step3'])->name('checkout.step3');
        Route::post('/checkout/confirmar', [CheckoutController::class, 'store'])->name('checkout.store');
        Route::get('/checkout/sucesso/{order}', [CheckoutController::class, 'sucesso'])->name('checkout.sucesso');
        Route::get('/checkout/pagamento/indisponivel/{order}', [CheckoutController::class, 'paymentUnavailable'])->name('checkout.payment-unavailable');
        Route::post('/checkout/pagamento/{order}/tentar-novamente', [CheckoutController::class, 'retryPayment'])->name('checkout.payment.retry');
    });

    // Pedidos do cliente
    Route::get('/meus-pedidos', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/meus-pedidos/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/meus-pedidos/{order}/cancelar', [OrderController::class, 'cancel'])->name('orders.cancel');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function (): void {
        Route::get('/', AdminDashboardController::class)->name('dashboard');
        Route::resource('categories', AdminCategoryController::class)->except('show');
        Route::resource('products', AdminProductController::class)->except('show');
        Route::resource('coupons', AdminCouponController::class)->except('show');
        Route::resource('users', AdminUserController::class)->only(['index', 'show', 'edit', 'update']);
        Route::get('pagamentos', [AdminPaymentController::class, 'index'])->name('payments.index');
        Route::get('pagamentos/{payment}', [AdminPaymentController::class, 'show'])->name('payments.show');
        Route::get('estoque', [AdminStockController::class, 'index'])->name('stock.index');
        Route::post('estoque/{product}/ajustar', [AdminStockController::class, 'update'])->name('stock.update');
        Route::get('estoque/movimentacoes', [AdminStockMovementController::class, 'index'])->name('stock-movements.index');
        Route::get('relatorios', AdminReportController::class)->name('reports.index');
        Route::resource('pedidos', AdminOrderController::class)
            ->parameters(['pedidos' => 'order'])
            ->only(['index', 'show', 'update']);
    });

require __DIR__.'/auth.php';
