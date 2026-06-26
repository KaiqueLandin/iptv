<?php

namespace App\Services\Provisioning;

use App\Enums\ServiceStatus;
use App\Models\Invoice;
use App\Models\Service;
use App\Notifications\ServiceActivatedNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Runs after a payment is confirmed: activates the order, provisions the IPTV
 * service (generates the activation code via the configured driver), stores it
 * on the customer's account, and delivers it (email/WhatsApp).
 *
 * This is the SaaS equivalent of WHMCS's "module CreateAccount + welcome email"
 * automation that fires when an invoice is marked Paid.
 */
class ProvisioningService
{
    public function __construct(
        protected ProvisioningManager $manager,
    ) {}

    /**
     * Provision and deliver the service tied to a paid invoice.
     *
     * Idempotent: if the order already has a service, it is returned as-is.
     */
    public function fulfillInvoice(Invoice $invoice): ?Service
    {
        $order = $invoice->order;

        if (! $order) {
            return null;
        }

        // Mark the order active (it was pending until payment).
        $order->activate();

        // Don't double-provision if a webhook is delivered twice.
        if ($existing = $order->service()->first()) {
            return $existing;
        }

        $driver = $this->manager->driver();

        $service = Service::create([
            'user_id' => $order->user_id,
            'product_id' => $order->product_id,
            'order_id' => $order->id,
            'status' => ServiceStatus::PENDING,
            'provider' => $driver->key(),
            'starts_at' => now(),
        ]);

        try {
            $result = $driver->provision($service);

            $service->fill([
                'username' => $result['username'] ?? null,
                'password' => $result['password'] ?? null,
                'activation_code' => $result['activation_code'] ?? null,
                'access_url' => $result['access_url'] ?? null,
                'provision_data' => $result['raw'] ?? null,
                'expires_at' => isset($result['expires_at'])
                    ? Carbon::parse($result['expires_at'])
                    : null,
            ]);
            $service->save();
            $service->markActive();
        } catch (\Throwable $e) {
            Log::error('Falha ao provisionar serviço', [
                'service_id' => $service->id,
                'error' => $e->getMessage(),
            ]);

            $service->markFailed($e->getMessage());

            return $service;
        }

        $this->deliver($service);

        return $service->refresh();
    }

    /**
     * Deliver the activation code to the customer via email (and, when
     * configured, WhatsApp). The code is always saved to the dashboard too.
     *
     * @return array<int, string>
     */
    public function deliver(Service $service): array
    {
        $channels = [];

        try {
            $service->user->notify(new ServiceActivatedNotification($service));
            $channels[] = 'email';
        } catch (\Throwable $e) {
            Log::warning('Falha ao enviar e-mail de ativação', [
                'service_id' => $service->id,
                'error' => $e->getMessage(),
            ]);
        }

        // WhatsApp delivery is a no-op placeholder until a provider
        // (Evolution API / Z-API / Twilio) is configured.
        if (config('services.whatsapp.enabled')) {
            $channels[] = $this->sendWhatsApp($service) ? 'whatsapp' : 'whatsapp_failed';
        }

        if ($channels) {
            $service->markDelivered($channels);
        }

        return $channels;
    }

    /**
     * Placeholder for WhatsApp delivery. Returns false until implemented.
     */
    protected function sendWhatsApp(Service $service): bool
    {
        Log::info('WhatsApp delivery solicitada mas não configurada', [
            'service_id' => $service->id,
        ]);

        return false;
    }
}
