<?php
declare(strict_types=1);

use PDO;
use PDOException;

/**
 * Repository for retrocession_rules table.
 */
class RuleRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Get all rules for a relationship.
     *
     * @param int $relationshipId
     * @return array
     */
    public function findByRelationship(int $relationshipId): array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM retrocession_rules WHERE relationship_id = :relationship_id ORDER BY applies_from DESC');
            $stmt->execute([':relationship_id' => $relationshipId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log('RuleRepository::findByRelationship error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Find the active rule at a given date.
     *
     * @param int $relationshipId
     * @param string $date
     * @return array|null
     */
    public function findActiveRule(int $relationshipId, string $date): ?array
    {
        $sql = 'SELECT * FROM retrocession_rules 
                WHERE relationship_id = :relationship_id
                  AND applies_from <= :date
                  AND (applies_to IS NULL OR applies_to >= :date)
                ORDER BY applies_from DESC
                LIMIT 1';
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':relationship_id' => $relationshipId, ':date' => $date]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            error_log('RuleRepository::findActiveRule error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a rule.
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
        $sql = 'INSERT INTO retrocession_rules (' . implode(',', $columns) . ') VALUES (' . implode(',', $placeholders) . ')';

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($filtered);
            $id = (int)$this->pdo->lastInsertId();
            return $this->findById($id);
        } catch (PDOException $e) {
            error_log('RuleRepository::create error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update a rule.
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
        $sql = 'UPDATE retrocession_rules SET ' . implode(', ', $setParts) . ' WHERE id = :id';
        $filtered['id'] = $id;

        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($filtered);
        } catch (PDOException $e) {
            error_log('RuleRepository::update error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Find rule by id.
     *
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM retrocession_rules WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            error_log('RuleRepository::findById error: ' . $e->getMessage());
            return null;
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
