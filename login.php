<?php
session_start();
require_once 'database.php'; // Connexion incluse ici

var_dump($_POST);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        try {
            // Vérifier si l'utilisateur existe
            $stmt = $pdo->prepare("SELECT Identifiant, Mdp FROM Admin WHERE Identifiant = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['Mdp'])) {
                $_SESSION['admin_id'] = $user['Identifiant'];
                header("Location: dashboard.php"); // Redirection après connexion réussie
                exit;
            } else {
                echo "<script>alert('Identifiant ou mot de passe incorrect !'); window.location.href='index.html';</script>";
            }
        } catch (PDOException $e) {
            die("Erreur : " . $e->getMessage());
        }
    } else {
        echo "<script>alert('Veuillez remplir tous les champs.'); window.location.href='index.html';</script>";
    }
}
?>
