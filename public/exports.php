<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/config/bootstrap.php';
requireAuth();

require_once __DIR__ . '/../app/repositories/ReceiptRepository.php';
require_once __DIR__ . '/../app/repositories/PaymentRepository.php';
require_once __DIR__ . '/../app/repositories/RetrocessionRepository.php';
require_once __DIR__ . '/../app/services/ExportCsvService.php';
require_once __DIR__ . '/../app/services/ExportPdfService.php';
require_once __DIR__ . '/../app/controllers/ExportController.php';

$pdo = getPDO();
$ctrl = new ExportController(
    new ExportCsvService(),
    new ExportPdfService(),
    new ReceiptRepository($pdo),
    new PaymentRepository($pdo),
    new RetrocessionRepository($pdo)
);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'receipts') { $ctrl->exportReceiptsCsv(); exit; }
    if ($action === 'payments') { $ctrl->exportPaymentsCsv(); exit; }
    if ($action === 'retrocessions') { $ctrl->exportRetrocessionsCsv(); exit; }
    if ($action === 'statement') { $ctrl->exportMonthlyStatementPdf(); exit; }
}

include __DIR__ . '/../app/views/exports/index.view.php';
