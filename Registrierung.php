<?php
    //session_start();
    $message = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        //$vname = htmlspecialchars(trim($_POST['vname']));
        //$nname = htmlspecialchars(trim($_POST['nname']));
        $email = htmlspecialchars(trim($_POST['email']));
        $passw = htmlspecialchars(trim($_POST['passw']));
        $passw2 = htmlspecialchars(trim($_POST['passw2']));

        if (!empty($email) && !empty($passw) && !empty($passw2) ) {
            if ($passw == $passw2) {
                $passwHash = password_hash($passw, PASSWORD_DEFAULT);  
                
                //Speichern in die DB
                require_once('db.php');
                try {

                    $stmt = $pdo->prepare("INSERT INTO kunden (user, passw) VALUES (:vname, :passw) ");

                    $stmt->bindParam(':user', $email);
                    $stmt->bindParam(':passw', $passwHash);

                    $stmt->execute();
                    
                    //header("location: login.php");

                    //2. Schritt Kundenid holen
                    $kundenID = $pdo->lastInsertId(); //last inserted id bezieht sich immer auf das prepare vom pdo, oben beim insert
                    echo "Die Kunden ID lautet: " . $kundenID;
                
                    } catch(PDOException $e){
                    if ($e->getCode() == 230000) { //Code für Duplicated Entry
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
    <title>Registrierung</title>
</head>
<body>
    <h1>Registrierung</h1>
    <form action="" method="post">
<!--   <label for="vname">Vorname:</label>
        <input type="text" name="vname" id="vname">
        <br><br>
        <label for="nname">Nachname:</label>
        <input type="text" name="nname" id="nname">
        <br><br> -->
        <label for="email">Email:</label>
        <input type="text" name="email" id="email">
        <br><br>
        <label for="passw">Passwort:</label>
        <input type="text" name="passw" id="passw">
        <br><br>
        <label for="passw2">Passwort wiederholen:</label>
        <input type="text" name="passw2" id="passw2">
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