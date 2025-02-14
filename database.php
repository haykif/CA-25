<?php
require_once __DIR__ . '/config.php'; // Assurez-vous que config.php est bien inclus

try {
    // Connexion à la base de données avec PDO
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Active les erreurs PDO
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Retourne les résultats sous forme de tableau associatif
        PDO::ATTR_EMULATE_PREPARES => false // Désactive l'émulation des requêtes préparées pour plus de sécurité
    ]);

    // Message facultatif pour indiquer que la connexion est réussie
    // echo "Connexion réussie à la base de données !";
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
