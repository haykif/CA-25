<?php
    require_once "database.php";
    $query = "SELECT * FROM Acces_log";
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
            <li><a href="#">Gestion des accès</a></li>
            <li><a href="./inscriptionAdmin.php">Inscription admin</a></li>
            <li><a href="../html/formulaire.html">Formulaire</a></li>
            <li><a href="./logs.php">Logs</a></li>
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
                    <th>Date Heure Sortie</th>
                    <th>ID Utilisateur</th>
                </tr>
            </thead>
            <tbody id="activity-log">
                <?php
                    // Boucle pour afficher chaque ligne de résultat dans le tableau
                    while ($row = $stmt->fetch()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['idAcces']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Date_heure_entree']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Date_heure_sortie']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['IdUser']) . "</td>";
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>
    </div>

</body>

</html>
