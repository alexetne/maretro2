<?php $pageTitle = 'Nouveau mot de passe'; include __DIR__ . '/../layouts/header.php'; ?>
<div class="auth-container">
    <div class="auth-card">
        <h2>Nouveau mot de passe</h2>
        <?php include __DIR__ . '/../layouts/flash.php'; ?>
        <form method="POST" action="/reset-password">
            <?= csrfInputField(); ?>
            <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? ($_GET['token'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirmation</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required>
            </div>
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </form>
    </div>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
