<?php
declare(strict_types=1);

/**
 * Service providing dashboard stats.
 */
class DashboardService
{
    private ReceiptRepository $receipts;
    private RetrocessionRepository $retrocessions;
    private PaymentRepository $payments;

    public function __construct(
        ReceiptRepository $receipts,
        RetrocessionRepository $retrocessions,
        PaymentRepository $payments
    ) {
        $this->receipts = $receipts;
        $this->retrocessions = $retrocessions;
        $this->payments = $payments;
    }

    public function getPractitionerDashboard(int $userId, ?string $start = null, ?string $end = null): array
    {
        $receipts = $this->filterByDate($this->receipts->findByPractitioner($userId), $start, $end, 'receipt_date');
        $retros = $this->filterByDate($this->retrocessions->findByPractitioner($userId), $start, $end, 'created_at');
        return $this->aggregate($receipts, $retros);
    }

    public function getHostingDashboard(int $userId, ?string $start = null, ?string $end = null): array
    {
        // Using practitioner view for hosting until specific repository methods exist
        return $this->getPractitionerDashboard($userId, $start, $end);
    }

    public function getAdminDashboard(?string $start = null, ?string $end = null): array
    {
        $receipts = $this->filterByDate($this->receipts->findByPeriod($start ?? '0000-01-01', $end ?? '9999-12-31'), $start, $end, 'receipt_date');
        // For admin retrocessions: derive via receipts list to avoid extra SQL
        $retros = []; // could fetch all if repository method available
        return $this->aggregate($receipts, $retros);
    }

    /**
     * Aggregate totals.
     */
    private function aggregate(array $receipts, array $retros): array
    {
        $totalReceipts = 0.0;
        foreach ($receipts as $r) {
            $totalReceipts += (float)($r['amount'] ?? 0);
        }

        $totalRetros = 0.0;
        $paid = 0.0;
        foreach ($retros as $retro) {
            $totalRetros += (float)($retro['retrocession_amount'] ?? 0);
            // Payments not directly available; rely on status and amount when status==paid
            if (($retro['status'] ?? '') === 'paid') {
                $paid += (float)($retro['retrocession_amount'] ?? 0);
            }
        }

        return [
            'total_receipts' => round($totalReceipts, 2),
            'total_retrocessions_due' => round($totalRetros, 2),
            'total_paid' => round($paid, 2),
            'remaining_to_pay' => round($totalRetros - $paid, 2),
            'count_receipts' => count($receipts),
            'count_retrocessions' => count($retros),
        ];
    }

    /**
     * Filter array by date range on specific key.
     */
    private function filterByDate(array $items, ?string $start, ?string $end, string $key): array
    {
        if (!$start && !$end) {
            return $items;
        }

        return array_values(array_filter($items, function ($item) use ($start, $end, $key) {
            $date = $item[$key] ?? null;
            if (!$date) {
                return false;
            }
            if ($start && $date < $start) {
                return false;
            }
            if ($end && $date > $end) {
                return false;
            }
            return true;
        }));
    }
}
