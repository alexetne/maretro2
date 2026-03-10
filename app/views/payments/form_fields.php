<div class="form-group">
    <label>Rétrocession</label>
    <input type="number" name="retrocession_id" value="<?= htmlspecialchars($payment['retrocession_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
</div>
<div class="form-group">
    <label>Date</label>
    <input type="date" name="payment_date" value="<?= htmlspecialchars($payment['payment_date'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>" required>
</div>
<div class="form-group">
    <label>Montant</label>
    <input type="number" step="0.01" name="amount" value="<?= htmlspecialchars($payment['amount'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
</div>
<div class="form-group">
    <label>Méthode</label>
    <input type="text" name="payment_method" value="<?= htmlspecialchars($payment['payment_method'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
</div>
<div class="form-group">
    <label>Référence</label>
    <input type="text" name="reference" value="<?= htmlspecialchars($payment['reference'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
</div>
<div class="form-group">
    <label>Justificatif (optionnel)</label>
    <input type="file" name="proof">
</div>
<div class="form-group">
    <label>Commentaire</label>
    <textarea name="comment"><?= htmlspecialchars($payment['comment'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
</div>
