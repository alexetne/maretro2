<?php $pageTitle = 'Inscription'; include __DIR__ . '/../layouts/header.php'; ?>
<div class="auth-container">
    <div class="auth-card">
        <h2>Créer un compte</h2>
        <?php include __DIR__ . '/../layouts/flash.php'; ?>
        <form method="POST" action="/register">
            <?= csrfInputField(); ?>
            <div class="form-group">
                <label for="first_name">Prénom</label>
                <input type="text" name="first_name" id="first_name" value="<?= htmlspecialchars($_POST['first_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="form-group">
                <label for="last_name">Nom</label>
                <input type="text" name="last_name" id="last_name" value="<?= htmlspecialchars($_POST['last_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirmation</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required>
            </div>
            <button type="submit" class="btn btn-primary">Créer</button>
        </form>
        <div class="auth-links">
            <a href="/login">Déjà inscrit ?</a>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
