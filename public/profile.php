<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/config/bootstrap.php';
requireAuth();

require_once __DIR__ . '/../app/repositories/UserRepository.php';
require_once __DIR__ . '/../app/services/AuthService.php';
require_once __DIR__ . '/../app/services/AuditService.php';
require_once __DIR__ . '/../app/repositories/AuditRepository.php';
require_once __DIR__ . '/../app/controllers/ProfileController.php';

$pdo = getPDO();
$userRepo  = new UserRepository($pdo);
$auditRepo = new AuditRepository($pdo);
$auditSrv  = new AuditService($auditRepo);
$authSrv   = new AuthService($userRepo);
$ctrl      = new ProfileController($userRepo, $authSrv, $auditSrv);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'password') {
        $ctrl->changePassword();
    } else {
        $ctrl->update();
    }
    exit;
}

$data = $ctrl->show();
$user = $data['user'] ?? user();
include __DIR__ . '/../app/views/profile/profile.view.php';
