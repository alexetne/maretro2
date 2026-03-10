<?php $pageTitle = 'Modifier cabinet'; include __DIR__ . '/../layouts/app.php'; ?>
<div class="card">
    <form method="POST" action="/cabinets/<?= (int)($cabinet['id'] ?? 0); ?>">
        <?= csrfInputField(); ?>
        <?php include __DIR__ . '/partials/form_fields.php'; ?>
        <button class="btn btn-primary" type="submit">Mettre à jour</button>
    </form>
</div>
<?php include __DIR__ . '/../layouts/app_end.php'; ?>
