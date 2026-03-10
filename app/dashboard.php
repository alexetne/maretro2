<?php
require_once __DIR__ . "/config/db.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: /login.php");
    exit();
}

$pdo = Database::getConnection();

// Totaux globaux
$totals = $pdo->query("
    SELECT
        COALESCE(SUM(CASE WHEN cp.payment_status = 'paid' THEN cp.amount_paid END), 0) AS paid_in,
        COALESCE(SUM(cs.royalty_amount_due), 0) AS royalties_due,
        COALESCE(SUM(c.total_amount), 0) AS consultations_amount,
        COUNT(DISTINCT c.id) AS consultations_count
    FROM consultations c
    LEFT JOIN consultation_payments cp ON cp.consultation_id = c.id
    LEFT JOIN consultation_settlements cs ON cs.consultation_id = c.id
")->fetch();

// Stats par cabinet
$offices = $pdo->query("
    SELECT
        mo.id,
        mo.name,
        mo.city,
        mo.phone,
        COUNT(DISTINCT c.id) AS consultations,
        COALESCE(SUM(CASE WHEN cp.payment_status = 'paid' THEN cp.amount_paid END), 0) AS paid_in
    FROM medical_offices mo
    LEFT JOIN consultations c ON c.medical_office_id = mo.id
    LEFT JOIN consultation_payments cp ON cp.consultation_id = c.id
    GROUP BY mo.id, mo.name, mo.city, mo.phone
    ORDER BY mo.name
")->fetchAll();

// Derniers encaissements
$recentPayments = $pdo->query("
    SELECT cp.payment_date, cp.amount_paid, pm.label AS method, c.consultation_date
    FROM consultation_payments cp
    JOIN payment_methods pm ON pm.id = cp.payment_method_id
    JOIN consultations c ON c.id = cp.consultation_id
    ORDER BY cp.payment_date DESC
    LIMIT 10
")->fetchAll();

// Derniers règlements
$recentSettlements = $pdo->query("
    SELECT cs.settled_at, cs.royalty_amount_due, cs.net_due_to_titulaire, cs.net_due_to_vacataire, mo.name AS office
    FROM consultation_settlements cs
    JOIN health_professional_offices hpo ON hpo.id = cs.health_professional_office_id
    JOIN medical_offices mo ON mo.id = hpo.medical_office_id
    ORDER BY cs.updated_at DESC
    LIMIT 10
")->fetchAll();

require_once __DIR__ . "/header.php";

function money($value) {
    return number_format((float)$value, 2, ',', ' ') . ' €';
}
?>

<h2>Tableau de bord</h2>

<section>
    <h3>Vue d'ensemble</h3>
    <div style="display:flex; gap:16px; flex-wrap:wrap;">
        <div style="flex:1; min-width:180px; padding:12px; background:#f1f5f9; border-radius:8px;">
            <strong>Encaissements</strong><br>
            <?php echo money($totals["paid_in"]); ?>
        </div>
        <div style="flex:1; min-width:180px; padding:12px; background:#f1f5f9; border-radius:8px;">
            <strong>Redevances dues</strong><br>
            <?php echo money($totals["royalties_due"]); ?>
        </div>
        <div style="flex:1; min-width:180px; padding:12px; background:#f1f5f9; border-radius:8px;">
            <strong>Chiffre consultations</strong><br>
            <?php echo money($totals["consultations_amount"]); ?>
        </div>
        <div style="flex:1; min-width:180px; padding:12px; background:#f1f5f9; border-radius:8px;">
            <strong>Consultations</strong><br>
            <?php echo (int)$totals["consultations_count"]; ?>
        </div>
    </div>
</section>

<section>
    <h3>Cabinets</h3>
    <table border="1" cellspacing="0" cellpadding="8" style="width:100%; border-collapse:collapse;">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Ville</th>
                <th>Téléphone</th>
                <th>Consultations</th>
                <th>Encaissements</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$offices): ?>
                <tr><td colspan="5" style="text-align:center;">Aucun cabinet</td></tr>
            <?php else: ?>
                <?php foreach ($offices as $office): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($office["name"]); ?></td>
                        <td><?php echo htmlspecialchars($office["city"]); ?></td>
                        <td><?php echo htmlspecialchars($office["phone"]); ?></td>
                        <td style="text-align:right;"><?php echo (int)$office["consultations"]; ?></td>
                        <td style="text-align:right;"><?php echo money($office["paid_in"]); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<section style="margin-top:24px;">
    <h3>Derniers encaissements</h3>
    <table border="1" cellspacing="0" cellpadding="8" style="width:100%; border-collapse:collapse;">
        <thead>
            <tr>
                <th>Date paiement</th>
                <th>Montant</th>
                <th>Méthode</th>
                <th>Date consultation</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$recentPayments): ?>
                <tr><td colspan="4" style="text-align:center;">Aucun paiement</td></tr>
            <?php else: ?>
                <?php foreach ($recentPayments as $p): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($p["payment_date"]); ?></td>
                        <td style="text-align:right;"><?php echo money($p["amount_paid"]); ?></td>
                        <td><?php echo htmlspecialchars($p["method"]); ?></td>
                        <td><?php echo htmlspecialchars($p["consultation_date"]); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<section style="margin-top:24px;">
    <h3>Derniers règlements</h3>
    <table border="1" cellspacing="0" cellpadding="8" style="width:100%; border-collapse:collapse;">
        <thead>
            <tr>
                <th>Date règlement</th>
                <th>Cabinet</th>
                <th>Redevance due</th>
                <th>Net titulaire</th>
                <th>Net vacataire</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$recentSettlements): ?>
                <tr><td colspan="5" style="text-align:center;">Aucun règlement</td></tr>
            <?php else: ?>
                <?php foreach ($recentSettlements as $s): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($s["settled_at"]); ?></td>
                        <td><?php echo htmlspecialchars($s["office"]); ?></td>
                        <td style="text-align:right;"><?php echo money($s["royalty_amount_due"]); ?></td>
                        <td style="text-align:right;"><?php echo money($s["net_due_to_titulaire"]); ?></td>
                        <td style="text-align:right;"><?php echo money($s["net_due_to_vacataire"]); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<?php require_once __DIR__ . "/footer.php"; ?>
