<?php
    session_start(); // Démarrage de la session / Start the session
    session_destroy(); // Destruction de la session / Destroy the session
    header('Location:connexion.php'); // Redirection vers la page de connexion / Redirect to the login page
?>
