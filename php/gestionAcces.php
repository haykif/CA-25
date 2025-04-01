<?php
    require_once "database.php";
    session_start();
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        header("Location: ./login.php"); // Redirige vers la page de connexion
        exit();
    }
    
    // On filtre sur Mail_verif = 1 dès la requête
    $query = "SELECT * FROM User WHERE Fonction != 'Admin' AND Mail_verif = 1 ORDER BY Nom";
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
                <li><a href="../html/formulaire.html">Formulaire</a></li>
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
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody id="activity-log">
                    <?php
                        // Boucle pour afficher chaque ligne de résultat dans le tableau
                        while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
                            echo "<tr>";
                            echo "<td>" . strtoupper(htmlspecialchars($row['Nom'] ?? '')) . "</td>";
                            echo "<td>" . ucfirst(strtolower(htmlspecialchars($row['Prenom'] ?? ''))) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Email'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row['Tel'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row['Motif'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars((new DateTime(explode(' ', $row['Date_debut'])[0]))->format('d-m-Y') ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars((new DateTime(explode(' ', $row['Date_fin'])[0]))->format('d-m-Y') ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row['idCarte'] ?? '') . "</td>";
                            echo "<td>";
                            // Si Verifier est NULL, affiche les boutons
                            if (is_null($row['Verifier'])) {
                                // Bouton accepter
                                echo '<form action="bouton.php" method="post" style="display:inline;">';
                                echo '<input type="hidden" name="userId" value="' . htmlspecialchars(isset($row['idCarte']) ? $row['idCarte'] : '') . '">';
                                echo '<input type="hidden" name="action" value="donner">';
                                echo '<input type="hidden" name="Nom" value="' . htmlspecialchars($row['Nom']) . '">';
                                echo '<input type="hidden" name="Prenom" value="' . htmlspecialchars($row['Prenom']) . '">';
                                echo '<input type="hidden" name="Email" value="' . htmlspecialchars($row['Email']) . '">';
                                echo '<button class="valider-btn" type="submit">Donner Accès</button>';
                                echo '</form>';
                                
                                // Bouton refuser
                                echo '<form action="bouton.php" method="post" style="display:inline; margin-left:5px;">';
                                echo '<input type="hidden" name="userId" value="' . htmlspecialchars(isset($row['idCarte']) ? $row['idCarte'] : '') . '">';
                                echo '<input type="hidden" name="action" value="refuser">';
                                echo '<input type="hidden" name="Nom" value="' . htmlspecialchars($row['Nom']) . '">';
                                echo '<input type="hidden" name="Prenom" value="' . htmlspecialchars($row['Prenom']) . '">';
                                echo '<input type="hidden" name="Email" value="' . htmlspecialchars($row['Email']) . '">';
                                echo '<button class="refuser-btn" type="submit">Refuser</button>';
                                echo '</form>';
                            }

                            if (($row['Verifier'] === 1))
                            {
                                echo '<form action="bouton.php" method="post" style="display:inline;">';
                                echo '<input type="hidden" name="Email" value="' . htmlspecialchars($row['Email']) . '">';
                                echo '<input type="hidden" name="Nom" value="' . htmlspecialchars($row['Nom']) . '">';
                                echo '<input type="hidden" name="action" value="supprimer">';
                                echo '<button class="supprimer-btn" type="submit">Supprimer Accès</button>';
                                echo '</form>';
                            }
                            
                            echo "</td>";
                            echo "</tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
        <script src="../js/donnerAcces.js"></script>
    </body>
</html>