<?php
declare(strict_types=1);

/**
 * Centralized audit logging service.
 */
class AuditService
{
    private AuditRepository $audits;

    public function __construct(AuditRepository $audits)
    {
        $this->audits = $audits;
    }

    public function logCreate(int $userId, string $entityType, int $entityId, array $newValues): bool
    {
        return $this->audits->log([
            'user_id' => $userId,
            'action' => 'create',
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'new_values' => $newValues,
        ]);
    }

    public function logUpdate(int $userId, string $entityType, int $entityId, array $oldValues, array $newValues): bool
    {
        return $this->audits->log([
            'user_id' => $userId,
            'action' => 'update',
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }

    public function logDelete(int $userId, string $entityType, int $entityId, array $oldValues = []): bool
    {
        return $this->audits->log([
            'user_id' => $userId,
            'action' => 'delete',
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValues,
        ]);
    }

    public function logLogin(int $userId, string $ipAddress = ''): bool
    {
        return $this->audits->log([
            'user_id' => $userId,
            'action' => 'login',
            'entity_type' => 'user',
            'entity_id' => $userId,
            'ip_address' => $ipAddress,
        ]);
    }
}
