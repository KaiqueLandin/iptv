<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Services\Payments\PaymentManager;
use App\Services\Provisioning\ProvisioningService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function __construct(
        protected PaymentManager $payments,
        protected ProvisioningService $provisioning,
    ) {}

    /**
     * Receive a gateway webhook/callback, confirm the matching payment, and
     * trigger provisioning + delivery. This is the equivalent of WHMCS's
     * modules/gateways/callback/* scripts.
     */
    public function handle(Request $request, string $gatewayKey): JsonResponse
    {
        $gateway = PaymentGateway::where('gateway_key', $gatewayKey)->first();

        if (! $gateway) {
            return response()->json(['message' => 'Gateway not found'], 404);
        }

        $driver = $this->payments->driver($gateway->gateway_key);

        // 1) Authenticity: reject any webhook we can't verify as coming from
        // the real gateway (signature/HMAC/secret). This prevents forged
        // "paid" callbacks from triggering free provisioning.
        if (! $driver->verifyWebhook($request, $gateway)) {
            Log::warning('Webhook rejeitado: assinatura/autenticidade inválida', [
                'gateway' => $gatewayKey,
                'ip' => $request->ip(),
            ]);

            return response()->json(['message' => 'invalid signature'], 403);
        }

        $result = $driver->handleWebhook($request->all(), $gateway);

        if (($result['status'] ?? 'unknown') !== 'paid') {
            // Nothing to do yet (pending/failed/unknown).
            return response()->json(['message' => 'ignored']);
        }

        // 2) A confirmed payment MUST resolve to a specific gateway transaction.
        // Without it we cannot safely identify which invoice was paid.
        $transactionId = $result['gateway_transaction_id'] ?? null;

        if (! $transactionId) {
            Log::warning('Webhook pago sem gateway_transaction_id; ignorado', [
                'gateway' => $gatewayKey,
            ]);

            return response()->json(['message' => 'missing transaction id'], 422);
        }

        $payment = Payment::query()
            ->where('payment_gateway_id', $gateway->id)
            ->where('gateway_transaction_id', $transactionId)
            ->where('status', PaymentStatus::PENDING)
            ->first();

        if (! $payment) {
            Log::warning('Webhook pago sem pagamento pendente correspondente', [
                'gateway' => $gatewayKey,
                'tx' => $transactionId,
            ]);

            return response()->json(['message' => 'no matching payment']);
        }

        $payment->markAsCompleted();
        $this->provisioning->fulfillInvoice($payment->invoice);

        return response()->json(['message' => 'ok']);
    }
}
