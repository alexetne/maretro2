<?php
declare(strict_types=1);

use RuntimeException;

/**
 * PDF export placeholder service.
 * Can be swapped with real PDF generator (e.g., Dompdf) later.
 */
class ExportPdfService
{
    public function generateMonthlyStatement(array $data, string $filePath): string
    {
        return $this->generateHtmlFile($data, $filePath, 'Monthly Statement');
    }

    public function generatePaymentReceipt(array $data, string $filePath): string
    {
        return $this->generateHtmlFile($data, $filePath, 'Payment Receipt');
    }

    private function generateHtmlFile(array $data, string $filePath, string $title): string
    {
        $html = '<html><head><meta charset="utf-8"><title>' . htmlspecialchars($title, ENT_QUOTES) . '</title></head><body>';
        $html .= '<h1>' . htmlspecialchars($title, ENT_QUOTES) . '</h1>';
        $html .= '<pre>' . htmlspecialchars(json_encode($data, JSON_PRETTY_PRINT), ENT_QUOTES) . '</pre>';
        $html .= '</body></html>';

        if (file_put_contents($filePath, $html) === false) {
            throw new RuntimeException('Unable to write PDF placeholder.');
        }

        return $filePath;
    }
}
