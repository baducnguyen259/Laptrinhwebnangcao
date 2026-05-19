<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $connection = null;
    private static string $lastError = '';

    public static function connection(): ?PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $config = self::loadConfig();
        $host = (string)($config['host'] ?? 'localhost');
        $dbName = (string)($config['database'] ?? 'webkiemthu');
        $username = (string)($config['username'] ?? 'root');
        $password = (string)($config['password'] ?? '');
        $port = (int)($config['port'] ?? 3306);
        $charset = (string)($config['charset'] ?? 'utf8mb4');

        $hosts = [$host];
        if ($host === 'localhost') {
            $hosts[] = '127.0.0.1';
        } elseif ($host === '127.0.0.1') {
            $hosts[] = 'localhost';
        }

        self::$lastError = '';

        foreach (array_values(array_unique($hosts)) as $tryHost) {
            $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $tryHost, $port, $dbName, $charset);

            try {
                self::$connection = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);

                return self::$connection;
            } catch (PDOException $exception) {
                self::$lastError = $exception->getMessage();
            }
        }

        error_log('Connection error: ' . self::$lastError);
        return null;
    }

    public static function lastError(): string
    {
        return self::$lastError;
    }

    /**
     * @return array<string, mixed>
     */
    private static function loadConfig(): array
    {
        $configPath = dirname(__DIR__, 2) . '/config/database.php';
        if (!is_file($configPath)) {
            return [];
        }

        $config = require $configPath;
        return is_array($config) ? $config : [];
    }
}
