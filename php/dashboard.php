<!DOCTYPE html>
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
            
            // Débogage
            error_log("Session dans dashboard : " . print_r($_SESSION, true));
            
            if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
                header("Location: ./login.php"); // Redirige vers la page de connexion
                exit();
            }

            // Récupération des informations de l'administrateur
            $adminQuery = "SELECT Nom, Prenom FROM User WHERE Identifiant = :identifiant";
            $adminStmt = $pdo->prepare($adminQuery);
            $adminStmt->execute(['identifiant' => $_SESSION['admin_id']]);
            $adminInfo = $adminStmt->fetch(PDO::FETCH_ASSOC);

            // Exécution de la requête pour récupérer les données
            $queryLogs = "SELECT a.*, u.Nom, u.Prenom 
                         FROM Acces_log a 
                         LEFT JOIN User u ON a.IdUser = u.idUser 
                         ORDER BY a.Date_heure_entree DESC LIMIT 5";
            $stmtLogs  = $pdo->query($queryLogs);
            
            // On filtre sur Mail_verif = 1 dès la requête
            $queryAcces = "SELECT * FROM User WHERE Fonction != 'Admin' AND Mail_verif = 1 AND Verifier IS NULL ORDER BY Date_debut DESC LIMIT 5";
            $stmtAcces = $pdo->query($queryAcces);

            $queryAuthorized = "SELECT COUNT(*) FROM User WHERE Fonction != 'Admin' AND Mail_verif = 1 AND Verifier = 1";
            $stmtAuthorized = $pdo->query($queryAuthorized);
            $authorizedCount = $stmtAuthorized->fetchColumn();
        ?>

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
                <h1>Bienvenue, <?php 
                    echo htmlspecialchars(($adminInfo['Prenom'] ?? 'Admin') . ' ' . ($adminInfo['Nom'] ?? '')); 
                ?></h1>

                <div class="cards">
                    <div class="card">
                        <h3>Porte</h3>
                        <p>État: <span id="door-status">Fermée</span></p>
                    </div>

                    <div class="card">
                        <h3>Présence</h3>
                        <p>Détection: <span id="presence-status">Initialisation du capteur de mouvement...</span></p>
                    </div>

                    <a href="./accesAutorise.php">
                        <div class="card">
                            <h3>Accès Autorisés</h3>
                            <p id="authorized-count"><?php echo $authorizedCount; ?></p>
                        </div>
                    </a>
                </div>
            </div>
            
            <div class="main-content">
                <h2>Dernières Activités</h2>

                <table>
                    <thead>
                        <tr>
                            <th>ID Accès</th>
                            <th>Date Heure Entrée</th>
                            <th>Date Heure Sortie</th>
                            <th>Utilisateur</th>
                            <th>Tentative d'accès</th>
                        </tr>
                    </thead>
                    
                    <tbody id="activity-log">
                        <?php
                            // Boucle pour afficher chaque ligne de résultat dans le tableau
                            while ($row = $stmtLogs->fetch()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['idAcces']) . "</td>";
                                echo "<td>" . ($row['Date_heure_entree'] ?? '' ? htmlspecialchars(date("d-m-Y H:i:s", strtotime($row['Date_heure_entree']))) : '') . "</td>";
                                echo "<td>" . htmlspecialchars(isset($row['Date_heure_sortie']) && $row['Date_heure_sortie'] ? date("d-m-Y H:i:s", strtotime($row['Date_heure_sortie'])) : '') . "</td>";
                                echo "<td>" . htmlspecialchars(($row['Prenom'] ?? '') . ' ' . ($row['Nom'] ?? '')) . "</td>";
                                echo "<td>" . htmlspecialchars($row['Resultat_tentative'] ?? '') . "</td>";
                                echo "</tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <div class="main-content">
                <h2>Demande d'autorisation</h2>

                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Fonction</th>
                            <th>Motif</th>
                            <th>Date de début</th>
                            <th>Date de fin</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody id="activity-log">
                        <?php
                            // Boucle pour afficher chaque ligne de résultat dans le tableau
                            while (($row = $stmtAcces->fetch(PDO::FETCH_ASSOC)) !== false) {
                                echo "<tr>";
                                echo "<td>" . strtoupper(htmlspecialchars($row['Nom'] ?? '')) . "</td>";
                                echo "<td>" . ucfirst(strtolower(htmlspecialchars($row['Prenom'] ?? ''))) . "</td>";
                                echo "<td>" . htmlspecialchars($row['Email'] ?? '') . "</td>";
                                echo "<td>" . "0" . htmlspecialchars($row['Tel'] ?? '') . "</td>";
                                echo "<td>" . htmlspecialchars($row['Fonction'] ?? '') . "</td>";
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

        <script src="../js/dashboard.js"></script>
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