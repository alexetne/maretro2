<?php $pageTitle = 'Dashboard'; include __DIR__ . '/../layouts/app.php'; ?>
<div class="grid kpi-grid">
    <div class="card"><p>Total encaissements</p><h3><?= htmlspecialchars($stats['total_receipts'] ?? '0', ENT_QUOTES, 'UTF-8'); ?> €</h3></div>
    <div class="card"><p>Rétrocessions dues</p><h3><?= htmlspecialchars($stats['total_retrocessions_due'] ?? '0', ENT_QUOTES, 'UTF-8'); ?> €</h3></div>
    <div class="card"><p>Payé</p><h3><?= htmlspecialchars($stats['total_paid'] ?? '0', ENT_QUOTES, 'UTF-8'); ?> €</h3></div>
    <div class="card"><p>Restant à payer</p><h3><?= htmlspecialchars($stats['remaining_to_pay'] ?? '0', ENT_QUOTES, 'UTF-8'); ?> €</h3></div>
</div>
<form class="card filter-form" method="GET" action="/dashboard">
    <div class="form-group inline">
        <label>Du</label><input type="date" name="start" value="<?= htmlspecialchars($_GET['start'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <label>Au</label><input type="date" name="end" value="<?= htmlspecialchars($_GET['end'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <button class="btn btn-secondary" type="submit">Filtrer</button>
    </div>
</form>
<?php include __DIR__ . '/../layouts/app_end.php'; ?>
