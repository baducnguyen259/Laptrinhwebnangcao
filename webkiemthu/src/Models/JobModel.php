<?php
declare(strict_types=1);

namespace App\Models;

use PDO;
use PDOStatement;

final class JobModel
{
    private PDO $db;
    private static bool $schemaChecked = false;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function ensureSchema(): void
    {
        if (self::$schemaChecked) {
            return;
        }

        $requiredColumns = [
            'field' => "VARCHAR(191) NULL",
            'job_type' => "VARCHAR(191) NULL",
            'experience' => "VARCHAR(191) NULL",
            'level' => "VARCHAR(191) NULL",
            'deadline' => "DATE NULL",
            'company_logo' => "VARCHAR(255) NULL",
            'company_size' => "VARCHAR(191) NULL",
            'company_industry' => "VARCHAR(191) NULL",
            'candidate_profile' => "TEXT NULL",
            'candidate_bonus' => "TEXT NULL",
            'skills' => "TEXT NULL",
            'education_requirements' => "TEXT NULL",
            'soft_skills' => "TEXT NULL",
            'benefits' => "TEXT NULL",
        ];

        foreach ($requiredColumns as $column => $definition) {
            $check = $this->db->prepare("SHOW COLUMNS FROM jobs LIKE :column_name");
            $check->bindValue(':column_name', $column);
            $check->execute();

            $exists = $check->fetch(PDO::FETCH_ASSOC);
            if ($exists) {
                continue;
            }

            $this->db->exec("ALTER TABLE jobs ADD COLUMN `{$column}` {$definition}");
        }

        self::$schemaChecked = true;
    }

    public function getActiveJobById(int $jobId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM jobs WHERE id = :id AND status != 'deleted' LIMIT 1");
        $stmt->bindValue(':id', $jobId, PDO::PARAM_INT);
        $stmt->execute();

        $job = $stmt->fetch(PDO::FETCH_ASSOC);
        return $job ?: null;
    }

    public function countEmployerJobs(int $employerId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM jobs WHERE employer_id = :employer_id AND status != 'deleted'");
        $stmt->bindValue(':employer_id', $employerId, PDO::PARAM_INT);
        $stmt->execute();

        return (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    }

    public function getEmployerJobsWithApplicantCount(int $employerId, int $limit, int $offset): array
    {
        $query = "SELECT
                    j.id,
                    j.title,
                    j.location,
                    j.status,
                    j.created_at,
                    (
                        SELECT COUNT(*)
                        FROM applications a
                        WHERE a.job_id = j.id
                    ) AS applicant_count
                  FROM jobs j
                  WHERE j.employer_id = :employer_id AND j.status != 'deleted'
                  ORDER BY j.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':employer_id', $employerId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findOpenJobsNoLimit(string $whereSql, array $params): array
    {
        $query = "SELECT * FROM jobs" . $whereSql . " ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countOpenJobs(string $whereSql, array $params): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM jobs" . $whereSql);
        $stmt->execute($params);

        return (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    }

    public function findOpenJobs(string $whereSql, array $params, int $limit, int $offset): array
    {
        $query = "SELECT * FROM jobs" . $whereSql . " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createJob(array $payload, int $employerId): bool
    {
        $query = "INSERT INTO jobs
                  (title, company, location, salary, description, field, job_type, experience, `level`, deadline,
                   company_logo, company_size, company_industry, candidate_profile, candidate_bonus, skills,
                   education_requirements, soft_skills, benefits, employer_id, status, created_at)
                  VALUES
                  (:title, :company, :location, :salary, :description, :field, :job_type, :experience, :job_level, :deadline,
                   :company_logo, :company_size, :company_industry, :candidate_profile, :candidate_bonus, :skills,
                   :education_requirements, :soft_skills, :benefits, :employer_id, 'open', NOW())";

        $stmt = $this->db->prepare($query);
        $this->bindJobPayload($stmt, $payload);
        $stmt->bindValue(':employer_id', $employerId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function updateJob(array $payload, int $jobId, int $employerId): ?int
    {
        $query = "UPDATE jobs SET
                    title = :title,
                    company = :company,
                    company_logo = :company_logo,
                    location = :location,
                    salary = :salary,
                    description = :description,
                    field = :field,
                    job_type = :job_type,
                    experience = :experience,
                    `level` = :job_level,
                    deadline = :deadline,
                    company_size = :company_size,
                    company_industry = :company_industry,
                    candidate_profile = :candidate_profile,
                    candidate_bonus = :candidate_bonus,
                    skills = :skills,
                    education_requirements = :education_requirements,
                    soft_skills = :soft_skills,
                    benefits = :benefits
                  WHERE id = :id AND employer_id = :employer_id";

        $stmt = $this->db->prepare($query);
        $this->bindJobPayload($stmt, $payload);
        $stmt->bindValue(':id', $jobId, PDO::PARAM_INT);
        $stmt->bindValue(':employer_id', $employerId, PDO::PARAM_INT);

        if (!$stmt->execute()) {
            return null;
        }

        return $stmt->rowCount();
    }

    public function employerOwnsJob(int $jobId, int $employerId): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM jobs WHERE id = :id AND employer_id = :employer_id");
        $stmt->bindValue(':id', $jobId, PDO::PARAM_INT);
        $stmt->bindValue(':employer_id', $employerId, PDO::PARAM_INT);
        $stmt->execute();

        return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateJobStatus(int $jobId, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE jobs SET status = :status WHERE id = :id");
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':id', $jobId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function softDeleteJob(int $jobId): bool
    {
        $stmt = $this->db->prepare("UPDATE jobs SET status = 'deleted' WHERE id = :id");
        $stmt->bindValue(':id', $jobId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    private function bindJobPayload(PDOStatement $stmt, array $payload): void
    {
        $stmt->bindValue(':title', $payload['title']);
        $stmt->bindValue(':company', $payload['company']);
        $stmt->bindValue(':company_logo', $payload['company_logo']);
        $stmt->bindValue(':location', $payload['location']);
        $stmt->bindValue(':salary', $payload['salary']);
        $stmt->bindValue(':description', $payload['description']);
        $stmt->bindValue(':field', $payload['field']);
        $stmt->bindValue(':job_type', $payload['job_type']);
        $stmt->bindValue(':experience', $payload['experience']);
        $stmt->bindValue(':job_level', $payload['job_level']);

        if ($payload['deadline'] === null) {
            $stmt->bindValue(':deadline', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':deadline', $payload['deadline']);
        }

        $stmt->bindValue(':company_size', $payload['company_size']);
        $stmt->bindValue(':company_industry', $payload['company_industry']);
        $stmt->bindValue(':candidate_profile', $payload['candidate_profile']);
        $stmt->bindValue(':candidate_bonus', $payload['candidate_bonus']);
        $stmt->bindValue(':skills', $payload['skills']);
        $stmt->bindValue(':education_requirements', $payload['education_requirements']);
        $stmt->bindValue(':soft_skills', $payload['soft_skills']);
        $stmt->bindValue(':benefits', $payload['benefits']);
    }
}

