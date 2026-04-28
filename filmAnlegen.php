<?php
//session_start();

/*if ($_SESSION['id'] != 1) {
    header('index.php');
}*/

$targetDir = "uploads/";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['titel']) && !empty($_POST['genre']) && !empty($_POST['beschreibung'])) {
        $titel = trim($_POST['titel']);
        $trailer = trim($_POST['trailer']);
        $genre = trim($_POST['genre']);
        $beschreibung = trim($_POST['beschreibung']);

        require_once('db.php');

        try {
            $stmt = $pdo->prepare("INSERT INTO film (titel, genre, trailer, beschreibung) VALUES (:titel, :genre, :trailer, :beschreibung)");

            $stmt->execute([
                ':titel' => $titel,
                ':trailer' => $trailer,
                ':genre' => $genre,
                ':beschreibung' => $beschreibung
            ]);

            echo "Film wurde in die Datenbank hochgeladen";

        } catch (PDOException $e) {
            die('Fehler beim Speichern in die Datenbank.');
        }
    }
}

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Film anlegen</title>
</head>
<body>
    <h1>Film anlegen</h1>

    <form action="" method="post">
        <label for="titel">Titel:</label>
        <input type="text" name="titel" id="titel" required>

        <label for="trailer">Trailer URL:</label>
	    <input type="text" name="trailer" id="trailer">

        <label for="genre">Genre:</label>
        <input type="text" name="genre" id="genre" required>

        <label for="beschreibung">Beschreibung:</label>
        <input type="text" name="beschreibung" id="beschreibung" required>

        <input type="submit" name="absenden" id="absenden" value="Absenden">

    </form>
</body>
</html>