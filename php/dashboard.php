<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="shortcut icon" href="../assets/favicon.ico" type="image/x-icon">
</head>

<body>

    <?php
        require_once "database.php";
        session_start();
        if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
            header("Location: ./login.php"); // Redirige vers la page de connexion
            exit();
        }

        // Exécution de la requête pour récupérer les données
        $query = "SELECT * FROM Acces_log";
        $stmt  = $pdo->query($query);
    ?>

    <div class="sidebar">
        <h2>Admin Dashboard</h2>
        <ul>
            <li><a href="./dashboard.php">Tableau de bord</a></li>
            <li><a href="#">Gestion des accès</a></li>
            <li><a href="./inscriptionAdmin.php">Inscription admin</a></li>
            <li><a href="./formulaire.php">Formulaire</a></li>
            <li><a href="./logs.php">Logs</a></li>
        </ul>
        <div class="logout">
            <a href="./logout.php">Déconnexion</a>
        </div>
    </div>

    <div class="main-content">
        <h1>Bienvenue, Admin</h1>
        <div class="cards">
            <div class="card">
                <h3>Porte</h3>
                <p>État: <span id="door-status">Fermée</span></p>
            </div>
            <div class="card">
                <h3>Présence</h3>
                <p>Détectée: <span id="presence-status">Non</span></p>
            </div>
            <div class="card">
                <h3>Accès Autorisés</h3>
                <p id="authorized-count">0</p>
            </div>
        </div>

        <h2>Dernières Activités</h2>
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

    <script src="../js/dashboard.js"></script>
</body>

</html>