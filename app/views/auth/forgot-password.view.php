<?php $pageTitle = 'Mot de passe oublié'; include __DIR__ . '/../layouts/header.php'; ?>
<div class="auth-container">
    <div class="auth-card">
        <h2>Réinitialiser le mot de passe</h2>
        <p>Entrez votre email pour recevoir un lien de réinitialisation.</p>
        <?php include __DIR__ . '/../layouts/flash.php'; ?>
        <form method="POST" action="/forgot-password">
            <?= csrfInputField(); ?>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Envoyer</button>
        </form>
        <div class="auth-links">
            <a href="/login">Retour à la connexion</a>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
