<?php $pageTitle = 'Connexion'; include __DIR__ . '/../layouts/header.php'; ?>
<div class="auth-container">
    <div class="auth-card">
        <h2>Connexion</h2>
        <?php include __DIR__ . '/../layouts/flash.php'; ?>
        <form method="POST" action="/login">
            <?= csrfInputField(); ?>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-group inline">
                <label><input type="checkbox" name="remember" value="1"> Se souvenir de moi</label>
            </div>
            <button type="submit" class="btn btn-primary">Se connecter</button>
        </form>
        <div class="auth-links">
            <a href="/forgot-password">Mot de passe oublié ?</a>
            <a href="/register">Créer un compte</a>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
