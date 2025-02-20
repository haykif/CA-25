<?php
    session_start();
    session_destroy();
    header("Location: ./login.php");
    exit();
?>

<!DOCTYPE html>
<html lang="fr">

    <head>
        <meta charset="UTF-8">
        <title>Admin logged out</title>
        <link rel="stylesheet" href="../css/logoutAdmin.css">
    </head>

    <body>

    </body>
</html>