<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class PayPalService
{
    private $client;
    private $apiUrl;
    private $authHeader;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiUrl = env('PAYPAL_API_URL'); // PayPal API base URL
        $this->authHeader = 'Bearer ' . $this->getAccessToken();
    }

    private function getAccessToken()
    {
        try {
            $response = $this->client->post($this->apiUrl . '/v1/oauth2/token', [
                'auth' => [env('PAYPAL_CLIENT_ID'), env('PAYPAL_CLIENT_SECRET')],
                'form_params' => [
                    'grant_type' => 'client_credentials'
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            return $data['access_token'];
        } catch (\Exception $e) {
            Log::error('PayPal Access Token Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function createOrder($amount)
    {
        try {
            $response = $this->client->post($this->apiUrl . '/v2/checkout/orders', [
                'headers' => [
                    'Authorization' => $this->authHeader,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [
                        [
                            'amount' => [
                                'currency_code' => 'USD',
                                'value' => number_format($amount, 2, '.', ''),
                            ]
                        ]
                    ],
                    'application_context' => [
                        'return_url' => url('/paypal/execute'),
                    ]
                ]
            ]);




            $data = json_decode($response->getBody(), true);

            return $data['links'][1]['href']; // Redirect URL
        } catch (\Exception $e) {
            Log::error('PayPal Order Creation Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function captureOrder($orderId)
    {
        try {
            $response = $this->client->post($this->apiUrl . '/v2/checkout/orders/' . $orderId . '/capture', [
                'headers' => [
                    'Authorization' => $this->authHeader,
                    'Content-Type' => 'application/json',
                ]
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('PayPal Order Capture Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
