<?php $pageTitle='Utilisateurs'; include __DIR__ . '/../layouts/app.php'; ?>
<div class="card">
    <table class="table">
        <thead><tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Actif</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach (($users ?? []) as $u): ?>
            <tr>
                <td><?= htmlspecialchars(($u['name'] ?? ($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($u['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($u['role'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= !empty($u['is_active']) ? 'Oui' : 'Non'; ?></td>
                <td>
                    <a class="btn" href="/admin/users/<?= (int)$u['id']; ?>">Voir</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../layouts/app_end.php'; ?>
