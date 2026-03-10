<?php
declare(strict_types=1);

use PDO;
use PDOException;

/**
 * Repository for cabinets table.
 */
class CabinetRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Find cabinet by id.
     *
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM cabinets WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $id]);
            $cabinet = $stmt->fetch(PDO::FETCH_ASSOC);
            return $cabinet ?: null;
        } catch (PDOException $e) {
            error_log('CabinetRepository::findById error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all cabinets.
     *
     * @return array
     */
    public function findAll(): array
    {
        try {
            $stmt = $this->pdo->query('SELECT * FROM cabinets ORDER BY id ASC');
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log('CabinetRepository::findAll error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Create a cabinet.
     *
     * @param array $data
     * @return array|null
     */
    public function create(array $data): ?array
    {
        $filtered = $this->filterColumns($data);
        if (empty($filtered)) {
            return null;
        }

        $columns = array_keys($filtered);
        $placeholders = array_map(fn($c) => ':' . $c, $columns);
        $sql = 'INSERT INTO cabinets (' . implode(',', $columns) . ') VALUES (' . implode(',', $placeholders) . ')';

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($filtered);
            $id = (int)$this->pdo->lastInsertId();
            return $this->findById($id);
        } catch (PDOException $e) {
            error_log('CabinetRepository::create error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update cabinet.
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
        $sql = 'UPDATE cabinets SET ' . implode(', ', $setParts) . ' WHERE id = :id';
        $filtered['id'] = $id;

        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($filtered);
        } catch (PDOException $e) {
            error_log('CabinetRepository::update error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete cabinet.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM cabinets WHERE id = :id');
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('CabinetRepository::delete error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Find cabinets for a given user.
     *
     * @param int $userId
     * @return array
     */
    public function findByUser(int $userId): array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM cabinets WHERE user_id = :user_id ORDER BY id ASC');
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log('CabinetRepository::findByUser error: ' . $e->getMessage());
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
