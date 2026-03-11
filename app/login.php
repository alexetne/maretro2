<?php

require_once __DIR__ . "/config/db.php";

session_start();

$pdo = Database::getConnection();

$error = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $ip = $_SERVER["REMOTE_ADDR"] ?? null;
    $userAgent = $_SERVER["HTTP_USER_AGENT"] ?? null;

    if (!$email || !$password) {
        $error = "Veuillez remplir tous les champs.";
    } else {

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(["email" => $email]);

        $user = $stmt->fetch();

        if ($user && password_verify($password, $user["password_hash"])) {

            if ($user["status"] !== "active") {
                $error = "Compte désactivé.";
            } else {

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["role"] = $user["role"];

                $stmt = $pdo->prepare("
                    INSERT INTO login_attempts
                    (user_id, email_attempted, ip_address, user_agent, success)
                    VALUES (:user_id, :email, :ip, :ua, 1)
                ");

                $stmt->execute([
                    "user_id" => $user["id"],
                    "email" => $email,
                    "ip" => $ip,
                    "ua" => $userAgent
                ]);

                $stmt = $pdo->prepare("
                    UPDATE users
                    SET last_login_at = NOW()
                    WHERE id = :id
                ");

                $stmt->execute(["id" => $user["id"]]);

                // Redirection vers l'onboarding si non terminé
                $initStmt = $pdo->prepare("SELECT status FROM user_init_configs WHERE user_id = :id");
                $initStmt->execute(["id" => $user["id"]]);
                $initStatus = $initStmt->fetchColumn();

                if ($initStatus === false || $initStatus !== "completed") {
                    header("Location: /startup/index.php");
                } else {
                    header("Location: /dashboard.php");
                }
                exit();
            }

        } else {

            $stmt = $pdo->prepare("
                INSERT INTO login_attempts
                (email_attempted, ip_address, user_agent, success, failure_reason)
                VALUES (:email, :ip, :ua, 0, 'invalid_credentials')
            ");

            $stmt->execute([
                "email" => $email,
                "ip" => $ip,
                "ua" => $userAgent
            ]);

            $error = "Email ou mot de passe incorrect.";
        }
    }
}

require_once __DIR__ . "/header.php";
?>

<h2>Connexion</h2>

<?php if ($error): ?>

<p style="color:red;">
<?php echo htmlspecialchars($error); ?>
</p>

<?php endif; ?>

<form method="POST">

<label>Email</label>
<br>
<input type="email" name="email" required>

<br><br>

<label>Mot de passe</label>
<br>
<input type="password" name="password" required>

<br><br>

<button type="submit">Se connecter</button>

</form>

<p>
<a href="/forgot-password.php">Mot de passe oublié</a>
</p>

<?php require_once __DIR__ . "/footer.php"; ?>
