<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/config/bootstrap.php';
requireAuth();

require_once __DIR__ . '/../app/repositories/UserRepository.php';
require_once __DIR__ . '/../app/services/AuditService.php';
require_once __DIR__ . '/../app/repositories/AuditRepository.php';
require_once __DIR__ . '/../app/controllers/UserController.php';

$pdo = getPDO();
$userRepo  = new UserRepository($pdo);
$auditRepo = new AuditRepository($pdo);
$auditSrv  = new AuditService($auditRepo);
$ctrl      = new UserController($userRepo, $auditSrv);

$users = $ctrl->index()['users'] ?? [];
// reuse admin users view for now
include __DIR__ . '/../app/views/admin/users.view.php';
