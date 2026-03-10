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
if ($method === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    if ($action === 'delete' && $id) { $ctrl->delete($id); exit; }
}

$receipt = null;
if (isset($_GET['id'])) {
    $receipt = $ctrl->show((int)$_GET['id']);
    include __DIR__ . '/../app/views/receipts/show.view.php';
    return;
}

$receipts = $ctrl->index()['receipts'] ?? [];
include __DIR__ . '/../app/views/receipts/index.view.php';
