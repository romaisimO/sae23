<?php
session_start(); // Start session
include 'config.php'; // Inclusion of configuration file for database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['login']; // Recovering the login from the form
    $pass = $_POST['password']; // Recover password from form

    // SQL query to check login information for Administration
    $query = "SELECT Login FROM `Administration` WHERE Login='$user' AND Mdp='$pass'";
    $result = mysqli_query($conn, $query); // Execute the query
    $result = mysqli_fetch_array($result); // Fetch the result

    if ($result && $result['Login'] == $user) {
        $_SESSION['login'] = $user; // Store the login in the session
        header('Location: ajout_suppr_capt.php'); // Redirect to the sensor add/delete page
        exit(); // Ensure to exit after redirection
    } else {
        // SQL query to check login information for Batiment
        $query = "SELECT GestioLog FROM `Batiment` WHERE GestioLog='$user' AND MdpGestio='$pass'";
        $result = mysqli_query($conn, $query); // Execute the query
        $result = mysqli_fetch_array($result); // Fetch the result

        if ($result && $result['GestioLog'] === $user) {
            $_SESSION['login'] = $user; // Store the login in the session
            header('Location: tableau_gestionaire.php'); // Redirect to the management page
            exit(); // Ensure to exit after redirection
        } else {
            header('Location: connexion.php?login_err=already'); // Redirect to the login page with an error message
            exit(); // Ensure to exit after redirection
        }
    }
}
?>