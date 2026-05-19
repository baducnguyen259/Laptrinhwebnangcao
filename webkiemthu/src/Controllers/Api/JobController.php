<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use App\Middleware\AuthMiddleware;
use App\Models\JobModel;
use App\Views\JsonView;

final class JobController
{
    private JobModel $jobModel;

    public function __construct(JobModel $jobModel)
    {
        $this->jobModel = $jobModel;
    }

    public function handle(string $method, array $queryParams = []): void
    {
        $this->jobModel->ensureSchema();

        if ($method === 'GET') {
            if (isset($queryParams['id'])) {
                $this->getJobById((int)$queryParams['id']);
                return;
            }

            if (($queryParams['view'] ?? '') === 'employer') {
                $this->getEmployerJobs($queryParams);
                return;
            }

            $this->searchJobs($queryParams);
            return;
        }

        if ($method === 'POST') {
            $this->createJob();
            return;
        }

        if ($method === 'PUT') {
            $this->updateJob($queryParams);
            return;
        }

        if ($method === 'PATCH') {
            $this->patchJobStatus($queryParams);
            return;
        }

        if ($method === 'DELETE') {
            $this->deleteJob($queryParams);
            return;
        }

        JsonView::render(["message" => "Method not allowed"], 405);
    }

    private function getJobById(int $jobId): void
    {
        $job = $this->jobModel->getActiveJobById($jobId);
        if (!$job) {
            JsonView::render(["message" => "Khong tim thay cong viec"], 404);
            return;
        }

        JsonView::render($job);
    }

    private function getEmployerJobs(array $queryParams): void
    {
        $user = AuthMiddleware::authenticate(['employer']);
        $employerId = (int)$user->data->id;

        $page = isset($queryParams['page']) ? max(1, (int)$queryParams['page']) : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $totalRows = $this->jobModel->countEmployerJobs($employerId);
        $totalPages = max(1, (int)ceil($totalRows / $limit));

        $jobs = $this->jobModel->getEmployerJobsWithApplicantCount($employerId, $limit, $offset);
        $jobs = array_map(static function (array $job): array {
            $job['applicant_count'] = (int)($job['applicant_count'] ?? 0);
            return $job;
        }, $jobs);

        JsonView::render([
            'success' => true,
            'jobs' => $jobs,
            'pagination' => [
                'page' => $page,
                'totalPages' => $totalPages,
                'totalJobs' => $totalRows,
                'limit' => $limit,
                'hasNextPage' => $page < $totalPages,
                'hasPrevPage' => $page > 1,
            ],
        ]);
    }

    private function searchJobs(array $queryParams): void
    {
        $page = isset($queryParams['page']) ? max(1, (int)$queryParams['page']) : 1;
        $limit = 6;
        $offset = ($page - 1) * $limit;

        $whereClauses = ["status = 'open'"];
        $params = [];

        if (!empty($queryParams['keyword'])) {
            $whereClauses[] = "(title LIKE :keyword OR description LIKE :keyword)";
            $params[':keyword'] = '%' . trim((string)$queryParams['keyword']) . '%';
        }

        if (!empty($queryParams['location'])) {
            $whereClauses[] = "location LIKE :location";
            $params[':location'] = '%' . trim((string)$queryParams['location']) . '%';
        }

        $salaryRanges = [];
        if (!empty($queryParams['salary_range'])) {
            $salaryRanges = array_values(array_filter(array_map('trim', explode(',', (string)$queryParams['salary_range']))));
        }

        $this->addContainsClause('field', 'field', $queryParams, $whereClauses, $params);
        $this->addContainsClause('experience', 'experience', $queryParams, $whereClauses, $params);
        $this->addContainsClause('type', 'job_type', $queryParams, $whereClauses, $params);

        $whereSql = " WHERE " . implode(' AND ', $whereClauses);
        $jobs = [];
        $totalRows = 0;
        $totalPages = 1;

        if (!empty($salaryRanges)) {
            $rawJobs = $this->jobModel->findOpenJobsNoLimit($whereSql, $params);
            $filteredJobs = array_values(array_filter($rawJobs, function (array $job) use ($salaryRanges): bool {
                $salaryVnd = $this->parseSalaryToVnd((string)($job['salary'] ?? ''));
                return $this->salaryMatchesRanges($salaryVnd, $salaryRanges);
            }));

            $totalRows = count($filteredJobs);
            $totalPages = max(1, (int)ceil($totalRows / $limit));
            $jobs = array_slice($filteredJobs, $offset, $limit);
        } else {
            $totalRows = $this->jobModel->countOpenJobs($whereSql, $params);
            $totalPages = max(1, (int)ceil($totalRows / $limit));
            $jobs = $this->jobModel->findOpenJobs($whereSql, $params, $limit, $offset);
        }

        JsonView::render([
            'success' => true,
            'jobs' => $jobs,
            'pagination' => [
                'page' => $page,
                'totalPages' => $totalPages,
                'totalJobs' => $totalRows,
                'limit' => $limit,
                'hasNextPage' => $page < $totalPages,
                'hasPrevPage' => $page > 1,
            ],
        ]);
    }

