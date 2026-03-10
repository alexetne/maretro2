<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/config/bootstrap.php';
requireAuth();

require_once __DIR__ . '/../app/repositories/ReceiptRepository.php';
require_once __DIR__ . '/../app/repositories/RetrocessionRepository.php';
require_once __DIR__ . '/../app/repositories/PaymentRepository.php';
require_once __DIR__ . '/../app/services/DashboardService.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';

$pdo = getPDO();
$dashSrv = new DashboardService(new ReceiptRepository($pdo), new RetrocessionRepository($pdo), new PaymentRepository($pdo));
$ctrl = new DashboardController($dashSrv);
$stats = $ctrl->index()['dashboard'] ?? ($ctrl->index()['stats'] ?? []);
include __DIR__ . '/../app/views/dashboard/index.view.php';
