<?php
require_once('db.php');
try {
    $stmt = $pdo->query("SELECT * FROM konto");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($users);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
