<?php
    require_once __DIR__ . './database.php'; // Inclusion de la connexion PDO

    // Récupérer les données du formulaire
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $fonction = $_POST['fonction'] ?? '';
    $motif = $_POST['motif'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $email = $_POST['email'] ?? '';
    $date_debut = $_POST['date-debut'] ?? '';
    $date_fin = $_POST['date-fin'] ?? '';

    $token = bin2hex(random_bytes(50)); // Générer un token unique
    $mail_envoye = 0;
    $mail_verif = 0;// Email non encore envoyé

    try {
        // Insérer les données dans la base
        $sql = "INSERT INTO Formulaire (Nom, Prenom, Email, Tel, Motif, Date_debut, Date_fin, Fonction, Mail_envoye, Mail_verif) 
                VALUES (:nom, :prenom, :email, :telephone, :motif, :date_debut, :date_fin, :fonction, :mail_envoye, :mail_verif)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':email' => $email,
            ':telephone' => $telephone,
            ':motif' => $motif,
            ':date_debut' => $date_debut,
            ':date_fin' => $date_fin,
            ':fonction' => $fonction,
            ':mail_envoye' => $mail_envoye,
            ':mail_verif' => $mail_verif

        ]);

        // Envoyer l'email de confirmation
        $to = $email;
        $subject = "Confirmation de votre inscription";
        $message = "
        Bonjour $prenom $nom,

        Merci de votre inscription. Veuillez confirmer votre adresse email en cliquant sur le lien ci-dessous :
        
        http://172.21.1.240/php/confirmMail.php?email=$email&token=$token

        Cordialement,
        L'équipe de validation.
        ";
        
        $headers = "From: noreply@173.21.1.240\r\n"; // À CHANGER
        $headers .= "Reply-To: noreply@173.21.1.240\r\n"; // À CHANGER
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n"; // À CHANGER

        if (mail($to, $subject, $message, $headers)) {
            echo "Inscription réussie ! Un email de confirmation vous a été envoyé.";

            // Mettre à jour le statut de l'email envoyé
            $update_sql = "UPDATE WhiteList SET Mail_envoye = 1 WHERE Email = :email";
            $update_stmt = $pdo->prepare($update_sql);
            $update_stmt->execute([':email' => $email]);
        } else {
            echo "Erreur lors de l'envoi de l'email.";
        }
    } catch (PDOException $e) {
        echo "Erreur lors de l'inscription : " . $e->getMessage();
    }
?>
