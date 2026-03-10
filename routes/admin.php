<?php
declare(strict_types=1);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path   = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
if ($path !== '/' && str_ends_with($path, '/')) { $path = rtrim($path, '/'); }

$render = static function (string $view, array $data = []): void {
    extract($data, EXTR_SKIP);
    include __DIR__ . '/../app/views/' . $view . '.view.php';
};

requireAuth();
if (!isAdmin()) {
    redirectWithMessage('/dashboard', 'error', 'Admin uniquement.');
}

require_once __DIR__ . '/../app/repositories/UserRepository.php';
require_once __DIR__ . '/../app/repositories/CabinetRepository.php';
require_once __DIR__ . '/../app/repositories/AuditRepository.php';
require_once __DIR__ . '/../app/repositories/ReceiptRepository.php';
require_once __DIR__ . '/../app/repositories/RetrocessionRepository.php';
require_once __DIR__ . '/../app/repositories/PaymentRepository.php';
require_once __DIR__ . '/../app/services/AuditService.php';
require_once __DIR__ . '/../app/services/AuthService.php';
require_once __DIR__ . '/../app/services/DashboardService.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';
require_once __DIR__ . '/../app/controllers/UserController.php';
require_once __DIR__ . '/../app/controllers/AuditController.php';

$pdo = getPDO();
$userRepo  = new UserRepository($pdo);
$cabRepo   = new CabinetRepository($pdo);
$auditRepo = new AuditRepository($pdo);
$auditSrv  = new AuditService($auditRepo);
$authSrv   = new AuthService($userRepo);
$dashSrv   = new DashboardService(new ReceiptRepository($pdo), new RetrocessionRepository($pdo), new PaymentRepository($pdo));

$adminCtrl = new AdminController($dashSrv, $userRepo, $cabRepo, $auditRepo);
$userCtrl  = new UserController($userRepo, $auditSrv);
$auditCtrl = new AuditController($auditRepo);

if ($method === 'GET' && $path === '/admin') { $render('admin/index', $adminCtrl->index()); return true; }
if ($method === 'GET' && $path === '/admin/users') { $render('admin/users', $adminCtrl->users()); return true; }
if ($method === 'GET' && $path === '/admin/cabinets') { $render('admin/cabinets', $adminCtrl->cabinets()); return true; }
if ($method === 'GET' && $path === '/admin/logs') { $render('admin/logs', $adminCtrl->logs()); return true; }
if ($method === 'GET' && $path === '/admin/logs/user') { $render('admin/logs', $auditCtrl->showByUser((int)($_GET['user_id'] ?? 0))); return true; }

// optional mutations
if ($method === 'POST' && $path === '/admin/users/create') { $userCtrl->create(); return true; }
if ($method === 'POST' && $path === '/admin/users/update') { $userCtrl->update((int)($_POST['id'] ?? 0)); return true; }
if ($method === 'POST' && $path === '/admin/users/delete') { $userCtrl->delete((int)($_POST['id'] ?? 0)); return true; }

http_response_code(404);
include __DIR__ . '/../app/views/errors/404.view.php';
return true;
