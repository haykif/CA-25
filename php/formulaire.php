<?php
require_once './database.php';
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$nom         = $_POST['nom']         ?? '';
$prenom      = $_POST['prenom']      ?? '';
$fonction    = $_POST['fonction']    ?? '';
$motif       = $_POST['motif']       ?? '';
$telephone   = $_POST['telephone']   ?? '';
$email       = $_POST['email']       ?? '';
$date_debut  = $_POST['date-debut']  ?? '';
$date_fin    = $_POST['date-fin']    ?? '';

$token       = bin2hex(random_bytes(50)); 
$mail_envoye = 0;
$mail_verif  = 0;

try {
    // Vérifier si l'email existe déjà
    $checkEmail = $pdo->prepare("SELECT COUNT(*) FROM User WHERE Email = :email");
    $checkEmail->execute([':email' => $email]);
    $emailExists = $checkEmail->fetchColumn();

    if ($emailExists) {
        echo "❌ Cet email est déjà utilisé. Veuillez en choisir un autre.";
        exit;
    }

    // Insertion en base
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

    // Envoi de mail
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'carteacces99@gmail.com';
        $mail->Password   = 'llvzctlmvjasxyfq';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('carteacces99@gmail.com', 'Charles Poncet');
        $mail->addAddress($email, "$prenom $nom");

        $confirmation_link = "http://ca25.charles-poncet.net:8083/php/confirmMail.php?email=" 
                            . urlencode($email) . "&token=" . $token;

        $mail->isHTML(true);
        $mail->Subject = "Confirmation de votre inscription";
        $mail->Body    = "Bonjour $prenom $nom,<br><br>"
                      . "Merci de votre inscription. Veuillez confirmer votre adresse email en cliquant sur le lien ci-dessous :<br><br>"
                      . "<a href='$confirmation_link'>$confirmation_link</a><br><br>"
                      . "Cordialement,<br>Lycee Charles Poncet.";
        $mail->AltBody = "Bonjour $prenom $nom,\n\n"
                      . "Merci de votre inscription. Veuillez confirmer votre adresse email en cliquant sur le lien suivant :\n\n"
                      . "$confirmation_link\n\n"
                      . "Cordialement,\nLycee Charles Poncet.";

        $mail->send();

        $update_sql = "UPDATE User SET Mail_envoye = 1 WHERE Email = :email";
        $update_stmt = $pdo->prepare($update_sql);
        $update_stmt->execute([':email' => $email]);

        echo "✅ Inscription réussie ! Un email de confirmation vous a été envoyé.";
    } catch (Exception $e) {
        echo "❌ Erreur lors de l'envoi de l'email : " . $mail->ErrorInfo;
    }
} catch (PDOException $e) {
    echo "❌ Erreur lors de l'inscription : " . $e->getMessage();
}
?>
