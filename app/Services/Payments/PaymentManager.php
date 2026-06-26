<?php

namespace App\Services\Payments;

use App\Services\Payments\Drivers\ManualPaymentDriver;
use InvalidArgumentException;

/**
 * Resolves a PaymentDriver implementation for a given gateway key.
 *
 * New gateways (Mercado Pago, Asaas, PagSeguro, ...) are registered here.
 * Until a real driver is implemented, gateways fall back to the manual
 * driver so the checkout flow keeps working end-to-end.
 */
class PaymentManager
{
    /**
     * Map of gateway_key => driver class.
     *
     * @var array<string, class-string<PaymentDriver>>
     */
    protected array $drivers = [
        'manual' => ManualPaymentDriver::class,
        // 'mercadopago' => MercadoPagoDriver::class,
        // 'asaas' => AsaasDriver::class,
        // 'pagseguro' => PagSeguroDriver::class,
    ];

    public function driver(string $gatewayKey): PaymentDriver
    {
        $class = $this->drivers[$gatewayKey] ?? null;

        if (! $class) {
            // Unknown/not-yet-implemented gateway: degrade to manual so the
            // purchase can still be recorded and confirmed by an admin.
            return app(ManualPaymentDriver::class);
        }

        $driver = app($class);

        if (! $driver instanceof PaymentDriver) {
            throw new InvalidArgumentException("Driver inválido para gateway [{$gatewayKey}].");
        }

        return $driver;
    }

    public function supports(string $gatewayKey): bool
    {
        return isset($this->drivers[$gatewayKey]);
    }
}
