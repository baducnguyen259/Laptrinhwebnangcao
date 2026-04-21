<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use App\Middleware\AuthMiddleware;
use App\Models\CandidateModel;
use App\Views\JsonView;

final class CandidateController
{
    private CandidateModel $candidateModel;

    public function __construct(CandidateModel $candidateModel)
    {
        $this->candidateModel = $candidateModel;
    }

    public function handle(string $method, array $queryParams = []): void
    {
        $user = AuthMiddleware::authenticate(['employer']);
        $employerId = (int)($user->data->id ?? 0);

        if ($method === 'GET') {
            $this->listCandidates($employerId, $queryParams);
            return;
        }

        if ($method === 'PATCH') {
            $this->updateCandidateStatus($employerId, $queryParams);
            return;
        }

        JsonView::render(["message" => "Method not allowed"], 405);
    }

    private function listCandidates(int $employerId, array $queryParams): void
    {
        if (!isset($queryParams['job_id'])) {
            JsonView::render(["message" => "Thieu ID cong viec."], 400);
            return;
        }

        $jobId = (int)$queryParams['job_id'];
        $job = $this->candidateModel->findOwnedJobByEmployer($jobId, $employerId);

        if (!$job) {
            JsonView::render(["message" => "Ban khong co quyen xem ung vien cua cong viec nay."], 403);
            return;
        }

        $candidates = $this->candidateModel->findCandidatesByJobId($jobId);
        JsonView::render([
            "success" => true,
            "job_title" => $job['title'],
            "candidates" => $candidates,
        ]);
    }

    private function updateCandidateStatus(int $employerId, array $queryParams): void
    {
        $data = json_decode((string)file_get_contents("php://input"));

        if (!isset($queryParams['id']) || !isset($data->status)) {
            JsonView::render(["message" => "Thieu ID ho so hoac trang thai moi."], 400);
            return;
        }

        $appId = (int)$queryParams['id'];
        $newStatus = (string)$data->status;

        if (!$this->candidateModel->isApplicationOwnedByEmployer($appId, $employerId)) {
            JsonView::render(["message" => "Ban khong co quyen cap nhat ho so nay."], 403);
            return;
        }

        if ($this->candidateModel->updateApplicationStatus($appId, $newStatus)) {
            JsonView::render(["message" => "Da cap nhat trang thai ung vien."]);
            return;
        }

        JsonView::render(["message" => "Loi he thong, khong the cap nhat."], 503);
    }
}
