<?php

namespace App\Services\Provisioning;

use App\Models\Service;

/**
 * A provisioning driver creates the actual IPTV line / activation code for a
 * service once payment is confirmed. This is the equivalent of WHMCS's
 * modules/servers/* CreateAccount() function.
 *
 * A real driver (e.g. XtreamProvisioningDriver) would call the IPTV panel
 * API. The SimulatedProvisioningDriver generates a fake code so the whole
 * flow works before the panel API is available.
 */
interface ProvisioningDriver
{
    /**
     * Unique key (e.g. "simulated", "xtream").
     */
    public function key(): string;

    /**
     * Provision the service and return the credentials to store:
     * [
     *   'username' => '...',
     *   'password' => '...',
     *   'activation_code' => '...',
     *   'access_url' => '...'|null,
     *   'expires_at' => CarbonInterface|string|null,
     *   'raw' => [...],
     * ]
     *
     * @return array<string, mixed>
     */
    public function provision(Service $service): array;
}
