<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/config/bootstrap.php';
http_response_code(403);
include __DIR__ . '/../app/views/errors/403.view.php';
