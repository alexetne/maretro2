<div class="topbar">
    <div>
        <h1 class="page-title"><?= htmlspecialchars($pageTitle ?? '', ENT_QUOTES, 'UTF-8'); ?></h1>
        <?php if (!empty($pageSubtitle)): ?>
            <p class="page-subtitle"><?= htmlspecialchars($pageSubtitle, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
    </div>
    <div class="topbar-actions">
        <?php if (!empty($actions) && is_array($actions)): ?>
            <?php foreach ($actions as $action): ?>
                <a class="btn btn-primary" href="<?= htmlspecialchars($action['href'] ?? '#', ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($action['label'] ?? '', ENT_QUOTES, 'UTF-8'); ?></a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
