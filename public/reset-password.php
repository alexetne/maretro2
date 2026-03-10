<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/config/bootstrap.php';
if (isAuthenticated()) { redirect('/dashboard.php'); }

require_once __DIR__ . '/../app/repositories/UserRepository.php';
require_once __DIR__ . '/../app/services/AuthService.php';
require_once __DIR__ . '/../app/services/MailService.php';
require_once __DIR__ . '/../app/controllers/PasswordResetController.php';

$pdo = getPDO();
$ctrl = new PasswordResetController(new MailService(), new AuthService(new UserRepository($pdo)), new UserRepository($pdo));

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method === 'POST') {
    $ctrl->resetPassword();
    exit;
}

$token = $_GET['token'] ?? '';
$data = $ctrl->showResetForm($token);
include __DIR__ . '/../app/views/auth/reset-password.view.php';
