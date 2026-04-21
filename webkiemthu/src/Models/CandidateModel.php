<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

final class CandidateModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findOwnedJobByEmployer(int $jobId, int $employerId): ?array
    {
        $query = "SELECT title FROM jobs WHERE id = :id AND employer_id = :employer_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $jobId, PDO::PARAM_INT);
        $stmt->bindValue(':employer_id', $employerId, PDO::PARAM_INT);
        $stmt->execute();

        $job = $stmt->fetch(PDO::FETCH_ASSOC);
        return $job ?: null;
    }

    public function findCandidatesByJobId(int $jobId): array
    {
        $query = "SELECT a.id, a.seeker_id, a.cv_text, a.status, a.created_at,
                         u.name as candidate_name, u.email as candidate_email
                  FROM applications a
                  JOIN users u ON a.seeker_id = u.id
                  WHERE a.job_id = :job_id
                  ORDER BY a.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':job_id', $jobId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isApplicationOwnedByEmployer(int $applicationId, int $employerId): bool
    {
        $query = "SELECT a.id
                  FROM applications a
                  JOIN jobs j ON a.job_id = j.id
                  WHERE a.id = :app_id AND j.employer_id = :employer_id";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':app_id', $applicationId, PDO::PARAM_INT);
        $stmt->bindValue(':employer_id', $employerId, PDO::PARAM_INT);
        $stmt->execute();

        return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateApplicationStatus(int $applicationId, string $status): bool
    {
        $query = "UPDATE applications SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':id', $applicationId, PDO::PARAM_INT);

        return $stmt->execute();
    }
}

