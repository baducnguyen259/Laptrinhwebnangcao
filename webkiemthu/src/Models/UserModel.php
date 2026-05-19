<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

final class UserModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createUser(string $name, string $email, string $passwordHash, string $role): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, password, role, active)
             VALUES (:name, :email, :password, :role, 1)"
        );
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':password', $passwordHash);
        $stmt->bindValue(':role', $role);

        return $stmt->execute();
    }

    public function findLoginUserByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT id, name, password, role
             FROM users
             WHERE email = :email
             LIMIT 1"
        );
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function findProfileById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT id, name, email, role, active
             FROM users
             WHERE id = :id
             LIMIT 1"
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function getAllUsersForDebug(): array
    {
        $stmt = $this->db->prepare("SELECT id, name, email, role FROM users");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

