<?php
declare(strict_types=1);

use RuntimeException;

/**
 * Handles retrocession calculations and creation.
 */
class RetrocessionCalculatorService
{
    private ReceiptRepository $receipts;
    private RetrocessionRepository $retrocessions;
    private RuleResolverService $ruleResolver;

    public function __construct(
        ReceiptRepository $receipts,
        RetrocessionRepository $retrocessions,
        RuleResolverService $ruleResolver
    ) {
        $this->receipts = $receipts;
        $this->retrocessions = $retrocessions;
        $this->ruleResolver = $ruleResolver;
    }

    /**
     * Calculate retrocession details based on rule.
     *
     * @param float $amount
     * @param array $rule
     * @return array{retrocession_amount:float,practitioner_kept_amount:float}
     *
     * @throws RuntimeException
     */
    public function calculate(float $amount, array $rule): array
    {
        if (!isset($rule['rule_type'], $rule['value'])) {
            throw new RuntimeException('Invalid rule configuration.');
        }

        $retrocession = 0.0;
        if ($rule['rule_type'] === 'percentage') {
            $retrocession = $amount * ((float)$rule['value'] / 100);
        } elseif ($rule['rule_type'] === 'fixed_amount') {
            $retrocession = (float)$rule['value'];
        } else {
            throw new RuntimeException('Unsupported rule type: ' . $rule['rule_type']);
        }

        // Prevent retrocession exceeding amount
        $retrocession = min($retrocession, $amount);

        $retrocession = round($retrocession, 2);
        $kept = round($amount - $retrocession, 2);

        return [
            'retrocession_amount' => $retrocession,
            'practitioner_kept_amount' => $kept,
        ];
    }

    /**
     * Create retrocession from receipt.
     *
     * @param int $receiptId
     * @return int retrocession id
     */
    public function createFromReceipt(int $receiptId): int
    {
        $receipt = $this->receipts->findById($receiptId);
        if (!$receipt) {
            throw new RuntimeException('Receipt not found.');
        }

        $rule = $this->ruleResolver->resolveActiveRule(
            (int)$receipt['relationship_id'],
            $receipt['receipt_date'],
            $receipt['act_type'] ?? null
        );

        if (!$rule) {
            throw new RuntimeException('No active retrocession rule found.');
        }

        $calc = $this->calculate((float)$receipt['amount'], $rule);

        $data = [
            'receipt_id' => $receiptId,
            'base_amount' => round((float)$receipt['amount'], 2),
            'retrocession_amount' => $calc['retrocession_amount'],
            'practitioner_kept_amount' => $calc['practitioner_kept_amount'],
            'status' => 'to_pay',
        ];

        $created = $this->retrocessions->create($data);
        if (!$created || !isset($created['id'])) {
            throw new RuntimeException('Unable to create retrocession.');
        }

        return (int)$created['id'];
    }

    /**
     * Recalculate retrocession from receipt (update existing record).
     *
     * @param int $receiptId
     * @return array Updated retrocession
     */
    public function recalculateFromReceipt(int $receiptId): array
    {
        $receipt = $this->receipts->findById($receiptId);
        if (!$receipt) {
            throw new RuntimeException('Receipt not found.');
        }

        $retro = $this->retrocessions->findByReceipt($receiptId);
        if (!$retro) {
            // If none exists, create it
            $newId = $this->createFromReceipt($receiptId);
            return $this->retrocessions->findById($newId) ?? [];
        }

        $rule = $this->ruleResolver->resolveActiveRule(
            (int)$receipt['relationship_id'],
            $receipt['receipt_date'],
            $receipt['act_type'] ?? null
        );

        if (!$rule) {
            throw new RuntimeException('No active retrocession rule found.');
        }

        $calc = $this->calculate((float)$receipt['amount'], $rule);
        $data = [
            'base_amount' => round((float)$receipt['amount'], 2),
            'retrocession_amount' => $calc['retrocession_amount'],
            'practitioner_kept_amount' => $calc['practitioner_kept_amount'],
        ];

        $this->retrocessions->updateStatus((int)$retro['id'], $retro['status']); // keep status
        $this->retrocessions->update((int)$retro['id'], $data);

        return $this->retrocessions->findById((int)$retro['id']) ?? [];
    }

    /**
     * Compute retrocession payment status.
     */
    public function computeStatus(float $retrocessionAmount, float $paidAmount): string
    {
        if ($paidAmount <= 0) {
            return 'to_pay';
        }
        if ($paidAmount + 0.01 >= $retrocessionAmount) { // small tolerance
            return 'paid';
        }
        return 'partially_paid';
    }
}
