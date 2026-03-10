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

if (isset($_GET['id'])) {
    $cabinet = $ctrl->show((int)$_GET['id'])['cabinet'] ?? [];
    include __DIR__ . '/../app/views/cabinets/show.view.php';
    return;
}

$cabinets = $ctrl->index()['cabinets'] ?? [];
include __DIR__ . '/../app/views/cabinets/index.view.php';
