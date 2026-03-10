<?php
declare(strict_types=1);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path   = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
if ($path !== '/' && str_ends_with($path, '/')) {
    $path = rtrim($path, '/');
}

$render = static function (string $view, array $data = []): void {
    extract($data, EXTR_SKIP);
    include __DIR__ . '/../app/views/' . $view . '.view.php';
};

require_once __DIR__ . '/../app/repositories/UserRepository.php';
require_once __DIR__ . '/../app/repositories/AuditRepository.php';
require_once __DIR__ . '/../app/services/AuditService.php';
require_once __DIR__ . '/../app/services/AuthService.php';
require_once __DIR__ . '/../app/services/MailService.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/PasswordResetController.php';

$userRepo  = new UserRepository(getPDO());
$auditRepo = new AuditRepository(getPDO());
$auditSrv  = new AuditService($auditRepo);
$authSrv   = new AuthService($userRepo);
$mailSrv   = new MailService();
$authCtrl  = new AuthController($authSrv, $auditSrv);
$resetCtrl = new PasswordResetController($mailSrv, $authSrv, $userRepo);

// Authentication pages
if ($method === 'GET' && $path === '/login') {
    if (isAuthenticated()) { redirect('/dashboard'); }
    $render('auth/login', $authCtrl->showLogin());
    return true;
}
if ($method === 'POST' && $path === '/login') {
    if (isAuthenticated()) { redirect('/dashboard'); }
    $authCtrl->login();
    return true;
}
if ($method === 'GET' && $path === '/register') {
    if (isAuthenticated()) { redirect('/dashboard'); }
    $render('auth/register', $authCtrl->showRegister());
    return true;
}
if ($method === 'POST' && $path === '/register') {
    if (isAuthenticated()) { redirect('/dashboard'); }
    $authCtrl->register();
    return true;
}
if ($method === 'GET' && $path === '/logout') {
    requireAuth();
    $authCtrl->logout();
    return true;
}

// Password reset
if ($method === 'GET' && $path === '/forgot-password') {
    if (isAuthenticated()) { redirect('/dashboard'); }
    $render('auth/forgot-password', $resetCtrl->showForgotForm());
    return true;
}
if ($method === 'POST' && $path === '/forgot-password') {
    $resetCtrl->sendResetLink();
    return true;
}
if ($method === 'GET' && $path === '/reset-password') {
    if (isAuthenticated()) { redirect('/dashboard'); }
    $token = $_GET['token'] ?? '';
    $render('auth/reset-password', $resetCtrl->showResetForm($token));
    return true;
}
if ($method === 'POST' && $path === '/reset-password') {
    $resetCtrl->resetPassword();
    return true;
}

return false;
