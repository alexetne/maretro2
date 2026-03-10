<?php $pageTitle='Règles de rétrocession'; $actions=[['href'=>'/rules/create','label'=>'Nouvelle règle']]; include __DIR__ . '/../layouts/app.php'; ?>
<div class="card">
    <table class="table">
        <thead><tr><th>Relation</th><th>Type</th><th>Valeur</th><th>Du</th><th>Au</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach (($rules ?? []) as $rule): ?>
            <tr>
                <td><?= htmlspecialchars($rule['relationship_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><span class="badge"><?= htmlspecialchars($rule['rule_type'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span></td>
                <td><?= htmlspecialchars($rule['value'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($rule['applies_from'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($rule['applies_to'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                    <a class="btn" href="/rules/<?= (int)$rule['id']; ?>/edit">Éditer</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../layouts/app_end.php'; ?>
