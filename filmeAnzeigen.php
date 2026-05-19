<?php
session_start();

if (empty($_SESSION['kid'])) {
    header('location: Login.php');
}

require_once('db.php');
$fid = $_SESSION['fid'];
$bewertungen = [];

try {

    $stmt = $pdo->prepare("SELECT * FROM film WHERE fid = :fid");
    $stmt->execute(['fid' => $fid]);
    $film = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $th) {
    die('Datenbankfehler: ' . $th->getMessage());
}

try {
    $stmt2 = $pdo->prepare('SELECT bewertung.bewertung, konto.email FROM bewertung INNER JOIN konto ON bewertung.kid_fk = konto.kid WHERE bewertung.fid_fk = :fid');
    $stmt2->execute(['fid' => $fid]);
    $bewertungen = $stmt2->fetch();
} catch (PDOException $e) {
    die('Datenbankfehler: ' . $e->getMessage());
}

if (isset($_POST['bewerten'])) {
    header('location: bewerten.php');
}

if (isset($_POST['liste'])) {
    header('location: filmListe.php');
}

?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PopcornCheck - Film</title>
</head>

<body>
<form action="" method="post">
    <h1><?php echo $film['titel']; ?></h1>

    <p>Genre: <?= htmlspecialchars($film['genre']) ?></p>

    <?php if (!empty($film['trailer'])): ?>
        <iframe width="300" height="169"
            src="https://www.youtube.com/embed/<?= htmlspecialchars($film['trailer']) ?>" frameborder="0"
            allowfullscreen>
        </iframe>
    <?php else: ?>
        <i>Es ist kein Trailer vorhanden.</i>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Benutzer</th>
                <th>Bewertung</th>
            </tr>
        </thead>

        <body>
            <?php if (!empty($bewertungen)): ?>
                <tr>
                    <th>
                        <p><?= htmlspecialchars($bewertungen['email']) ?></p>
                    </th>
                    <th>
                        <p><?= htmlspecialchars($bewertungen['bewertung']) ?></p>
                    </th>
                </tr>
            <?php else: ?>
                <tr>
                    <th></th>
                    <th><p>Noch keine Bewertungen.</p></th>
                </tr>
            <?php endif; ?>
        </body>
    </table>

    <button type="submit" name="bewerten">Bewertung schreiben</button>
    <button type="submit" name="liste">Zurück zur Filmliste</button>
</form>
</body>

</html>