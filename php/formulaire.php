<?php
require_once './database.php'; // Connexion à la base de données

// 1. Inclure l'autoload de Composer
require __DIR__ . '/vendor/autoload.php';

// 2. Importer les classes PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Récupérer les données du formulaire
$nom         = $_POST['nom']         ?? '';
$prenom      = $_POST['prenom']      ?? '';
$fonction    = $_POST['fonction']    ?? '';
$motif       = $_POST['motif']       ?? '';
$telephone   = $_POST['telephone']   ?? '';
$email       = $_POST['email']       ?? '';
$date_debut  = $_POST['date-debut']  ?? '';
$date_fin    = $_POST['date-fin']    ?? '';

// Générer un token unique
$token       = bin2hex(random_bytes(50)); 
$mail_envoye = 0;
$mail_verif  = 0; // Email non encore vérifié

try {
    // Insérer les données dans ta table (assure-toi que la colonne "token" existe bien)
    $sql = "INSERT INTO User 
            (Nom, Prenom, Email, Tel, Motif, Date_debut, Date_fin, Fonction, Mail_envoye, Mail_verif, token)
            VALUES 
            (:nom, :prenom, :email, :telephone, :motif, :date_debut, :date_fin, :fonction, :mail_envoye, :mail_verif, :token)";
    
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

    // Maintenant, on va envoyer l'e-mail de confirmation via PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configuration du serveur SMTP (ici avec Gmail)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';           // Serveur SMTP
        $mail->SMTPAuth   = true;
        $mail->Username   = 'carteacces99@gmail.com';     // Ton adresse SMTP
        $mail->Password   = 'llvzctlmvjasxyfq';             // Ton mot de passe d'application (ou autre)
        $mail->SMTPSecure = 'tls';                        // Chiffrement TLS
        $mail->Port       = 587;                          // Port SMTP

        // Expéditeur et destinataire
        $mail->setFrom('carteacces99@gmail.com', 'Charles Poncet');
        $mail->addAddress($email, $prenom . ' ' . $nom);

        // Construire le lien de confirmation (pointez vers le fichier confirmMail.php)
        $confirmation_link = "http://173.21.1.162/php/confirmMail.php?email=" 
                             . urlencode($email) . "&token=" . $token;

        // Contenu du mail
        $mail->isHTML(true);
        $mail->Subject = "Confirmation de votre inscription";
        $mail->Body    = "Bonjour $prenom $nom,<br><br>"
                       . "Merci de votre inscription. Veuillez confirmer votre adresse email en cliquant sur le lien ci-dessous :<br><br>"
                       . "<a href='$confirmation_link'>$confirmation_link</a><br><br>"
                       . "Cordialement,<br>L'équipe de validation.";
        // Version texte (fallback)
        $mail->AltBody = "Bonjour $prenom $nom,\n\n"
                       . "Merci de votre inscription. Veuillez confirmer votre adresse email en cliquant sur le lien ci-dessous :\n\n"
                       . "$confirmation_link\n\n"
                       . "Cordialement,\nL'équipe de validation.";

        // Envoi du mail
        $mail->send();

        // Mettre à jour la colonne Mail_envoye à 1
        $update_sql = "UPDATE User SET Mail_envoye = 1 WHERE Email = :email";
        $update_stmt = $pdo->prepare($update_sql);
        $update_stmt->execute([':email' => $email]);

        // Message de confirmation
        echo "Inscription réussie ! Un email de confirmation vous a été envoyé.";

    } catch (Exception $e) {
        echo "Erreur lors de l'envoi de l'email : " . $mail->ErrorInfo;
        // header("Location: ../html/formulaire.html");
        // exit;
    }

} catch (PDOException $e) {
    echo "Erreur lors de l'inscription : " . $e->getMessage();
    // header("Location: ../html/formulaire.html");
    // exit;
}
?>
