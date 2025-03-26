<?php
    session_start();
    session_destroy();
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Déconnexion...</title>
        <meta http-equiv="refresh" content="3;url=./login.php">
        <link rel="stylesheet" href="../css/logout.css">
        <link rel="shortcut icon" href="../assets/favicon.ico" type="image/x-icon">
    </head>
    
    <body>
        <div class="container">
            <h2>Déconnexion réussie! <br>Vous allez être redirigé vers la page de connexion...</h2>
        </div>
    </body>
</html>