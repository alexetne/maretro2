<?php
declare(strict_types=1);

use PDO;
use PDOException;

/**
 * Repository for practitioner_relationships table.
 */
class RelationshipRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Find relationship by id.
     *
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM practitioner_relationships WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            error_log('RelationshipRepository::findById error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get relationships for a cabinet.
     *
     * @param int $cabinetId
     * @return array
     */
    public function findByCabinet(int $cabinetId): array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM practitioner_relationships WHERE cabinet_id = :cabinet_id ORDER BY start_date DESC');
            $stmt->execute([':cabinet_id' => $cabinetId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log('RelationshipRepository::findByCabinet error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Create a relationship.
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
        $sql = 'INSERT INTO practitioner_relationships (' . implode(',', $columns) . ') VALUES (' . implode(',', $placeholders) . ')';

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($filtered);
            $id = (int)$this->pdo->lastInsertId();
            return $this->findById($id);
        } catch (PDOException $e) {
            error_log('RelationshipRepository::create error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update a relationship.
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
        $sql = 'UPDATE practitioner_relationships SET ' . implode(', ', $setParts) . ' WHERE id = :id';
        $filtered['id'] = $id;

        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($filtered);
        } catch (PDOException $e) {
            error_log('RelationshipRepository::update error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Close relationship by setting end_date to today.
     *
     * @param int $id
     * @return bool
     */
    public function closeRelationship(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare('UPDATE practitioner_relationships SET end_date = CURDATE() WHERE id = :id');
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('RelationshipRepository::closeRelationship error: ' . $e->getMessage());
            return false;
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
