<?php
declare(strict_types=1);

namespace App\Config;

use RuntimeException;

final class AppConfig
{
    /**
     * @var array<string, mixed>|null
     */
    private static ?array $config = null;

    public static function bootstrap(): void
    {
        $timezone = (string)self::get('timezone', 'Asia/Ho_Chi_Minh');
        date_default_timezone_set($timezone);
    }

    public static function jwtSecret(): string
    {
        $jwt = self::get('jwt', []);
        $secret = '';

        if (is_array($jwt)) {
            $secret = (string)($jwt['secret'] ?? '');
        }

        if ($secret === '') {
            throw new RuntimeException('JWT secret is not configured.');
        }

        return $secret;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $config = self::load();
        return $config[$key] ?? $default;
    }

    /**
     * @return array<string, mixed>
     */
    private static function load(): array
    {
        if (self::$config !== null) {
            return self::$config;
        }

        $configPath = dirname(__DIR__, 2) . '/config/app.php';
        if (!is_file($configPath)) {
            self::$config = [];
            return self::$config;
        }

        $config = require $configPath;
        self::$config = is_array($config) ? $config : [];

        return self::$config;
    }
}
