<?php
declare(strict_types=1);

use RuntimeException;

/**
 * Handles CSV/PDF exports.
 */
class ExportController
{
    private ExportCsvService $csv;
    private ExportPdfService $pdf;
    private ReceiptRepository $receipts;
    private PaymentRepository $payments;
    private RetrocessionRepository $retrocessions;

    public function __construct(
        ExportCsvService $csv,
        ExportPdfService $pdf,
        ReceiptRepository $receipts,
        PaymentRepository $payments,
        RetrocessionRepository $retrocessions
    ) {
        $this->csv = $csv;
        $this->pdf = $pdf;
        $this->receipts = $receipts;
        $this->payments = $payments;
        $this->retrocessions = $retrocessions;
    }

    public function exportReceiptsCsv(): void
    {
        requireAuth();
        $start = $_GET['start'] ?? '0000-01-01';
        $end = $_GET['end'] ?? '9999-12-31';
        $data = $this->receipts->findByPeriod($start, $end);
        $path = $this->csv->exportReceipts($data, '/tmp/receipts.csv');
        $this->outputFile($path, 'text/csv');
    }

    public function exportPaymentsCsv(): void
    {
        requireAuth();
        $start = $_GET['start'] ?? '0000-01-01';
        $end = $_GET['end'] ?? '9999-12-31';
        $data = $this->payments->findByPeriod($start, $end);
        $path = $this->csv->exportPayments($data, '/tmp/payments.csv');
        $this->outputFile($path, 'text/csv');
    }

    public function exportRetrocessionsCsv(): void
    {
        requireAuth();
        $data = $this->retrocessions->findByPractitioner((int)user()['id']);
        $path = $this->csv->exportRetrocessions($data, '/tmp/retrocessions.csv');
        $this->outputFile($path, 'text/csv');
    }

    public function exportMonthlyStatementPdf(): void
    {
        requireAuth();
        $data = ['user' => user(), 'generated_at' => date(DATE_ATOM)];
        $path = $this->pdf->generateMonthlyStatement($data, '/tmp/statement.html');
        $this->outputFile($path, 'text/html');
    }

    private function outputFile(string $path, string $contentType): void
    {
        if (!is_readable($path)) {
            flash('error', 'Export file missing.');
            redirectBack();
        }
        header('Content-Type: ' . $contentType);
        header('Content-Disposition: attachment; filename=' . basename($path));
        readfile($path);
        exit;
    }
}
