<?php
session_start();
include 'config.php'; // Inclusion du fichier de configuration pour la connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['login']; // Récupération du login depuis le formulaire
    $pass = $_POST['password']; // Récupération du mot de passe depuis le formulaire


    // SQL query to check login information for Administration
    $query = "SELECT Login FROM `Administration` WHERE Login='$user' AND Mdp='$pass'";
    $result = mysqli_query($conn, $query); // Exécution de la requête
    $result = mysqli_fetch_array($result); // Récupération du résultat


    if ($result && $result['Login'] == $user) {
        $_SESSION['login'] = $user; // Stockage du login dans la session
        header('Location: ajout_suppr_capt.php'); // Redirection vers la page d'ajout/suppression des capteurs
        exit(); // Assurez-vous de sortir après la redirection
    } else {
        
        // SQL query to check login information for Batiment
        $query = "SELECT GestioLog FROM `Batiment` WHERE GestioLog='$user' AND MdpGestio='$pass'";
        $result = mysqli_query($conn, $query); // Exécution de la requête
        $result = mysqli_fetch_array($result); // Récupération du résultat

        if ($result && $result['GestioLog'] === $user) {
            $_SESSION['login'] = $user; // Stockage du login dans la session
            header('Location: tableau_gestionaire.php'); // Redirection vers la page de gestion
            exit(); // Assurez-vous de sortir après la redirection
        } else {
            header('Location: connexion.php?login_err=already'); // Redirection vers la page de connexion avec message d'erreur
            exit(); // Assurez-vous de sortir après la redirection
        }
    }
}
?>