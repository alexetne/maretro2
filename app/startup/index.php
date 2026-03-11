<?php
require_once __DIR__ . "/../config/db.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: /login.php");
    exit();
}

$pdo = Database::getConnection();
$userId = (int) $_SESSION["user_id"];

$stepDefinitions = [
    ["profile_setup", "Compléter mon profil"],
    ["add_cabinet", "Créer mon cabinet"],
    ["add_payment", "Configurer les méthodes de paiement"],
    ["first_patient", "Ajouter un patient"],
    ["first_consultation", "Enregistrer une première consultation"],
];

function ensureInitConfig(PDO $pdo, int $userId, array $definitions): array
{
    $configStmt = $pdo->prepare("SELECT * FROM user_init_configs WHERE user_id = :uid");
    $configStmt->execute(["uid" => $userId]);
    $config = $configStmt->fetch();

    $totalSteps = count($definitions);
    $now = date('Y-m-d H:i:s');

    if (!$config) {
        $insert = $pdo->prepare("
            INSERT INTO user_init_configs
            (user_id, status, current_step, total_steps, started_at, last_seen_at)
            VALUES (:uid, 'in_progress', :current_step, :total_steps, :started_at, :last_seen_at)
        ");
        $insert->execute([
            "uid" => $userId,
            "current_step" => $definitions[0][0],
            "total_steps" => $totalSteps,
            "started_at" => $now,
            "last_seen_at" => $now,
        ]);

        $configId = (int) $pdo->lastInsertId();

        $stepInsert = $pdo->prepare("
            INSERT INTO user_init_config_steps (user_init_config_id, step_key, step_label, step_order)
            VALUES (:cid, :skey, :slabel, :sorder)
        ");
        foreach ($definitions as $index => [$key, $label]) {
            $stepInsert->execute([
                "cid" => $configId,
                "skey" => $key,
                "slabel" => $label,
                "sorder" => $index + 1,
            ]);
        }

        $configStmt->execute(["uid" => $userId]);
        $config = $configStmt->fetch();
    } else {
        // refresh totals and last_seen
        $update = $pdo->prepare("
            UPDATE user_init_configs
            SET total_steps = :total_steps, last_seen_at = :seen
            WHERE id = :id
        ");
        $update->execute([
            "total_steps" => $totalSteps,
            "seen" => $now,
            "id" => $config["id"],
        ]);
    }

    return $config;
}

$config = ensureInitConfig($pdo, $userId, $stepDefinitions);
$configId = (int) $config["id"];

// Actions
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? null;
    $stepKey = $_POST["step_key"] ?? null;

    if ($action === "complete_step" && $stepKey) {
        $pdo->prepare("
            UPDATE user_init_config_steps
            SET status = 'completed', completed_at = NOW()
            WHERE user_init_config_id = :cid AND step_key = :skey
        ")->execute(["cid" => $configId, "skey" => $stepKey]);
    }

    if ($action === "skip") {
        $pdo->prepare("
            UPDATE user_init_configs
            SET status = 'skipped', completed_at = NOW()
            WHERE id = :cid
        ")->execute(["cid" => $configId]);
        header("Location: /dashboard.php");
        exit();
    }

    // Recalculate progression
    $stepsData = $pdo->prepare("
        SELECT status, step_key
        FROM user_init_config_steps
        WHERE user_init_config_id = :cid
        ORDER BY step_order
    ");
    $stepsData->execute(["cid" => $configId]);
    $stepsData = $stepsData->fetchAll();

    $completed = 0;
    $nextStep = null;
    foreach ($stepsData as $row) {
        if ($row["status"] === "completed") {
            $completed++;
        } elseif ($nextStep === null) {
            $nextStep = $row["step_key"];
        }
    }

    $progress = $config["total_steps"] > 0 ? round(($completed / $config["total_steps"]) * 100, 2) : 0;
    $newStatus = ($completed >= $config["total_steps"]) ? "completed" : "in_progress";

    $pdo->prepare("
        UPDATE user_init_configs
        SET completed_steps = :done,
            progress_percent = :progress,
            status = :status,
            current_step = :current_step,
            completed_at = CASE WHEN :status = 'completed' THEN NOW() ELSE completed_at END
        WHERE id = :cid
    ")->execute([
        "done" => $completed,
        "progress" => $progress,
        "status" => $newStatus,
        "current_step" => $nextStep,
        "cid" => $configId,
    ]);

    if ($newStatus === "completed") {
        header("Location: /dashboard.php");
        exit();
    }

    header("Location: /startup/index.php");
    exit();
}

// Fetch updated data for display
$steps = $pdo->prepare("
    SELECT step_key, step_label, step_order, status, completed_at
    FROM user_init_config_steps
    WHERE user_init_config_id = :cid
    ORDER BY step_order
");
$steps->execute(["cid" => $configId]);
$steps = $steps->fetchAll();

// refresh config
$configStmt = $pdo->prepare("SELECT * FROM user_init_configs WHERE id = :id");
$configStmt->execute(["id" => $configId]);
$config = $configStmt->fetch();

require_once __DIR__ . "/../header.php";
?>

<h2>Configuration initiale</h2>
<p>Progression : <?php echo $config["progress_percent"]; ?> % (<?php echo $config["completed_steps"]; ?>/<?php echo $config["total_steps"]; ?>)</p>

<?php if ($config["status"] === "completed"): ?>
    <p>Onboarding terminé ! <a href="/dashboard.php">Accéder au tableau de bord</a></p>
<?php else: ?>
    <form method="POST" style="margin-bottom:16px;">
        <input type="hidden" name="action" value="skip">
        <button type="submit">Passer la configuration</button>
    </form>

    <table border="1" cellspacing="0" cellpadding="8" style="width:100%; border-collapse:collapse;">
        <thead>
            <tr>
                <th>Étape</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($steps as $step): ?>
                <tr>
                    <td><?php echo htmlspecialchars($step["step_label"]); ?></td>
                    <td><?php echo htmlspecialchars($step["status"]); ?></td>
                    <td>
                        <?php if ($step["status"] !== "completed"): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="complete_step">
                                <input type="hidden" name="step_key" value="<?php echo htmlspecialchars($step["step_key"]); ?>">
                                <button type="submit">Marquer comme fait</button>
                            </form>
                        <?php else: ?>
                            Complété le <?php echo htmlspecialchars($step["completed_at"]); ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once __DIR__ . "/../footer.php"; ?>
