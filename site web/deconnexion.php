<?php
    session_start(); // Start the session
    session_destroy(); // Destroy the session
    header('Location:connexion.php'); // Redirect to the login page
?>