<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Services\Payments\PaymentManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentManager $payments,
    ) {}

    /**
     * Create a charge for the invoice using the chosen gateway, then redirect
     * back to the payment page showing the charge details (Pix/boleto/etc).
     */
    public function store(Request $request, Invoice $invoice): RedirectResponse
    {
        abort_unless($invoice->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'gateway_id' => ['required', 'exists:payment_gateways,id'],
        ]);

        if ($invoice->status->value === 'paid') {
            return redirect()->route('checkout.show', $invoice)
                ->with('info', 'Esta fatura já foi paga.');
        }

        $gateway = PaymentGateway::active()->findOrFail($validated['gateway_id']);
        $driver = $this->payments->driver($gateway->gateway_key);

        $charge = $driver->createCharge($invoice, $gateway);

        $fee = $gateway->calculateFee((float) $invoice->total);

        Payment::create([
            'invoice_id' => $invoice->id,
            'user_id' => $invoice->user_id,
            'payment_gateway_id' => $gateway->id,
            'amount' => $invoice->total,
            'fee' => $fee,
            'net_amount' => (float) $invoice->total - $fee,
            'status' => 'pending',
            'gateway_transaction_id' => $charge['gateway_transaction_id'] ?? null,
            'gateway_response' => $charge['raw'] ?? [],
            'payment_method' => $charge['method'] ?? $gateway->gateway_key,
        ]);

        return redirect()->route('checkout.show', $invoice)
            ->with('charge', $charge);
    }
}
