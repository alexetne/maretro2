<?php
// Page d'accueil du tableau de bord
$pageTitle = 'Tableau de bord';
require __DIR__ . '/app/header.php';

// Données fictives pour le rendu initial (à remplacer par des requêtes MySQL)
$kpis = [
    ['label' => 'Encaissements du mois', 'value' => '12 500 €'],
    ['label' => 'Rétrocessions dues', 'value' => '3 125 €'],
    ['label' => 'Déjà réglé', 'value' => '2 000 €'],
    ['label' => 'Reste à payer', 'value' => '1 125 €'],
];

$encaissements = [
    ['date' => '2026-03-02', 'praticien' => 'Dr. Martin', 'acte' => 'Consultation', 'montant' => '120 €'],
    ['date' => '2026-03-05', 'praticien' => 'Dr. Leroy', 'acte' => 'Kinésithérapie', 'montant' => '75 €'],
    ['date' => '2026-03-07', 'praticien' => 'Dr. Martin', 'acte' => 'Consultation', 'montant' => '120 €'],
    ['date' => '2026-03-08', 'praticien' => 'Dr. Perez', 'acte' => 'Orthophonie', 'montant' => '90 €'],
];
?>
<section class="card">
    <h1 class="section-title">Vue synthétique</h1>
    <div class="form-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
        <?php foreach ($kpis as $kpi): ?>
            <div class="card" style="padding: 16px 18px; background: #0b1224;">
                <p class="label" style="margin: 0 0 6px;"><?php echo htmlspecialchars($kpi['label']); ?></p>
                <p style="font-size: 22px; font-weight: 800; margin: 0;"><?php echo htmlspecialchars($kpi['value']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="card" style="margin-top: 18px;">
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px;">
        <h2 class="section-title" style="margin: 0;">Derniers encaissements</h2>
        <a class="button" href="/add_encaissement.php">+ Ajouter</a>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Praticien</th>
                <th>Acte</th>
                <th style="text-align:right;">Montant</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($encaissements as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['date']); ?></td>
                    <td><?php echo htmlspecialchars($row['praticien']); ?></td>
                    <td><?php echo htmlspecialchars($row['acte']); ?></td>
                    <td style="text-align:right; font-weight:700;"><?php echo htmlspecialchars($row['montant']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<section class="card" style="margin-top: 18px; display: grid; gap: 12px; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));">
    <div>
        <h3 class="section-title" style="margin-top: 0;">Actions rapides</h3>
        <div style="display: flex; flex-direction: column; gap: 10px;">
            <a class="button" href="/encaissements.php">Voir tous les encaissements</a>
            <a class="button" href="/retrocessions.php">Calculer les rétrocessions</a>
            <a class="button" href="/paiements.php">Enregistrer un paiement</a>
        </div>
    </div>
    <div>
        <h3 class="section-title" style="margin-top: 0;">Rappels</h3>
        <ul style="color: var(--muted); line-height: 1.6; padding-left: 18px; margin: 0;">
            <li>Vérifier les encaissements du mois courant.</li>
            <li>Mettre à jour les règles de rétrocession si besoin.</li>
            <li>Exporter le relevé PDF pour les praticiens hébergés.</li>
        </ul>
    </div>
</section>

<?php require __DIR__ . '/app/footer.php'; ?>
