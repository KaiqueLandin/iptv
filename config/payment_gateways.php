<?php

/*
|--------------------------------------------------------------------------
| Payment Gateway Presets
|--------------------------------------------------------------------------
|
| Defines the supported payment gateways and the credential fields each one
| requires. The admin UI uses this to render the correct configuration form,
| and the controller uses the field keys to validate/whitelist credentials.
|
| Field "type" maps to an HTML input type ("text" or "password"). Secret
| values should use "password" so they are masked in the UI.
|
*/

return [
    'presets' => [
        'asaas' => [
            'name' => 'Asaas',
            'description' => 'Pagamentos via Pix, boleto e cartão (Asaas).',
            'fields' => [
                ['key' => 'api_key', 'label' => 'API Key', 'type' => 'password', 'required' => true],
                ['key' => 'webhook_token', 'label' => 'Token do Webhook', 'type' => 'password', 'required' => false],
            ],
        ],
        'mercadopago' => [
            'name' => 'Mercado Pago',
            'description' => 'Pix, boleto e cartão via Mercado Pago.',
            'fields' => [
                ['key' => 'access_token', 'label' => 'Access Token', 'type' => 'password', 'required' => true],
                ['key' => 'public_key', 'label' => 'Public Key', 'type' => 'text', 'required' => true],
                ['key' => 'webhook_secret', 'label' => 'Webhook Secret', 'type' => 'password', 'required' => false],
            ],
        ],
        'pagseguro' => [
            'name' => 'PagSeguro',
            'description' => 'Checkout PagSeguro (PagBank).',
            'fields' => [
                ['key' => 'email', 'label' => 'E-mail da conta', 'type' => 'text', 'required' => true],
                ['key' => 'token', 'label' => 'Token', 'type' => 'password', 'required' => true],
            ],
        ],
        'stripe' => [
            'name' => 'Stripe',
            'description' => 'Cartão internacional via Stripe.',
            'fields' => [
                ['key' => 'publishable_key', 'label' => 'Publishable Key', 'type' => 'text', 'required' => true],
                ['key' => 'secret_key', 'label' => 'Secret Key', 'type' => 'password', 'required' => true],
                ['key' => 'webhook_secret', 'label' => 'Webhook Secret', 'type' => 'password', 'required' => false],
            ],
        ],
        'paypal' => [
            'name' => 'PayPal',
            'description' => 'Pagamentos internacionais via PayPal.',
            'fields' => [
                ['key' => 'client_id', 'label' => 'Client ID', 'type' => 'text', 'required' => true],
                ['key' => 'client_secret', 'label' => 'Client Secret', 'type' => 'password', 'required' => true],
            ],
        ],
    ],
];
