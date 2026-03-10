<?php $pageTitle = 'Profil'; include __DIR__ . '/../layouts/app.php'; ?>
<div class="grid two-cols">
    <div class="card">
        <h3>Informations personnelles</h3>
        <form method="POST" action="/profile">
            <?= csrfInputField(); ?>
            <div class="form-group">
                <label>Prénom</label>
                <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label>Nom</label>
                <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-group">
                <label>Téléphone</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <button class="btn btn-primary" type="submit">Mettre à jour</button>
        </form>
    </div>
    <div class="card">
        <h3>Changer le mot de passe</h3>
        <form method="POST" action="/profile/password">
            <?= csrfInputField(); ?>
            <div class="form-group">
                <label>Mot de passe actuel</label>
                <input type="password" name="current_password" required>
            </div>
            <div class="form-group">
                <label>Nouveau mot de passe</label>
                <input type="password" name="new_password" required>
            </div>
            <div class="form-group">
                <label>Confirmation</label>
                <input type="password" name="new_password_confirmation" required>
            </div>
            <button class="btn btn-secondary" type="submit">Changer</button>
        </form>
    </div>
</div>
<?php include __DIR__ . '/../layouts/app_end.php'; ?>
