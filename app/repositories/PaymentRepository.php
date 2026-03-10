<?php
declare(strict_types=1);

use PDO;
use PDOException;

/**
 * Repository for payments table.
 */
class PaymentRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Find payments for a retrocession.
     *
     * @param int $retrocessionId
     * @return array
     */
    public function findByRetrocession(int $retrocessionId): array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM payments WHERE retrocession_id = :retrocession_id ORDER BY payment_date DESC');
            $stmt->execute([':retrocession_id' => $retrocessionId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log('PaymentRepository::findByRetrocession error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Create payment.
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
        $sql = 'INSERT INTO payments (' . implode(',', $columns) . ') VALUES (' . implode(',', $placeholders) . ')';

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($filtered);
            $id = (int)$this->pdo->lastInsertId();
            return $this->findById($id);
        } catch (PDOException $e) {
            error_log('PaymentRepository::create error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Find payments in a period.
     *
     * @param string $start
     * @param string $end
     * @return array
     */
    public function findByPeriod(string $start, string $end): array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM payments WHERE payment_date BETWEEN :start AND :end ORDER BY payment_date DESC');
            $stmt->execute([':start' => $start, ':end' => $end]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log('PaymentRepository::findByPeriod error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Find payment by id.
     *
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM payments WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            error_log('PaymentRepository::findById error: ' . $e->getMessage());
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
