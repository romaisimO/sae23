<?php
$servername = "localhost"; // Nom du serveur MySQL // MySQL server name
$username = "root"; // Nom d'utilisateur MySQL // MySQL username
$password = "passroot"; // Mot de passe MySQL // MySQL password
$dbname = "sae23"; // Nom de la base de données // Database name

// Connexion à la base de données // Connect to the database
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Vérifier la connexion // Check the connection
if (!$conn) {
    die("Échec de la connexion : " . mysqli_connect_error()); // Affiche un message d'erreur si la connexion échoue // Display an error message if the connection fails
}
?>
