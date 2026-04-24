<?php
    //session_start();
    $message = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  
        $email = htmlspecialchars(trim($_POST['email']));
        $passw = htmlspecialchars(trim($_POST['passw']));
       
        if (!empty($email) && !empty($passw)) {
           
                    //Speichern in die DB
                    require_once('db.php');
                    try {

                    //1. User aus der DB holen
            require_once('db.php');
            $stmt = $pdo->prepare("SELECT bid, vname, email, passw FROM benutzer WHERE email = :email");
            
            //$stmt->bindParam(":email,$email)
            //$stmt->execute();

            $stmt->execute(['email' => $email]);
            //Da das SELECT Daten zurückliefert,müssen wir diese Daten in einem Array entgegennehmen.
            $user = $stmt->fetch(); //Der gwünschte Datensatz des "eingeloggten" Users wird zurückgegeben.

            //2. Passwort überprüfen
            if ($user && password_verify($passw, $user['passw'])) {
                //Password_verify gibt ein true/false zurück
                //User darf sich einloggen - Passwort und Email stimmen

                //SICHERHEITS-UPDATE
                //Prüfen, ob der Hash veraltet ist (wenn ja, erneuern und in der DB speichern)
                if (password_needs_rehash($user['passw'], PASSWORD_DEFAULT)) {
                    //Neuen Hash generieren
                    $newHash = password_hash($passw, PASSWORD_DEFAULT);
                    //Neuen Hash in der DB speicher
                    $updateStmt = $pdo->prepare("UPDATE benutzer SET passw = :passw WHERE bid = :bid");
                    $updateStmt->execute([
                        'passw' => $newHash,
                        'bid' => $user['bid']
                    ]);
                }
                    //Session setzen (Schutz vor Session Fixation)
                    session_regenerate_id(true);

                    //Die Session mit Daten befüllen
                    $_SESSION['bid'] = $user['bid'];
                    $_SESSION['vname'] = $user['vname'];
                    $_SESSION['email'] = $user['email'];
                    setcookie("vname",$_SESSION['vname'], time()+660*60*24*14);

                    header("location: startseite.php");
                    $message = "Erfolgreich eingeloggt! Hallo " . htmlspecialchars($user['vname']) . "!";
                
            }
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
        <input type="text" name="passw" id="passw">
       
        <br><br>
        <input type="submit" value="Speichern" name="submit">
        

    </form>
    <?php if ($message): ?>
    <p>
        <?= $message?>
    </p>
    <?php endif; ?>
</body>

</html>