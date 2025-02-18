<!DOCTYPE html>

<html lang="fr">
    

<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php"); // redirige vers la page de connexion
    exit();
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Contrôle d'Accès</title>
    <link rel="stylesheet" href="./css/dashboard.css">
</head>
<body>
    <div class="sidebar">
        <h2>Admin Dashboard</h2>
        <ul>
            <li><a href="#">Tableau de bord</a></li>
            <li><a href="#">Gestion des accès</a></li>
            <li><a href="#">Formulaire</a></li>
            <li><a href="#">Logs</a></li>
            <li><a href="./inscriptionAdmin.php">Inscription admin</a></li>
        </ul>
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
                    <th>Nom</th>
                    <th>Heure</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody id="activity-log">
                <tr>
                    <td>Jean Dupont</td>
                    <td>14:30</td>
                    <td>Accès autorisé</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <script src="./js/dashboard.js"></script>
</body>
</html>
