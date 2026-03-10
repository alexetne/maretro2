<?php
declare(strict_types=1);

use PDO;
use PDOException;

/**
 * Repository for retrocessions table.
 */
class RetrocessionRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Find retrocessions by receipt.
     *
     * @param int $receiptId
     * @return array|null
     */
    public function findByReceipt(int $receiptId): ?array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM retrocessions WHERE receipt_id = :receipt_id LIMIT 1');
            $stmt->execute([':receipt_id' => $receiptId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            error_log('RetrocessionRepository::findByReceipt error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Find retrocessions for a practitioner (via receipts).
     *
     * @param int $userId
     * @return array
     */
    public function findByPractitioner(int $userId): array
    {
        $sql = 'SELECT r.* 
                FROM retrocessions r
                INNER JOIN receipts rc ON rc.id = r.receipt_id
                WHERE rc.practitioner_id = :user_id
                ORDER BY r.id DESC';
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log('RetrocessionRepository::findByPractitioner error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Create a retrocession.
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
        $sql = 'INSERT INTO retrocessions (' . implode(',', $columns) . ') VALUES (' . implode(',', $placeholders) . ')';

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($filtered);
            $id = (int)$this->pdo->lastInsertId();
            return $this->findById($id);
        } catch (PDOException $e) {
            error_log('RetrocessionRepository::create error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update retrocession status.
     *
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateStatus(int $id, string $status): bool
    {
        try {
            $stmt = $this->pdo->prepare('UPDATE retrocessions SET status = :status WHERE id = :id');
            return $stmt->execute([':status' => $status, ':id' => $id]);
        } catch (PDOException $e) {
            error_log('RetrocessionRepository::updateStatus error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Find by id.
     *
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM retrocessions WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            error_log('RetrocessionRepository::findById error: ' . $e->getMessage());
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