    private function createJob(): void
    {
        $user = AuthMiddleware::authenticate(['employer']);
        $employerId = (int)$user->data->id;

        $data = $this->readJsonBody();
        if (!is_object($data)) {
            JsonView::render(["message" => "Du lieu khong hop le"], 400);
            return;
        }

        $payload = $this->buildJobPayload($data);
        if ($payload['title'] === '' || $payload['company'] === '' || $payload['location'] === '' || $payload['salary'] === '') {
            JsonView::render(["message" => "Vui long nhap day du tieu de, cong ty, dia diem va muc luong"], 400);
            return;
        }

        if ($this->jobModel->createJob($payload, $employerId)) {
            JsonView::render(["message" => "Dang tin tuyen dung thanh cong"], 201);
            return;
        }

        JsonView::render(["message" => "Khong the tao tin tuyen dung"], 503);
    }

    private function updateJob(array $queryParams): void
    {
        $user = AuthMiddleware::authenticate(['employer']);
        $employerId = (int)$user->data->id;

        $jobId = isset($queryParams['id']) ? (int)$queryParams['id'] : 0;
        $data = $this->readJsonBody();

        if ($jobId <= 0 || !is_object($data)) {
            JsonView::render(["message" => "Du lieu khong hop le hoac thieu ID cong viec"], 400);
            return;
        }

        $payload = $this->buildJobPayload($data);
        if ($payload['title'] === '' || $payload['location'] === '') {
            JsonView::render(["message" => "Tieu de va dia diem khong duoc de trong"], 400);
            return;
        }

        $rowCount = $this->jobModel->updateJob($payload, $jobId, $employerId);
        if ($rowCount === null) {
            JsonView::render(["message" => "Khong the cap nhat tin tuyen dung"], 503);
            return;
        }

        if ($rowCount === 0) {
            JsonView::render(["message" => "Khong tim thay cong viec hoac ban khong co quyen sua"], 403);
            return;
        }

        JsonView::render(["message" => "Cap nhat tin tuyen dung thanh cong"], 200);
    }

    private function patchJobStatus(array $queryParams): void
    {
        $user = AuthMiddleware::authenticate(['employer']);
        $employerId = (int)$user->data->id;

        $jobId = isset($queryParams['id']) ? (int)$queryParams['id'] : 0;
        $data = $this->readJsonBody();

        if ($jobId <= 0 || !is_object($data) || empty($data->status)) {
            JsonView::render(["message" => "Thieu ID cong viec hoac trang thai moi"], 400);
            return;
        }

        $newStatus = trim((string)$data->status);
        $allowed = ['open', 'closed', 'pending', 'deleted'];
        if (!in_array($newStatus, $allowed, true)) {
            JsonView::render(["message" => "Trang thai khong hop le"], 400);
            return;
        }

        if (!$this->jobModel->employerOwnsJob($jobId, $employerId)) {
            JsonView::render(["message" => "Ban khong co quyen thay doi cong viec nay"], 403);
            return;
        }

        if ($this->jobModel->updateJobStatus($jobId, $newStatus)) {
            JsonView::render(["message" => "Cap nhat trang thai thanh cong"], 200);
            return;
        }

        JsonView::render(["message" => "Khong the cap nhat trang thai"], 503);
    }

    private function deleteJob(array $queryParams): void
    {
        $user = AuthMiddleware::authenticate(['employer']);
        $employerId = (int)$user->data->id;

        $jobId = isset($queryParams['id']) ? (int)$queryParams['id'] : 0;
        if ($jobId <= 0) {
            JsonView::render(["message" => "Thieu ID cong viec"], 400);
            return;
        }

        if (!$this->jobModel->employerOwnsJob($jobId, $employerId)) {
            JsonView::render(["message" => "Ban khong co quyen xoa cong viec nay"], 403);
            return;
        }

        if ($this->jobModel->softDeleteJob($jobId)) {
            JsonView::render(["message" => "Da xoa tin tuyen dung thanh cong"], 200);
            return;
        }

        JsonView::render(["message" => "Khong the xoa tin tuyen dung"], 503);
    }

    private function readJsonBody(): mixed
    {
        return json_decode((string)file_get_contents("php://input"));
    }

    private function normalizeText($value): string
    {
        if ($value === null) {
            return '';
        }
        return trim((string)$value);
    }

    private function normalizeDate($value): ?string
    {
        $text = $this->normalizeText($value);
        if ($text === '') {
            return null;
        }
        $timestamp = strtotime($text);
        if ($timestamp === false) {
            return null;
        }
        return date('Y-m-d', $timestamp);
    }

