<?php
declare(strict_types=1);

use RuntimeException;

/**
 * Service handling payments.
 */
class PaymentService
{
    private PaymentRepository $payments;
    private RetrocessionRepository $retrocessions;
    private RetrocessionCalculatorService $retroCalc;

    public function __construct(
        PaymentRepository $payments,
        RetrocessionRepository $retrocessions,
        RetrocessionCalculatorService $retroCalc
    ) {
        $this->payments = $payments;
        $this->retrocessions = $retrocessions;
        $this->retroCalc = $retroCalc;
    }

    /**
     * Create a payment and update retrocession status.
     *
     * @param array $data
     * @return int Payment id
     */
    public function create(array $data): int
    {
        if (empty($data['retrocession_id']) || empty($data['amount'])) {
            throw new RuntimeException('retrocession_id and amount are required.');
        }
        if ((float)$data['amount'] <= 0) {
            throw new RuntimeException('Payment amount must be greater than zero.');
        }

        $created = $this->payments->create($data);
        if (!$created || !isset($created['id'])) {
            throw new RuntimeException('Unable to create payment.');
        }

        $this->updateRetrocessionStatus((int)$data['retrocession_id']);
        return (int)$created['id'];
    }

    /**
     * Payments for a retrocession.
     */
    public function findByRetrocession(int $retrocessionId): array
    {
        return $this->payments->findByRetrocession($retrocessionId);
    }

    /**
     * Payments in period.
     */
    public function findByPeriod(string $start, string $end): array
    {
        return $this->payments->findByPeriod($start, $end);
    }

    /**
     * Sum paid amount for a retrocession.
     */
    public function getPaidAmount(int $retrocessionId): float
    {
        $payments = $this->findByRetrocession($retrocessionId);
        $sum = 0.0;
        foreach ($payments as $p) {
            $sum += (float)($p['amount'] ?? 0);
        }
        return round($sum, 2);
    }

    /**
     * Update retrocession status based on payments.
     */
    public function updateRetrocessionStatus(int $retrocessionId): void
    {
        $retro = $this->retrocessions->findById($retrocessionId);
        if (!$retro) {
            throw new RuntimeException('Retrocession not found.');
        }

        $paid = $this->getPaidAmount($retrocessionId);
        $status = $this->retroCalc->computeStatus((float)$retro['retrocession_amount'], $paid);
        $this->retrocessions->updateStatus($retrocessionId, $status);
    }
}
