<?php
declare(strict_types=1);

namespace App\Services;

use App\Config\AppConfig;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

final class AuthTokenService
{
    public function createLoginToken(array $user): string
    {
        $payload = [
            "iss" => "webkiemthu",
            "iat" => time(),
            "exp" => time() + 3600,
            "data" => [
                "id" => $user['id'],
                "name" => $user['name'],
                "role" => $user['role'],
            ],
        ];

        return JWT::encode($payload, AppConfig::jwtSecret(), 'HS256');
    }

    public function decodeBearerToken(string $token): object
    {
        return JWT::decode($token, new Key(AppConfig::jwtSecret(), 'HS256'));
    }
}
