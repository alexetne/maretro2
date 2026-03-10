<?php $pageTitle='Admin'; include __DIR__ . '/../layouts/app.php'; ?>
<div class="grid kpi-grid">
    <div class="card"><p>Utilisateurs</p><h3><?= htmlspecialchars($stats['users'] ?? '0', ENT_QUOTES, 'UTF-8'); ?></h3></div>
    <div class="card"><p>Cabinets</p><h3><?= htmlspecialchars($stats['cabinets'] ?? '0', ENT_QUOTES, 'UTF-8'); ?></h3></div>
    <div class="card"><p>Logs</p><h3><?= htmlspecialchars($stats['logs'] ?? '0', ENT_QUOTES, 'UTF-8'); ?></h3></div>
</div>
<div class="card">
    <a class="btn" href="/admin/users">Gérer utilisateurs</a>
    <a class="btn" href="/admin/cabinets">Gérer cabinets</a>
    <a class="btn" href="/admin/logs">Voir logs</a>
</div>
<?php include __DIR__ . '/../layouts/app_end.php'; ?>
