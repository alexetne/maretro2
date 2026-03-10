<?php $pageTitle='Modifier relation'; include __DIR__ . '/../layouts/app.php'; ?>
<div class="card">
    <form method="POST" action="/relationships/<?= (int)($relationship['id'] ?? 0); ?>">
        <?= csrfInputField(); ?>
        <?php include __DIR__ . '/form_fields.php'; ?>
        <button class="btn btn-primary" type="submit">Mettre à jour</button>
    </form>
</div>
<?php include __DIR__ . '/../layouts/app_end.php'; ?>
