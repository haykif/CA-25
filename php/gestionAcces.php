<?php
    require_once "database.php";
    $query = "SELECT * FROM User WHERE Fonction != 'Admin'";
    $stmt = $pdo->query($query);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Gestion des accès</title>
    <link rel="stylesheet" href="../css/gestionAcces.css">
    <link rel="shortcut icon" href="../assets/favicon.ico" type="image/x-icon">
</head>

<body>

    <div class="sidebar">
        <h2>Admin Dashboard</h2>
        <ul>
            <li><a href="./dashboard.php">Tableau de bord</a></li>
            <li><a href="./gestionAcces.php">Gestion des accès</a></li>
            <li><a href="./inscriptionAdmin.php">Inscription admin</a></li>
            <li><a href="./formulaire.php">Formulaire</a></li>
            <li><a href="./logs.php">Logs</a></li>
        </ul>
        <div class="logout">
            <a href="./logout.php">Déconnexion</a>
        </div>
    </div>

    <div class="main-content">
        <h1>Gestion des accès</h1>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Motif</th>
                    <th>Date de début</th>
                    <th>Date de fin</th>
                    <th>ID Carte</th>
                </tr>
            </thead>
            <tbody id="activity-log">
                <?php
                    // Boucle pour afficher chaque ligne de résultat dans le tableau
                    while ($row = $stmt->fetch()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['Nom']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Prenom']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Tel']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Motif']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Date_debut']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Date_fin']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['idCarte']) . "</td>";
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>
    </div>

</body>

</html>