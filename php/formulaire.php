<?php
require_once './database.php'; // Connexion à la base de données

// Récupérer et nettoyer les données du formulaire
$nom       = trim($_POST['nom'] ?? '');
$prenom    = trim($_POST['prenom'] ?? '');
$fonction  = trim($_POST['fonction'] ?? '');
$motif     = trim($_POST['motif'] ?? '');
$telephone = trim($_POST['telephone'] ?? '');
$email     = trim($_POST['email'] ?? '');
$date_debut_input = $_POST['date-debut'] ?? '';
$date_fin_input   = $_POST['date-fin'] ?? '';

// Validation des dates avec le format attendu (YYYY-MM-DD)
$date_debut = DateTime::createFromFormat('Y-m-d', $date_debut_input);
$date_fin   = DateTime::createFromFormat('Y-m-d', $date_fin_input);

// Si la date n'est pas valide, on peut soit afficher une erreur soit définir une valeur par défaut (ici NULL)
$date_debut = $date_debut ? $date_debut->format('Y-m-d') : null;
$date_fin   = $date_fin ? $date_fin->format('Y-m-d') : null;

// Vérification du téléphone : s'il est vide ou non numérique, on le met à NULL
if ($telephone === '' || !is_numeric($telephone)) {
    $telephone = null;
}

// Générer un token unique
$token = bin2hex(random_bytes(50));

$mail_envoye = 0;
$mail_verif  = 0; // Email non encore vérifié



    // Envoyer l'email de confirmation
    $to = $email;
    $subject = "Confirmation de votre inscription";
    $confirmation_link = "http://172.21.1.162/php/confirmMail.php?email=" . urlencode($email) . "&token=" . $token;
    $message = "Bonjour $prenom $nom,\n\nMerci de votre inscription. Veuillez confirmer votre adresse email en cliquant sur le lien ci-dessous :\n\n$confirmation_link\n\nCordialement,\nL'équipe de validation.";
    
    $headers = "From: noreply@173.21.1.162\r\n";
    $headers .= "Reply-To: noreply@173.21.1.162\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    if (mail($to, $subject, $message, $headers)) {
            // On s'assure que la table User comporte bien les colonnes indiquées
            $sql = "INSERT INTO User (Nom, Prenom, Email, Tel, Motif, Date_debut, Date_fin, Fonction, Mail_envoye, Mail_verif, token) 
                    VALUES (:nom, :prenom, :email, :telephone, :motif, :date_debut, :date_fin, :fonction, :mail_envoye, :mail_verif, :token)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nom'        => $nom,
                ':prenom'     => $prenom,
                ':email'      => $email,
                ':telephone'  => $telephone,
                ':motif'      => $motif,
                ':date_debut' => $date_debut,
                ':date_fin'   => $date_fin,
                ':fonction'   => $fonction,
                ':mail_envoye'=> $mail_envoye,
                ':mail_verif' => $mail_verif,
                ':token'      => $token
            ]);
       
            echo "Inscription réussie ! Un email de confirmation vous a été envoyé.";

        // Mettre à jour le statut de l'email envoyé dans la même table
        $update_sql = "UPDATE User SET Mail_envoye = 1 WHERE Email = :email";
        $update_stmt = $pdo->prepare($update_sql);
        $update_stmt->execute([':email' => $email]);
     else {
        echo "Erreur lors de l'envoi de l'email.";
    }
} catch (PDOException $e) {
    echo "Erreur lors de l'inscription : " . $e->getMessage();
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
