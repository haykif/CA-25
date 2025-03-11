<?php
    require_once './database.php'; // Connexion à la base de données
    session_start();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Récupération et nettoyage des entrées utilisateur
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (!empty($username) && !empty($password)) {
            try {
                // Préparer et exécuter la requête pour récupérer l'utilisateur
                $stmt = $pdo->prepare("SELECT Identifiant, Mdp FROM Admin WHERE Identifiant = :username");
                $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Vérifier si l'utilisateur existe et si le mot de passe correspond au hash stocké
                if ($user && password_verify($password, $user['Mdp'])) {
                    // Authentification réussie : initialisation de la session et redirection
                    $_SESSION['admin_id'] = $user['Identifiant'];
                    $_SESSION['is_admin'] = true;
                    header("Location: ./dashboard.php");
                    exit;
                } else {
                    // Si la vérification échoue
                    echo "<script>alert('Identifiant ou mot de passe incorrect !'); window.location.href='../index.html';</script>";
                }
            } catch (PDOException $e) {
                // En production, on loggue l'erreur et on affiche un message générique
                error_log("Erreur de connexion : " . $e->getMessage());
                echo "<script>alert('Une erreur interne est survenue.'); window.location.href='../index.html';</script>";
                exit;
            }
        } else {
            echo "<script>alert('Veuillez remplir tous les champs.'); window.location.href='../index.html';</script>";
        }
    } else {
        // Si la requête n'est pas de type POST, rediriger vers la page d'accueil
        header("Location: ../index.html");
        exit;
    }
?>
