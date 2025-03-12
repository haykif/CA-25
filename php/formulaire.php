<?php
require_once './database.php'; // Connexion à la base de données

// Récupérer les données du formulaire
$nom       = $_POST['nom'] ?? '';
$prenom    = $_POST['prenom'] ?? '';
$fonction  = $_POST['fonction'] ?? '';
$motif     = $_POST['motif'] ?? '';
$telephone = $_POST['telephone'] ?? '';
$email     = $_POST['email'] ?? '';
$date_debut= $_POST['date-debut'] ?? '';
$date_fin  = $_POST['date-fin'] ?? '';

// Générer un token unique
$token = bin2hex(random_bytes(50)); 
$mail_envoye = 0;
$mail_verif  = 0; // Email non encore vérifié

try {
    // Insérer les données dans la base, en ajoutant la colonne "token" dans ta table si nécessaire
    $sql = "INSERT INTO User (Nom, Prenom, Email, Tel, Motif, Date_debut, Date_fin, Fonction, Mail_envoye, Mail_verif, token) 
            VALUES (:nom, :prenom, :email, :telephone, :motif, :date_debut, :date_fin, :fonction, :mail_envoye, :mail_verif, :token)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nom'         => $nom,
        ':prenom'      => $prenom,
        ':email'       => $email,
        ':telephone'   => $telephone,
        ':motif'       => $motif,
        ':date_debut'  => $date_debut,
        ':date_fin'    => $date_fin,
        ':fonction'    => $fonction,
        ':mail_envoye' => $mail_envoye,
        ':mail_verif'  => $mail_verif,
        ':token'       => $token
    ]);

    // Préparer l'envoi de l'email de confirmation
    $to = $email;
    $subject = "Confirmation de votre inscription";
    $confirmation_link = "http://172.21.1.240/php/confirmMail.php?email=" . urlencode($email) . "&token=" . $token;
    $message = "Bonjour $prenom $nom,\n\n" .
               "Merci de votre inscription. Veuillez confirmer votre adresse email en cliquant sur le lien ci-dessous :\n\n" .
               "$confirmation_link\n\n" .
               "Cordialement,\nL'équipe de validation.";
    
    $headers = "From: noreply@tonsite.com\r\n" .
               "Reply-To: noreply@tonsite.com\r\n" .
               "Content-Type: text/plain; charset=UTF-8\r\n";

    if (mail($to, $subject, $message, $headers)) {
        echo "Inscription réussie ! Un email de confirmation vous a été envoyé.";

        // Mettre à jour le statut de l'email envoyé dans la même table
        $update_sql = "UPDATE User SET Mail_envoye = 1 WHERE Email = :email";
        $update_stmt = $pdo->prepare($update_sql);
        $update_stmt->execute([':email' => $email]);
    } else {
        echo "Erreur lors de l'envoi de l'email.";
        header("Location: ../html/formulaire.html");
    }
} catch (PDOException $e) {
    echo "Erreur lors de l'inscription : " . $e->getMessage();
    header("Location: ../html/formulaire.html");
}
?>