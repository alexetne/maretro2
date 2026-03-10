<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/config.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?php echo $_ENV['APP_NAME']; ?></title>

<link rel="stylesheet" href="/assets/css/style.css">

</head>

<body>

<header>

<h1><?php echo $_ENV['APP_NAME']; ?></h1>

<nav>
<a href="/">Accueil</a>
<a href="/login.php">Connexion</a>
<a href="/register.php">Inscription</a>
</nav>

</header>

<main>
