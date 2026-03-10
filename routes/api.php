<?php
declare(strict_types=1);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path   = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
if ($path !== '/' && str_ends_with($path, '/')) { $path = rtrim($path, '/'); }

$respond = static function (int $status, array $payload): void {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
};

requireAuth();

require_once __DIR__ . '/../app/repositories/ReceiptRepository.php';
require_once __DIR__ . '/../app/repositories/RetrocessionRepository.php';
require_once __DIR__ . '/../app/repositories/PaymentRepository.php';
require_once __DIR__ . '/../app/repositories/RuleRepository.php';
require_once __DIR__ . '/../app/services/RuleResolverService.php';
require_once __DIR__ . '/../app/services/RetrocessionCalculatorService.php';
require_once __DIR__ . '/../app/services/PaymentService.php';
require_once __DIR__ . '/../app/services/DashboardService.php';
require_once __DIR__ . '/../app/services/ReceiptService.php';

$pdo = getPDO();
$receiptRepo = new ReceiptRepository($pdo);
$retroRepo   = new RetrocessionRepository($pdo);
$payRepo     = new PaymentRepository($pdo);
$ruleRepo    = new RuleRepository($pdo);
$ruleSrv     = new RuleResolverService($ruleRepo);
$retroCalc   = new RetrocessionCalculatorService($receiptRepo, $retroRepo, $ruleSrv);
$paySrv      = new PaymentService($payRepo, $retroRepo, $retroCalc);
$dashSrv     = new DashboardService($receiptRepo, $retroRepo, $payRepo);
$receiptSrv  = new ReceiptService($receiptRepo, $retroCalc);

try {
    if ($method === 'GET' && $path === '/api/dashboard') {
        $data = $dashSrv->getAdminDashboard();
        $respond(200, ['success' => true, 'data' => $data]);
        return true;
    }

    if ($method === 'GET' && $path === '/api/receipts') {
        $respond(200, ['success' => true, 'data' => $receiptRepo->findByPeriod($_GET['start'] ?? '0000-01-01', $_GET['end'] ?? '9999-12-31')]);
        return true;
    }

    if ($method === 'GET' && $path === '/api/payments') {
        $respond(200, ['success' => true, 'data' => $payRepo->findByPeriod($_GET['start'] ?? '0000-01-01', $_GET['end'] ?? '9999-12-31')]);
        return true;
    }

    if ($method === 'GET' && $path === '/api/retrocessions') {
        $respond(200, ['success' => true, 'data' => $retroRepo->findByPractitioner((int)user()['id'])]);
        return true;
    }

    if ($method === 'POST' && $path === '/api/retrocessions/calculate') {
        $receiptId = (int)($_POST['receipt_id'] ?? 0);
        $data = $retroCalc->recalculateFromReceipt($receiptId);
        $respond(200, ['success' => true, 'data' => $data]);
        return true;
    }

    if ($method === 'POST' && $path === '/api/payments/status') {
        $retroId = (int)($_POST['retrocession_id'] ?? 0);
        $paySrv->updateRetrocessionStatus($retroId);
        $respond(200, ['success' => true]);
        return true;
    }

    $respond(404, ['success' => false, 'message' => 'Not found']);
    return true;
} catch (Throwable $e) {
    $respond(500, ['success' => false, 'message' => 'Server error']);
    return true;
}
