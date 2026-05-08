<?php
session_start();
require_once('db.php');
$filme = [];
$bewertungen = [];

try {
  
    $stmt = $pdo->prepare("SELECT * FROM film");
    $stmt->execute(); 
    $filme = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $th) {
    die('Datenbankfehler: ' . $th->getMessage());
}
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PopcornCheck - Filme</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 15px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f4f4f4;
        }

        .bewertung-eintrag {
            border-bottom: 1px dashed #ccc;
            margin-bottom: 10px;
            padding-bottom: 5px;
        }

        .bewertung-eintrag:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
    </style>
</head>

<body>

    <h1>Filme</h1>

    <?php if (!empty($filme)): ?>
        <table>
            <thead>
                <tr>
                    <th>Titel</th>
                    <th>Genre</th>
                    <th>Trailer</th>
                    <th>Beschreibung</th>
                    <th>Bewertungen</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($filme as $row): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($row['titel']) ?></strong>
                        </td>
                        <td>
                            <?= htmlspecialchars($row['genre']) ?>
                        </td>
                        <td>
                            <?php if (!empty($row['trailer'])): ?>
                                <iframe width="300" height="169"
                                    src="https://www.youtube.com/embed/<?= htmlspecialchars($row['trailer']) ?>" frameborder="0"
                                    allowfullscreen>
                                </iframe>
                            <?php else: ?>
                                <i>Es ist kein Trailer vorhanden.</i>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($row['beschreibung']) ?>
                        </td>
                        <td>
                                <?php //
                                        //$stmt2 = $pdo->prepare("SELECT bewertung FROM bewertung WHERE film_id = ?");
                                        //$stmt2->execute([$row['id']]);
                                        //$bewertungen = $stmt2->fetchAll(PDO::FETCH_ASSOC);
                                        $stmt2 = $pdo->prepare('SELECT bewertung FROM bewertung WHERE fid_fk = ?');
                                        $stmt2->execute([$row['fid']]);
                                        $bewertungen = $stmt2->fetch();
                                        
                                        ?>
                                <!-- ?=  kurschreibweise echo -->
                                <?php if (!empty($bewertungen)): ?>
                                    <?php  ?>
                                       
                                            <p><?=   htmlspecialchars($bewertungen['bewertung']) ?></p>
                                        
                                    <?php //endforeach; ?>
                                <?php else: ?>
                                    <p>Noch keine Bewertungen.</p>
                                <?php endif; ?>
                           
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p>Es wurden noch keine Filme in der Datenbank gefunden.</p>
    <?php endif; ?>

</body>

</html>