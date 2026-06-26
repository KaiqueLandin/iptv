<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Provisioning Driver
    |--------------------------------------------------------------------------
    |
    | Which driver generates the IPTV activation code/credentials after a
    | payment is confirmed. "simulated" generates a fake code locally. When the
    | real IPTV panel API is integrated, register it in ProvisioningManager and
    | set PROVISIONING_DRIVER accordingly (e.g. "xtream").
    |
    */
    'driver' => env('PROVISIONING_DRIVER', 'simulated'),
];
