<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/config/bootstrap.php';

// Redirect based on auth state
if (isAuthenticated()) {
    redirect('/dashboard.php');
} else {
    redirect('/login.php');
}
