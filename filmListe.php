<?php
session_start();

if (empty($_SESSION['kid'])) {
    header('location: Login.php');
}

require_once('db.php');
$sql = "SELECT fid, titel, genre, beschreibung FROM film";
$params = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['suchen'])) {
        if (!empty($_POST['suche'])) {
            $suche = trim(htmlspecialchars($_POST['suche']));
            $suchParameter = "%" . $suche . "%";

            $sql = "SELECT fid, titel, genre, beschreibung FROM film WHERE titel LIKE :suchParameter";
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
    <title>Film Liste</title>
</head>

<body>
    <h1>Film Liste:</h1>

    <form action="" method="post">

        <label for="suche">Filmtitel suchen:</label>
        <input type="text" name="suche" id="suche">
        <button type="submit" name="suchen" id="suchen">Suchen</button>

        <br><br>

        <table>
            <tr>
                <th>Titel</th>
                <th>Genre</th>
                <th>Beschreibung</th>
            </tr>
            <?php while ($row = $result->fetch()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['titel']); ?></td>
                    <td><?php echo htmlspecialchars($row['genre']); ?></td>
                    <td><?php echo htmlspecialchars($row['beschreibung']); ?></td>
                    <td><button type="submit" name="anzeigen" value="<?php echo $row['fid']; ?>">Anzeigen</button></td>
                </tr>
            <?php endwhile; ?>
        </table>

        <?php if ($_SESSION['kid'] === 1):?>
            <button type="submit" name="anlegen">Film anlegen</button>
        <?php endif; ?>

        <button type="submit" name="logout">Logout</button>
    </form>
</body>

</html>