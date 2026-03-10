<?php
declare(strict_types=1);

/**
 * Retrocession display and recalculation controller.
 */
class RetrocessionController
{
    private RetrocessionRepository $retrocessions;
    private RetrocessionCalculatorService $calculator;
    private AuditService $audit;

    public function __construct(
        RetrocessionRepository $retrocessions,
        RetrocessionCalculatorService $calculator,
        AuditService $audit
    ) {
        $this->retrocessions = $retrocessions;
        $this->calculator = $calculator;
        $this->audit = $audit;
    }

    public function index(): array
    {
        requireAuth();
        $practitionerId = (int)($_GET['practitioner'] ?? 0);
        $list = [];
        if ($practitionerId > 0) {
            $list = $this->retrocessions->findByPractitioner($practitionerId);
        } elseif (isHostedPractitioner()) {
            $list = $this->retrocessions->findByPractitioner((int)user()['id']);
        }
        return ['retrocessions' => $list];
    }

    public function show(int $id): array
    {
        requireAuth();
        return ['retrocession' => $this->retrocessions->findById($id)];
    }

    public function recalculate(int $receiptId): void
    {
        requireAuth();
        if (!isAdmin() && !isHostingPractitioner()) {
            redirectWithMessage('/retrocessions', 'error', 'Unauthorized.');
        }
        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            redirectBack();
        }

        try {
            $result = $this->calculator->recalculateFromReceipt($receiptId);
            $this->audit->logUpdate((int)user()['id'], 'retrocession', (int)($result['id'] ?? 0), [], ['recalculated' => true]);
            flash('success', 'Retrocession recalculated.');
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());
        }
        redirect('/retrocessions');
    }
}
