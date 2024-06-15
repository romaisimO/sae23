<?php
$servername = "localhost"; // MySQL server name
$username = "root"; // MySQL username
$password = "passroot"; // MySQL password
$dbname = "sae23"; // Database name

// Connect to the database
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check the connection
if (!$conn) {
    die("Échec de la connexion : " . mysqli_connect_error()); // Display an error message if the connection fails
}
?>