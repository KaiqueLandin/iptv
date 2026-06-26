<?php

namespace App\Services\Payments;

use App\Models\Invoice;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;

/**
 * A payment driver wraps a single gateway provider (Mercado Pago, Asaas, ...).
 *
 * Each driver knows how to create a charge for an invoice and how to
 * interpret the gateway's webhook callback. This mirrors the
 * modules/gateways/* concept in WHMCS.
 */
interface PaymentDriver
{
    /**
     * Unique key matching PaymentGateway::$gateway_key (e.g. "mercadopago").
     */
    public function key(): string;

    /**
     * Create a charge for the given invoice.
     *
     * Returns a normalized structure the checkout UI can render, e.g.:
     * [
     *   'gateway_transaction_id' => '...',
     *   'method' => 'pix'|'boleto'|'card'|'manual',
     *   'pix_qr_code' => '...'|null,
     *   'pix_copy_paste' => '...'|null,
     *   'boleto_url' => '...'|null,
     *   'redirect_url' => '...'|null,
     *   'instructions' => '...'|null,
     *   'raw' => [...],
     * ]
     *
     * @return array<string, mixed>
     */
    public function createCharge(Invoice $invoice, PaymentGateway $gateway): array;

    /**
     * Verify the authenticity of an incoming webhook request (signature/HMAC,
     * shared secret, source IP, etc.) BEFORE any payment is confirmed.
     *
     * Drivers that cannot receive webhooks (e.g. the manual gateway) must
     * return false so no automated confirmation happens.
     */
    public function verifyWebhook(Request $request, PaymentGateway $gateway): bool;

    /**
     * Interpret an incoming webhook payload and return a normalized result:
     * [
     *   'gateway_transaction_id' => '...',
     *   'status' => 'paid'|'pending'|'failed'|'unknown',
     * ]
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function handleWebhook(array $payload, PaymentGateway $gateway): array;
}
