<?php
session_start();

require_once('db.php');
$produkte = [];


    try {
        $stmt = $pdo->query("SELECT * FROM film");
        $produkte = $stmt->fetchAll();
    
    } catch (\PDOException $th) {
        $th->getMessage();
        die('Keine Filme gefunden');
        
    }
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filme anzeigen</title>
</head>
<body>
    <? if (!empty($produkte)) { ?>
        <table>
            <thead>
                <tr>
                    <th>Titel</th>
                    <th>Genre</th>
                    <th>Trailer</th>
                    <th>Beschreibung</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produkte as $row): ?>
                <tr>
                    <td><? htmlspecialchars($produkte['titel']) ?></td>
                    <td><? htmlspecialchars($produkte['genre']) ?></td>
                    <td><? htmlspecialchars($produkte['trailer']) ?></td>
                    <td><? htmlspecialchars($produkte['beschreibung']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            
        </table>
    <? }  ?>
    
</body>
</html>