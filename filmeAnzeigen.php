<?php
session_start();
 
if (empty($_SESSION['kid'])) {
    header('location: Login.php');
    exit;
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
    $stmt2 = $pdo->prepare('SELECT bewertung.bewertung, bewertung.sterne, konto.email FROM bewertung INNER JOIN konto ON bewertung.kid_fk = konto.kid WHERE bewertung.fid_fk = :fid');
    $stmt2->execute(['fid' => $fid]);
    $bewertungen = $stmt2->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Datenbankfehler: ' . $e->getMessage());
}
 
if (isset($_POST['bewerten'])) {
    $_SESSION['bewertung_fid'] = $fid;
    header('location: filmBewerten.php');
    exit;
}
 
if (isset($_POST['liste'])) {
    header('location: filmListe.php');
    exit;
}

if (isset($_POST['bearbeiten'])) {
    header('location: filmBearbeiten.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="de">
 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PopcornCheck - <?= htmlspecialchars($film['titel']) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
 
<body class="dashboard-layout">
    <form action="" method="post">
        <!-- Top Navbar Header -->
        <header class="dashboard-header">
            <div class="header-brand">
                <img src="Logo.png" alt="PopcornCheck Logo">
                <div class="header-title-container">
                    <h1>PopcornCheck</h1>
                    <p>Filmdetails & Bewertungen</p>
                </div>
            </div>
            <div class="header-actions">
                <button type="submit" name="liste" class="btn btn-secondary">Zurück zur Filmliste</button>
            </div>
        </header>
 
        <!-- Two-column responsive layout -->
        <div class="movie-details-layout">
            <!-- Left Pane: Movie info, Trailer and Description -->
            <div class="detail-info-pane">
                <div style="margin-bottom: 24px;">
                    <span class="badge badge-genre" style="margin-bottom: 8px;"><?= htmlspecialchars($film['genre']) ?></span>
                    <h1 style="font-size: 2.2rem; margin-bottom: 12px;"><?= htmlspecialchars($film['titel']) ?></h1>
                </div>
 
                <?php if (!empty($film['trailer'])): ?>
                    <div class="trailer-container">
                        <iframe
                            src="https://www.youtube.com/embed/<?= htmlspecialchars($film['trailer']) ?>"
                            frameborder="0"
                            allowfullscreen>
                        </iframe>
                    </div>
                <?php else: ?>
                    <div class="no-trailer">
                        <span>Es ist kein Trailer vorhanden.</span>
                    </div>
                <?php endif; ?>
 
                <div class="detail-meta-item">
                    <label>Beschreibung</label>
                    <p style="white-space: pre-line; line-height: 1.7;"><?= htmlspecialchars($film['beschreibung']) ?></p>
                </div>
 
                <div class="detail-actions">
                    <button type="submit" name="bewerten" class="btn btn-primary">Bewertung schreiben</button>
                    <?php if ((int)$_SESSION['kid'] === 1): ?>
                        <button type="submit" name="bearbeiten" class="btn btn-primary">Film bearbeiten</button>
                    <?php endif; ?>
                </div>
            </div>
 
            <!-- Right Pane: Reviews list -->
            <div class="detail-reviews-pane">
                <h2 style="font-size: 1.4rem; margin-bottom: 20px; border-bottom: 1px solid var(--bg-card-border); padding-bottom: 12px;">Community Bewertungen</h2>
 
                <div class="table-container" style="margin: 0; background: transparent; border: none; box-shadow: none;">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Benutzer</th>
                                <th style="text-align: right; width: 120px;">Bewertung</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($bewertungen)): ?>
                                <?php foreach ($bewertungen as $b): ?>
                                    <tr>
                                        <td>
                                            <span style="font-weight: 500; color: #ffffff;"><?= htmlspecialchars($b['email']) ?></span>
                                        </td>
                                        <td style="text-align: right; font-weight: 700; color: var(--primary-color);">
                                            <?php
                                            $stars = isset($b['sterne']) ? intval($b['sterne']) : 0;
                                            for ($i = 0; $i < $stars; $i++) echo '★';
                                            for ($i = $stars; $i < 5; $i++) echo '☆';
                                            ?>
                                            <br>
                                            <span style="font-size: 0.8em; font-weight: normal; color: #ccc;"><?= htmlspecialchars($b['bewertung']) ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" style="text-align: center; font-style: italic; color: var(--text-muted); padding: 40px 0;">
                                        Noch keine Bewertungen vorhanden. Sei der Erste!
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
</body>
 
</html>