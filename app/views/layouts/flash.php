<?php if (!empty($_SESSION['flash'])): ?>
    <div class="flash-container">
        <?php foreach ($_SESSION['flash'] as $type => $messages): ?>
            <?php foreach ((array)$messages as $message): ?>
                <div class="alert alert-<?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?>">
                    <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
        <?php unset($_SESSION['flash']); ?>
    </div>
<?php endif; ?>
