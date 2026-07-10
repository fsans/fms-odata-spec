<?php

declare(strict_types=1);

namespace FmsOData\Spec\Auth;

/**
 * Static facade for authentication helpers.
 *
 * Mirrors basicAuth(), bearerAuth(), and normalizeAuthToken() from the
 * TS/Python packages.
 */
final class Auth
{
    /** Build a Basic auth header value from account and password. */
    public static function basicAuth(string $account, string $password): string
    {
        $raw = $account . ':' . $password;
        $encoded = \base64_encode($raw);

        return 'Basic ' . $encoded;
    }

    /** Build a Bearer auth header value from an OAuth session token. */
    public static function bearerAuth(string $token): string
    {
        return 'Bearer ' . $token;
    }

    /**
     * Normalize a token string: if it already has a scheme prefix, use as-is.
     *
     * Bare tokens default to Bearer. Callers should use basicAuth() or
     * bearerAuth() helpers for explicit scheme construction.
     */
    public static function normalizeAuthToken(string $token): string
    {
        if (\str_starts_with($token, 'Basic ') || \str_starts_with($token, 'Bearer ')) {
            return $token;
        }

        return 'Bearer ' . $token;
    }
}
