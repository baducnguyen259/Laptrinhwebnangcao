<?php
declare(strict_types=1);

use App\Controllers\AdminJobsController;
use App\Controllers\Api\Admin\AdminApplicationController;
use App\Controllers\Api\Admin\AdminReportController;
use App\Controllers\Api\Admin\AdminUserController;
use App\Controllers\Api\AnalyzeController;
use App\Controllers\Api\ApplicationController;
use App\Controllers\Api\AuthController;
use App\Controllers\Api\CandidateController;
use App\Controllers\Api\JobController;
use App\Controllers\Api\UploadController;
use App\Core\Database;
use App\Middleware\AdminMiddleware;
use App\Models\AdminApplicationModel;
use App\Models\AdminJobModel;
use App\Models\AdminReportModel;
use App\Models\AdminUserModel;
use App\Models\ApplicationModel;
use App\Models\CandidateModel;
use App\Models\JobModel;
use App\Models\UserModel;
use App\Services\AuthTokenService;
use App\Services\CompanyLogoUploadService;
use App\Services\KeywordAnalyzeService;
use App\Views\JsonView;

$allMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];
$routes = [];
foreach ($allMethods as $method) {
    $routes[$method] = [];
}

$registerEndpoint = static function (string $endpoint, callable $handler) use (&$routes, $allMethods): void {
    $pathWithoutExt = $endpoint === 'index' ? '/api' : '/api/' . $endpoint;
    $pathWithExt = '/api/' . ($endpoint === 'index' ? 'index.php' : $endpoint . '.php');

    foreach ($allMethods as $method) {
        $routes[$method][$pathWithoutExt] = $handler;
        $routes[$method][$pathWithExt] = $handler;
    }
};

$registerEndpoint('index', static function (): void {
    header("Content-Type: application/json; charset=UTF-8");
    http_response_code(404);
    echo json_encode(["message" => "Not found"], JSON_UNESCAPED_UNICODE);
});

$registerEndpoint('auth', static function (): void {
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");

    if (strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'OPTIONS') {
        http_response_code(200);
        return;
    }

    $db = Database::connection();
    if (!$db) {
        JsonView::render(["message" => "Khong the ket noi database"], 500);
        return;
    }

    $action = strtolower(trim((string)($_GET['action'] ?? '')));
    $method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));

    $rawInput = file_get_contents("php://input");
    $decodedInput = json_decode(is_string($rawInput) ? $rawInput : '', true);
    $input = is_array($decodedInput) ? $decodedInput : [];

    $controller = new AuthController(
        new UserModel($db),
        new AuthTokenService()
    );
    $controller->handle($action, $method, $input);
});

$registerEndpoint('jobs', static function (): void {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, Origin");
    header("Access-Control-Max-Age: 3600");
    header("Content-Type: application/json; charset=UTF-8");

    if (strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'OPTIONS') {
        http_response_code(200);
        return;
    }

    $db = Database::connection();
    if (!$db) {
        JsonView::render(["message" => "Khong the ket noi database"], 500);
        return;
    }

    $method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    $queryParams = is_array($_GET) ? $_GET : [];

    $controller = new JobController(new JobModel($db));
    $controller->handle($method, $queryParams);
});

$registerEndpoint('applications', static function (): void {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, Origin");
    header("Access-Control-Max-Age: 3600");
    header("Content-Type: application/json; charset=UTF-8");

    if (strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'OPTIONS') {
        http_response_code(200);
        return;
    }

    $db = Database::connection();
    if (!$db) {
        JsonView::render(["message" => "Khong the ket noi database"], 500);
        return;
    }

    $method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    $queryParams = is_array($_GET) ? $_GET : [];

    $controller = new ApplicationController(new ApplicationModel($db));
    $controller->handle($method, $queryParams);
});

$registerEndpoint('candidates', static function (): void {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, PATCH, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, Origin");
    header("Access-Control-Max-Age: 3600");
    header("Content-Type: application/json; charset=UTF-8");

    if (strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'OPTIONS') {
        http_response_code(200);
        return;
    }

    $db = Database::connection();
    if (!$db) {
        JsonView::render(["message" => "Khong the ket noi database"], 500);
        return;
    }

    $method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    $queryParams = is_array($_GET) ? $_GET : [];

    $controller = new CandidateController(new CandidateModel($db));
    $controller->handle($method, $queryParams);
});

$registerEndpoint('analyze', static function (): void {
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");

    if (strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'OPTIONS') {
        http_response_code(200);
        return;
    }

    $method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    $rawBody = (string)file_get_contents("php://input");

    $controller = new AnalyzeController(new KeywordAnalyzeService());
    $controller->handle($method, $rawBody);
});

$registerEndpoint('upload_company_logo', static function (): void {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, Origin");
    header("Access-Control-Max-Age: 3600");
    header("Content-Type: application/json; charset=UTF-8");

    if (strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'OPTIONS') {
        http_response_code(200);
        return;
    }

    $method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    $files = is_array($_FILES) ? $_FILES : [];
    $scriptName = (string)($_SERVER['SCRIPT_NAME'] ?? '');

    $controller = new UploadController(new CompanyLogoUploadService());
    $controller->handle($method, $files, $scriptName);
});

$registerEndpoint('admin_users', static function (): void {
    header("Content-Type: application/json");

    $db = Database::connection();
    if (!$db) {
        JsonView::render(["message" => "Khong the ket noi database"], 500);
        return;
    }

    AdminMiddleware::enforce();

    $method = strtoupper((string)($_SERVER["REQUEST_METHOD"] ?? 'GET'));
    $controller = new AdminUserController(new AdminUserModel($db));
    $controller->handle($method);
});

$registerEndpoint('admin_jobs', static function (): void {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, PATCH, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, Origin");
    header("Access-Control-Max-Age: 3600");
    header("Content-Type: application/json; charset=UTF-8");

    if (strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'OPTIONS') {
        http_response_code(200);
        return;
    }

    $db = Database::connection();
    if (!$db) {
        JsonView::render(["message" => "Khong the ket noi database"], 500);
        return;
    }

    AdminMiddleware::enforce();

    $method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    $controller = new AdminJobsController(new AdminJobModel($db));
    $controller->handle($method);
});

$registerEndpoint('admin_applications', static function (): void {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, Origin");
    header("Access-Control-Max-Age: 3600");
    header("Content-Type: application/json; charset=UTF-8");

    if (strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'OPTIONS') {
        http_response_code(200);
        return;
    }

    $db = Database::connection();
    if (!$db) {
        JsonView::render(["message" => "Khong the ket noi database"], 500);
        return;
    }

    AdminMiddleware::enforce();

    $method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    $queryParams = is_array($_GET) ? $_GET : [];
    $controller = new AdminApplicationController(new AdminApplicationModel($db));
    $controller->handle($method, $queryParams);
});

$registerEndpoint('admin_reports', static function (): void {
    header("Content-Type: application/json");

    $db = Database::connection();
    if (!$db) {
        JsonView::render(["message" => "Khong the ket noi database"], 500);
        return;
    }

    AdminMiddleware::enforce();

    $method = strtoupper((string)($_SERVER["REQUEST_METHOD"] ?? 'GET'));
    $controller = new AdminReportController(new AdminReportModel($db));
    $controller->handle($method);
});

return $routes;
