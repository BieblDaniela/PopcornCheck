<?php
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = htmlspecialchars(trim($_POST['email']));
    $passw = trim($_POST['passw']);

    if (!empty($email) && !empty($passw)) {
        
        require_once('db.php');
        try {

            //1. User aus der DB holen

            $stmt = $pdo->prepare("SELECT * FROM konto WHERE email = :email");
            $stmt->execute(['email' => $email]);

            $user = $stmt->fetch();
        } catch (PDOException $e) {
            $e->getMessage();
        }
        //2. Passwort überprüfen
        if ($user && password_verify($passw, $user['passwort'])) {

            if (password_needs_rehash($user['passwort'], PASSWORD_DEFAULT)) {
                $newHash = password_hash($passw, PASSWORD_DEFAULT);
                $updateStmt = $pdo->prepare("UPDATE konto SET passwort = :passwort WHERE kid = :kid");
                $updateStmt->execute([
                    'passwort' => $newHash,
                    'kid' => $user['kid']
                ]);
            }
            session_start();
            //Session setzen 
            session_regenerate_id(true);

            //Die Session mit Daten befüllen
            $_SESSION['kid'] = $user['kid'];

            $_SESSION['email'] = $user['email'];

            header("Location: filmListe.php");
            exit;
        }else {
        $message = 'Dieser Benutzer existiert nicht';
        }

    } else {
        $message = 'Die Daten wurden nicht übermittelt';
    }
}
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PopcornCheck - Login</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="centered-layout">
    <div class="card">
        <div class="logo-container">
            <img src="Logo.png" alt="PopcornCheck Logo" class="logo-img">
        </div>
        <h1>Login</h1>

        <?php if ($message): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form action="" method="post">
            <div class="form-group">
                <label for="email">E-Mail Adresse</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="name@example.com" required>
            </div>
            
            <div class="form-group">
                <label for="passw">Passwort</label>
                <input type="password" name="passw" id="passw" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" name="submit" class="btn btn-primary btn-block">Anmelden</button>
        </form>

        <div class="auth-footer">
            Noch kein Konto? <a href="Registrierung.php">Jetzt registrieren</a>
        </div>
    </div>
</body>

</html>