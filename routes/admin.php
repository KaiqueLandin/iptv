<?php

use App\Http\Controllers\Admin\AdminAffiliateController;
use App\Http\Controllers\Admin\AdminBrandController;
use App\Http\Controllers\Admin\AdminCouponController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDomainController;
use App\Http\Controllers\Admin\AdminEmailTemplateController;
use App\Http\Controllers\Admin\AdminInvoiceController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminPaymentGatewayController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminServerModuleController;
use App\Http\Controllers\Admin\AdminSubscriptionController;
use App\Http\Controllers\Admin\AdminTicketController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminWebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->middleware(['auth', 'verified', 'admin'])
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');

        // Users Management
        Route::resource('users', AdminUserController::class);

        // Products Management
        Route::resource('products', AdminProductController::class);

        // Brands Management
        Route::resource('brands', AdminBrandController::class)->except(['show']);

        // Orders Management
        Route::resource('orders', AdminOrderController::class)->except(['create', 'store']);
        Route::post('orders/{order}/activate', [AdminOrderController::class, 'activate'])->name('orders.activate');
        Route::post('orders/{order}/suspend', [AdminOrderController::class, 'suspend'])->name('orders.suspend');
        Route::post('orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('orders.cancel');

        // Invoices Management
        Route::resource('invoices', AdminInvoiceController::class);
        Route::post('invoices/{invoice}/mark-paid', [AdminInvoiceController::class, 'markPaid'])->name('invoices.mark-paid');
        Route::post('invoices/{invoice}/send', [AdminInvoiceController::class, 'send'])->name('invoices.send');

        // Subscriptions Management
        Route::resource('subscriptions', AdminSubscriptionController::class)->except(['create', 'store']);
        Route::post('subscriptions/{subscription}/pause', [AdminSubscriptionController::class, 'pause'])->name('subscriptions.pause');
        Route::post('subscriptions/{subscription}/resume', [AdminSubscriptionController::class, 'resume'])->name('subscriptions.resume');
        Route::post('subscriptions/{subscription}/cancel', [AdminSubscriptionController::class, 'cancel'])->name('subscriptions.cancel');

        // Tickets/Support Management
        Route::resource('tickets', AdminTicketController::class);
        Route::post('tickets/{ticket}/reply', [AdminTicketController::class, 'reply'])->name('tickets.reply');
        Route::post('tickets/{ticket}/close', [AdminTicketController::class, 'close'])->name('tickets.close');
        Route::post('tickets/{ticket}/reopen', [AdminTicketController::class, 'reopen'])->name('tickets.reopen');

        // Coupons Management
        Route::resource('coupons', AdminCouponController::class);

        // Affiliates Management
        Route::resource('affiliates', AdminAffiliateController::class)->only(['index', 'show', 'update']);
        Route::post('affiliates/{affiliate}/approve', [AdminAffiliateController::class, 'approve'])->name('affiliates.approve');
        Route::get('affiliate-commissions', [AdminAffiliateController::class, 'commissions'])->name('affiliate-commissions.index');
        Route::post('affiliate-commissions/{commission}/approve', [AdminAffiliateController::class, 'approveCommission'])->name('affiliate-commissions.approve');
        Route::get('affiliate-payouts', [AdminAffiliateController::class, 'payouts'])->name('affiliate-payouts.index');
        Route::post('affiliate-payouts/{payout}/complete', [AdminAffiliateController::class, 'completePayout'])->name('affiliate-payouts.complete');

        // Payments Management
        Route::resource('payments', AdminPaymentController::class)->only(['index', 'show']);

        // Payment Gateways Configuration
        Route::resource('payment-gateways', AdminPaymentGatewayController::class);

        // Email Templates Management
        Route::resource('email-templates', AdminEmailTemplateController::class);

        // Webhooks Management
        Route::resource('webhooks', AdminWebhookController::class);
        Route::get('webhooks/{webhook}/logs', [AdminWebhookController::class, 'logs'])->name('webhooks.logs');

        // Server Modules Configuration
        Route::resource('server-modules', AdminServerModuleController::class);
        Route::post('server-modules/{serverModule}/test', [AdminServerModuleController::class, 'testConnection'])->name('server-modules.test');

        // Domains Management
        Route::resource('domains', AdminDomainController::class);
        Route::post('domains/{domain}/renew', [AdminDomainController::class, 'renew'])->name('domains.renew');

        // Reports
        Route::get('reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('reports/revenue', [AdminReportController::class, 'revenue'])->name('reports.revenue');
        Route::get('reports/customers', [AdminReportController::class, 'customers'])->name('reports.customers');
        Route::get('reports/products', [AdminReportController::class, 'products'])->name('reports.products');
    });
