<?php
session_start();

if (!isset($_SESSION['kid'])) {
    header('location: Login.php');
    exit;
}

require_once('db.php');

$message = '';
$film_id = $_SESSION['bewertung_fid'] ?? '';

if (empty($film_id)) {
    header('location: filmListe.php');
    exit;
}

// Fetch film for display
$film = null;
try {
    $stmt = $pdo->prepare("SELECT titel FROM film WHERE fid = :fid");
    $stmt->execute(['fid' => $film_id]);
    $film = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Datenbankfehler: " . $e->getMessage());
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['zurueck'])) {
        header('location: filmeAnzeigen.php');
        exit;
    }

    $sterne = $_POST['sterne'] ?? '';
    $zusatz = $_POST['zusatz'] ?? '';

    if (mb_strlen($zusatz) > 200) {
            $error = "Die Bewertung darf maximal 200 Zeichen lang sein.";
    }
    
    if (empty($error)) {
        $blacklist = ['scheiß', 'scheiss', 'drecks', 'kack', 'bullshit', 'kotz', 'schmutz', 'abfall', 'rotze', 'arschloch', 'idiot', 'depp', 'spast', 'opfer', 'behindert', 'wichs', 'fotze', 'hurensohn', 'schlampe', 'bastard', 'missgeburt', 'schwuchtel'];

        $containsBlacklistedWort = false;
        foreach ($blacklist as $wort) {
            // stripos sucht, ob das Blacklist-Wort (unabhängig von Groß-/Kleinschreibung)
            if (stripos($zusatz, $wort) !== false) {
                $containsBlacklistedWort = true;
                break;
            }
        }

        if ($containsBlacklistedWort) {
            $message = "Deine Bewertung enthält nicht erlaubte Wörter und wurde blockiert.";
        } elseif (!empty($sterne)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO bewertung (bewertung, sterne, fid_fk, kid_fk) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$zusatz, $sterne, $film_id, $_SESSION['kid']])) {
                    $message = "Bewertung erfolgreich gespeichert!";
                } else {
                    $message = "Fehler beim Speichern der Bewertung.";
                }
            } catch (PDOException $e) {
                $message = "Datenbankfehler beim Speichern: " . $e->getMessage();
            }
        } else {
            $message = "Bitte gib eine Sterne-Bewertung ein.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PopcornCheck - Film bewerten</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .rating {
            direction: rtl;
            display: inline-flex;
            gap: 4px;
        }

        .rating input {
            display: none;
        }

        .rating label {
            font-size: 40px;
            color: rgba(255, 255, 255, 0.2);
            cursor: pointer;
            transition: color 0.2s;
        }

        .rating label:hover,
        .rating label:hover~label,
        .rating input:checked~label {
            color: var(--primary-color);
        }
    </style>
</head>

<body class="centered-layout">
    <div class="card">
        <div class="logo-container">
            <h1 style="background: none; -webkit-text-fill-color: initial; color: #fff;">Film bewerten</h1>
            <?php if ($film): ?>
                <p style="font-size: 1.1rem; color: var(--primary-color); margin-bottom: 20px;">
                    <?= htmlspecialchars($film['titel']) ?>
                </p>
            <?php endif; ?>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($message)): ?>
            <div class="alert <?= strpos($message, 'erfolgreich') !== false ? 'alert-info' : 'alert-danger' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
            <?php if (strpos($message, 'erfolgreich') !== false): ?>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="filmeAnzeigen.php" class="btn btn-secondary">Zurück zum Film</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (empty($message) || strpos($message, 'erfolgreich') === false): ?>
            <form action="" method="post">
                <div class="form-group" style="align-items: center; margin-bottom: 24px;">
                    <label>Deine Bewertung</label>
                    <div class="rating">
                        <input type="radio" id="star5" name="sterne" value="5" required /><label for="star5"
                            title="5 Sterne">★</label>
                        <input type="radio" id="star4" name="sterne" value="4" /><label for="star4"
                            title="4 Sterne">★</label>
                        <input type="radio" id="star3" name="sterne" value="3" /><label for="star3"
                            title="3 Sterne">★</label>
                        <input type="radio" id="star2" name="sterne" value="2" /><label for="star2"
                            title="2 Sterne">★</label>
                        <input type="radio" id="star1" name="sterne" value="1" /><label for="star1"
                            title="1 Stern">★</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="zusatz">Zusätzliche Infos (optional)</label>
                    <textarea name="zusatz" id="zusatz" class="form-control" placeholder="Wie fandest du den Film?"></textarea>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 24px;">
                    <button type="submit" name="zurueck" class="btn btn-secondary" formnovalidate
                        style="flex: 1;">Abbrechen</button>
                    <button type="submit" name="speichern" class="btn btn-primary" style="flex: 1;">Speichern</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>