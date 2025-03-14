<?php
require_once "database.php";

$userId = $_POST['userId'] ?? '';
$action = $_POST['action'] ?? '';

if ($userId !== '') {
    // Accès autorisé
    if ($action === 'donner') {
        $query = "UPDATE User SET Verifier = 1 WHERE idCarte = :userId";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
        if ($stmt->execute()) {
            echo "Accès donné avec succès pour l'utilisateur ayant l'ID : " . htmlspecialchars($userId);
        } else {
            echo "Erreur lors de la mise à jour.";
        }
    }
    // Accès refusé
    elseif ($action === 'refuser') {
        $query = "DELETE FROM User WHERE idCarte = :userId";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
        if ($stmt->execute()) {
            echo "L'utilisateur avec l'ID " . htmlspecialchars($userId) . " a été supprimé avec succès.";
        } else {
            echo "Erreur lors de la suppression de l'utilisateur.";
        }
    }
    else {
        echo "Action non valide.";
    }
} else {
    echo "Aucun identifiant utilisateur fourni.";
}
?>
