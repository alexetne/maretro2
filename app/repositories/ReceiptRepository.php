<?php
declare(strict_types=1);

use PDO;
use PDOException;

/**
 * Repository for receipts table.
 */
class ReceiptRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Find receipt by id.
     *
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM receipts WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            error_log('ReceiptRepository::findById error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Find receipts by practitioner.
     *
     * @param int $userId
     * @return array
     */
    public function findByPractitioner(int $userId): array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM receipts WHERE practitioner_id = :practitioner_id ORDER BY receipt_date DESC');
            $stmt->execute([':practitioner_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log('ReceiptRepository::findByPractitioner error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Find receipts within a date range.
     *
     * @param string $start
     * @param string $end
     * @return array
     */
    public function findByPeriod(string $start, string $end): array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM receipts WHERE receipt_date BETWEEN :start AND :end ORDER BY receipt_date DESC');
            $stmt->execute([':start' => $start, ':end' => $end]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log('ReceiptRepository::findByPeriod error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Create receipt.
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
        $sql = 'INSERT INTO receipts (' . implode(',', $columns) . ') VALUES (' . implode(',', $placeholders) . ')';

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($filtered);
            $id = (int)$this->pdo->lastInsertId();
            return $this->findById($id);
        } catch (PDOException $e) {
            error_log('ReceiptRepository::create error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update receipt.
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
        $sql = 'UPDATE receipts SET ' . implode(', ', $setParts) . ' WHERE id = :id';
        $filtered['id'] = $id;

        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($filtered);
        } catch (PDOException $e) {
            error_log('ReceiptRepository::update error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete receipt.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM receipts WHERE id = :id');
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('ReceiptRepository::delete error: ' . $e->getMessage());
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
