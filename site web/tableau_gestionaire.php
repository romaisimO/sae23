<!DOCTYPE html>
<html lang="fr">

<head>
    <title>SAE 23</title>
    <meta charset="utf-8" />
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
            <input type="checkbox" id="nav-check" />
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
    <?php
include 'config.php';

// Démarrer la session // Start the session
session_start();

if (isset($_SESSION['login'])) {
    // L'utilisateur est connecté // User is logged in
    $login = $_SESSION['login'];
} else {
    // L'utilisateur n'est pas connecté // User is not logged in
    // Rediriger vers la page de connexion // Redirect to the login page
    header('Location: connexion.php');
    exit(); // Assurez-vous de quitter après la redirection // Make sure to exit after redirect
}

// Connexion à la base de données en utilisant le style procédural // Connect to the database using procedural style
$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("La connexion à la base de données a échoué : " . mysqli_connect_error());
}

// Définir le nombre de lignes à afficher par défaut // Define default number of rows to display
$num_rows_to_display = 12; // Défaut à 12 // Set default to 12
$show_all = false;
$date = '';
$sensor = '';
$start_hour = '';
$end_hour = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtenir le nombre de lignes à afficher à partir du formulaire // Get the number of rows to display from the form input
    $num_rows_to_display = $_POST['num_rows'];
    if ($num_rows_to_display == 'all') {
        $show_all = true;
    } else {
        $num_rows_to_display = (int)$num_rows_to_display;
    }
    $date = $_POST['date'];
    $sensor = $_POST['sensor'];
    $start_hour = $_POST['start_hour'];
    $end_hour = $_POST['end_hour'];
}

// Sélectionner tous les capteurs pour le formulaire pour le gestionnaire connecté // Select all sensors for the form for the logged-in manager
$sql_sensors = "SELECT Capteur.NomCapteur 
                FROM Capteur
                JOIN Salle ON Capteur.NomSalle = Salle.NomSalle
                JOIN Batiment ON Salle.BatID = Batiment.BatID
                WHERE Batiment.GestioLog = '$login'";
$result_sensors = mysqli_query($conn, $sql_sensors);

