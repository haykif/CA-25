<?php
    require_once "database.php";
    session_start();
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        header("Location: ./login.php"); // Redirige vers la page de connexion
        exit();
    }

    $query = "SELECT * FROM Acces_log ORDER BY Date_heure_entree DESC";
    $stmt = $pdo->query($query);
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
                <li><a href="./logsAdmin.php">Logs admin</a></li>
                <li><a href="./logsServer.php">Logs serveur</a></li>
            </ul>

            <div class="logout">
                <a href="./logout.php">Déconnexion</a>
            </div>
        </div>

        <div class="main-content">
            <h1>Dernières Activités</h1>

            <table>
                <thead>
                    <tr>
                        <th>ID Accès</th>
                        <th>Date Heure Entrée</th>
                        <th>Tentative</th>
                        <th>Date Heure Sortie</th>
                        <th>UID</th>
                        <th>ID Utilisateur</th>
                    </tr>
                </thead>

                <tbody id="activity-log">
                    <?php
                        // Boucle pour afficher chaque ligne de résultat dans le tableau
                        while ($row = $stmt->fetch()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['idAcces'] ?? '') . "</td>";
                            echo "<td>" . ($row['Date_heure_entree'] ?? '' ? htmlspecialchars(date("d-m-Y H:i:s", strtotime($row['Date_heure_entree']))) : '') . "</td>";
                            echo "<td>" . htmlspecialchars($row['Resultat_tentative'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars(isset($row['Date_heure_sortie']) && $row['Date_heure_sortie'] ? date("d-m-Y H:i:s", strtotime($row['Date_heure_sortie'])) : '') . "</td>";
                            $uid_dec = $row['UID'] ?? 0;
                            $uid_hex = strtoupper(dechex($uid_dec));
                            echo "<td title='$uid_dec'>" . htmlspecialchars($uid_hex) . "</td>";
                            echo "<td>" . htmlspecialchars($row['IdUser'] ?? '') . "</td>";
                            echo "</tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </body>
</html>