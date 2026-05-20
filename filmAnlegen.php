<?php
session_start();

if (empty($_SESSION['kid']) || (int)$_SESSION['kid'] !== 1) {
    header('location: filmListe.php');
    exit;
}

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

            header('location: filmListe.php');

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
    <title>PopcornCheck - Film anlegen</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="centered-layout">
    <div class="card" style="max-width: 500px;">
        <div class="logo-container">
            <img src="Logo.png" alt="PopcornCheck Logo" class="logo-img">
        </div>
        <h1>Film anlegen</h1>

        <form action="" method="post">
            <div class="form-group">
                <label for="titel">Titel</label>
                <input type="text" name="titel" id="titel" class="form-control" required placeholder="z.B. Inception">
            </div>

            <div class="form-group">
                <label for="trailer">Trailer URL (YouTube)</label>
                <input type="text" name="trailer" id="trailer" class="form-control" placeholder="z.B. https://www.youtube.com/watch?v=...">
            </div>

            <div class="form-group">
                <label for="genre">Genre</label>
                <input type="text" name="genre" id="genre" class="form-control" required placeholder="z.B. Science Fiction">
            </div>

            <div class="form-group">
                <label for="beschreibung">Beschreibung</label>
                <textarea name="beschreibung" id="beschreibung" class="form-control" required placeholder="Kurze Inhaltsangabe des Films..."></textarea>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <a href="filmListe.php" class="btn btn-secondary" style="flex: 1;">Abbrechen</a>
                <button type="submit" name="absenden" id="absenden" class="btn btn-primary" style="flex: 1.5;">Film speichern</button>
            </div>
        </form>
    </div>
</body>
</html>