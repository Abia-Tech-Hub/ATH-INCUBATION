<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    public function test_home_page_returns_the_security_headers_and_content_security_policy(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Content-Security-Policy');

        $this->assertStringContainsString(
            "script-src 'self' 'nonce-",
            (string) $response->headers->get('Content-Security-Policy'),
        );
    }
}
