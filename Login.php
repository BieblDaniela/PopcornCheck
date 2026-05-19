<?php
session_start();
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
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
    <title>Login</title>
</head>

<body>
    <h1>Login</h1>
    <form action="" method="post">

        <label for="email">Email:</label>
        <input type="text" name="email" id="email">
        <br><br>
        <label for="passw">Passwort:</label>
        <input type="password" name="passw" id="passw">

        <br><br>
        <input type="submit" value="Login" name="submit">
    </form>
    <?php if ($message): ?>
        <p>
            <?= $message ?>
        </p>
    <?php endif; ?>
</body>

</html>