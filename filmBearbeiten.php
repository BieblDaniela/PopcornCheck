<?php
session_start();

if (empty($_SESSION['kid']) || (int)$_SESSION['kid'] !== 1) {
    header('location: filmListe.php');
    exit;
}

$fid = $_SESSION['fid'];
require_once('db.php');
try {
    $stmt = $pdo->prepare("SELECT * FROM film WHERE fid = :fid");
    $stmt->execute(['fid' => $fid]);
    $film = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $th) {
    die('Datenbankfehler: ' . $th->getMessage());
}

function getYoutubeId($url) {
    // Erkennt Video-IDs aus verschiedenen Formaten (watch?v=, share, embed)
    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $url, $match);
    return isset($match[1]) ? $match[1] : null;
}
$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['titel']) && !empty($_POST['genre']) && !empty($_POST['beschreibung'])) {
        $titel = trim($_POST['titel']);
        $raw_trailer = trim($_POST['trailer']);
        $genre = trim($_POST['genre']);
        $beschreibung = trim($_POST['beschreibung']);

        if (mb_strlen($titel) > 100) {
            $error = "Der Titel darf maximal 100 Zeichen lang sein.";
        } elseif (mb_strlen($genre) > 100) {
            $error = "Das Genre darf maximal 100 Zeichen lang sein.";
        } elseif (mb_strlen($beschreibung) > 1000) {
            $error = "Die Beschreibung darf maximal 1000 Zeichen lang sein.";
        } else {
            $videoID = getYoutubeId($raw_trailer);

            if ($videoID === null) {
                die("Ungültige URL eingabe.");
            }

            try {
                $stmtUpdate = $pdo->prepare("UPDATE film SET titel = :titel, trailer = :trailer, genre = :genre, beschreibung = :beschreibung WHERE fid = :fid");

                $stmtUpdate->execute([
                    'titel'        => $titel,
                    'trailer'      => $videoID, 
                    'genre'        => $genre,
                    'beschreibung' => $beschreibung,
                    'fid'          => $fid 
                ]);

                header('location: filmeAnzeigen.php');

            } catch (PDOException $e) {
                die('Fehler beim Speichern in die Datenbank.');
            }
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
        <h1>Film bearbeiten</h1>

        <form action="" method="post">
            <div class="form-group">
                <label for="titel">Titel</label>
                <input type="text" name="titel" id="titel" class="form-control" value="<?php echo htmlspecialchars($film['titel']) ?>" maxlength="100">
            </div>

            <div class="form-group">
                <label for="trailer">Trailer URL (YouTube)</label>
                <input type="text" name="trailer" id="trailer" class="form-control" value="<?php echo "https://www.youtube.com/embed/" . htmlspecialchars($film['trailer']) ?>">
            </div>

            <div class="form-group">
                <label for="genre">Genre</label>
                <input type="text" name="genre" id="genre" class="form-control" value="<?php echo htmlspecialchars($film['genre']) ?>" maxlength="100">
            </div>

            <div class="form-group">
                <label for="beschreibung">Beschreibung</label>
                <textarea name="beschreibung" id="beschreibung" class="form-control" maxlength="1000"><?php echo htmlspecialchars($film['beschreibung']) ?></textarea>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <a href="filmListe.php" class="btn btn-secondary" style="flex: 1;">Abbrechen</a>
                <button type="submit" name="absenden" id="absenden" class="btn btn-primary" style="flex: 1.5;">Änderungen speichern</button>
            </div>
        </form>
    </div>
</body>
</html>