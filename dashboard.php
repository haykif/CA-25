<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Contrôle d'Accès</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        * {
            scroll-behavior: smooth;
        }

        body {
            display: flex;
            font-family: 'Poppins', sans-serif;
            background: url('./image/professional_website_background.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
            margin: 0;
        }
        .sidebar {
            width: 250px;
            background: rgba(44, 62, 80, 0.9);
            color: white;
            padding: 20px;
            position: fixed;
            height: 100%;
            box-shadow: 4px 0px 10px rgba(0, 0, 0, 0.2);
        }
        .sidebar h2 {
            text-align: left;
            font-size: 1.5rem;
            margin-left: 10px;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            margin: 20px 0;
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            font-size: 1.1rem;
            display: block;
            padding: 10px;
            border-radius: 5px;
            transition: 0.3s;
        }
        .sidebar ul li a:hover {
            background: #34495e;
        }
        .main-content {
            margin-left: 310px;
            margin-right: 20px;
            margin-top: 20px;
            padding: 20px;
            width: calc(100% - 270px);
            backdrop-filter: blur(8px);
            background: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
        }

        .cards {
            display: flex;
            gap: 20px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            flex: 1;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }
        th {
            background: #2c3e50;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Admin Dashboard</h2>
        <ul>
            <li><a href="#">Tableau de bord</a></li>
            <li><a href="#">Gestion des accès</a></li>
            <li><a href="#">Formulaire</a></li>
            <li><a href="#">Logs</a></li>
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
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let doorStatus = document.getElementById("door-status");
            let presenceStatus = document.getElementById("presence-status");
            let authorizedCount = document.getElementById("authorized-count");
            
            setTimeout(() => {
                doorStatus.textContent = "Ouverte";
                presenceStatus.textContent = "Oui";
                authorizedCount.textContent = "5";
            }, 2000);
        });
    </script>
</body>
</html>
