<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

final class ApplicationModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function createApplication(int $jobId, int $seekerId, string $cvText): bool
    {
        $query = "INSERT INTO applications SET job_id=:job_id, seeker_id=:seeker_id, cv_text=:cv_text, status='submitted'";
        $stmt = $this->db->prepare($query);

        $stmt->bindValue(':job_id', $jobId, PDO::PARAM_INT);
        $stmt->bindValue(':seeker_id', $seekerId, PDO::PARAM_INT);
        $stmt->bindValue(':cv_text', $cvText);

        return $stmt->execute();
    }

    public function countBySeekerId(int $seekerId): int
    {
        $query = "SELECT COUNT(*) as total FROM applications WHERE seeker_id = :seeker_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':seeker_id', $seekerId, PDO::PARAM_INT);
        $stmt->execute();

        return (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    }

    public function findBySeekerIdPaged(int $seekerId, int $limit, int $offset): array
    {
        $query = "SELECT a.*, j.title, j.company
                  FROM applications a
                  LEFT JOIN jobs j ON a.job_id = j.id
                  WHERE a.seeker_id = :seeker_id
                  ORDER BY a.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':seeker_id', $seekerId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

