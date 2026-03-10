<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/config/bootstrap.php';
requireAuth();

require_once __DIR__ . '/../app/repositories/ReceiptRepository.php';
require_once __DIR__ . '/../app/repositories/RetrocessionRepository.php';
require_once __DIR__ . '/../app/repositories/RuleRepository.php';
require_once __DIR__ . '/../app/services/RuleResolverService.php';
require_once __DIR__ . '/../app/services/RetrocessionCalculatorService.php';
require_once __DIR__ . '/../app/services/ReceiptService.php';
require_once __DIR__ . '/../app/services/AuditService.php';
require_once __DIR__ . '/../app/repositories/AuditRepository.php';
require_once __DIR__ . '/../app/controllers/ReceiptController.php';

$pdo = getPDO();
$receiptRepo = new ReceiptRepository($pdo);
$retroRepo   = new RetrocessionRepository($pdo);
$ruleRepo    = new RuleRepository($pdo);
$ruleSrv     = new RuleResolverService($ruleRepo);
$retroCalc   = new RetrocessionCalculatorService($receiptRepo, $retroRepo, $ruleSrv);
$receiptSrv  = new ReceiptService($receiptRepo, $retroCalc);
$auditSrv    = new AuditService(new AuditRepository($pdo));
$ctrl        = new ReceiptController($receiptSrv, $receiptRepo, $auditSrv);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

if ($method === 'POST') {
    if ($id > 0) { $ctrl->update($id); } else { $ctrl->create(); }
    exit;
}

if ($id > 0) {
    $receipt = $ctrl->show($id)['receipt'] ?? [];
    include __DIR__ . '/../app/views/receipts/edit.view.php';
} else {
    $receipt = [];
    include __DIR__ . '/../app/views/receipts/create.view.php';
}
