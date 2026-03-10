<?php $pageTitle='Nouveau paiement'; include __DIR__ . '/../layouts/app.php'; ?>
<div class="card">
    <form method="POST" action="/payments" enctype="multipart/form-data">
        <?= csrfInputField(); ?>
        <?php include __DIR__ . '/form_fields.php'; ?>
        <button class="btn btn-primary" type="submit">Enregistrer</button>
    </form>
</div>
<?php include __DIR__ . '/../layouts/app_end.php'; ?>
