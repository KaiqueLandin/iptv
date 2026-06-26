<?php

use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\WishlistController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

// Google OAuth
Route::get('auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');

// Public "Buy" entry point used by the landing page plan buttons.
// Guests are redirected to registration and returned here afterwards.
Route::get('buy/{product}', [CheckoutController::class, 'buy'])->name('checkout.buy');

// Payment gateway webhooks (no auth/CSRF: called by external gateways).
Route::post('webhooks/payments/{gatewayKey}', [PaymentWebhookController::class, 'handle'])
    ->name('webhooks.payments');

// Public API routes
Route::prefix('api')->group(function () {
    Route::get('products', [ProductController::class, 'index'])->name('api.products.index');
});

// Authenticated API routes
Route::prefix('api')
    ->middleware(['auth'])
    ->group(function () {
        Route::get('wallet/balance', [WalletController::class, 'balance'])->name('api.wallet.balance');
        Route::get('wallet/history', [WalletController::class, 'history'])->name('api.wallet.history');
        Route::post('products/refresh', [ProductController::class, 'refresh'])->name('api.products.refresh');
    });

// Admin analytics API (JSON, consumed by dashboard charts).
Route::prefix('api/admin/analytics')
    ->middleware(['auth', 'verified', 'admin'])
    ->name('api.admin.analytics.')
    ->group(function () {
        Route::get('revenue', [AnalyticsController::class, 'revenue'])->name('revenue');
        Route::get('customers', [AnalyticsController::class, 'customers'])->name('customers');
        Route::get('products', [AnalyticsController::class, 'products'])->name('products');
    });

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');
    });

Route::middleware(['auth'])->group(function () {
    Route::get('invitations/{invitation}/accept', [TeamInvitationController::class, 'accept'])->name('invitations.accept');
    Route::delete('invitations/{invitation}', [TeamInvitationController::class, 'decline'])->name('invitations.decline');
});

// Checkout + provisioned services (the "buy → pay → receive code" flow).
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('checkout/product/{product}', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('checkout/{invoice}', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('checkout/{invoice}/pay', [PaymentController::class, 'store'])->name('checkout.pay');

    Route::get('services', [ServiceController::class, 'index'])->name('services.index');
    Route::get('services/{service}', [ServiceController::class, 'show'])->name('services.show');

    // Order tracking (customer-facing).
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    // Wishlist / favoritos.
    Route::get('wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('wishlist/{product}', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('wishlist/{product}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');

    // Customer support tickets.
    Route::get('tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::post('tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');
});

require __DIR__.'/settings.php';
require __DIR__.'/admin.php';
