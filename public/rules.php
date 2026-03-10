<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/config/bootstrap.php';
requireAuth();

require_once __DIR__ . '/../app/repositories/RuleRepository.php';
require_once __DIR__ . '/../app/services/AuditService.php';
require_once __DIR__ . '/../app/repositories/AuditRepository.php';
require_once __DIR__ . '/../app/controllers/RetrocessionRuleController.php';

$pdo = getPDO();
$ruleRepo  = new RuleRepository($pdo);
$auditRepo = new AuditRepository($pdo);
$auditSrv  = new AuditService($auditRepo);
$ctrl      = new RetrocessionRuleController($ruleRepo, $auditSrv);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    if ($action === 'update' && $id) { $ctrl->update($id); exit; }
    if ($action === 'delete' && $id) { $ctrl->delete($id); exit; }
    $ctrl->create();
    exit;
}

$rule = [];
if (isset($_GET['id'])) {
    $rule = $ctrl->show((int)$_GET['id']);
    include __DIR__ . '/../app/views/rules/edit.view.php';
    return;
}

if (isset($_GET['create'])) {
    include __DIR__ . '/../app/views/rules/create.view.php';
    return;
}

$rules = $ctrl->index()['rules'] ?? [];
include __DIR__ . '/../app/views/rules/index.view.php';
