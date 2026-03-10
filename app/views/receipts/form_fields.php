<div class="form-group">
    <label>Praticien</label>
    <input type="number" name="practitioner_id" value="<?= htmlspecialchars($receipt['practitioner_id'] ?? ($currentUser['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required>
</div>
<div class="form-group">
    <label>Relation</label>
    <input type="number" name="relationship_id" value="<?= htmlspecialchars($receipt['relationship_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
</div>
<div class="form-group">
    <label>Cabinet</label>
    <input type="number" name="cabinet_id" value="<?= htmlspecialchars($receipt['cabinet_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
</div>
<div class="form-group">
    <label>Date</label>
    <input type="date" name="receipt_date" value="<?= htmlspecialchars($receipt['receipt_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
</div>
<div class="form-group">
    <label>Type d'acte</label>
    <input type="text" name="act_type" value="<?= htmlspecialchars($receipt['act_type'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
</div>
<div class="form-group">
    <label>Montant</label>
    <input type="number" step="0.01" name="amount" value="<?= htmlspecialchars($receipt['amount'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
</div>
<div class="form-group">
    <label>Commentaire</label>
    <textarea name="comment"><?= htmlspecialchars($receipt['comment'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
</div>
