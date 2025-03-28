<?php
    require_once "database.php";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'] ?? '';
        $uid = $_POST['uid'] ?? '';

        if (!empty($email) && !empty($uid)) {
            try {
                // Insertion dans Carte
                $stmt = $pdo->prepare("INSERT INTO Carte (idCarte) VALUES (:uid)");
                $stmt->execute(['uid' => $uid]);

                // Mise à jour de l'utilisateur
                $stmt = $pdo->prepare("UPDATE User SET idCarte = :uid WHERE Email = :email");
                $stmt->execute(['uid' => $uid, 'email' => $email]);

                echo json_encode(['status' => 'success', 'message' => 'Carte ajoutée']);
            } catch (PDOException $e) {
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'uid ou email manquant']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Requête non autorisée']);
    }
?>