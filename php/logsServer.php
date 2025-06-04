<?php
    require_once "database.php";
    session_start();
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        header("Location: ./login.php"); // Redirige vers la page de connexion
        exit();
    }

    // Ajout de débogage
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    try {
        // Vérifier la structure de la table User
        $query = "SHOW COLUMNS FROM User";
        $stmt = $pdo->query($query);
        error_log("Structure de la table User :");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            error_log(print_r($row, true));
        }

        // Requête principale modifiée
        $query = "SELECT a.*, u.Nom, u.Prenom 
                  FROM Acces_log a 
                  LEFT JOIN User u ON a.IdUser = u.idUser 
                  ORDER BY a.Date_heure_entree DESC";
        
        $stmt = $pdo->query($query);
        
    } catch (PDOException $e) {
        error_log("Erreur SQL : " . $e->getMessage());
    }
    
    if (isset($_POST['clear_logs'])) {
        $stmtClear = $pdo->prepare("DELETE FROM Acces_log");
        $stmtClear->execute();
    }
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Logs</title>
        <link rel="stylesheet" href="../css/logs.css">
        <link rel="shortcut icon" href="../assets/favicon.ico" type="image/x-icon">
    </head>

    <body>
        <div class="sidebar">
            <h2>Admin Dashboard</h2>
            
            <ul>
                <li><a href="./dashboard.php">Tableau de bord</a></li>
                <li><a href="./gestionAcces.php">Gestion des accès</a></li>
                <li><a href="./inscriptionAdmin.php">Inscription admin</a></li>
                <li><a href="../html/formulaire.html">Formulaire</a></li>
                <li><a href="./logsAdmin.php">Historique connexion admin</a></li>
                <li><a href="./logsServer.php">Historique des accès</a></li>
            </ul>

            <div class="logout">
                <a href="./logout.php">Déconnexion</a>
            </div>
        </div>

        <div class="main-content">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h1>Dernières Activités</h1>
                <form method="POST" onsubmit="return confirm('⚠️ Tu es sûr de vouloir tout effacer ?');">
                    <button type="submit" name="clear_logs" style="background-color: #c0392b; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">
                        🗑 Effacer le journal
                    </button>
                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID Accès</th>
                        <th>Date Heure Entrée</th>
                        <th>Tentative</th>
                        <th>Date Heure Sortie</th>
                        <th>UID</th>
                        <th>Utilisateur</th>
                    </tr>
                </thead>

                <tbody id="activity-log">
                    <?php
                        // Boucle pour afficher chaque ligne de résultat dans le tableau
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['idAcces'] ?? '') . "</td>";
                            echo "<td>" . ($row['Date_heure_entree'] ?? '' ? htmlspecialchars(date("d-m-Y H:i:s", strtotime($row['Date_heure_entree']))) : '') . "</td>";
                            echo "<td>" . htmlspecialchars($row['Resultat_tentative'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars(isset($row['Date_heure_sortie']) && $row['Date_heure_sortie'] ? date("d-m-Y H:i:s", strtotime($row['Date_heure_sortie'])) : '') . "</td>";
                            $uid_dec = intval($row['UID'] ?? 0);
                            $uid_hex = strtoupper(dechex($uid_dec));
                            echo "<td title='$uid_dec'>" . htmlspecialchars($uid_hex) . "</td>";
                            echo "<td>" . htmlspecialchars(($row['Prenom'] ?? '') . ' ' . ($row['Nom'] ?? '')) . "</td>";
                            echo "</tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </body>
</html>
