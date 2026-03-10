<?php $pageTitle='Accès interdit'; include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/navbar.php'; ?>
<div class="container">
    <div class="card">
        <h1>403</h1>
        <p>Vous n'avez pas l'autorisation d'accéder à cette page.</p>
        <a class="btn btn-primary" href="<?= isAuthenticated() ? '/dashboard' : '/login'; ?>">Retour</a>
    </div>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
