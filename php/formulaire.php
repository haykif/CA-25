<?php
    require_once './database.php'; // Connexion à la base de données
    
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
        $sql = "INSERT INTO User (Nom, Prenom, Email, Tel, Motif, Date_debut, Date_fin, Fonction, Mail_envoye, Mail_verif) 
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

 
    //Envoi Mail
    
    // Génération d'un token de confirmation (pour plus de sécurité, envisage d'utiliser d'autres méthodes)
    $token = md5(uniqid($email, true));
    
    // Création du lien de confirmation
    $link = "http://tonsite.com/confirm.php?email=" . urlencode($email) . "&token=" . $token;
    
    // Préparation du contenu de l'email
    $subject = "Confirmez votre adresse email";
    $message = "Bonjour,\n\nMerci de vous être inscrit.\nVeuillez cliquer sur le lien suivant pour confirmer votre adresse email :\n$link\n\nCordialement,\nL'équipe";
    
    // Préparation des en-têtes de l'email
    $headers = "From: no-reply@tonsite.com\r\n";
    $headers .= "Reply-To: support@tonsite.com\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    // Envoi de l'email
    if(mail($email, $subject, $message, $headers)) {
        echo "Email de confirmation envoyé.";
    } else {
        echo "Erreur lors de l'envoi de l'email.";
    }

    
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'Inscription</title>
    <link rel="stylesheet" href="../css/formulaire.css">
    <link rel="shortcut icon" href="../assets/favicon.ico" type="image/x-icon">
</head>
<body>
    <div class="form-container">
        <h2>Formulaire</h2>
        <form action="" method="post">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" required>
            
            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom" required>
            
            <label for="fonction">Fonction</label>
            <input type="text" id="fonction" name="fonction" required>

            <label for="email">Motif</label>
            <input type="text" id="motif" name="motif" required>
            
            <label for="telephone">Numéro de téléphone</label>
            <input type="tel" id="telephone" name="telephone" required>
            
            <label for="email">Adresse mail</label>
            <input type="email" id="email" name="email" required>
            
            <label for="date-debut">Date de début</label>
            <input type="date" id="date-debut" name="date-debut" required>
            
            <label for="date-fin">Date de fin</label>
            <input type="date" id="date-fin" name="date-fin" required>
            
            <button type="submit" class="submit-btn">Envoyer</button>
        </form>
    </div>
</body>
</html>
