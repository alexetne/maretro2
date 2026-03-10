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

requireAuth(); // all routes require auth

require_once __DIR__ . '/../app/repositories/UserRepository.php';
require_once __DIR__ . '/../app/repositories/CabinetRepository.php';
require_once __DIR__ . '/../app/repositories/RelationshipRepository.php';
require_once __DIR__ . '/../app/repositories/RuleRepository.php';
require_once __DIR__ . '/../app/repositories/ReceiptRepository.php';
require_once __DIR__ . '/../app/repositories/RetrocessionRepository.php';
require_once __DIR__ . '/../app/repositories/PaymentRepository.php';
require_once __DIR__ . '/../app/repositories/AuditRepository.php';
require_once __DIR__ . '/../app/services/AuditService.php';
require_once __DIR__ . '/../app/services/AuthService.php';
require_once __DIR__ . '/../app/services/RuleResolverService.php';
require_once __DIR__ . '/../app/services/RetrocessionCalculatorService.php';
require_once __DIR__ . '/../app/services/ReceiptService.php';
require_once __DIR__ . '/../app/services/PaymentService.php';
require_once __DIR__ . '/../app/services/DashboardService.php';
require_once __DIR__ . '/../app/services/ExportCsvService.php';
require_once __DIR__ . '/../app/services/ExportPdfService.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';
require_once __DIR__ . '/../app/controllers/ProfileController.php';
require_once __DIR__ . '/../app/controllers/CabinetController.php';
require_once __DIR__ . '/../app/controllers/RelationshipController.php';
require_once __DIR__ . '/../app/controllers/RetrocessionRuleController.php';
require_once __DIR__ . '/../app/controllers/ReceiptController.php';
require_once __DIR__ . '/../app/controllers/RetrocessionController.php';
require_once __DIR__ . '/../app/controllers/PaymentController.php';
require_once __DIR__ . '/../app/controllers/ExportController.php';

$pdo = getPDO();

$userRepo   = new UserRepository($pdo);
$cabRepo    = new CabinetRepository($pdo);
$relRepo    = new RelationshipRepository($pdo);
$ruleRepo   = new RuleRepository($pdo);
$receiptRepo= new ReceiptRepository($pdo);
$retroRepo  = new RetrocessionRepository($pdo);
$payRepo    = new PaymentRepository($pdo);
$auditRepo  = new AuditRepository($pdo);

$auditSrv   = new AuditService($auditRepo);
$authSrv    = new AuthService($userRepo);
$ruleResSrv = new RuleResolverService($ruleRepo);
$retroCalc  = new RetrocessionCalculatorService($receiptRepo, $retroRepo, $ruleResSrv);
$receiptSrv = new ReceiptService($receiptRepo, $retroCalc);
$paymentSrv = new PaymentService($payRepo, $retroRepo, $retroCalc);
$dashSrv    = new DashboardService($receiptRepo, $retroRepo, $payRepo);
$csvSrv     = new ExportCsvService();
$pdfSrv     = new ExportPdfService();

$dashboardCtrl   = new DashboardController($dashSrv);
$profileCtrl     = new ProfileController($userRepo, $authSrv, $auditSrv);
$cabinetCtrl     = new CabinetController($cabRepo, $auditSrv);
$relationshipCtrl= new RelationshipController($relRepo, $auditSrv);
$ruleCtrl        = new RetrocessionRuleController($ruleRepo, $auditSrv);
$receiptCtrl     = new ReceiptController($receiptSrv, $receiptRepo, $auditSrv);
$retroCtrl       = new RetrocessionController($retroRepo, $retroCalc, $auditSrv);
$paymentCtrl     = new PaymentController($paymentSrv, $payRepo, $retroRepo, $auditSrv);
$exportCtrl      = new ExportController($csvSrv, $pdfSrv, $receiptRepo, $payRepo, $retroRepo);

// Root
if ($method === 'GET' && $path === '/') {
    redirect(isAuthenticated() ? '/dashboard' : '/login');
    return true;
}

// Dashboard
if ($method === 'GET' && $path === '/dashboard') {
    $render('dashboard/index', $dashboardCtrl->index());
    return true;
}

// Profile
if ($method === 'GET' && $path === '/profile') {
    $render('profile/profile', $profileCtrl->show());
    return true;
}
if ($method === 'POST' && $path === '/profile/update') {
    $profileCtrl->update();
    return true;
}
if ($method === 'POST' && $path === '/profile/change-password') {
    $profileCtrl->changePassword();
    return true;
}

