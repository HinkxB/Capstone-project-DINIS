<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Do not execute for non-responses (like binary file downloads)
        if (method_exists($response, 'header')) {
            $response->header('X-Frame-Options', 'DENY'); // Prevents clickjacking
            $response->header('X-XSS-Protection', '1; mode=block'); // Prevents basic XSS
            $response->header('X-Content-Type-Options', 'nosniff'); // Prevents MIME sniffing
            $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains'); // Forces HTTPS
            $response->header('Content-Security-Policy', "default-src 'self'"); // Strict CSP
            
            // Remove Laravel/PHP signature headers
            $response->headers->remove('X-Powered-By');
            $response->headers->remove('Server');
        }

        return $response;
    }
}