    private function normalizeMultiline($value): string
    {
        $text = $this->normalizeText($value);
        if ($text === '') {
            return '';
        }
        $lines = preg_split('/\r\n|\r|\n/', $text) ?: [];
        $clean = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line !== '') {
                $clean[] = $line;
            }
        }
        return implode("\n", $clean);
    }

    private function normalizeCsvOrLines($value): string
    {
        $text = $this->normalizeText($value);
        if ($text === '') {
            return '';
        }
        $parts = preg_split('/[\r\n,]+/', $text) ?: [];
        $clean = [];
        foreach ($parts as $part) {
            $part = trim($part);
            if ($part !== '') {
                $clean[] = $part;
            }
        }
        $clean = array_values(array_unique($clean));
        return implode(', ', $clean);
    }

    private function buildJobPayload(object $data): array
    {
        return [
            'title' => $this->normalizeText($data->title ?? ''),
            'company' => $this->normalizeText($data->company ?? ''),
            'company_logo' => $this->normalizeText($data->company_logo ?? ''),
            'location' => $this->normalizeText($data->location ?? ''),
            'salary' => $this->normalizeText($data->salary ?? ''),
            'description' => $this->normalizeMultiline($data->description ?? ''),
            'field' => $this->normalizeText($data->field ?? ''),
            'job_type' => $this->normalizeText($data->job_type ?? ''),
            'experience' => $this->normalizeText($data->experience ?? ''),
            'job_level' => $this->normalizeText($data->job_level ?? ($data->level ?? '')),
            'deadline' => $this->normalizeDate($data->deadline ?? null),
            'company_size' => $this->normalizeText($data->company_size ?? ''),
            'company_industry' => $this->normalizeText($data->company_industry ?? ''),
            'candidate_profile' => $this->normalizeMultiline($data->candidate_profile ?? ''),
            'candidate_bonus' => $this->normalizeMultiline($data->candidate_bonus ?? ''),
            'skills' => $this->normalizeCsvOrLines($data->skills ?? ''),
            'education_requirements' => $this->normalizeMultiline($data->education_requirements ?? ''),
            'soft_skills' => $this->normalizeMultiline($data->soft_skills ?? ''),
            'benefits' => $this->normalizeMultiline($data->benefits ?? ''),
        ];
    }

    private function addContainsClause(string $paramName, string $columnName, array $queryParams, array &$clauses, array &$params): void
    {
        if (empty($queryParams[$paramName])) {
            return;
        }

        $values = array_filter(array_map('trim', explode(',', (string)$queryParams[$paramName])));
        if (empty($values)) {
            return;
        }

        $likeClauses = [];
        foreach ($values as $index => $value) {
            $key = ":{$columnName}_like_{$index}";
            $likeClauses[] = "`{$columnName}` LIKE {$key}";
            $params[$key] = '%' . $value . '%';
        }

        $clauses[] = '(' . implode(' OR ', $likeClauses) . ')';
    }

    private function parseSalaryToVnd(?string $salaryRaw): ?int
    {
        $salary = trim((string)$salaryRaw);
        if ($salary === '') {
            return null;
        }

        $normalized = strtolower($salary);
        $normalized = str_replace(["\xC2\xA0", ' '], '', $normalized);
        $normalized = str_replace(['vnđ', 'vnd', 'đ', 'dong'], '', $normalized);
        $normalized = str_replace(['triệu', 'trieu', 'tr'], 'm', $normalized);

        if (preg_match('/^\d+$/', $normalized) === 1) {
            $plain = (int)$normalized;
            return $plain > 1000 ? $plain : $plain * 1000000;
        }

        if (preg_match('/^(\d+(?:\.\d+)?)m$/', $normalized, $matches) === 1) {
            return (int)round((float)$matches[1] * 1000000);
        }

        if (preg_match('/^(\d+(?:\.\d+)?)k$/', $normalized, $matches) === 1) {
            return (int)round((float)$matches[1] * 1000);
        }

        $matched = preg_match_all('/\d+(?:\.\d+)?/', $normalized, $numberMatches);
        if ($matched === false || $matched === 0 || empty($numberMatches[0])) {
            return null;
        }

        $numbers = array_map('floatval', $numberMatches[0]);
        $candidate = max($numbers);

        if (str_contains($normalized, 'm')) {
            return (int)round($candidate * 1000000);
        }

        if (str_contains($normalized, 'k')) {
            return (int)round($candidate * 1000);
        }

        if ($candidate >= 1000000) {
            return (int)round($candidate);
        }

        if ($candidate >= 1000) {
            return (int)round($candidate * 1000);
        }

        return (int)round($candidate * 1000000);
    }

    private function salaryMatchesRanges(?int $salaryVnd, array $ranges): bool
    {
        if (empty($ranges)) {
            return true;
        }

        foreach ($ranges as $range) {
            if ($range === 'agreement' && $salaryVnd === null) {
                return true;
            }

            if ($salaryVnd === null) {
                continue;
            }

            if ($range === 'under_10' && $salaryVnd < 10000000) {
                return true;
            }

            if ($range === '10_20' && $salaryVnd >= 10000000 && $salaryVnd <= 20000000) {
                return true;
            }

            if ($range === 'over_20' && $salaryVnd > 20000000) {
                return true;
            }
        }

        return false;
    }
}