// Cabinets
if ($method === 'GET' && $path === '/cabinets') {
    $render('cabinets/index', ['cabinets' => $cabinetCtrl->index()['cabinets'] ?? []]);
    return true;
}
if ($method === 'GET' && $path === '/cabinets/show') {
    $id = (int)($_GET['id'] ?? 0);
    $render('cabinets/show', ['cabinet' => $cabinetCtrl->show($id)['cabinet'] ?? []]);
    return true;
}
if ($method === 'POST' && $path === '/cabinets/create') { $cabinetCtrl->create(); return true; }
if ($method === 'POST' && $path === '/cabinets/update') { $cabinetCtrl->update((int)($_POST['id'] ?? 0)); return true; }
if ($method === 'POST' && $path === '/cabinets/delete') { $cabinetCtrl->delete((int)($_POST['id'] ?? 0)); return true; }

// Relationships
if ($method === 'GET' && $path === '/relationships') {
    $render('relationships/index', $relationshipCtrl->index()); return true; }
if ($method === 'GET' && $path === '/relationships/show') {
    $render('relationships/edit', $relationshipCtrl->show((int)($_GET['id'] ?? 0))); return true; }
if ($method === 'POST' && $path === '/relationships/create') { $relationshipCtrl->create(); return true; }
if ($method === 'POST' && $path === '/relationships/update') { $relationshipCtrl->update((int)($_POST['id'] ?? 0)); return true; }
if ($method === 'POST' && $path === '/relationships/close') { $relationshipCtrl->close((int)($_POST['id'] ?? 0)); return true; }

// Rules
if ($method === 'GET' && $path === '/rules') { $render('rules/index', ['rules'=>$ruleCtrl->index()['rules']??[]]); return true; }
if ($method === 'GET' && $path === '/rules/show') { $render('rules/edit', $ruleCtrl->show((int)($_GET['id'] ?? 0))); return true; }
if ($method === 'POST' && $path === '/rules/create') { $ruleCtrl->create(); return true; }
if ($method === 'POST' && $path === '/rules/update') { $ruleCtrl->update((int)($_POST['id'] ?? 0)); return true; }
if ($method === 'POST' && $path === '/rules/delete') { $ruleCtrl->delete((int)($_POST['id'] ?? 0)); return true; }

// Receipts
if ($method === 'GET' && $path === '/receipts') { $render('receipts/index', $receiptCtrl->index()); return true; }
if ($method === 'GET' && $path === '/receipts/show') { $render('receipts/show', $receiptCtrl->show((int)($_GET['id'] ?? 0))); return true; }
if ($method === 'POST' && $path === '/receipts/create') { $receiptCtrl->create(); return true; }
if ($method === 'POST' && $path === '/receipts/update') { $receiptCtrl->update((int)($_POST['id'] ?? 0)); return true; }
if ($method === 'POST' && $path === '/receipts/delete') { $receiptCtrl->delete((int)($_POST['id'] ?? 0)); return true; }

// Retrocessions
if ($method === 'GET' && $path === '/retrocessions') { $render('retrocessions/index', $retroCtrl->index()); return true; }
if ($method === 'GET' && $path === '/retrocessions/show') { $render('retrocessions/show', $retroCtrl->show((int)($_GET['id'] ?? 0))); return true; }
if ($method === 'POST' && $path === '/retrocessions/recalculate') { $retroCtrl->recalculate((int)($_POST['receipt_id'] ?? 0)); return true; }

// Payments
if ($method === 'GET' && $path === '/payments') { $render('payments/index', $paymentCtrl->index()); return true; }
if ($method === 'GET' && $path === '/payments/show') { $render('payments/show', $paymentCtrl->show((int)($_GET['id'] ?? 0))); return true; }
if ($method === 'POST' && $path === '/payments/create') { $paymentCtrl->create(); return true; }
if ($method === 'POST' && $path === '/payments/update') { $paymentCtrl->update((int)($_POST['id'] ?? 0)); return true; }
if ($method === 'POST' && $path === '/payments/delete') { $paymentCtrl->delete((int)($_POST['id'] ?? 0)); return true; }

// History
if ($method === 'GET' && $path === '/history') { $render('history/index', []); return true; }

// Exports
if ($method === 'GET' && $path === '/exports') { $render('exports/index', []); return true; }
if ($method === 'POST' && $path === '/exports/receipts-csv') { $exportCtrl->exportReceiptsCsv(); return true; }
if ($method === 'POST' && $path === '/exports/payments-csv') { $exportCtrl->exportPaymentsCsv(); return true; }
if ($method === 'POST' && $path === '/exports/retrocessions-csv') { $exportCtrl->exportRetrocessionsCsv(); return true; }
if ($method === 'POST' && $path === '/exports/monthly-statement-pdf') { $exportCtrl->exportMonthlyStatementPdf(); return true; }

// No match
http_response_code(404);
include __DIR__ . '/../app/views/errors/404.view.php';
return true;
