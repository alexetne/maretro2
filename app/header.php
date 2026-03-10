<?php
// Common page header; expects optional $pageTitle
$pageTitle = $pageTitle ?? 'Rétrocessions';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="/app/style.css">
</head>
<body>
<header class="site-header">
    <div class="brand">
        <span class="brand-mark">R</span>
        <div class="brand-text">
            <span class="brand-title">Rétro</span>
            <span class="brand-subtitle">Gestion</span>
        </div>
    </div>
    <nav class="main-nav">
        <a href="/index.php">Tableau de bord</a>
        <a href="/encaissements.php">Encaissements</a>
        <a href="/retrocessions.php">Rétrocessions</a>
        <a href="/paiements.php">Paiements</a>
        <a href="/users.php">Utilisateurs</a>
    </nav>
</header>
<main class="page">
