<?php
    require_once "database.php";
    session_start();
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        header("Location: ./login.php"); // Redirige vers la page de connexion
        exit();
    }
    
    // On filtre sur Mail_verif = 1 dès la requête
    $queryAutorisee = "SELECT * FROM User WHERE Fonction != 'Admin' AND Mail_verif = 1 AND Verifier = 1 ORDER BY Nom";
    $stmtAutorisee = $pdo->query($queryAutorisee);

    // On filtre sur Mail_verif = 1 dès la requête
    $queryDemande = "SELECT * FROM User WHERE Fonction != 'Admin' AND Mail_verif = 1 AND Verifier IS NULL ORDER BY Nom";
    $stmtDemande = $pdo->query($queryDemande);
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
                <li><a href="./logsAdmin.php">Historique connexion admin</a></li>
                <li><a href="./logsServer.php">Historique des accès</a></li>
            </ul>

            <div class="logout">
                <a href="./logout.php">Déconnexion</a>
            </div>
        </div>

        <div class="main-container">
            <div class="main-content">
                <h1>Gestion des accès</h1>
                <h2>Accès autorisés</h2>

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
                            while (($row = $stmtAutorisee->fetch(PDO::FETCH_ASSOC)) !== false) {
                                echo "<tr>";
                                echo "<td>" . strtoupper(htmlspecialchars($row['Nom'] ?? '')) . "</td>";
                                echo "<td>" . ucfirst(strtolower(htmlspecialchars($row['Prenom'] ?? ''))) . "</td>";
                                echo "<td>" . htmlspecialchars($row['Email'] ?? '') . "</td>";
                                echo "<td>" . "0" . htmlspecialchars($row['Tel'] ?? '') . "</td>";
                                echo "<td>" . htmlspecialchars($row['Motif'] ?? '') . "</td>";
                                echo "<td>" . htmlspecialchars((new DateTime(explode(' ', $row['Date_debut'])[0]))->format('d-m-Y') ?? '') . "</td>";
                                echo "<td>" . htmlspecialchars((new DateTime(explode(' ', $row['Date_fin'])[0]))->format('d-m-Y') ?? '') . "</td>";
                                echo "<td>" . htmlspecialchars($row['idCarte'] ?? '') . "</td>";
                                echo "<td>";

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

            <div class="main-content" id="demande-section">
                <h2>Demande en attente de traitement</h2>

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
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody id="activity-log">
                        <?php
                            // Boucle pour afficher chaque ligne de résultat dans le tableau
                            while (($row = $stmtDemande->fetch(PDO::FETCH_ASSOC)) !== false) {
                                echo "<tr>";
                                echo "<td>" . strtoupper(htmlspecialchars($row['Nom'] ?? '')) . "</td>";
                                echo "<td>" . ucfirst(strtolower(htmlspecialchars($row['Prenom'] ?? ''))) . "</td>";
                                echo "<td>" . htmlspecialchars($row['Email'] ?? '') . "</td>";
                                echo "<td>" . "0" . htmlspecialchars($row['Tel'] ?? '') . "</td>";
                                echo "<td>" . htmlspecialchars($row['Motif'] ?? '') . "</td>";
                                echo "<td>" . htmlspecialchars((new DateTime(explode(' ', $row['Date_debut'])[0]))->format('d-m-Y') ?? '') . "</td>";
                                echo "<td>" . htmlspecialchars((new DateTime(explode(' ', $row['Date_fin'])[0]))->format('d-m-Y') ?? '') . "</td>";
                                echo "<td>";
                                
                                // Si Verifier est NULL, affiche les boutons
                                if (is_null($row['Verifier'])) {
                                    // Bouton accepter
                                    echo '<form onsubmit="event.preventDefault(); openModal(this);" method="post" style="display:inline;">';
                                    echo '<input type="hidden" name="userId" value="' . htmlspecialchars($row['idCarte'] ?? '') . '">';
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
                                
                                echo "</td>";
                                echo "</tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="modalPython" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close-btn" onclick="closeModal()">&times;</span>
                
                <p>Veuillez lancer l'exécutable Python et scanner la carte.<br>
                Ensuite, chargez le fichier JSON généré :</p>
                
                <input type="file" id="jsonFileInput" accept=".json">
                <button onclick="submitJson()">Valider UID</button>

                <hr style="margin: 20px 0;">

                <p style="margin-bottom: 10px;">Vous n'avez pas encore l'application ?</p>

                <div class="download-section" style="display: flex; justify-content: center; align-items: center; gap: 20px;">
                    <a id="download-mac" href="../downloader/macos/UIDScanner Installer.dmg" download>
                        <img src="../assets/dl-for-macos.png" alt="Télécharger pour macOS" style="height:50px;">
                    </a>
                    <a id="download-win" href="../downloader/windows/UIDScanner_Installer.exe" download>
                        <img src="../assets/dl-for-windows.png" alt="Télécharger pour Windows" style="height:50px;">
                    </a>
                </div>
            </div>
        </div>


        <script src="../js/modal.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const tableauVide = document.querySelector('#demande-section tbody');
                if (!tableauVide || tableauVide.children.length === 0) {
                    document.getElementById("demande-section").style.display = "none";
                }
            });
        </script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const platform = navigator.platform.toLowerCase();
                const mac = document.getElementById("download-mac");
                const win = document.getElementById("download-win");

                if (platform.includes("mac")) {
                    win.style.display = "none";
                } else if (platform.includes("win")) {
                    mac.style.display = "none";
                }
            });
        </script>
    </body>
</html>