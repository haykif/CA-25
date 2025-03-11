<?php
    require_once './database.php'; 

    session_start();
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        header("Location: ./login.php"); // redirige vers la page de connexion
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // 1. Récupérer et nettoyer les données
        $nom         = trim($_POST['nom'] ?? '');
        $prenom      = trim($_POST['prenom'] ?? '');
        $identifiant = trim($_POST['identifiant'] ?? '');
        $mdp         = trim($_POST['mdp'] ?? '');
        $email       = trim($_POST['email'] ?? '');
        $tel         = trim($_POST['tel'] ?? '');

        // 2. Vérifier que tous les champs sont remplis
        if (empty($nom) || empty($prenom) || empty($identifiant) || empty($mdp) || empty($email) || empty($tel)) {
            die("Veuillez remplir tous les champs.");
        }
        
        if (strlen($tel) !== 10) {
                die("TEL Invalide");
        }
        
        // 3. Hasher le mot de passe
        $hashedMdp = password_hash($mdp, PASSWORD_DEFAULT);

        try {
            // 4. Préparer la requête d'insertion
            //    On n'inclut pas idAdmin si c'est un AUTO_INCREMENT
            $stmt = $pdo->prepare("
                INSERT INTO Admin (Nom, Prenom, Identifiant, Mdp, Email, Tel)
                VALUES (:nom, :prenom, :identifiant, :mdp, :email, :tel)
            ");

            // 5. Lier les paramètres
            $stmt->bindParam(':nom',         $nom,         PDO::PARAM_STR);
            $stmt->bindParam(':prenom',      $prenom,      PDO::PARAM_STR);
            $stmt->bindParam(':identifiant', $identifiant, PDO::PARAM_STR);
            $stmt->bindParam(':mdp',         $hashedMdp,   PDO::PARAM_STR);
            $stmt->bindParam(':email',       $email,       PDO::PARAM_STR);
            $stmt->bindParam(':tel',         $tel,         PDO::PARAM_INT);

            // 6. Exécuter l'insertion
            if ($stmt->execute()) {
                echo "<script>
                alert('Inscription réussi');
                window.location.href = './dashboard.php';
            </script>";
            } else {
                echo "<script>
                alert('Inscription échoué');
                window.location.href = './inscriptionAdmin.php';
            </script>";
            }
        } catch (PDOException $e) {
            // 7. Log en cas d'erreur
            error_log("Erreur d'insertion : " . $e->getMessage());
            echo "Une erreur interne est survenue.";
            var_dump($stmt->errorInfo());
        }
    } else {
        // 8. Affichage du formulaire si on n'est pas en POST
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Inscription Admin</title>
        <link rel="stylesheet" href="../css/inscriptionAdmin.css">
        <link rel="shortcut icon" href="../assets/favicon.ico" type="image/x-icon">
    </head>

    <body>
        <div class="sidebar">
            <h2>Admin Dashboard</h2>
            <ul>
                <li><a href="./dashboard.php">Tableau de bord</a></li>
                <li><a href="#">Gestion des accès</a></li>
                <li><a href="./inscriptionAdmin.php">Inscription admin</a></li>
                <li><a href="../html/formulaire.html">Formulaire</a></li>
                <li><a href="#">Logs</a></li>
            </ul>
            <div class="logout">
                <a href="./logout.php">Déconnexion</a>
              </div>
        </div>

        <div class="registration-container">
            <h2>Inscription d'un nouvel Admin</h2>
            <form action="" method="post">
                <label for="nom">Nom</label>
                <input type="text" name="nom" id="nom" required>
                <br><br>
                <label for="prenom">Prénom</label>
                <input type="text" name="prenom" id="prenom" required>
                <br><br>
                <label for="identifiant">Identifiant</label>
                <input type="text" name="identifiant" id="identifiant" required>
                <br><br>
                <label for="mdp">Mot de passe</label>
                <input type="password" name="mdp" id="mdp" required>
                <br><br>
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
                <br><br>
                <label for="tel">Téléphone</label>
                <input type="number" name="tel" id="tel" required>
                <br><br>
                <button type="submit">Inscrire</button>
            </form>
        </div>
    </body>
</html>

<?php
}
?>
