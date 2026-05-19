<?php
session_start();
require_once('db.php');

//$fid = $_GET['fid'] ?? null;
$fid = 2;

if (!$fid) {
    die('Kein Film ausgewählt. ');
}

$film = null;
$bewertungen = [];

try {
    // 1. Film laden
    $stmt = $pdo->prepare("SELECT * FROM film WHERE fid = ?");
    $stmt->execute([$fid]);
    $film = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$film) {
        die('Film nicht gefunden.');
    }

    // 2. Bewertungen inklusive User-Email laden
    $stmt2 = $pdo->prepare("
        SELECT b.bewertung, k.email 
        FROM bewertung b
        JOIN konto k ON b.kid_fk = k.kid
        WHERE b.fid_fk = ?
    ");
    $stmt2->execute([$fid]);
    $bewertungen = $stmt2->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $th) {
    die('Datenbankfehler: ' . $th->getMessage());
}
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PopcornCheck - <?= htmlspecialchars($film['titel']) ?></title>
    
   
</head>

<body>

    <form action="" method="post">
        <div class="film-details">
            <h2><?= htmlspecialchars($film['titel']) ?></h2>
            <p><strong>Genre:</strong> <?= htmlspecialchars($film['genre']) ?></p>
            <p><strong>Beschreibung:</strong><br> <?= nl2br(htmlspecialchars($film['beschreibung'])) ?></p>

            <?php if (!empty($film['trailer'])): ?>
                <div style="margin-top: 15px;">
                    <strong>Trailer:</strong><br>
                    <iframe width="560" height="315"
                        src="https://www.youtube.com/embed/<?= htmlspecialchars($film['trailer']) ?>" frameborder="0"
                        allowfullscreen>
                    </iframe>
                </div>
            <?php else: ?>
                <p><i>Es ist kein Trailer vorhanden.</i></p>
            <?php endif; ?>
        </div>

        <div class="bewertungen-section">
            <h3>Bewertungen</h3>
            <?php if (!empty($bewertungen)): ?>
                <?php foreach ($bewertungen as $b): ?>
                    <div class="bewertung-eintrag">
                        <div class="user-email"><?= htmlspecialchars($b['email']) ?></div>
                        <div class="stars">
                            <?= str_repeat('★', (int) $b['bewertung']) ?><span
                                style="color: #ccc;"><?= str_repeat('★', 5 - (int) $b['bewertung']) ?></span>
                            <span style="color: black; font-size: 14px; margin-left: 5px;">(<?= htmlspecialchars($b['bewertung']) ?>
                                / 5)</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Für diesen Film gibt es noch keine Bewertungen.</p>
            <?php endif; ?>
        </div>
    </form>

</body>

</html>