<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/config/bootstrap.php';
requireAuth();

require_once __DIR__ . '/../app/repositories/RelationshipRepository.php';
require_once __DIR__ . '/../app/services/AuditService.php';
require_once __DIR__ . '/../app/repositories/AuditRepository.php';
require_once __DIR__ . '/../app/controllers/RelationshipController.php';

$pdo = getPDO();
$relRepo   = new RelationshipRepository($pdo);
$auditRepo = new AuditRepository($pdo);
$auditSrv  = new AuditService($auditRepo);
$ctrl      = new RelationshipController($relRepo, $auditSrv);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    if ($action === 'update' && $id) { $ctrl->update($id); exit; }
    if ($action === 'close' && $id) { $ctrl->close($id); exit; }
    $ctrl->create();
    exit;
}

$relationship = null;
if (isset($_GET['id'])) {
    $relationship = $ctrl->show((int)$_GET['id']);
    include __DIR__ . '/../app/views/relationships/edit.view.php';
    return;
}

$relationships = $ctrl->index()['relationships'] ?? [];
include __DIR__ . '/../app/views/relationships/index.view.php';
