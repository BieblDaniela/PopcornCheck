
<?php
    $message = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
       
        $email = htmlspecialchars(trim($_POST['email']));
        $passw = (trim($_POST['passw']));
        $passw2 = (trim($_POST['passw2']));

        if (!empty($email) && !empty($passw) && !empty($passw2) ) {
            if ($passw == $passw2) {
                $passwHash = password_hash($passw, PASSWORD_DEFAULT);  
                
                //Speichern in die DB
                require_once('db.php');
                try {
                    $stmt = $pdo->prepare("INSERT INTO konto (email, passwort) VALUES (:email, :passwort) ");

                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':passwort', $passwHash);

                    $stmt->execute();

                   

                    header("location: Login.php");
                
                    } catch(PDOException $e){
                    if ($e->getCode() == 23000) { //Code für Duplicated Entry
                        $message = "Daten sind bereits im System";
                          die("Daten sind bereits im System");
                    }
                    else {
                        $e->getMessage();
                        die("FEHLER beim Speichern der Daten in der Datenbank");
                    }
                }
            }else{
                $message = 'Die Passwörter stimmmen nicht überein';
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
    <title>PopcornCheck - Registrierung</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="centered-layout">
    <div class="card">
        <div class="logo-container">
            <img src="Logo.png" alt="PopcornCheck Logo" class="logo-img">
        </div>
        <h1>Registrierung</h1>

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

            <div class="form-group">
                <label for="passw2">Passwort wiederholen</label>
                <input type="password" name="passw2" id="passw2" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" name="submit" class="btn btn-primary btn-block">Registrieren</button>
        </form>

        <div class="auth-footer">
            Bereits registriert? <a href="Login.php">Jetzt anmelden</a>
        </div>
    </div>
</body>

</html>