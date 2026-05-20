<?php
session_start();

$_SESSION = array();

//if (isset($_COOKIE['vname'])) {
    //setcookie('vname', '', time() - 3600); // Cookie löschen
    //echo $_COOKIE['vname'];
//}

session_destroy();



?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PopcornCheck - Logout</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="centered-layout">
    <div class="card" style="text-align: center;">
        <div class="logo-container">
            <img src="Logo.png" alt="PopcornCheck Logo" class="logo-img">
        </div>
        <h1>Abgemeldet</h1>
        <p style="margin-bottom: 24px;">Sie haben sich erfolgreich abgemeldet.</p>
        <a href="Login.php" class="btn btn-primary btn-block">Zurück zum Login</a>
    </div>
</body>

</html>