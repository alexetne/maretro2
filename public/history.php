<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/config/bootstrap.php';
requireAuth();

require_once __DIR__ . '/../app/repositories/AuditRepository.php';
require_once __DIR__ . '/../app/controllers/AuditController.php';

$repo = new AuditRepository(getPDO());
$ctrl = new AuditController($repo);
$history = $ctrl->index()['logs'] ?? [];
include __DIR__ . '/../app/views/history/index.view.php';
