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
    <title>Logout</title>
</head>

<body>
    <p>Sie sind abgemeldet</p>
   <p style="text-align: center;"><a href="Login.php" >Zurück zum Login</a></p>
</body>

</html>