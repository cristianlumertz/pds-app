<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_web_response_includes_security_headers(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy');
        $response->assertHeader('Content-Security-Policy');
        $response->assertHeaderMissing('Strict-Transport-Security');

        $csp = $response->headers->get('Content-Security-Policy');

        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString("object-src 'none'", $csp);
        $this->assertStringContainsString("frame-ancestors 'none'", $csp);
        $this->assertStringNotContainsString('upgrade-insecure-requests', $csp);
        $this->assertStringContainsString('http://localhost:5173', $csp);
        $this->assertStringContainsString('http://127.0.0.1:5173', $csp);
        $this->assertStringContainsString('ws://localhost:5173', $csp);
        $this->assertStringContainsString('ws://127.0.0.1:5173', $csp);
    }

    public function test_production_response_keeps_hsts_and_upgrade_insecure_requests(): void
    {
        $this->app->detectEnvironment(fn () => 'production');

        $response = $this->get('/');

        $response->assertOk();
        $response->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        $csp = $response->headers->get('Content-Security-Policy');

        $this->assertStringContainsString('upgrade-insecure-requests', $csp);
        $this->assertStringNotContainsString('http://localhost:5173', $csp);
        $this->assertStringNotContainsString('ws://localhost:5173', $csp);
    }
}
