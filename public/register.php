<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/config/bootstrap.php';

if (isAuthenticated()) {
    redirect('/dashboard.php');
}

require_once __DIR__ . '/../app/repositories/UserRepository.php';
require_once __DIR__ . '/../app/services/AuthService.php';
require_once __DIR__ . '/../app/services/AuditService.php';
require_once __DIR__ . '/../app/repositories/AuditRepository.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';

$userRepo  = new UserRepository(getPDO());
$auditRepo = new AuditRepository(getPDO());
$auditSrv  = new AuditService($auditRepo);
$authSrv   = new AuthService($userRepo);
$authCtrl  = new AuthController($authSrv, $auditSrv);

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $authCtrl->register();
    exit;
}

$data = $authCtrl->showRegister();
include __DIR__ . '/../app/views/auth/register.view.php';
