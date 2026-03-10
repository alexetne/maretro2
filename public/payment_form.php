<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/config/bootstrap.php';
requireAuth();

require_once __DIR__ . '/../app/repositories/PaymentRepository.php';
require_once __DIR__ . '/../app/repositories/RetrocessionRepository.php';
require_once __DIR__ . '/../app/repositories/ReceiptRepository.php';
require_once __DIR__ . '/../app/repositories/RuleRepository.php';
require_once __DIR__ . '/../app/services/RuleResolverService.php';
require_once __DIR__ . '/../app/services/RetrocessionCalculatorService.php';
require_once __DIR__ . '/../app/services/PaymentService.php';
require_once __DIR__ . '/../app/services/AuditService.php';
require_once __DIR__ . '/../app/repositories/AuditRepository.php';
require_once __DIR__ . '/../app/controllers/PaymentController.php';

$pdo = getPDO();
$paymentRepo = new PaymentRepository($pdo);
$retroRepo   = new RetrocessionRepository($pdo);
$receiptRepo = new ReceiptRepository($pdo);
$ruleSrv     = new RuleResolverService(new RuleRepository($pdo));
$retroCalc   = new RetrocessionCalculatorService($receiptRepo, $retroRepo, $ruleSrv);
$paymentSrv  = new PaymentService($paymentRepo, $retroRepo, $retroCalc);
$auditSrv    = new AuditService(new AuditRepository($pdo));
$ctrl        = new PaymentController($paymentSrv, $paymentRepo, $retroRepo, $auditSrv);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) { $ctrl->update($id); } else { $ctrl->create(); }
    exit;
}

$payment = [];
include __DIR__ . '/../app/views/payments/create.view.php';
