<?php
declare(strict_types=1);

use PDO;
use PDOException;

/**
 * Repository for users table.
 */
class UserRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
    * Find user by primary key.
    *
    * @param int $id
    * @return array|null
    */
    public function findById(int $id): ?array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user ?: null;
        } catch (PDOException $e) {
            error_log('UserRepository::findById error: ' . $e->getMessage());
            return null;
        }
    }

    /**
    * Find user by email.
    *
    * @param string $email
    * @return array|null
    */
    public function findByEmail(string $email): ?array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user ?: null;
        } catch (PDOException $e) {
            error_log('UserRepository::findByEmail error: ' . $e->getMessage());
            return null;
        }
    }

    /**
    * Create a new user.
    *
    * @param array $data
    * @return array|null Newly created user or null on failure.
    */
    public function create(array $data): ?array
    {
        $filtered = $this->filterColumns($data);
        if (empty($filtered)) {
            return null;
        }

        $columns = array_keys($filtered);
        $placeholders = array_map(fn($c) => ':' . $c, $columns);
        $sql = 'INSERT INTO users (' . implode(',', $columns) . ') VALUES (' . implode(',', $placeholders) . ')';

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($filtered);
            $id = (int)$this->pdo->lastInsertId();
            return $this->findById($id);
        } catch (PDOException $e) {
            error_log('UserRepository::create error: ' . $e->getMessage());
            return null;
        }
    }

    /**
    * Update a user by id.
    *
    * @param int $id
    * @param array $data
    * @return bool
    */
    public function update(int $id, array $data): bool
    {
        $filtered = $this->filterColumns($data);
        if (empty($filtered)) {
            return false;
        }

        $setParts = [];
        foreach ($filtered as $column => $_) {
            $setParts[] = $column . ' = :' . $column;
        }
        $sql = 'UPDATE users SET ' . implode(', ', $setParts) . ' WHERE id = :id';
        $filtered['id'] = $id;

        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($filtered);
        } catch (PDOException $e) {
            error_log('UserRepository::update error: ' . $e->getMessage());
            return false;
        }
    }

    /**
    * Delete a user by id.
    *
    * @param int $id
    * @return bool
    */
    public function delete(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('UserRepository::delete error: ' . $e->getMessage());
            return false;
        }
    }

    /**
    * Get all users.
    *
    * @return array
    */
    public function findAll(): array
    {
        try {
            $stmt = $this->pdo->query('SELECT * FROM users ORDER BY id ASC');
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log('UserRepository::findAll error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Filter column names to safe identifiers.
     *
     * @param array $data
     * @return array
     */
    private function filterColumns(array $data): array
    {
        $safe = [];
        foreach ($data as $key => $value) {
            if (preg_match('/^[a-zA-Z0-9_]+$/', (string)$key)) {
                $safe[$key] = $value;
            }
        }
        return $safe;
    }
}
