<?php
$uploadDir = '../data/';
$uploadFile = $uploadDir . 'doorStatus.txt';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
        echo "OK";
    } else {
        echo "Erreur upload.";
    }
} else {
    echo "Requête non valide.";
}
?>
