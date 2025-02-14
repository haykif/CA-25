<?php
session_start();

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Configuration de la connexion à la base de données
    $host = 'localhost';       // ou l'adresse de ton serveur
    $dbname = 'nom_de_ta_db';    // nom de ta base de données
    $dbuser = 'utilisateur_db';  // ton utilisateur de base de données
    $dbpass = 'mot_de_passe_db'; // ton mot de passe

    try {
        // Connexion à la base de données en utilisant PDO
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Préparation de la requête pour récupérer l'utilisateur
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérification de l'existence de l'utilisateur et du mot de passe
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            header("Location: dashboard.php"); // redirige vers le Dashboard
            exit();
        } else {
            echo "Identifiants incorrects.";
        }
    } catch (PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }
} else {
    echo "Veuillez remplir tous les champs.";
}
?>
