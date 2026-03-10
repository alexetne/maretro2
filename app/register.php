<?php

require_once __DIR__ . "/config/db.php";

session_start();

$pdo = Database::getConnection();
$error = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $email    = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm  = $_POST["confirm_password"] ?? "";

    if (!$username || !$email || !$password || !$confirm) {
        $error = "Veuillez remplir tous les champs.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email invalide.";
    } elseif (strlen($password) < 8) {
        $error = "Le mot de passe doit contenir au moins 8 caractères.";
    } elseif ($password !== $confirm) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("
                INSERT INTO users (username, email, password_hash, role, status)
                VALUES (:username, :email, :password_hash, 'user', 'active')
            ");

            $stmt->execute([
                "username"      => $username,
                "email"         => $email,
                "password_hash" => $hash,
            ]);

            header("Location: /login.php?registered=1");
            exit();
        } catch (PDOException $e) {
            // 1062 = duplicate entry
            if (($e->errorInfo[1] ?? null) === 1062) {
                $error = "Nom d'utilisateur ou email déjà utilisé.";
            } else {
                throw $e;
            }
        }
    }
}

require_once __DIR__ . "/header.php";
?>

<h2>Inscription</h2>

<?php if ($error): ?>
<p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<form method="POST">
    <label>Pseudo</label><br>
    <input type="text" name="username" value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
    <br><br>

    <label>Email</label><br>
    <input type="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
    <br><br>

    <label>Mot de passe</label><br>
    <input type="password" name="password" required>
    <br><br>

    <label>Confirmer le mot de passe</label><br>
    <input type="password" name="confirm_password" required>
    <br><br>

    <button type="submit">S'inscrire</button>
</form>

<p>Déjà un compte ? <a href="/login.php">Se connecter</a></p>

<?php require_once __DIR__ . "/footer.php"; ?>
