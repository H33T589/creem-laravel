<?php

namespace Creem\Laravel\Tests;

// Use Orchestra Testbench instead of standard PHPUnit TestCase
use Orchestra\Testbench\TestCase;
use Creem\Laravel\Creem;
use Creem\Laravel\CreemServiceProvider;
use Illuminate\Support\Facades\Http;

class CreemTest extends TestCase
{
    /**
     * Get package providers.
     */
    protected function getPackageProviders($app): array
    {
        return [
            CreemServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     */
    protected function getEnvironmentSetUp($app): void
    {
        // Set up default config for tests
        $app['config']->set('creem.api_key', 'test_api_key');
        $app['config']->set('creem.api_url', 'https://api.creem.io/v1');
    }

    /** @test */
    public function it_can_be_instantiated()
    {
        // We can resolve it from the container now
        $creem = $this->app->make(Creem::class);
        $this->assertInstanceOf(Creem::class, $creem);
    }

    /** @test */
    public function it_can_create_a_checkout_session()
    {
        // Mock the HTTP request
        Http::fake([
            'https://api.creem.io/v1/*' => Http::response(['id' => 'sess_123', 'status' => 'open'], 200),
        ]);

        $creem = $this->app->make(Creem::class);
        
        $response = $creem->createCheckoutSession([
            'amount' => 1000,
            'currency' => 'usd'
        ]);

        $this->assertEquals('sess_123', $response['id']);
        $this->assertEquals('open', $response['status']);
    }

    /** @test */
    public function it_throws_exception_on_api_failure()
    {
        // Mock a failed response
        Http::fake([
            'https://api.creem.io/v1/*' => Http::response(['error' => 'Invalid request'], 400),
        ]);

        $this->expectException(\Exception::class);
        // The message starts with "CREEM API Error"
        $this->expectExceptionMessage('CREEM API Error');

        $creem = $this->app->make(Creem::class);
        $creem->createCheckoutSession(['amount' => -50]);
    }
    
    /** @test */
    public function it_verifies_webhooks_correctly()
    {
        $secret = 'webhook_secret';
        $payload = '{"event": "payment.successful"}';
        
        // Generate a valid signature
        $validSignature = hash_hmac('sha256', $payload, $secret);
        
        $creem = new Creem('test-key', 'https://api.creem.io/v1');
        
        $this->assertTrue($creem->verifyWebhook($payload, $validSignature, $secret));
        $this->assertFalse($creem->verifyWebhook($payload, 'invalid_signature', $secret));
    }

        /** @test */
    public function it_can_retrieve_a_checkout_session()
    {
        Http::fake([
            'https://api.creem.io/v1/*' => Http::response(['id' => 'sess_999', 'status' => 'completed'], 200),
        ]);

        $creem = $this->app->make(Creem::class);
        $response = $creem->getCheckoutSession('sess_999');

        $this->assertEquals('sess_999', $response['id']);
    }
}