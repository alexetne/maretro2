<div class="form-group">
    <label>Cabinet</label>
    <input type="number" name="cabinet_id" value="<?= htmlspecialchars($relationship['cabinet_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
</div>
<div class="form-group">
    <label>Praticien hébergé</label>
    <input type="number" name="hosted_practitioner_id" value="<?= htmlspecialchars($relationship['hosted_practitioner_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
</div>
<div class="form-group">
    <label>Praticien hébergeant</label>
    <input type="number" name="hosting_practitioner_id" value="<?= htmlspecialchars($relationship['hosting_practitioner_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
</div>
<div class="form-group">
    <label>Date de début</label>
    <input type="date" name="start_date" value="<?= htmlspecialchars($relationship['start_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
</div>
<div class="form-group">
    <label>Date de fin</label>
    <input type="date" name="end_date" value="<?= htmlspecialchars($relationship['end_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
</div>
<div class="form-group">
    <label>Notes</label>
    <textarea name="notes"><?= htmlspecialchars($relationship['notes'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
</div>
