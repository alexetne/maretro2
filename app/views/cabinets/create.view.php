<?php $pageTitle = 'Nouveau cabinet'; include __DIR__ . '/../layouts/app.php'; ?>
<div class="card">
    <form method="POST" action="/cabinets">
        <?= csrfInputField(); ?>
        <?php include __DIR__ . '/partials/form_fields.php'; ?>
        <button class="btn btn-primary" type="submit">Enregistrer</button>
    </form>
</div>
<?php include __DIR__ . '/../layouts/app_end.php'; ?>
