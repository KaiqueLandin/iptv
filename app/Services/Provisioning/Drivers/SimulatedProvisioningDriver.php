<?php

namespace App\Services\Provisioning\Drivers;

use App\Models\Service;
use App\Services\Provisioning\ProvisioningDriver;
use Illuminate\Support\Str;

/**
 * Generates a fake IPTV activation code/credentials without calling any
 * external panel. Used as the default so the purchase → delivery flow works
 * end-to-end before the real IPTV panel API (Xtream, etc.) is integrated.
 */
class SimulatedProvisioningDriver implements ProvisioningDriver
{
    public function key(): string
    {
        return 'simulated';
    }

    public function provision(Service $service): array
    {
        $username = 'usr_'.Str::lower(Str::random(8));
        $password = Str::random(12);

        return [
            'username' => $username,
            'password' => $password,
            'activation_code' => strtoupper(Str::random(4).'-'.Str::random(4).'-'.Str::random(4)),
            'access_url' => config('app.url').'/iptv/'.$username.'.m3u',
            'expires_at' => now()->addMonth(),
            'raw' => [
                'provider' => 'simulated',
                'generated_at' => now()->toIso8601String(),
            ],
        ];
    }
}
