<?php
    require_once './database.php'; // Connexion à la base de données

    // Récupérer les paramètres depuis l'URL
    $email = $_GET['email'] ?? '';
    $token = $_GET['token'] ?? '';

    // Vérifier que les paramètres existent
    if (empty($email) || empty($token)) {
        echo "Lien de vérification invalide.";
        exit;
    }

    // Vérifier que l'utilisateur existe avec cet email et ce token
    $stmt = $pdo->prepare("SELECT * FROM User WHERE Email = :email AND token = :token");
    $stmt->execute([':email' => $email, ':token' => $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "Lien de vérification invalide ou déjà utilisé.";
        exit;
    }

    // Mettre à jour la colonne Mail_verif à 1
    // Mettre à jour la colonne Mail_verif à 1 et date_demande à l'heure actuelle
    $update = $pdo->prepare("
        UPDATE User 
        SET Mail_verif = 1, date_demande = NOW()
        WHERE Email = :email AND token = :token
    ");
    if ($update->execute([':email' => $email, ':token' => $token])) {
        echo "Votre adresse email a été vérifiée avec succès !";
    } else {
        echo "Erreur lors de la vérification. Veuillez réessayer.";
    }
?>