?>

    <!DOCTYPE html>
    <html>

    <head>
        <title>SAE 23</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="author" content="DSM" />
        <meta name="description" content="SAE 23" />
        <meta name="keywords" content="HTML, CSS, PHP" />
    </head>

    <body>
        <h1>Données des capteurs</h1>

        <section class="bulle">
            <form method="post" action="">
                <label for="num_rows">Nombre de lignes à afficher:</label>
                <select name="num_rows" id="num_rows">
                    <option value="6"
                        <?php if (isset($_POST['num_rows']) && $_POST['num_rows'] == 6) echo 'selected'; ?>>6
                    </option>
                    <option value="12"
                        <?php if ((isset($_POST['num_rows']) && $_POST['num_rows'] == 12) || !isset($_POST['num_rows'])) echo 'selected'; ?>>
                        12</option>
                    <option value="24"
                        <?php if (isset($_POST['num_rows']) && $_POST['num_rows'] == 24) echo 'selected'; ?>>
                        24</option>
                    <option value="48"
                        <?php if (isset($_POST['num_rows']) && $_POST['num_rows'] == 48) echo 'selected'; ?>>
                        48</option>
                    <option value="all"
                        <?php if (isset($_POST['num_rows']) && $_POST['num_rows'] == 'all') echo 'selected'; ?>>Tout
                        afficher</option>
                </select><br>

                <label for="date">Sélectionner la date:</label>
                <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>"><br>

                <label for="sensor">Sélectionner le capteur:</label>
                <select name="sensor" id="sensor">
                    <option value="">Tous les capteurs</option>
                    <?php
                while ($row = mysqli_fetch_assoc($result_sensors)) {
                    echo "<option value='" . $row['NomCapteur'] . "'" . ($sensor == $row['NomCapteur'] ? ' selected' : '') . ">" . $row['NomCapteur'] . "</option>";
                }
                ?>
                </select><br>

                <label for="start_hour">Sélectionner l'heure de début:</label>
                <input type="time" id="start_hour" name="start_hour"
                    value="<?php echo htmlspecialchars($start_hour); ?>"><br>

                <label for="end_hour">Sélectionner l'heure de fin:</label>
                <input type="time" id="end_hour" name="end_hour" value="<?php echo htmlspecialchars($end_hour); ?>"><br>

                <input type="submit" value="Afficher">
            </form>
        </section>

        <section class="bulle">
            <table id="data-table">
                <?php
            // Requête SQL pour récupérer les mesures pour un utilisateur spécifique // SQL query to retrieve measurements for a specific user
            $sql = "SELECT Batiment.NomBat AS Batiment, Salle.NomSalle AS Salle, Capteur.TypeCapteur AS Type, Capteur.Unite, Mesure.Date, Mesure.Horaire, Mesure.Valeur
                    FROM Capteur
                    JOIN Mesure ON Capteur.NomCapteur = Mesure.NomCapteur
                    JOIN Salle ON Capteur.NomSalle = Salle.NomSalle
                    JOIN Batiment ON Salle.BatID = Batiment.BatID
                    WHERE Batiment.GestioLog = '" . $login . "'";

            // Ajouter la condition de date si elle est définie // Add date condition if set
            if (!empty($date)) {
                $sql .= " AND Mesure.Date = '$date'";
            }

            // Ajouter la condition de capteur si elle est définie // Add sensor condition if set
            if (!empty($sensor)) {
                $sql .= " AND Capteur.NomCapteur = '$sensor'";
            }

            // Ajouter la condition de plage d'heures si définie // Add hour range condition if set
            if (!empty($start_hour) && !empty($end_hour)) {
                $sql .= " AND Mesure.Horaire BETWEEN '$start_hour' AND '$end_hour'";
            }

            // Appliquer la limite si toutes les lignes ne sont pas affichées // Apply the limit if not showing all rows
            if (!$show_all) {
                $sql .= " ORDER BY Mesure.Date DESC, Mesure.Horaire DESC LIMIT $num_rows_to_display";
            } else {
                $sql .= " ORDER BY Mesure.Date DESC, Mesure.Horaire DESC";
            }

            $result = mysqli_query($conn, $sql);

            // Générer le tableau HTML avec les données récupérées // Generate the HTML table with the retrieved data
            if (mysqli_num_rows($result) > 0) {
                // En-tête du tableau // Table header
                echo "<table>";
                echo "<tr>";
                echo "<th>Batiment</th>";
                echo "<th>Salle</th>";
                echo "<th>Type</th>";
                echo "<th>Date</th>";
                echo "<th>Horaire</th>";
                echo "<th>Valeur</th>";
                echo "</tr>";

                // Données du tableau // Table data
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['Batiment'] . "</td>";
                    echo "<td>" . $row['Salle'] . "</td>";
                    echo "<td>" . $row['Type'] . "</td>";
                    echo "<td>" . $row['Date'] . "</td>";
                    echo "<td>" . $row['Horaire'] . "</td>";
                    echo "<td>" . $row['Valeur'] . " " . $row['Unite'] . "</td>";  // Ajouter l'unité à la valeur // Append unit to value
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<tr><td colspan='7'>Aucune donnée disponible.</td></tr>";
                // Afficher un message si aucune donnée n'est trouvée // Display a message if no data is found
            }

            // Fermer la connexion à la base de données // Close the database connection
            mysqli_close($conn);
            ?>
            </table>
        </section>

        <section class="bulle">
            <h2>Métriques des capteurs</h2>
            <?php
        // Se reconnecter à la base de données // Reconnect to the database
        $conn = mysqli_connect($servername, $username, $password, $dbname);

        // Vérifier la connexion // Check connection
        if (!$conn) {
            die("Échec de la connexion : " . mysqli_connect_error());
        }

        // Requête SQL pour calculer les métriques pour un utilisateur spécifique // SQL query to calculate metrics for a specific user
        $sql = "SELECT Capteur.TypeCapteur, Capteur.Unite, AVG(Mesure.Valeur) AS Moyenne, MIN(Mesure.Valeur) AS Minimum, MAX(Mesure.Valeur) AS Maximum
                FROM Capteur
                JOIN Mesure ON Capteur.NomCapteur = Mesure.NomCapteur
                JOIN Salle ON Capteur.NomSalle = Salle.NomSalle
                JOIN Batiment ON Salle.BatID = Batiment.BatID
                WHERE Batiment.GestioLog = '" . $login . "'
                GROUP BY Capteur.TypeCapteur, Capteur.Unite";

        // Exécuter la requête SQL // Execute the SQL query
        $result = mysqli_query($conn, $sql);

        // Vérifier si des résultats sont retournés // Check if any results are returned
        if (mysqli_num_rows($result) > 0) {
            // Boucle à travers les résultats et afficher les valeurs // Loop through the results and display the values
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<p>Type de Capteur: " . $row["TypeCapteur"] . "<br>";
                echo "Moyenne: " . $row["Moyenne"] . " " . $row["Unite"] . "<br>";  // Ajouter l'unité à la valeur moyenne // Append unit to average value
                echo "Minimum: " . $row["Minimum"] . " " . $row["Unite"] . "<br>";  // Ajouter l'unité à la valeur minimum // Append unit to minimum value
                echo "Maximum: " . $row["Maximum"] . " " . $row["Unite"] . "</p><br>";  // Ajouter l'unité à la valeur maximum // Append unit to maximum value
            }
        } else {
            echo "<p>Aucun résultat trouvé.</p>";
        }

        // Fermer la connexion à la base de données // Close the database connection
        mysqli_close($conn);
        ?>
        </section>
    </body>

    </html>