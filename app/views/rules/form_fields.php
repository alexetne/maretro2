<div class="form-group">
    <label>Relation</label>
    <input type="number" name="relationship_id" value="<?= htmlspecialchars($rule['relationship_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
</div>
<div class="form-group">
    <label>Type de règle</label>
    <select name="rule_type" required>
        <?php $type = $rule['rule_type'] ?? ''; ?>
        <option value="percentage" <?= $type==='percentage'?'selected':''; ?>>Pourcentage</option>
        <option value="fixed_amount" <?= $type==='fixed_amount'?'selected':''; ?>>Montant fixe</option>
    </select>
</div>
<div class="form-group">
    <label>Valeur</label>
    <input type="number" step="0.01" name="value" value="<?= htmlspecialchars($rule['value'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
</div>
<div class="form-group">
    <label>Acte (optionnel)</label>
    <input type="text" name="act_type" value="<?= htmlspecialchars($rule['act_type'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
</div>
<div class="form-group">
    <label>Applique à partir du</label>
    <input type="date" name="applies_from" value="<?= htmlspecialchars($rule['applies_from'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
</div>
<div class="form-group">
    <label>Jusqu'au</label>
    <input type="date" name="applies_to" value="<?= htmlspecialchars($rule['applies_to'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
</div>
