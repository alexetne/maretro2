<?php
declare(strict_types=1);

/**
 * Dashboard controller.
 */
class DashboardController
{
    private DashboardService $dashboard;

    public function __construct(DashboardService $dashboard)
    {
        $this->dashboard = $dashboard;
    }

    public function index(): array
    {
        requireAuth();
        $start = $_GET['start'] ?? null;
        $end = $_GET['end'] ?? null;

        if (isAdmin()) {
            $data = $this->dashboard->getAdminDashboard($start, $end);
        } elseif (isHostingPractitioner()) {
            $data = $this->dashboard->getHostingDashboard((int)user()['id'], $start, $end);
        } else {
            $data = $this->dashboard->getPractitionerDashboard((int)user()['id'], $start, $end);
        }

        return ['dashboard' => $data];
    }
}
