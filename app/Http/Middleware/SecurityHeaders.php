<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=(), usb=(), bluetooth=(), accelerometer=(), gyroscope=()'
        );
        $response->headers->set('Content-Security-Policy', $this->contentSecurityPolicy());

        if (app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }

    private function contentSecurityPolicy(): string
    {
        if (! app()->environment('production')) {
            return $this->localContentSecurityPolicy();
        }

        return implode('; ', [
            "default-src 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "frame-ancestors 'none'",
            "form-action 'self' https://*.pagar.me https://*.pagarme.com.br",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https:",
            "style-src 'self' 'unsafe-inline' https:",
            "img-src 'self' data: https:",
            "font-src 'self' data: https:",
            "connect-src 'self' https://*.pagar.me https://*.pagarme.com.br https://viacep.com.br",
            'upgrade-insecure-requests',
        ]);
    }

    private function localContentSecurityPolicy(): string
    {
        $httpOrigins = $this->viteHttpOrigins();
        $wsOrigins = $this->viteWebSocketOrigins($httpOrigins);

        return implode('; ', [
            "default-src 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "frame-ancestors 'none'",
            "form-action 'self' https://*.pagar.me https://*.pagarme.com.br",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' ".$this->implodeSources($httpOrigins)." https:",
            "style-src 'self' 'unsafe-inline' ".$this->implodeSources($httpOrigins)." https:",
            "img-src 'self' data: blob: ".$this->implodeSources($httpOrigins)." https:",
            "font-src 'self' data: ".$this->implodeSources($httpOrigins)." https:",
            "connect-src 'self' ".$this->implodeSources([...$httpOrigins, ...$wsOrigins]).' https://*.pagar.me https://*.pagarme.com.br https://viacep.com.br',
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function viteHttpOrigins(): array
    {
        $origins = [
            'http://localhost:5173',
            'http://127.0.0.1:5173',
        ];

        $configuredUrl = env('VITE_DEV_SERVER_URL');
        if (is_string($configuredUrl) && trim($configuredUrl) !== '') {
            $configuredOrigin = $this->originFromUrl($configuredUrl);

            if ($configuredOrigin !== null && str_starts_with($configuredOrigin, 'http://')) {
                $origins[] = $configuredOrigin;
            }
        }

        $hotFileOrigin = $this->viteHotFileOrigin();
        if ($hotFileOrigin !== null && str_starts_with($hotFileOrigin, 'http://')) {
            $origins[] = $hotFileOrigin;
        }

        return array_values(array_unique($origins));
    }

    /**
     * @param  array<int, string>  $httpOrigins
     * @return array<int, string>
     */
    private function viteWebSocketOrigins(array $httpOrigins): array
    {
        $origins = [
            'ws://localhost:5173',
            'ws://127.0.0.1:5173',
        ];

        foreach ($httpOrigins as $origin) {
            $origins[] = preg_replace('/^http:/', 'ws:', $origin) ?? $origin;
        }

        return array_values(array_unique($origins));
    }

    private function originFromUrl(string $url): ?string
    {
        $parts = parse_url($url);

        if (! is_array($parts) || ! isset($parts['scheme'], $parts['host'])) {
            return null;
        }

        $origin = strtolower($parts['scheme']).'://'.$parts['host'];

        if (isset($parts['port'])) {
            $origin .= ':'.$parts['port'];
        }

        return $origin;
    }

    private function viteHotFileOrigin(): ?string
    {
        $hotFile = public_path('hot');

        if (! is_file($hotFile)) {
            return null;
        }

        $url = trim((string) file_get_contents($hotFile));

        return $url !== '' ? $this->originFromUrl($url) : null;
    }

    /**
     * @param  array<int, string>  $sources
     */
    private function implodeSources(array $sources): string
    {
        return implode(' ', $sources);
    }
}
