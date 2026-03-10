<?php
declare(strict_types=1);

// Role helper functions

function isAdmin(): bool
{
    $u = user();
    return isset($u['role']) && $u['role'] === 'admin';
}

function isHostingPractitioner(): bool
{
    $u = user();
    return isset($u['role']) && $u['role'] === 'hosting_practitioner';
}

function isHostedPractitioner(): bool
{
    $u = user();
    return isset($u['role']) && $u['role'] === 'hosted_practitioner';
}
