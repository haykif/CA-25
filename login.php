<?php
session_start(); // Toujours démarrer la session en premier

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Ici, tu peux mettre ta logique de vérification (par exemple, avec une base de données).
    // Pour cet exemple, on va tester avec des valeurs statiques.
    if ($username === "admin" && $password === "secret") {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header("Location: dashboard.php"); // Redirection vers le dashboard
        exit(); // Toujours exit après une redirection
    } else {
        echo "Identifiants incorrects";
    }
} else {
    echo "Veuillez remplir tous les champs.";
}
?>
