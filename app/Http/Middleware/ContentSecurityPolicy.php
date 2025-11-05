<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Add CSP headers for Google reCAPTCHA Enterprise
        $csp = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.google.com https://www.gstatic.com",
            "frame-src 'self' https://www.google.com https://recaptcha.google.com",
            "style-src 'self' 'unsafe-inline' https://www.gstatic.com",
            "img-src 'self' data: https:",
            "font-src 'self' data:",
            "connect-src 'self' https://www.google.com https://www.gstatic.com",
        ];

        $response->headers->set('Content-Security-Policy', implode('; ', $csp));

        return $response;
    }
}
