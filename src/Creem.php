<?php

namespace Creem\Laravel;

use Illuminate\Support\Facades\Http;
use Exception;

class Creem
{
    /**
     * The API key for authenticating with CREEM.
     */
    protected string $apiKey;

    /**
     * The base URL for the CREEM API.
     */
    protected string $apiUrl;

    /**
     * Create a new Creem instance.
     */
    public function __construct(string $apiKey, string $apiUrl)
    {
        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl;
    }

    /**
     * Create a new checkout session.
     *
     * @param array $data The data for the session (e.g., amount, currency)
     * @return array The API response
     * @throws Exception
     */
    public function createCheckoutSession(array $data): array
    {
        return $this->request('POST', '/checkout/sessions', $data);
    }

    /**
     * Retrieve a checkout session by ID.
     *
     * @param string $sessionId
     * @return array
     * @throws Exception
     */
    public function getCheckoutSession(string $sessionId): array
    {
        return $this->request('GET', "/checkout/sessions/{$sessionId}");
    }

    /**
     * Verify a webhook signature.
     *
     * @param string $payload The raw request body
     * @param string $signature The signature from the header
     * @param string $secret The webhook secret
     * @return bool
     */
    public function verifyWebhook(string $payload, string $signature, string $secret): bool
    {
        $expectedSignature = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Make a request to the CREEM API.
     *
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $endpoint API endpoint
     * @param array $data Request data
     * @return array
     * @throws Exception
     */
    protected function request(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->apiUrl . $endpoint;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->{$method}($url, $data);

        if ($response->failed()) {
            throw new Exception("CREEM API Error: " . $response->body());
        }

        return $response->json();
    }
}