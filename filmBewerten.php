<?php
session_start();


if (!isset($_SESSION['kid'])) {
    die('<h3>Du musst eingeloggt sein, um eine Bewertung abzugeben.</h3><p><a href="Login.php">Login</a></p>');
}

require_once('db.php');

$message = '';
$filme = [];

// Filme für Dropdown laden
try {
    $stmt = $pdo->prepare("SELECT * FROM film");
    $stmt->execute();
    $filme = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Datenbankfehler: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $film_id = $_POST['film_id'] ?? '';
    $bewertung = $_POST['bewertung'] ?? '';

    if (!empty($film_id) && !empty($bewertung)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO bewertung (bewertung, fid_fk, kid_fk) VALUES (?, ?, ?)");
            if ($stmt->execute([$bewertung, $film_id, $_SESSION['kid']])) {
                $message = "Bewertung erfolgreich gespeichert!";
            } else {
                $message = "Fehler beim Speichern der Bewertung.";
            }
        } catch (PDOException $e) {
            $message = "Datenbankfehler beim Speichern: " . $e->getMessage();
        }
    } else {
        $message = "Bitte wähle einen Film aus und gib eine Bewertung ein.";
    }
}
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filme Bewerten</title>
    <style>
        /* Sterne Bewertung */
        .rating {
            direction: rtl;
            display: inline-block;
        }

        .rating input {
            display: none;
        }

        .rating label {
            font-size: 30px;
            color: #ccc;
            cursor: pointer;
        }

        .rating label:hover,
        .rating label:hover~label,
        .rating input:checked~label {
            color: gold;
        }
    </style>
</head>

<body>
    <h1>Film bewerten</h1>

    <?php if (!empty($message)): ?>
        <p><strong><?= htmlspecialchars($message) ?></strong></p>
    <?php endif; ?>

    <form action="" method="post">
        <div>
            <label for="film_id">Film Titel:</label>
            <select name="film_id" id="film_id" required>
                <option value="">-- Bitte wählen --</option>
                <?php foreach ($filme as $film): ?>
                    <option value="<?= htmlspecialchars($film['fid']) ?>"><?= htmlspecialchars($film['titel']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <br>

        <div>
            <label>Bewertung:</label><br>
            <div class="rating">
                <input type="radio" id="star5" name="bewertung" value="5" required /><label for="star5"
                    title="5 Sterne">★</label>
                <input type="radio" id="star4" name="bewertung" value="4" /><label for="star4"
                    title="4 Sterne">★</label>
                <input type="radio" id="star3" name="bewertung" value="3" /><label for="star3"
                    title="3 Sterne">★</label>
                <input type="radio" id="star2" name="bewertung" value="2" /><label for="star2"
                    title="2 Sterne">★</label>
                <input type="radio" id="star1" name="bewertung" value="1" /><label for="star1" title="1 Stern">★</label>
            </div>
        </div>
        <br>

        <button type="submit">Senden</button>
    </form>
</body>

</html>