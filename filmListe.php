<?php
session_start();

if (empty($_SESSION['kid'])) {
    header('location: Login.php');
}

require_once('db.php');
$sql = "SELECT fid, titel, genre, beschreibung FROM film ORDER BY fid DESC";
$params = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['suchen'])) {
        if (!empty($_POST['suche'])) {
            $suche = trim(htmlspecialchars($_POST['suche']));
            $suchParameter = "%" . $suche . "%";

            $sql = "SELECT fid, titel, genre, beschreibung FROM film WHERE titel LIKE :suchParameter ORDER BY fid DESC";
            $params = [':suchParameter' => $suchParameter];
        }
    }
}

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $result = $stmt;
} catch (PDOException $e) {
    die('Fehler beim Holen der Daten: ' . $e->getMessage());
}

if (isset($_POST['anzeigen'])) {
    $_SESSION['fid'] = $_POST['anzeigen'];
    header('location: filmeAnzeigen.php');
}

if (isset($_POST['anlegen'])) {
    header('location: filmAnlegen.php');
}

if (isset($_POST['logout'])) {
    header('location: logout.php');
}

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PopcornCheck - Film Liste</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="dashboard-layout">
    <form action="" method="post">
        <!-- Dashboard Top Header / Navbar -->
        <header class="dashboard-header">
            <div class="header-brand">
                <img src="Logo.png" alt="PopcornCheck Logo">
                <div class="header-title-container">
                    <h1>PopcornCheck</h1>
                    <p>Angemeldet als: <strong><?= htmlspecialchars($_SESSION['email']) ?></strong></p>
                </div>
            </div>
            <div class="header-actions">
                <?php if ((int)$_SESSION['kid'] === 1): ?>
                    <button type="submit" name="anlegen" class="btn btn-primary">Film anlegen</button>
                <?php endif; ?>
                <button type="submit" name="logout" class="btn btn-danger">Abmelden</button>
            </div>
        </header>

        <!-- Search Bar Section -->
        <div class="search-container">
            <input type="text" name="suche" id="suche" class="form-control" placeholder="Filmtitel suchen...">
            <button type="submit" name="suchen" id="suchen" class="btn btn-secondary">Suchen</button>
        </div>

        <!-- Movie List Table -->
        <div class="table-container">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Titel</th>
                        <th>Genre</th>
                        <th>Beschreibung</th>
                        <th style="width: 120px; text-align: right;">Aktion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch()): ?>
                        <tr>
                            <td style="font-weight: 600; color: #ffffff;"><?php echo htmlspecialchars($row['titel']); ?></td>
                            <td>
                                <span class="badge badge-genre"><?php echo htmlspecialchars($row['genre']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($row['beschreibung']); ?></td>
                            <td style="text-align: right;">
                                <button type="submit" name="anzeigen" value="<?php echo $row['fid']; ?>" class="btn btn-secondary btn-small" style="padding: 8px 16px; font-size: 0.85rem;">Anzeigen</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </form>
</body>

</html>