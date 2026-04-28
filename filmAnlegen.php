<?php
//session_start();

/*if ($_SESSION['id'] != 1) {
    header('index.php');
}*/

function getYoutubeId($url) {
    // Erkennt Video-IDs aus verschiedenen Formaten (watch?v=, share, embed)
    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $url, $match);
    return isset($match[1]) ? $match[1] : null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['titel']) && !empty($_POST['genre']) && !empty($_POST['beschreibung'])) {
        $titel = trim($_POST['titel']);
        $raw_trailer = trim($_POST['trailer']);
        $genre = trim($_POST['genre']);
        $beschreibung = trim($_POST['beschreibung']);

        $videoID = getYoutubeId($raw_trailer);

        if ($videoID === null) {
            die("Ungültige URL eingabe.");
        }

        require_once('db.php');

        try {
            $stmt = $pdo->prepare("INSERT INTO film (titel, genre, trailer, beschreibung) VALUES (:titel, :genre, :trailer, :beschreibung)");

            $stmt->execute([
                ':titel' => $titel,
                ':trailer' => $videoID,
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

        <label for="trailer">Trailer URL (YouTube):</label>
	    <input type="text" name="trailer" id="trailer">

        <label for="genre">Genre:</label>
        <input type="text" name="genre" id="genre" required>

        <label for="beschreibung">Beschreibung:</label>
        <textarea name="beschreibung" id="beschreibung" required></textarea>

        <input type="submit" name="absenden" id="absenden" value="Absenden">

    </form>
</body>
</html>