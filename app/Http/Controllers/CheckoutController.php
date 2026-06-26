<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\Invoice;
use App\Models\PaymentGateway;
use App\Models\Product;
use App\Services\Checkout\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function __construct(
        protected CheckoutService $checkout,
    ) {}

    /**
     * Entry point for the public landing page "Buy" buttons.
     *
     * Guests are sent to registration (which collects their phone) and brought
     * back here afterwards; authenticated users go straight to checkout.
     *
     * Idempotent: if the user already has a pending unpaid invoice for this
     * product, they are sent to that invoice instead of creating a duplicate.
     */
    public function buy(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->is_active, 404);

        if (! $request->user()) {
            $request->session()->put('url.intended', route('checkout.buy', $product));

            return redirect()->route('register');
        }

        // Return existing pending invoice instead of creating duplicates.
        $existing = Invoice::query()
            ->whereNotNull('order_id')
            ->whereHas('order', fn ($q) => $q
                ->where('user_id', $request->user()->id)
                ->where('product_id', $product->id)
                ->where('status', 'pending')
            )
            ->where('status', 'unpaid')
            ->latest()
            ->first();

        if ($existing) {
            return redirect()->route('checkout.show', $existing);
        }

        $invoice = $this->checkout->purchase($request->user(), $product);

        return redirect()->route('checkout.show', $invoice);
    }

    /**
     * Start a purchase for a product and redirect to the invoice payment page.
     */
    public function store(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->is_active, 404);

        $invoice = $this->checkout->purchase($request->user(), $product);

        return redirect()->route('checkout.show', $invoice);
    }

    /**
     * Show the payment page for an invoice: list active gateways.
     */
    public function show(Request $request, Invoice $invoice): Response
    {
        abort_unless($invoice->user_id === $request->user()->id, 403);

        $invoice->load(['items', 'order.product.crossSells', 'payments']);

        $gateways = PaymentGateway::active()->get()->map(fn (PaymentGateway $gateway) => [
            'id' => $gateway->id,
            'name' => $gateway->name,
            'gateway_key' => $gateway->gateway_key,
            'description' => $gateway->description,
        ]);

        $crossSells = $invoice->order?->product?->crossSells
            ->where('is_active', true)
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->price,
                'description' => $product->description
                    ? strip_tags($product->description)
                    : null,
                'checkoutUrl' => route('checkout.store', $product),
            ])
            ->values() ?? collect();

        return Inertia::render('Checkout/Show', [
            'invoice' => [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'status' => $invoice->status->value,
                'total' => $invoice->total,
                'items' => $invoice->items->map(fn ($item) => [
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'amount' => $item->amount,
                ]),
            ],
            'gateways' => $gateways,
            'crossSells' => $crossSells,
            'charge' => session('charge'),
            'pendingPayment' => $invoice->payments
                ->firstWhere('status', PaymentStatus::PENDING)?->only([
                    'id', 'transaction_id', 'payment_method',
                ]),
        ]);
    }
}
