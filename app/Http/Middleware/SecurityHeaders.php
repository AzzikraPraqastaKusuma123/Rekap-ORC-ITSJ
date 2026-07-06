<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Append strict security headers to prevent sniffing, XSS, and Clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN'); // Prevent clickjacking (framing)
        $response->headers->set('X-XSS-Protection', '1; mode=block'); // Enable browser's native XSS protection
        $response->headers->set('X-Content-Type-Options', 'nosniff'); // Prevent MIME sniffing
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains'); // Force HTTPS strictly

        return $response;
    }
}
