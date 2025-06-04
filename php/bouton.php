<?php
    require_once "database.php";

    // Inclure l'autoload de Composer
    require __DIR__ . '/vendor/autoload.php';


    // Importer les classes PHPMailer
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    // Générer un token unique
    $token     = bin2hex(random_bytes(50));

    $userId    = $_POST['idUser'] ?? '';
    $action    = $_POST['action'] ?? '';
    $nom       = $_POST['Nom'] ?? '';
    $prenom    = $_POST['Prenom'] ?? '';
    $userEmail = $_POST['Email'] ?? '';  // Renommé pour éviter le conflit

    // Créer l'instance PHPMailer
    $mailer = new PHPMailer(true);

    if ($nom !== '') {

        // Accès autorisé
        if ($action === 'donner') {

            $uid = $_POST['uid'] ?? '';

            if ($uid === '') {
                echo "Erreur : UID manquant.";
                exit();
            }
            
            // 1. Insérer la nouvelle carte dans la table Carte
            $queryCarte = "INSERT INTO Carte (RFID) VALUES (:rfid)";
            $stmtCarte = $pdo->prepare($queryCarte);
            $stmtCarte->bindParam(':rfid', $uid, PDO::PARAM_STR);
            
            if (!$stmtCarte->execute()) {
                echo "Erreur lors de l'enregistrement de la carte.";
                exit();
            }
            
            // 2. Récupérer l’ID de la carte qu'on vient d’ajouter
            $idCarte = $pdo->lastInsertId();
            
            // 3. Mettre à jour l'utilisateur pour lier la carte
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

            // Envoi du mail à l'utilisateur
            try {
                // Configuration du serveur SMTP (ici avec Gmail)
                $mailer->isSMTP();
                $mailer->Host       = 'smtp.gmail.com';      // Serveur SMTP
                $mailer->SMTPAuth   = true;
                $mailer->Username   = 'carteacces99@gmail.com';// Ton adresse SMTP
                $mailer->Password   = 'llvzctlmvjasxyfq';      // Ton mot de passe d'application
                $mailer->SMTPSecure = 'tls';                 // Chiffrement TLS
                $mailer->Port       = 587;                   // Port SMTP

                // Expéditeur et destinataire
                $mailer->setFrom('carteacces99@gmail.com', 'Charles Poncet');
                $mailer->addAddress($userEmail, $prenom . ' ' . $nom);

                // Contenu du mail
                $mailer->isHTML(true);
                $mailer->Subject = "Acces accordé";
                $mailer->Body    = "Bonjour $prenom $nom,<br><br>"
                                . "Nous vous contactons pour vous informer que l'accès vous est accorde.<br><br>"
                                . "Cordialement,<br>L'equipe de validation.";
                // Version texte (fallback)
                $mailer->AltBody = "Bonjour $prenom $nom,\n\n"
                                . "Nous vous contactons pour vous informer que l'accès vous est accorde.\n\n"
                                . "Cordialement,\nL'équipe de validation.";

                // Envoi du mail
                $mailer->send();
            } catch (Exception $e) {
                echo "Erreur lors de l'envoi de l'email : " . $mailer->ErrorInfo;
            }

        // Accès refusé
        } elseif ($action === 'refuser' || $action === 'supprimer') {
            $query = "DELETE FROM User WHERE Email = :Email";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':Email', $userEmail, PDO::PARAM_STR);
            if ($stmt->execute()) {
                echo "L'utilisateur avec a été supprimé avec succès.";
            } else {
                echo "Erreur lors de la suppression de l'utilisateur.";
            }

            // Envoi du mail à l'utilisateur
            try {
                // Configuration du serveur SMTP (ici avec Gmail)
                $mailer->isSMTP();
                $mailer->Host       = 'smtp.gmail.com';      // Serveur SMTP
                $mailer->SMTPAuth   = true;
                $mailer->Username   = 'carteacces99@gmail.com';// Ton adresse SMTP
                $mailer->Password   = 'llvzctlmvjasxyfq';      // Ton mot de passe d'application
                $mailer->SMTPSecure = 'tls';                 // Chiffrement TLS
                $mailer->Port       = 587;                   // Port SMTP

                // Expéditeur et destinataire
                $mailer->setFrom('carteacces99@gmail.com', 'Charles Poncet');
                $mailer->addAddress($userEmail, $prenom . ' ' . $nom);

                // Contenu du mail
                $mailer->isHTML(true);
                $mailer->Subject = "Acces refuse";
                $mailer->Body    = "Bonjour $prenom $nom,<br><br>"
                                . "Nous vous contactons pour vous informer que l'accès vous est refuse.<br><br>"
                                . "Cordialement,<br>L'équipe de validation.";
                // Version texte (fallback)
                $mailer->AltBody = "Bonjour $prenom $nom,\n\n"
                                . "Nous vous contactons pour vous informer que l'acces vous est refuse.\n\n"
                                . "Cordialement,\nL'équipe de validation.";

                // Envoi du mail
                $mailer->send();
            } catch (Exception $e) {
                echo "Erreur lors de l'envoi de l'email : " . $mailer->ErrorInfo;
            }

        } 
        
        else {
            echo "Action non valide.";
        }
    } else {
        echo "Aucun identifiant utilisateur fourni.";
    }
?>
