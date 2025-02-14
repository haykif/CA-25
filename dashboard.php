<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Administrateur</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #1e1e2f;
            margin: 0;
            padding: 0;
            color: #ffffff;
        }

        .header {
            background-color: #2b2b3d;
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .logout-btn {
            position: absolute;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
        }

        .logout-btn svg {
            width: 24px;
            height: 24px;
            fill: white;
        }

        .logout-btn:hover svg {
            fill: #e74c3c;
        }

        .container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px;
            padding: 0 20px;
        }

        .card {
            background: #2b2b3d;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            padding: 20px;
            text-align: left;
        }

        .card h3 {
            margin-top: 0;
            font-size: 18px;
        }

        .actions button {
            display: block;
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .actions button:hover {
            background-color: #4a4a5a;
        }

        .btn-blue {
            background-color: #3498db;
            color: white;
        }

        .btn-red {
            background-color: #e74c3c;
            color: white;
        }

        .footer {
            text-align: center;
            margin: 20px;
            font-size: 14px;
            color: #cccccc;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Bienvenue, Admin</h1>
    <button class="logout-btn" onclick="alert('Déconnexion en cours...')">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M16 13v-2H7V9l-5 3 5 3v-2h9zm3-11H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 16H5V4h14v14z"/>
        </svg>
    </button>
</div>

<div class="container">
    <!-- Informations Générales -->
    <div class="card">
        <h3>Informations Générales</h3>
        <p><strong>Nombre d'utilisateurs:</strong> 250</p>
        <p><strong>Sessions actives:</strong> 35</p>
        <p><strong>Erreurs signalées:</strong> 5</p>
    </div>

    <!-- Actions Rapides -->
    <div class="card actions">
        <h3>Actions Rapides</h3>
        <button class="btn-blue" onclick="alert('Gestion des utilisateurs')">Gérer les utilisateurs</button>
        <button class="btn-blue" onclick="alert('Affichage des logs')">Voir les logs</button>
        <button class="btn-blue" onclick="alert('Ouverture des paramètres')">Paramètres</button>
    </div>

    <!-- Statistiques -->
    <div class="card" style="grid-column: span 2; text-align: center;">
        <h3>Statistiques</h3>
        <p>[Graphiques statistiques à intégrer]</p>
    </div>
</div>

<div class="footer">
    <p>&copy; 2025 Tableau de Bord Administrateur</p>
</div>

</body>
</html>
