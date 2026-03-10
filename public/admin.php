<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/config/bootstrap.php';
requireAuth();
if (!isAdmin()) { redirect('/unauthorized.php'); }

require_once __DIR__ . '/../app/repositories/UserRepository.php';
require_once __DIR__ . '/../app/repositories/CabinetRepository.php';
require_once __DIR__ . '/../app/repositories/AuditRepository.php';
require_once __DIR__ . '/../app/repositories/ReceiptRepository.php';
require_once __DIR__ . '/../app/repositories/RetrocessionRepository.php';
require_once __DIR__ . '/../app/repositories/PaymentRepository.php';
require_once __DIR__ . '/../app/services/DashboardService.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';
require_once __DIR__ . '/../app/controllers/AuditController.php';

$pdo = getPDO();
$dashSrv = new DashboardService(new ReceiptRepository($pdo), new RetrocessionRepository($pdo), new PaymentRepository($pdo));
$adminCtrl = new AdminController($dashSrv, new UserRepository($pdo), new CabinetRepository($pdo), new AuditRepository($pdo));
$auditCtrl = new AuditController(new AuditRepository($pdo));

$section = $_GET['section'] ?? '';
switch ($section) {
    case 'users':
        $users = $adminCtrl->users()['users'] ?? [];
        include __DIR__ . '/../app/views/admin/users.view.php';
        break;
    case 'cabinets':
        $cabinets = $adminCtrl->cabinets()['cabinets'] ?? [];
        include __DIR__ . '/../app/views/admin/cabinets.view.php';
        break;
    case 'logs':
        $logs = $adminCtrl->logs()['logs'] ?? [];
        include __DIR__ . '/../app/views/admin/logs.view.php';
        break;
    case 'logs_user':
        $logs = $auditCtrl->showByUser((int)($_GET['user_id'] ?? 0))['logs'] ?? [];
        include __DIR__ . '/../app/views/admin/logs.view.php';
        break;
    default:
        $stats = $adminCtrl->index()['dashboard'] ?? [];
        include __DIR__ . '/../app/views/admin/index.view.php';
}
