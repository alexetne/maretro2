<div class="form-group">
    <label>Nom</label>
    <input type="text" name="name" value="<?= htmlspecialchars($cabinet['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
</div>
<div class="form-group">
    <label>Adresse</label>
    <input type="text" name="address" value="<?= htmlspecialchars($cabinet['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
</div>
<div class="form-group">
    <label>Ville</label>
    <input type="text" name="city" value="<?= htmlspecialchars($cabinet['city'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
</div>
<div class="form-group">
    <label>Code postal</label>
    <input type="text" name="postal_code" value="<?= htmlspecialchars($cabinet['postal_code'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
</div>
<div class="form-group">
    <label>Pays</label>
    <input type="text" name="country" value="<?= htmlspecialchars($cabinet['country'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
</div>
<div class="form-group">
    <label>Téléphone</label>
    <input type="text" name="phone" value="<?= htmlspecialchars($cabinet['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
</div>
<div class="form-group">
    <label>Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($cabinet['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
</div>
