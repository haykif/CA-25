<?php
// Bibliotheque
require __DIR__ . '/vendor/autoload.php';
require_once './database.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Démarage session
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ./login.php");
    exit();
}

// Si le formulaire est fournis
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom         = trim($_POST['nom'] ?? '');
    $prenom      = trim($_POST['prenom'] ?? '');
    $identifiant = trim($_POST['identifiant'] ?? '');
    $mdp         = trim($_POST['mdp'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $tel         = trim($_POST['tel'] ?? '');
    $superuser   = 1;
    $fonction    = "Admin";

    if (empty($nom) || empty($prenom) || empty($identifiant) || empty($mdp) || empty($email) || empty($tel)) {
        die("Veuillez remplir tous les champs.");
    }

    if (strlen($tel) !== 10) {
        die("TEL invalide");
    }

    try {
        //  Vérification des doublons
        $checkStmt = $pdo->prepare("
            SELECT COUNT(*) FROM User 
            WHERE Identifiant = :identifiant OR Email = :email
        ");
        $checkStmt->execute([
            ':identifiant' => $identifiant,
            ':email' => $email
        ]);

        if ($checkStmt->fetchColumn() > 0) {
            echo "<script>
                alert('Identifiant ou email déjà utilisé.');
                window.location.href = './inscriptionAdmin.php';
            </script>";
            exit();
        }

        // Hashement du mdp en bcrypt
        $hashedMdp = password_hash($mdp, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            INSERT INTO User (Nom, Prenom, Identifiant, Mdp, Email, Tel, SuperUser, Fonction)
            VALUES (:nom, :prenom, :identifiant, :mdp, :email, :tel, :superuser, :fonction)
        ");

        // Liaison des paramètres
        $stmt->bindParam(':nom',         $nom,         PDO::PARAM_STR);
        $stmt->bindParam(':prenom',      $prenom,      PDO::PARAM_STR);
        $stmt->bindParam(':identifiant', $identifiant, PDO::PARAM_STR);
        $stmt->bindParam(':mdp',         $hashedMdp,   PDO::PARAM_STR);
        $stmt->bindParam(':email',       $email,       PDO::PARAM_STR);
        $stmt->bindParam(':tel',         $tel,         PDO::PARAM_INT);
        $stmt->bindParam(':superuser',   $superuser,   PDO::PARAM_INT);
        $stmt->bindParam(':fonction',    $fonction,    PDO::PARAM_STR);

        if ($stmt->execute()) {
            // 🆔 Récupérer l'ID du nouvel utilisateur
            $newUserId = $pdo->lastInsertId();

            // ✉️ Envoi email
            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'carteacces99@gmail.com';
                $mail->Password   = 'llvzctlmvjasxyfq';
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                $mail->setFrom('carteacces99@gmail.com', 'Charles Poncet');
                $mail->addAddress($email, "$prenom $nom");
                $mail->isHTML(true);

                $mail->Subject = "Confirmation de votre inscription";
                $mail->Body    = "Bonjour $prenom $nom,<br><br>"
                    . "Félicitations, vous avez obtenu les droits admin pour le dashboard 'CA25'.<br>"
                    . "Votre identifiant est : <strong>$identifiant</strong><br>"
                    . "Votre ID utilisateur est : <strong>$newUserId</strong><br><br>"
                    . "Cordialement,<br>Lycée Charles Poncet.";
                $mail->AltBody = "Bonjour $prenom $nom,\n\n"
                    . "Félicitations, vous avez obtenu les droits admin pour le dashboard 'CA25'.\n"
                    . "Identifiant : $identifiant\n"
                    . "ID utilisateur : $newUserId\n\n"
                    . "Cordialement,\nLycée Charles Poncet.";

                $mail->send();
            } catch (Exception $e) {
                error_log("Erreur envoi mail : " . $mail->ErrorInfo);
            }

            echo "<script>
                alert('Inscription réussie');
                window.location.href = './dashboard.php';
            </script>";
            exit();
        } else {
            echo "<script>
                alert('Inscription échouée');
                window.location.href = './inscriptionAdmin.php';
            </script>";
            exit();
        }

    } catch (Exception $e) {
        echo "<script>
            alert(\"Erreur lors de l'inscription.\");
            window.location.href = './inscriptionAdmin.php';
        </script>";
        exit();
    }
}
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
            <li><a href="./gestionAcces.php">Gestion des accès</a></li>
            <li><a href="./inscriptionAdmin.php">Inscription admin</a></li>
            <li><a href="../html/formulaire.html">Formulaire</a></li>
            <li><a href="./logsAdmin.php">Historique connexion admin</a></li>
            <li><a href="./logsServer.php">Historique des accès</a></li>
        </ul>
        <div class="logout">
            <a href="./logout.php">Déconnexion</a>
        </div>
    </div>

    <div class="main-container">
        <div class="main-content">
            <h2>Inscription d'un nouvel Admin</h2>
            <form action="" method="post">
                <label for="nom">Nom</label>
                <input type="text" name="nom" id="nom" required>

                <label for="prenom">Prénom</label>
                <input type="text" name="prenom" id="prenom" required>

                <label for="identifiant">Identifiant</label>
                <input type="text" name="identifiant" id="identifiant" required>

                <label for="mdp">Mot de passe</label>
                <input type="password" name="mdp" id="mdp" required>

                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>

                <label for="tel">Téléphone</label>
                <input type="number" name="tel" id="tel" required>

                <button type="submit">Inscrire</button>
            </form>
        </div>
    </div>
</body>
</html>