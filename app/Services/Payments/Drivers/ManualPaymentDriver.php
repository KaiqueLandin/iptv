<?php

namespace App\Services\Payments\Drivers;

use App\Models\Invoice;
use App\Models\PaymentGateway;
use App\Services\Payments\PaymentDriver;
use Illuminate\Http\Request;

/**
 * Manual / offline payment driver.
 *
 * Acts like WHMCS's "Bank Transfer / Offline" gateway: it doesn't talk to any
 * external API. The charge is created as pending and an admin confirms the
 * payment manually (or the customer marks they paid via Pix key, etc.).
 *
 * This is the safe default that lets the full checkout flow work end-to-end
 * before real gateways (Mercado Pago, Asaas) are plugged in.
 */
class ManualPaymentDriver implements PaymentDriver
{
    public function key(): string
    {
        return 'manual';
    }

    public function createCharge(Invoice $invoice, PaymentGateway $gateway): array
    {
        $settings = $gateway->settings ?? [];

        return [
            'gateway_transaction_id' => null,
            'method' => 'manual',
            'pix_qr_code' => null,
            'pix_copy_paste' => $settings['pix_key'] ?? null,
            'boleto_url' => null,
            'redirect_url' => null,
            'instructions' => $settings['instructions']
                ?? 'Realize o pagamento e aguarde a confirmação manual pelo suporte.',
            'raw' => [],
        ];
    }

    public function handleWebhook(array $payload, PaymentGateway $gateway): array
    {
        // Manual gateway has no webhook; confirmation happens via admin action.
        return [
            'gateway_transaction_id' => $payload['transaction_id'] ?? null,
            'status' => 'unknown',
        ];
    }

    public function verifyWebhook(Request $request, PaymentGateway $gateway): bool
    {
        // The manual gateway never receives webhooks, so it can never be
        // authenticated. Confirmation is always done manually by an admin.
        return false;
    }
}
