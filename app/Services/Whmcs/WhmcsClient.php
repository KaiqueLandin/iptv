<?php

namespace App\Services\Whmcs;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhmcsClient
{
    protected string $apiUrl;

    protected string $identifier;

    protected string $secret;

    public function __construct()
    {
        $this->apiUrl = config('services.whmcs.api_url');
        $this->identifier = config('services.whmcs.api_identifier');
        $this->secret = config('services.whmcs.api_secret');
    }

    /**
     * Make a request to the WHMCS API
     */
    protected function request(string $action, array $params = []): array
    {
        try {
            $response = Http::timeout(30)
                ->asForm()
                ->post($this->apiUrl, array_merge([
                    'action' => $action,
                    'identifier' => $this->identifier,
                    'secret' => $this->secret,
                    'responsetype' => 'json',
                ], $params));

            if ($response->failed()) {
                Log::error('WHMCS API request failed', [
                    'action' => $action,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                throw new \Exception('WHMCS API request failed');
            }

            $data = $response->json();

            if (isset($data['result']) && $data['result'] === 'error') {
                Log::error('WHMCS API returned error', [
                    'action' => $action,
                    'message' => $data['message'] ?? 'Unknown error',
                ]);

                throw new \Exception($data['message'] ?? 'WHMCS API error');
            }

            return $data;
        } catch (\Exception $e) {
            Log::error('WHMCS API exception', [
                'action' => $action,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Get products from WHMCS
     */
    public function getProducts(array $productIds = []): array
    {
        $params = [];

        if (! empty($productIds)) {
            $params['pid'] = implode(',', $productIds);
        }

        return $this->request('GetProducts', $params);
    }

    /**
     * Get or create a client in WHMCS
     */
    public function getOrCreateClient(string $email, array $clientData = []): array
    {
        // Try to find existing client
        try {
            $response = $this->request('GetClientsDetails', [
                'email' => $email,
                'stats' => false,
            ]);

            if (isset($response['userid'])) {
                return $response;
            }
        } catch (\Exception $e) {
            // Client not found, create new one
        }

        // Create new client
        return $this->request('AddClient', array_merge([
            'email' => $email,
            'country' => 'BR',
            'phonenumber' => $clientData['phone'] ?? '',
        ], $clientData));
    }

    /**
     * Create SSO token for client area
     */
    public function createSsoToken(int $clientId): string
    {
        $response = $this->request('CreateSsoToken', [
            'client_id' => $clientId,
        ]);

        return $response['access_token'] ?? '';
    }

    /**
     * Get client details
     */
    public function getClientDetails(int $clientId): array
    {
        return $this->request('GetClientsDetails', [
            'clientid' => $clientId,
            'stats' => true,
        ]);
    }

    /**
     * Get invoices for a client
     */
    public function getInvoices(int $clientId, int $limit = 10): array
    {
        return $this->request('GetInvoices', [
            'userid' => $clientId,
            'limitnum' => $limit,
            'orderby' => 'date',
            'order' => 'DESC',
        ]);
    }

    /**
     * Get orders for a client
     */
    public function getOrders(int $clientId, int $limit = 10): array
    {
        return $this->request('GetOrders', [
            'userid' => $clientId,
            'limitnum' => $limit,
        ]);
    }
}
