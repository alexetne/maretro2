<?php
declare(strict_types=1);

/**
 * Payment management controller.
 */
class PaymentController
{
    private PaymentService $service;
    private PaymentRepository $payments;
    private RetrocessionRepository $retrocessions;
    private AuditService $audit;

    public function __construct(
        PaymentService $service,
        PaymentRepository $payments,
        RetrocessionRepository $retrocessions,
        AuditService $audit
    ) {
        $this->service = $service;
        $this->payments = $payments;
        $this->retrocessions = $retrocessions;
        $this->audit = $audit;
    }

    public function index(): array
    {
        requireAuth();
        $start = $_GET['start'] ?? '0000-01-01';
        $end = $_GET['end'] ?? '9999-12-31';
        return ['payments' => $this->payments->findByPeriod($start, $end)];
    }

    public function show(int $id): array
    {
        requireAuth();
        return ['payment' => $this->payments->findById($id)];
    }

    public function create(): void
    {
        requireAuth();
        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            redirectBack();
        }

        $data = $this->sanitizeInput();
        try {
            $id = $this->service->create($data);
            $this->audit->logCreate((int)user()['id'], 'payment', $id, $data);
            flash('success', 'Payment recorded.');
            redirect('/payments/' . $id);
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());
            redirectBack();
        }
    }

    public function update(int $id): void
    {
        requireAuth();
        flash('error', 'Payment update not supported.');
        redirectBack();
    }

    public function delete(int $id): void
    {
        requireAuth();
        flash('error', 'Payment delete not supported.');
        redirectBack();
    }

    private function sanitizeInput(): array
    {
        return [
            'retrocession_id' => (int)($_POST['retrocession_id'] ?? 0),
            'payment_date' => sanitizeString($_POST['payment_date'] ?? date('Y-m-d')),
            'amount' => (float)($_POST['amount'] ?? 0),
            'payment_method' => sanitizeString($_POST['payment_method'] ?? ''),
            'reference' => sanitizeString($_POST['reference'] ?? ''),
            'comment' => sanitizeString($_POST['comment'] ?? ''),
        ];
    }
}
