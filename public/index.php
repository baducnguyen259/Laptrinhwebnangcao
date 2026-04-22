<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\AppConfig;
use App\Core\Router;

AppConfig::bootstrap();

$router = new Router();

/**
 * @param array<string, array<string, callable|string>> $routeTable
 */
function registerRoutes(Router $router, array $routeTable): void
{
    foreach ($routeTable as $method => $pathMap) {
        foreach ($pathMap as $path => $handler) {
            $router->add($method, $path, $handler);
        }
    }
}

$webRoutes = require __DIR__ . '/../routes/web.php';
registerRoutes($router, $webRoutes);

$apiRoutes = require __DIR__ . '/../routes/api.php';
registerRoutes($router, $apiRoutes);

$router->setNotFoundHandler('PageController@notFound');
$router->dispatch();

