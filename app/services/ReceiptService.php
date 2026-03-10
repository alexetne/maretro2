<?php
declare(strict_types=1);

use RuntimeException;

/**
 * Service handling receipt business logic.
 */
class ReceiptService
{
    private ReceiptRepository $receipts;
    private RetrocessionCalculatorService $retroCalc;

    public function __construct(ReceiptRepository $receipts, RetrocessionCalculatorService $retroCalc)
    {
        $this->receipts = $receipts;
        $this->retroCalc = $retroCalc;
    }

    /**
     * Create receipt and auto-create retrocession.
     *
     * @param array $data
     * @return int New receipt id
     */
    public function create(array $data): int
    {
        $this->validate($data);
        $created = $this->receipts->create($data);
        if (!$created || !isset($created['id'])) {
            throw new RuntimeException('Unable to create receipt.');
        }

        $this->retroCalc->createFromReceipt((int)$created['id']);
        return (int)$created['id'];
    }

    /**
     * Update receipt and recalculate retrocession.
     */
    public function update(int $id, array $data): bool
    {
        $this->validate($data, false);
        $ok = $this->receipts->update($id, $data);
        if ($ok) {
            $this->retroCalc->recalculateFromReceipt($id);
        }
        return $ok;
    }

    /**
     * Delete receipt (retrocession deletion handled by FK cascade if set).
     */
    public function delete(int $id): bool
    {
        return $this->receipts->delete($id);
    }

    /**
     * Get receipts for practitioner.
     */
    public function findByPractitioner(int $userId): array
    {
        return $this->receipts->findByPractitioner($userId);
    }

    /**
     * Get receipts by period.
     */
    public function findByPeriod(string $start, string $end): array
    {
        return $this->receipts->findByPeriod($start, $end);
    }

    /**
     * Basic validation of required fields.
     */
    private function validate(array $data, bool $isCreate = true): void
    {
        $required = ['practitioner_id', 'relationship_id', 'cabinet_id', 'receipt_date', 'amount'];
        foreach ($required as $field) {
            if ($isCreate && empty($data[$field])) {
                throw new RuntimeException("Field {$field} is required.");
            }
        }

        if (isset($data['amount']) && (float)$data['amount'] <= 0) {
            throw new RuntimeException('Amount must be positive.');
        }
    }
}
