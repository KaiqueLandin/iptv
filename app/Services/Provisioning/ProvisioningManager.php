<?php

namespace App\Services\Provisioning;

use App\Services\Provisioning\Drivers\SimulatedProvisioningDriver;

/**
 * Resolves the provisioning driver used to generate IPTV activation codes.
 *
 * The active driver is configurable; until a real IPTV panel integration is
 * added, it defaults to the simulated driver. New panels register here.
 */
class ProvisioningManager
{
    /**
     * Map of driver key => class.
     *
     * @var array<string, class-string<ProvisioningDriver>>
     */
    protected array $drivers = [
        'simulated' => SimulatedProvisioningDriver::class,
        // 'xtream' => XtreamProvisioningDriver::class,
    ];

    /**
     * Resolve the driver for the given key, or the configured default.
     */
    public function driver(?string $key = null): ProvisioningDriver
    {
        $key = $key ?? config('provisioning.driver', 'simulated');

        $class = $this->drivers[$key] ?? SimulatedProvisioningDriver::class;

        return app($class);
    }
}
