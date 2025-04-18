<?php
header('Content-Type: application/json');

// Exemple : lire l'état de la porte depuis un fichier ou une base de données
$doorStatusFile = '../data/door_status.txt';

if (file_exists($doorStatusFile)) {
    $status = trim(file_get_contents($doorStatusFile));
    echo json_encode(['status' => $status]);
} else {
    echo json_encode(['status' => 'inconnu']);
}
?>