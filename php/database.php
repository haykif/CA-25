<?php
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'dbca25');
    define('DB_USER', 'dbca25');
    define('DB_PASS', 'admin');

    define('DB_DSN', 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8');

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
