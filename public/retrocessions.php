<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/config/bootstrap.php';
requireAuth();

require_once __DIR__ . '/../app/repositories/RetrocessionRepository.php';
require_once __DIR__ . '/../app/repositories/ReceiptRepository.php';
require_once __DIR__ . '/../app/repositories/RuleRepository.php';
require_once __DIR__ . '/../app/services/RuleResolverService.php';
require_once __DIR__ . '/../app/services/RetrocessionCalculatorService.php';
require_once __DIR__ . '/../app/services/AuditService.php';
require_once __DIR__ . '/../app/repositories/AuditRepository.php';
require_once __DIR__ . '/../app/controllers/RetrocessionController.php';

$pdo = getPDO();
$receiptRepo = new ReceiptRepository($pdo);
$retroRepo   = new RetrocessionRepository($pdo);
$ruleSrv     = new RuleResolverService(new RuleRepository($pdo));
$retroCalc   = new RetrocessionCalculatorService($receiptRepo, $retroRepo, $ruleSrv);
$auditSrv    = new AuditService(new AuditRepository($pdo));
$ctrl        = new RetrocessionController($retroRepo, $retroCalc, $auditSrv);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method === 'POST' && isset($_POST['receipt_id'])) {
    $ctrl->recalculate((int)$_POST['receipt_id']);
    exit;
}

$retrocession = null;
if (isset($_GET['id'])) {
    $retrocession = $ctrl->show((int)$_GET['id']);
    include __DIR__ . '/../app/views/retrocessions/show.view.php';
    return;
}

$retrocessions = $ctrl->index()['retrocessions'] ?? [];
include __DIR__ . '/../app/views/retrocessions/index.view.php';
