<?php
session_start(); // Démarrage de la session
include 'config.php'; // Inclusion du fichier de configuration pour la connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['login']; // Récupération du login depuis le formulaire
    $pass = $_POST['password']; // Récupération du mot de passe depuis le formulaire

    // Requête SQL pour vérifier les informations de connexion pour l'Administration
    // SQL query to check login information for Administration
    $query = "SELECT Login FROM `Administration` WHERE Login='$user' AND Mdp='$pass'";
    $result = mysqli_query($conn, $query); // Exécution de la requête / Execute the query
    $result = mysqli_fetch_array($result); // Récupération du résultat / Fetch the result

    if ($result && $result['Login'] == $user) {
        $_SESSION['login'] = $user; // Stockage du login dans la session / Store the login in the session
        header('Location: ajout_suppr_capt.php'); // Redirection vers la page d'ajout/suppression des capteurs / Redirect to the sensor add/delete page
        exit(); // Assurez-vous de sortir après la redirection / Ensure to exit after redirection
    } else {
        // Requête SQL pour vérifier les informations de connexion pour Batiment
        // SQL query to check login information for Batiment
        $query = "SELECT GestioLog FROM `Batiment` WHERE GestioLog='$user' AND MdpGestio='$pass'";
        $result = mysqli_query($conn, $query); // Exécution de la requête / Execute the query
        $result = mysqli_fetch_array($result); // Récupération du résultat / Fetch the result

        if ($result && $result['GestioLog'] === $user) {
            $_SESSION['login'] = $user; // Stockage du login dans la session / Store the login in the session
            header('Location: tableau_gestionaire.php'); // Redirection vers la page de gestion / Redirect to the management page
            exit(); // Assurez-vous de sortir après la redirection / Ensure to exit after redirection
        } else {
            header('Location: connexion.php?login_err=already'); // Redirection vers la page de connexion avec message d'erreur / Redirect to the login page with an error message
            exit(); // Assurez-vous de sortir après la redirection / Ensure to exit after redirection
        }
    }
}
?>
