<?php
    require_once "database.php";
    session_start();
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        header("Location: ./login.php"); // Redirige vers la page de connexion
        exit();
    }

    $queryAuthorized = "SELECT * FROM User WHERE Fonction != 'Admin' AND Mail_verif = 1 AND Verifier = 1 ORDER BY Nom";
    $stmtAuthorized = $pdo->query($queryAuthorized);

    $queryAdminAuthorized = "SELECT * FROM User WHERE Fonction = 'Admin' ORDER BY Nom";
    $stmtAdminAuthorized = $pdo->query($queryAdminAuthorized);
    
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Gestion des accès</title>
        <link rel="stylesheet" href="../css/accesAutorise.css">
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
            <h1>Accès Autorisées</h1>

            <h2>Employés</h2>

            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Fonction</th>
                        <th>Date de début</th>
                        <th>Date de fin</th>
                        <th>ID Carte</th>
                    </tr>
                </thead>

                <tbody id="activity-log">
                    <?php
                        while ($row = $stmtAuthorized->fetch()) {
                            echo "<tr>";
                                echo "<td>" . strtoupper(htmlspecialchars($row['Nom'] ?? '')) . "</td>";
                                echo "<td>" . ucfirst(strtolower(htmlspecialchars($row['Prenom'] ?? ''))) . "</td>";
                                echo "<td>" . htmlspecialchars($row['Email'] ?? '') . "</td>";
                                echo "<td>" . "0" . htmlspecialchars($row['Tel'] ?? '') . "</td>";
                                echo "<td>" . htmlspecialchars($row['Fonction'] ?? '') . "</td>";
                                echo "<td>" . htmlspecialchars((new DateTime(explode(' ', $row['Date_debut'])[0]))->format('d-m-Y') ?? '') . "</td>";
                                echo "<td>" . htmlspecialchars((new DateTime(explode(' ', $row['Date_fin'])[0]))->format('d-m-Y') ?? '') . "</td>";
                                echo "<td>" . htmlspecialchars($row['idCarte'] ?? '') . "</td>";
                            echo "</tr>";
                        }
                    ?>
                </tbody>
            </table>

            <h2>Administrateur</h2>

            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                    </tr>
                </thead>
                
                <tbody id="activity-log">
                    <?php
                        function formatDate($dateStr) {
                            if (!empty($dateStr)) {
                                return htmlspecialchars((new DateTime(explode(' ', $dateStr)[0]))->format('d-m-Y'));
                            }
                            return '';
                        }
                        
                        while ($row = $stmtAdminAuthorized->fetch()) {
                            echo "<tr>";
                                echo "<td>" . strtoupper(htmlspecialchars($row['Nom'] ?? '')) . "</td>";
                                echo "<td>" . ucfirst(strtolower(htmlspecialchars($row['Prenom'] ?? ''))) . "</td>";
                                echo "<td>" . htmlspecialchars($row['Email'] ?? '') . "</td>";
                                echo "<td>" . "0" . htmlspecialchars($row['Tel'] ?? '') . "</td>";
                            echo "</tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </body>
</html>