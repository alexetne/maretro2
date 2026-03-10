<?php
declare(strict_types=1);

use PDO;
use PDOException;

/**
 * Repository for audit_logs table.
 */
class AuditRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Insert a log entry.
     *
     * @param array $data
     * @return bool
     */
    public function log(array $data): bool
    {
        $filtered = $this->filterColumns($data);
        if (isset($filtered['old_values']) && is_array($filtered['old_values'])) {
            $filtered['old_values'] = json_encode($filtered['old_values'], JSON_UNESCAPED_UNICODE);
        }
        if (isset($filtered['new_values']) && is_array($filtered['new_values'])) {
            $filtered['new_values'] = json_encode($filtered['new_values'], JSON_UNESCAPED_UNICODE);
        }

        if (empty($filtered)) {
            return false;
        }

        $columns = array_keys($filtered);
        $placeholders = array_map(fn($c) => ':' . $c, $columns);
        $sql = 'INSERT INTO audit_logs (' . implode(',', $columns) . ') VALUES (' . implode(',', $placeholders) . ')';

        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($filtered);
        } catch (PDOException $e) {
            error_log('AuditRepository::log error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Find logs for a user.
     *
     * @param int $userId
     * @return array
     */
    public function findByUser(int $userId): array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM audit_logs WHERE user_id = :user_id ORDER BY created_at DESC');
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log('AuditRepository::findByUser error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all audit logs.
     *
     * @return array
     */
    public function findAll(): array
    {
        try {
            $stmt = $this->pdo->query('SELECT * FROM audit_logs ORDER BY created_at DESC');
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log('AuditRepository::findAll error: ' . $e->getMessage());
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
