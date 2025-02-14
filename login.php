<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php'; // Inclusion de la connexion PDO

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Préparation de la requête SQL pour éviter les injections SQL
    $stmt = $conn->prepare("SELECT * FROM Admin WHERE Identifiant = username' LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Vérification du mot de passe
        if (password_verify($password, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $user['username'];
            echo "Connexion réussie !";
            header("Location: dashboard.php"); // Redirection vers la page admin
            exit();
        } else {
            echo "<script>alert('Mot de passe incorrect !');</script>";
        }
    } else {
        echo "<script>alert('Utilisateur non trouvé !');</script>";
    }

    $stmt->close();
    $conn->close();
}

?>