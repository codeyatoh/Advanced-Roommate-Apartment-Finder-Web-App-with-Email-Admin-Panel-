<?php

class StripeService {
    private $apiKey;
    private $apiUrl = 'https://api.stripe.com/v1';

    public function __construct() {
        // Hardcoded for demo purposes, ideally from env/config
        $this->apiKey = 'sk_test_51QO8q2G4k5y6Z7x8...'; // Placeholder, user needs to provide real key or I'll use a dummy one for structure
    }

    private function request($endpoint, $method = 'GET', $data = []) {
        $ch = curl_init();
        $url = $this->apiUrl . $endpoint;

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->apiKey . ':');
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public function createCheckoutSession($amount, $currency = 'php', $successUrl, $cancelUrl, $metadata = []) {
        // Mock Mode for Testing
        if (strpos($this->apiKey, 'sk_test_51QO8q2G4k5y6Z7x8') !== false) {
            return [
                'id' => 'cs_test_' . uniqid(),
                'url' => $successUrl . '&session_id=cs_test_' . uniqid() // Redirect directly to success
            ];
        }

        $data = [
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => 'Rent Payment',
                    ],
                    'unit_amount' => $amount * 100, // Amount in cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'metadata' => $metadata,
        ];

        return $this->request('/checkout/sessions', 'POST', $data);
    }
}
