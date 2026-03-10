<?php
declare(strict_types=1);

use RuntimeException;

/**
 * CSV export service.
 */
class ExportCsvService
{
    public function exportReceipts(array $receipts, string $filePath): string
    {
        return $this->export($receipts, $filePath, ['id', 'practitioner_id', 'receipt_date', 'amount', 'act_type']);
    }

    public function exportPayments(array $payments, string $filePath): string
    {
        return $this->export($payments, $filePath, ['id', 'retrocession_id', 'payment_date', 'amount', 'payment_method', 'reference']);
    }

    public function exportRetrocessions(array $retrocessions, string $filePath): string
    {
        return $this->export($retrocessions, $filePath, ['id', 'receipt_id', 'retrocession_amount', 'practitioner_kept_amount', 'status', 'created_at']);
    }

    /**
     * Generic CSV writer.
     */
    private function export(array $rows, string $filePath, array $headers): string
    {
        $fh = fopen($filePath, 'w');
        if ($fh === false) {
            throw new RuntimeException('Unable to write CSV file.');
        }

        // Add UTF-8 BOM for Excel compatibility
        fwrite($fh, "\xEF\xBB\xBF");
        fputcsv($fh, $headers);

        foreach ($rows as $row) {
            $line = [];
            foreach ($headers as $h) {
                $line[] = $row[$h] ?? '';
            }
            fputcsv($fh, $line);
        }

        fclose($fh);
        return $filePath;
    }
}
