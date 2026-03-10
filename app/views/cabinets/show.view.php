<?php $pageTitle = 'Cabinet'; include __DIR__ . '/../layouts/app.php'; ?>
<div class="card">
    <h3><?= htmlspecialchars($cabinet['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h3>
    <p><?= htmlspecialchars($cabinet['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
    <p><?= htmlspecialchars($cabinet['city'] ?? '', ENT_QUOTES, 'UTF-8'); ?> <?= htmlspecialchars($cabinet['postal_code'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
    <p><?= htmlspecialchars($cabinet['country'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
    <p>Tél: <?= htmlspecialchars($cabinet['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
    <p>Email: <?= htmlspecialchars($cabinet['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
</div>
<?php if (!empty($practitioners)): ?>
<div class="card">
    <h4>Praticiens</h4>
    <ul>
        <?php foreach ($practitioners as $p): ?>
            <li><?= htmlspecialchars($p['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>
<?php include __DIR__ . '/../layouts/app_end.php'; ?>
