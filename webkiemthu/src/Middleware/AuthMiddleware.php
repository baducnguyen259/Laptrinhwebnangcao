<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Services\AuthTokenService;
use App\Views\JsonView;
use Throwable;

final class AuthMiddleware
{
    public static function authenticate(array $allowedRoles = []): object
    {
        $authHeader = self::resolveAuthorizationHeader();
        if ($authHeader === '') {
            JsonView::render(['message' => 'Unauthorized'], 401);
            exit;
        }

        $token = preg_replace('/^Bearer\s+/i', '', $authHeader);
        $token = is_string($token) ? trim($token) : '';
        if ($token === '') {
            JsonView::render(['message' => 'Unauthorized'], 401);
            exit;
        }

        try {
            $decoded = (new AuthTokenService())->decodeBearerToken($token);
        } catch (Throwable) {
            JsonView::render(['message' => 'Invalid token'], 401);
            exit;
        }

        $role = $decoded->role ?? ($decoded->data->role ?? null);
        if (!empty($allowedRoles) && !in_array((string)$role, $allowedRoles, true)) {
            JsonView::render(['message' => 'Forbidden'], 403);
            exit;
        }

        return $decoded;
    }

    private static function resolveAuthorizationHeader(): string
    {
        $headers = function_exists('apache_request_headers') ? apache_request_headers() : [];

        $header = $headers['Authorization']
            ?? $headers['authorization']
            ?? ($_SERVER['HTTP_AUTHORIZATION'] ?? ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? ''));

        return is_string($header) ? trim($header) : '';
    }
}
