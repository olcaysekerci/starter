<?php

namespace App\Modules\User\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class UserSecurityMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // IP bazlı rate limiting
        $ip = $request->ip();
        $key = 'user-module-' . $ip;
        
        if (RateLimiter::tooManyAttempts($key, 100)) {
            Log::warning('User module rate limit exceeded', [
                'ip' => $ip,
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl()
            ]);
            
            return response()->json([
                'error' => 'Çok fazla istek gönderdiniz. Lütfen bekleyin.'
            ], 429);
        }
        
        RateLimiter::hit($key);

        // User-Agent kontrolü
        $userAgent = $request->userAgent();
        if (!$userAgent || strlen($userAgent) > 500) {
            Log::warning('Suspicious user agent detected', [
                'ip' => $ip,
                'user_agent' => $userAgent
            ]);
            
            return response()->json([
                'error' => 'Geçersiz istek.'
            ], 400);
        }

        // Referer kontrolü (CSRF koruması için)
        if ($request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('DELETE')) {
            $referer = $request->header('Referer');
            $host = $request->getHost();
            
            if (!$referer || !str_contains($referer, $host)) {
                Log::warning('Suspicious request without proper referer', [
                    'ip' => $ip,
                    'referer' => $referer,
                    'host' => $host,
                    'method' => $request->method(),
                    'url' => $request->fullUrl()
                ]);
                
                return response()->json([
                    'error' => 'Geçersiz istek kaynağı.'
                ], 400);
            }
        }

        // XSS koruması için header kontrolü
        $response = $next($request);
        
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        return $response;
    }
} 