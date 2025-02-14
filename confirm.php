<?php
$servername = "localhost";
$username = "root";  // Remplacez par votre utilisateur MySQL
$password = "";  // Remplacez par votre mot de passe MySQL
$dbname = "dbca25";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];

    // Vérifier si l'email est bien enregistré
    $stmt = $conn->prepare("SELECT * FROM WhiteList WHERE Email = ? AND Mail_envoye = 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Mettre à jour le statut de vérification
        $update_stmt = $conn->prepare("UPDATE WhiteList SET Mail_verifie = 1 WHERE Email = ?");
        $update_stmt->bind_param("s", $email);
        if ($update_stmt->execute()) {
            echo "Votre email a été confirmé avec succès !";
        } else {
            echo "Erreur lors de la confirmation.";
        }
        $update_stmt->close();
    } else {
        echo "Lien invalide ou email déjà vérifié.";
    }

    $stmt->close();
} else {
    echo "Lien de confirmation invalide.";
}

$conn->close();
?>
