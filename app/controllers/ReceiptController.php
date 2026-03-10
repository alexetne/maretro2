<?php
declare(strict_types=1);

/**
 * Receipt controller.
 */
class ReceiptController
{
    private ReceiptService $service;
    private ReceiptRepository $receipts;
    private AuditService $audit;

    public function __construct(ReceiptService $service, ReceiptRepository $receipts, AuditService $audit)
    {
        $this->service = $service;
        $this->receipts = $receipts;
        $this->audit = $audit;
    }

    public function index(): array
    {
        requireAuth();
        $start = $_GET['start'] ?? null;
        $end = $_GET['end'] ?? null;

        if (isAdmin() || isHostingPractitioner()) {
            $list = $this->receipts->findByPeriod($start ?? '0000-01-01', $end ?? '9999-12-31');
        } else {
            $list = $this->receipts->findByPractitioner((int)user()['id']);
        }

        return ['receipts' => $list];
    }

    public function show(int $id): array
    {
        requireAuth();
        return ['receipt' => $this->receipts->findById($id)];
    }

    public function create(): void
    {
        requireAuth();
        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            redirectBack();
        }

        $data = $this->collectInput();
        try {
            $id = $this->service->create($data);
            $this->audit->logCreate((int)user()['id'], 'receipt', $id, $data);
            flash('success', 'Receipt created.');
            redirect('/receipts/' . $id);
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());
            redirectBack();
        }
    }

    public function update(int $id): void
    {
        requireAuth();
        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            redirectBack();
        }

        $data = $this->collectInput(false);
        $old = $this->receipts->findById($id) ?? [];
        try {
            if ($this->service->update($id, $data)) {
                $this->audit->logUpdate((int)user()['id'], 'receipt', $id, $old, $data);
                flash('success', 'Receipt updated.');
                redirect('/receipts/' . $id);
            }
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());
        }
        redirectBack();
    }

    public function delete(int $id): void
    {
        requireAuth();
        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            redirectBack();
        }

        $old = $this->receipts->findById($id) ?? [];
        if ($this->service->delete($id)) {
            $this->audit->logDelete((int)user()['id'], 'receipt', $id, $old);
            flash('success', 'Receipt deleted.');
            redirect('/receipts');
        }
        flash('error', 'Unable to delete receipt.');
        redirectBack();
    }

    private function collectInput(bool $isCreate = true): array
    {
        $data = [
            'practitioner_id' => (int)($_POST['practitioner_id'] ?? user()['id'] ?? 0),
            'relationship_id' => (int)($_POST['relationship_id'] ?? 0),
            'cabinet_id' => (int)($_POST['cabinet_id'] ?? 0),
            'receipt_date' => sanitizeString($_POST['receipt_date'] ?? ''),
            'amount' => (float)($_POST['amount'] ?? 0),
            'act_type' => sanitizeString($_POST['act_type'] ?? ''),
            'comment' => sanitizeString($_POST['comment'] ?? ''),
        ];

        if ($isCreate && $data['amount'] <= 0) {
            throw new RuntimeException('Amount must be positive.');
        }
        return $data;
    }
}
