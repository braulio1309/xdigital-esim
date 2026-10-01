<?php

namespace Tests\Feature\Security;

use Symfony\Component\HttpFoundation\Cookie;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    /** @test */
    public function application_responses_include_security_headers_and_csrf_cookie_uses_lax_same_site(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy')
            ->assertHeader('Cross-Origin-Opener-Policy', 'same-origin-allow-popups')
            ->assertHeader('Cross-Origin-Resource-Policy', 'same-site')
            ->assertHeader('Content-Security-Policy');

        $csrfCookie = collect($response->headers->getCookies())
            ->first(function (Cookie $cookie) {
                return $cookie->getName() === 'XSRF-TOKEN';
            });

        $this->assertNotNull($csrfCookie);
        $this->assertFalse($csrfCookie->isHttpOnly());
        $this->assertSame('lax', $csrfCookie->getSameSite());
    }
}