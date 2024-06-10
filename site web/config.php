<?php
$servername = "localhost"; // MySQL server name
$username = "root"; // MySQL username
$password = "passroot"; // MySQL password
$dbname = "sae23"; // Database name

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
?>
