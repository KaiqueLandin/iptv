<?php

namespace App\Services\Checkout;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Creates the Order + Invoice for a purchase, mirroring what WHMCS does when
 * a product is added to the cart and checked out.
 */
class CheckoutService
{
    /**
     * Start a purchase: create a pending order and an unpaid invoice.
     */
    public function purchase(User $user, Product $product): Invoice
    {
        return DB::transaction(function () use ($user, $product) {
            $order = Order::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'status' => 'pending',
                'amount' => $product->price,
                'billing_cycle' => $product->billing_cycle?->value ?? 'monthly',
            ]);

            $invoice = Invoice::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'status' => 'unpaid',
                'due_date' => now()->addDay(),
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $product->name,
                'quantity' => 1,
                'unit_price' => $product->price,
            ]);

            // InvoiceItem::saved() recalculates the invoice totals.
            return $invoice->fresh(['items', 'order']);
        });
    }
}
