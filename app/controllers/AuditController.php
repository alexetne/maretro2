<?php
declare(strict_types=1);

/**
 * Audit log viewer (admin only).
 */
class AuditController
{
    private AuditRepository $audits;

    public function __construct(AuditRepository $audits)
    {
        $this->audits = $audits;
    }

    public function index(): array
    {
        $this->requireAdmin();
        return ['logs' => $this->audits->findAll()];
    }

    public function showByUser(int $userId): array
    {
        $this->requireAdmin();
        return ['logs' => $this->audits->findByUser($userId)];
    }

    private function requireAdmin(): void
    {
        requireAuth();
        if (!isAdmin()) {
            redirectWithMessage('/dashboard', 'error', 'Admin only.');
        }
    }
}
