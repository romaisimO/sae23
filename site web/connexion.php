<!DOCTYPE html>
<html lang="fr">

<head>
    <title>SAE 23</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="author" content="DSM" />
    <meta name="description" content="SAE 23" />
    <meta name="keywords" content="HTML, CSS, Portfolio" />
    <link rel="stylesheet" href="./styles/style.css" />
    <link rel="stylesheet" href="./styles/rwd.css" />
    <link rel="stylesheet" href="./styles/style2.css" />
</head>

<body>

    <header>

        <div class="nav">
            <input type="checkbox" id="nav-check">
            <div class="nav-btn">
                <label for="nav-check">
                    <span></span>
                    <span></span>
                    <span></span>
                </label>
            </div>
            <nav class="nav-links">
                <ul>
                    <li><a href="index.html" class="first">Accueil</a></li>
                    <li><a href="consultation.php" class="first">Consultation</a></li>
                    <li><a href="connexion.php">Connexion</a></li>
                    <li><a href="gestion_de_projet.html">Gestion de projet</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <section class="sujet">
        <h1> CONNEXION </h1>
        <section class="bulle">
            <form action="login.php" method="POST">
                <p class="justify">
                    <strong>Utilisateurs</strong> <br> <br>
                </p>
                <label><b>Nom d'utilisateur</b></label><br>
                <input type="text" placeholder="Entrer le nom d'utilisateur" name="login" required><br><br><br>
                <label><b>Mot De Passe</b></label><br>
                <input type="password" placeholder="Entrer le mot de passe" name="password" required><br><br><br>
                <button type="submit" class="btn btn-primary btn-block">Connexion</button>
            </form>
        </section>
    </section>
    <footer>
        <ul class="IUT">
            <li><a href="https://www.iut-blagnac.fr/fr/" target="_blank">IUT de Blagnac</a></li>
            <li>Département Réseaux et Télécommunications</li>
            <li><a href="mentions-légales.html">Mentions légales</a></li>
        </ul>
    </footer>

</body>

</html>