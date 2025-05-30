<?php
require_once './database.php'; // Connexion à la base de données
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Récupérer les paramètres depuis l'URL
$email = $_GET['email'] ?? '';
$token = $_GET['token'] ?? '';

// Récupérer tous les super utilisateurs
$adminStmt = $pdo->query("SELECT Email, Prenom, Nom FROM User WHERE SuperUser = 1");
$admin = $adminStmt->fetchAll(PDO::FETCH_ASSOC);

// Vérifier que les paramètres existent
if (empty($email) || empty($token)) {
    exit("❌ Lien de vérification invalide.");
}

// Rechercher l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM User WHERE Email = :email AND token = :token");
$stmt->execute([':email' => $email, ':token' => $token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    exit("❌ Lien de vérification invalide ou déjà utilisé.");
}

// Récupérer nom et prénom depuis la base
$prenom = $user['Prenom'] ?? '';
$nom    = $user['Nom'] ?? '';
$fonction = $user['Fonction'] ?? '';
$motif    = $user['Motif'] ?? '';

// Mettre à jour l'utilisateur (confirmation du mail)
$update = $pdo->prepare("
    UPDATE User 
    SET Mail_verif = 1, date_demande = NOW()
    WHERE Email = :email AND token = :token
");

if (!$update->execute([':email' => $email, ':token' => $token])) {
    exit("❌ Erreur lors de la vérification. Veuillez réessayer.");
}

// Envoyer un mail à l'admin
try {
    $mail = new PHPMailer(true);

    // Configuration du serveur SMTP
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'carteacces99@gmail.com';
    $mail->Password   = 'llvzctlmvjasxyfq';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('carteacces99@gmail.com', 'Charles Poncet');
    foreach ($admin as $a){
        $mail->addBCC($a['Email'], $a['Prenom'] . ' ' . $a['Nom']);
    }
    $mail->isHTML(true);
    $mail->Subject = "Demande d'acces";
   $mail->isHTML(true);
    $mail->Subject = "Demande d'acces confirmee";
    $mail->Body    = "Bonjour Admin,<br><br>"
                  . "Une demande d'accès a été confirmée pour :<br>"
                  . "<strong>Nom :</strong> $prenom $nom<br>"
                  . "<strong>Fonction :</strong> $fonction<br>"
                  . "<strong>Motif :</strong> $motif<br><br>"
                  . "Cordialement,<br>Lycée Charles Poncet.";
    $mail->AltBody = "Une demande d'accès confirmée pour : $prenom $nom - Fonction : $fonction - Motif : $motif";

    $mail->send();

    echo "✅ Bonjour <strong>$prenom $nom</strong>, votre adresse email a été vérifiée avec succès !";
} catch (Exception $e) {
    echo "✅ Vérification réussie, mais erreur lors de l'envoi du mail à l'admin : {$mail->ErrorInfo}";
}

 // Suppression des utilisateurs dont la demande de vérification a expiré
$delai = 1;
$deleteExpired = $pdo->prepare("
    DELETE FROM User
    WHERE Verifier IS NULL 
      AND Mail_verif = 1 
      AND date_demande < (NOW() - INTERVAL :delai MINUTE)
");
$deleteExpired->bindValue(':delai', $delai, PDO::PARAM_INT);
$deleteExpired->execute();

?>
