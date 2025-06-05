<?php
require_once "database.php";
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$token     = bin2hex(random_bytes(50));
$userId    = $_POST['idUser'] ?? '';
$action    = $_POST['action'] ?? '';
$nom       = $_POST['Nom'] ?? '';
$prenom    = $_POST['Prenom'] ?? '';
$userEmail = $_POST['Email'] ?? '';

$mailer = new PHPMailer(true);

if ($nom !== '') {
    // --- Donner l'accès ---
    if ($action === 'donner') {
        $uid = $_POST['uid'] ?? '';

        if ($uid === '') {
            echo "Erreur : UID manquant.";
            exit();
        }

        $queryCarte = "INSERT INTO Carte (RFID) VALUES (:rfid)";
        $stmtCarte = $pdo->prepare($queryCarte);
        $stmtCarte->bindParam(':rfid', $uid, PDO::PARAM_STR);

        if (!$stmtCarte->execute()) {
            echo "Erreur lors de l'enregistrement de la carte.";
            exit();
        }

        $idCarte = $pdo->lastInsertId();

        $queryUser = "UPDATE User SET Verifier = 1, idCarte = :idCarte WHERE Email = :Email";
        $stmtUser = $pdo->prepare($queryUser);
        $stmtUser->bindParam(':idCarte', $idCarte, PDO::PARAM_INT);
        $stmtUser->bindParam(':Email', $userEmail, PDO::PARAM_STR);

        if ($stmtUser->execute()) {
            echo "Carte insérée et utilisateur mis à jour avec succès.";
        } else {
            echo "Erreur lors de la mise à jour de l'utilisateur.";
            exit();
        }

        try {
            $mailer->isSMTP();
            $mailer->Host       = 'smtp.gmail.com';
            $mailer->SMTPAuth   = true;
            $mailer->Username   = 'carteacces99@gmail.com';
            $mailer->Password   = 'llvzctlmvjasxyfq';
            $mailer->SMTPSecure = 'tls';
            $mailer->Port       = 587;

            $mailer->setFrom('carteacces99@gmail.com', 'Charles Poncet');
            $mailer->addAddress($userEmail, "$prenom $nom");

            $mailer->isHTML(true);
            $mailer->Subject = "Accès accorde";
            $mailer->Body    = "Bonjour $prenom $nom,<br><br>Nous vous informons que l'accès vous est accordé.<br><br>Cordialement,<br>L'équipe de validation.";
            $mailer->AltBody = "Bonjour $prenom $nom,\n\nNous vous informons que l'accès vous est accordé.\n\nCordialement,\nL'équipe de validation.";

            $mailer->send();
        } catch (Exception $e) {
            echo "Erreur lors de l'envoi de l'email : " . $mailer->ErrorInfo;
        }

    // --- Refuser ou supprimer l'accès ---
    } elseif ($action === 'refuser' || $action === 'supprimer') {
        // Supprimer les logs liés à l'utilisateur
        $queryLog = "DELETE FROM Acces_log WHERE IdUser = (SELECT idUser FROM User WHERE Email = :Email)";
        $stmtLog = $pdo->prepare($queryLog);
        $stmtLog->bindParam(':Email', $userEmail, PDO::PARAM_STR);
        $stmtLog->execute();
        
        // Ensuite, supprimer l'utilisateur
        $query = "DELETE FROM User WHERE Email = :Email";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':Email', $userEmail, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $message = "L'utilisateur a été supprimé avec succès.";
            $statusClass = "success";
        } else {
            $message = "Erreur lors de la suppression de l'utilisateur.";
            $statusClass = "error";
        }

        echo <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Suppression de l'utilisateur</title>
    <meta http-equiv="refresh" content="2;url=./gestionAcces.php">
    <link rel="shortcut icon" href="../assets/favicon.ico" type="image/x-icon">
    <style>
        body {
            font-family: "Poppins", sans-serif;
            display: flex;
            justify-content: center;
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            margin: 0;
        }
    
        .container {
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: auto;
            animation: fadeIn 1s ease-in-out;
        }
    
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    
        h2 {
            text-align: center;
        }
    
        /* LIGHT MODE */
        @media (prefers-color-scheme: light) {
            body {
                background: url("../assets/light-4k.webp") no-repeat center center fixed;
            }
            .container {
                background: rgba(255, 255, 255, 0.8);
            }
            h2 {
                color: #8dc7cc;
            }
        }
    
        /* DARK MODE */
        @media (prefers-color-scheme: dark) {
            body {
                background: url("../assets/dark-4k.webp") no-repeat center center fixed;
            }
            .container {
                background: rgba(47, 47, 47, 0.8);
            }
            h2 {
                color: #f4a757;
            }
        }
    </style>
</head>
<body>
    <div class="container {$statusClass}">
        <h2>{$message}</h2>
    </div>
</body>
</html>
HTML;

        // Envoi du mail pour refus
        try {
            $mailer->isSMTP();
            $mailer->Host       = 'smtp.gmail.com';
            $mailer->SMTPAuth   = true;
            $mailer->Username   = 'carteacces99@gmail.com';
            $mailer->Password   = 'llvzctlmvjasxyfq';
            $mailer->SMTPSecure = 'tls';
            $mailer->Port       = 587;

            $mailer->setFrom('carteacces99@gmail.com', 'Charles Poncet');
            $mailer->addAddress($userEmail, "$prenom $nom");

            $mailer->isHTML(true);
            $mailer->Subject = "Accès refusee";
            $mailer->Body    = "Bonjour $prenom $nom,<br><br>Nous vous informons que l'accès vous est refusé.<br><br>Cordialement,<br>L'équipe de validation.";
            $mailer->AltBody = "Bonjour $prenom $nom,\n\nNous vous informons que l'accès vous est refusé.\n\nCordialement,\nL'équipe de validation.";

            $mailer->send();
        } catch (Exception $e) {
            echo "Erreur lors de l'envoi de l'email : " . $mailer->ErrorInfo;
        }

    } else {
        echo "Action non valide.";
    }

} else {
    echo "Aucun identifiant utilisateur fourni.";
}
?>
