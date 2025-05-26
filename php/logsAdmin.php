<?php
    require_once "database.php";
    session_start();
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        header("Location: ./login.php"); // Redirige vers la page de connexion
        exit();
    }

    $query = "SELECT 
        Connect_log_admin.HeureConnexion AS HeureConnexion, 
        User.Identifiant AS Identifiant, 
        User.Prenom AS Prenom, 
        User.Nom AS Nom
    FROM Connect_log_admin 
    INNER JOIN User ON Connect_log_admin.idAdmin = User.idUser 
    ORDER BY HeureConnexion DESC";
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
                <li><a href="./logsAdmin.php">Historique connexion admin</a></li>
                <li><a href="./logsServer.php">Historique des accès</a></li>
            </ul>

            <div class="logout">
                <a href="./logout.php">Déconnexion</a>
            </div>
        </div>

        <div class="main-content">
            <h1>Dernières Connexions</h1>

            <table>
                <thead>
                    <tr>
                        <th>Identifiant du Superutilisateur</th>
                        <th>Prénom du Superutilisateur</th>
                        <th>Nom du Superutilisateur</th>
                        <th>Date et Heure de connexion</th>
                    </tr>
                </thead>

                <tbody id="activity-log">
                    <?php
                        // Boucle pour afficher chaque ligne de résultat dans le tableau
                        while ($row = $stmt->fetch()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['Identifiant'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row['Prenom'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row['Nom'] ?? '') . "</td>";
                            echo "<td>" . ($row['HeureConnexion'] ?? '' ? htmlspecialchars(date("d-m-Y H:i:s", strtotime($row['HeureConnexion']))) : '') . "</td>";
                            echo "</tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </body>
</html>