<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use App\Models\UserModel;
use App\Services\AuthTokenService;
use App\Views\JsonView;
use Throwable;

final class AuthController
{
    private UserModel $userModel;
    private AuthTokenService $tokenService;

    public function __construct(UserModel $userModel, AuthTokenService $tokenService)
    {
        $this->userModel = $userModel;
        $this->tokenService = $tokenService;
    }

    public function handle(string $action, string $method, array $input): void
    {
        if ($action === 'register' && $method === 'POST') {
            $this->register($input);
            return;
        }

        if ($action === 'login' && $method === 'POST') {
            $this->login($input);
            return;
        }

        if ($action === 'me' && $method === 'GET') {
            $this->me();
            return;
        }

        if ($action === 'get_users' && $method === 'GET') {
            $this->getUsers();
            return;
        }

        JsonView::render([
            "message" => "API khong ton tai hoac sai action",
            "action" => $action,
            "method" => $method,
        ], 404);
    }

    private function register(array $input): void
    {
        if (empty($input['name']) || empty($input['email']) || empty($input['password'])) {
            JsonView::render(["message" => "Thieu thong tin dang ky"], 400);
            return;
        }

        $email = (string)$input['email'];
        if ($this->userModel->emailExists($email)) {
            JsonView::render(["message" => "Email da ton tai"], 400);
            return;
        }

        $passwordHash = password_hash((string)$input['password'], PASSWORD_BCRYPT);
        $requestedRole = strtolower(trim((string)($input['role'] ?? 'seeker')));
        $role = in_array($requestedRole, ['seeker', 'employer'], true) ? $requestedRole : 'seeker';

        $created = $this->userModel->createUser(
            (string)$input['name'],
            $email,
            $passwordHash,
            $role
        );

        if (!$created) {
            JsonView::render(["message" => "Loi he thong"], 500);
            return;
        }

        JsonView::render(["message" => "Dang ky thanh cong"], 201);
    }

    private function login(array $input): void
    {
        if (empty($input['email']) || empty($input['password'])) {
            JsonView::render(["message" => "Thieu email hoac mat khau"], 400);
            return;
        }

        $user = $this->userModel->findLoginUserByEmail((string)$input['email']);
        if (!$user) {
            JsonView::render(["message" => "Email khong ton tai"], 401);
            return;
        }

        if (!password_verify((string)$input['password'], (string)$user['password'])) {
            JsonView::render(["message" => "Sai mat khau"], 401);
            return;
        }

        try {
            $jwt = $this->tokenService->createLoginToken($user);
        } catch (Throwable $e) {
            JsonView::render(["message" => "Loi tao token dang nhap"], 500);
            return;
        }

        JsonView::render([
            "message" => "Dang nhap thanh cong",
            "token" => $jwt,
        ]);
    }

    private function me(): void
    {
        $authHeader = $this->getAuthorizationHeader();
        if ($authHeader === '' || stripos($authHeader, 'Bearer ') !== 0) {
            JsonView::render(["message" => "Thieu token xac thuc"], 401);
            return;
        }

        $token = trim(substr($authHeader, 7));

        try {
            $decoded = $this->tokenService->decodeBearerToken($token);
            $userId = (int)($decoded->data->id ?? 0);
        } catch (Throwable $e) {
            JsonView::render(["message" => "Phien dang nhap het han hoac khong hop le"], 401);
            return;
        }

        if ($userId <= 0) {
            JsonView::render(["message" => "Token khong hop le"], 401);
            return;
        }

        $user = $this->userModel->findProfileById($userId);
        if (!$user) {
            JsonView::render(["message" => "Khong tim thay nguoi dung"], 404);
            return;
        }

        JsonView::render([
            "user" => [
                "id" => (int)$user['id'],
                "name" => $user['name'],
                "email" => $user['email'],
                "role" => $user['role'],
                "active" => (int)$user['active'],
            ],
        ]);
    }

    private function getUsers(): void
    {
        $users = $this->userModel->getAllUsersForDebug();

        JsonView::render([
            "total" => count($users),
            "data" => $users,
        ]);
    }

    private function getAuthorizationHeader(): string
    {
        $headers = function_exists('apache_request_headers') ? apache_request_headers() : [];
        return $headers['Authorization']
            ?? $headers['authorization']
            ?? ($_SERVER['HTTP_AUTHORIZATION'] ?? ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? ''));
    }
}

