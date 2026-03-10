<?php $pageTitle='Relations'; $actions=[['href'=>'/relationships/create','label'=>'Nouvelle relation']]; include __DIR__ . '/../layouts/app.php'; ?>
<div class="card">
    <table class="table">
        <thead><tr><th>Hébergé</th><th>Hébergeant</th><th>Cabinet</th><th>Début</th><th>Fin</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach (($relationships ?? []) as $rel): ?>
            <tr>
                <td><?= htmlspecialchars($rel['hosted_name'] ?? $rel['hosted_practitioner_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($rel['hosting_name'] ?? $rel['hosting_practitioner_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($rel['cabinet_name'] ?? $rel['cabinet_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($rel['start_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($rel['end_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                    <a class="btn" href="/relationships/<?= (int)$rel['id']; ?>/edit">Éditer</a>
                    <form method="POST" action="/relationships/<?= (int)$rel['id']; ?>/close" class="inline">
                        <?= csrfInputField(); ?>
                        <button class="btn btn-danger" type="submit">Clore</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../layouts/app_end.php'; ?>
