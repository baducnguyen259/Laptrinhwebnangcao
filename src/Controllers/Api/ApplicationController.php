<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use App\Middleware\AuthMiddleware;
use App\Models\ApplicationModel;
use App\Views\JsonView;

final class ApplicationController
{
    private ApplicationModel $applicationModel;

    public function __construct(ApplicationModel $applicationModel)
    {
        $this->applicationModel = $applicationModel;
    }

    public function handle(string $method, array $queryParams = []): void
    {
        $user = AuthMiddleware::authenticate(['seeker']);
        $userId = (int)($user->data->id ?? 0);

        if ($userId <= 0) {
            JsonView::render(["message" => "Token khong hop le."], 401);
            return;
        }

        if ($method === 'POST') {
            $this->create($userId);
            return;
        }

        if ($method === 'GET') {
            $this->list($userId, $queryParams);
            return;
        }

        JsonView::render(["message" => "Method not allowed"], 405);
    }

    private function create(int $userId): void
    {
        $data = json_decode((string)file_get_contents("php://input"));
        if (empty($data?->job_id) || empty($data?->cv_text)) {
            JsonView::render(["message" => "Thieu thong tin ung tuyen."], 400);
            return;
        }

        $created = $this->applicationModel->createApplication(
            (int)$data->job_id,
            $userId,
            (string)$data->cv_text
        );

        if (!$created) {
            JsonView::render(["message" => "Loi he thong, vui long thu lai sau."], 503);
            return;
        }

        JsonView::render(["message" => "Ung tuyen thanh cong."], 201);
    }

    private function list(int $userId, array $queryParams): void
    {
        $page = isset($queryParams['page']) ? (int)$queryParams['page'] : 1;
        $limit = 6;
        $offset = ($page - 1) * $limit;

        $totalRows = $this->applicationModel->countBySeekerId($userId);
        $totalPages = (int)ceil($totalRows / $limit);

        $applications = $this->applicationModel->findBySeekerIdPaged($userId, $limit, $offset);

        JsonView::render([
            'applications' => $applications,
            'pagination' => [
                'page' => $page,
                'totalPages' => $totalPages,
                'totalApplications' => $totalRows,
            ],
        ]);
    }
}
