<?php
declare(strict_types=1);

/**
 * Admin area controller.
 */
class AdminController
{
    private DashboardService $dashboard;
    private UserRepository $users;
    private CabinetRepository $cabinets;
    private AuditRepository $audits;

    public function __construct(
        DashboardService $dashboard,
        UserRepository $users,
        CabinetRepository $cabinets,
        AuditRepository $audits
    ) {
        $this->dashboard = $dashboard;
        $this->users = $users;
        $this->cabinets = $cabinets;
        $this->audits = $audits;
    }

    public function index(): array
    {
        $this->requireAdmin();
        return ['dashboard' => $this->dashboard->getAdminDashboard()];
    }

    public function users(): array
    {
        $this->requireAdmin();
        return ['users' => $this->users->findAll()];
    }

    public function cabinets(): array
    {
        $this->requireAdmin();
        return ['cabinets' => $this->cabinets->findAll()];
    }

    public function logs(): array
    {
        $this->requireAdmin();
        return ['logs' => $this->audits->findAll()];
    }

    private function requireAdmin(): void
    {
        requireAuth();
        if (!isAdmin()) {
            redirectWithMessage('/dashboard', 'error', 'Admin only.');
        }
    }
}
