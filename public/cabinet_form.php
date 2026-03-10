<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/config/bootstrap.php';
requireAuth();

require_once __DIR__ . '/../app/repositories/CabinetRepository.php';
require_once __DIR__ . '/../app/services/AuditService.php';
require_once __DIR__ . '/../app/repositories/AuditRepository.php';
require_once __DIR__ . '/../app/controllers/CabinetController.php';

$pdo = getPDO();
$cabRepo   = new CabinetRepository($pdo);
$auditRepo = new AuditRepository($pdo);
$auditSrv  = new AuditService($auditRepo);
$ctrl      = new CabinetController($cabRepo, $auditSrv);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

if ($method === 'POST') {
    if ($id > 0) {
        $ctrl->update($id);
    } else {
        $ctrl->create();
    }
    exit;
}

if ($id > 0) {
    $cabinet = $ctrl->show($id)['cabinet'] ?? [];
    include __DIR__ . '/../app/views/cabinets/edit.view.php';
} else {
    $cabinet = [];
    include __DIR__ . '/../app/views/cabinets/create.view.php';
}
