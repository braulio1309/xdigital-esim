<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SecureHeaders.
 */
class SecureHeaders
{
    public function handle($request, Closure $next)
    {
        if (!app()->environment(['local', 'testing']) && !$request->isSecure()) {
            return redirect()->secure($request->getRequestUri(), Response::HTTP_PERMANENTLY_REDIRECT);
        }

        $response = $next($request);

        if ($request->hasSession()) {
            $response->headers->set('X-CSRF-TOKEN', $request->session()->token());
        }

        $response->headers->remove('X-Powered-By');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(self "https://js.stripe.com")');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin-allow-popups');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-site');
        $response->headers->set('Content-Security-Policy', implode('; ', [
            "default-src 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "frame-ancestors 'none'",
            "form-action 'self' https://checkout.stripe.com https://checkout.razorpay.com",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://js.stripe.com https://checkout.razorpay.com https://cdnjs.cloudflare.com https://www.google.com https://www.gstatic.com https://maps.googleapis.com https://maps.gstatic.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://maxcdn.bootstrapcdn.com",
            "font-src 'self' data: https://fonts.gstatic.com",
            "img-src 'self' data: blob: https:",
            "connect-src 'self' https: wss:",
            "frame-src 'self' https://js.stripe.com https://hooks.stripe.com https://checkout.stripe.com https://www.google.com https://www.recaptcha.net",
            "worker-src 'self' blob:",
        ]));

        if ($request->isSecure() && !app()->environment(['local', 'testing'])) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000');
        }

        return $response;
    }
}
