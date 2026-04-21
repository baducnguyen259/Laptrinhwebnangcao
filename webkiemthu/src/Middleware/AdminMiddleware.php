<?php
declare(strict_types=1);

namespace App\Middleware;

final class AdminMiddleware
{
    public static function enforce(): void
    {
        AuthMiddleware::authenticate(['admin']);
    }
}
