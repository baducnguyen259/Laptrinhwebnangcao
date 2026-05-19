<?php
declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class Router
{
    /**
     * @var array<string, array<string, callable|string>>
     */
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    /**
     * @var callable|string|null
     */
    private $notFoundHandler = null;

    public function add(string $httpMethod, string $path, callable|string $handler): void
    {
        $method = strtoupper($httpMethod);
        if (!isset($this->routes[$method])) {
            $this->routes[$method] = [];
        }

        $this->routes[$method][$this->normalizePath($path)] = $handler;
    }

    public function get(string $path, callable|string $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable|string $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function setNotFoundHandler(callable|string $handler): void
    {
        $this->notFoundHandler = $handler;
    }

    public function dispatch(?string $method = null, ?string $requestUri = null, ?string $scriptName = null): void
    {
        $httpMethod = strtoupper($method ?? (string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        $uri = $requestUri ?? (string)($_SERVER['REQUEST_URI'] ?? '/');
        $script = $scriptName ?? (string)($_SERVER['SCRIPT_NAME'] ?? '/index.php');

        $requestPath = (string)(parse_url($uri, PHP_URL_PATH) ?? '/');
        $routePath = $this->extractRoutePath($requestPath, $script);

        $handler = $this->routes[$httpMethod][$routePath] ?? null;
        if ($handler === null) {
            $this->handleNotFound();
            return;
        }

        $this->invoke($handler);
    }

    private function normalizePath(string $path): string
    {
        $clean = '/' . trim($path, '/');
        return $clean === '//' ? '/' : $clean;
    }

    private function extractRoutePath(string $requestPath, string $scriptName): string
    {
        $basePath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
        $currentPath = str_replace('\\', '/', $requestPath);

        if ($basePath !== '' && $basePath !== '/' && str_starts_with($currentPath, $basePath)) {
            $currentPath = substr($currentPath, strlen($basePath));
        }

        return $this->normalizePath($currentPath);
    }

    private function invoke(callable|string $handler): void
    {
        if (is_callable($handler)) {
            $handler();
            return;
        }

        if (!is_string($handler) || !str_contains($handler, '@')) {
            throw new RuntimeException('Invalid route handler format.');
        }

        [$controllerName, $methodName] = explode('@', $handler, 2);
        $controllerClass = str_contains($controllerName, '\\')
            ? $controllerName
            : 'App\\Controllers\\' . $controllerName;

        if (!class_exists($controllerClass)) {
            throw new RuntimeException("Controller not found: {$controllerClass}");
        }

        $controller = new $controllerClass();
        if (!method_exists($controller, $methodName)) {
            throw new RuntimeException("Method not found: {$controllerClass}@{$methodName}");
        }

        $controller->{$methodName}();
    }

    private function handleNotFound(): void
    {
        if ($this->notFoundHandler !== null) {
            $this->invoke($this->notFoundHandler);
            return;
        }

        http_response_code(404);
        header('Content-Type: text/plain; charset=UTF-8');
        echo '404 Not Found';
    }
}
