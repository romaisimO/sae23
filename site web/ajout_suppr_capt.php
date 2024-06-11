<?php
include 'config.php';

session_start(); // Démarrer la session // Start the session

if (isset($_SESSION['login'])) {
    // L'utilisateur est connecté // User is logged in
    $login = $_SESSION['login'];
} else {
    // L'utilisateur n'est pas connecté // User is not logged in
    // Rediriger vers la page de connexion // Redirect to the login page
    header('Location: connexion.php');
    exit(); // S'assurer de sortir après la redirection // Make sure to exit after redirect
}

// Se connecter à la base de données // Connect to the database
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    // Afficher un message d'erreur si la connexion à la base de données échoue // Display an error message if the database connection fails
    die("La connexion à la base de données a échoué : " . mysqli_connect_error());
}

// Vérifier si le formulaire 'ajouter_capteur' a été soumis // Check if the 'ajouter_capteur' form has been submitted
if (isset($_POST['ajouter_capteur'])) {
    // Récupérer les données du formulaire // Retrieve form data
    $NomCapteur = $_POST['NomCapteur'];
    $TypeCapteur = $_POST['TypeCapteur'];
    $Unite = $_POST['Unite'];
    $BatID = $_POST['BatID'];
    $NomSalle = $_POST['NomSalle'];

    // Vérifier si le 'BatID' spécifié existe dans la table 'Batiment' // Check if the specified 'BatID' exists in the 'Batiment' table
    $sql_check_batiment = "SELECT * FROM Batiment WHERE BatID = '$BatID'";
    $result_check_batiment = mysqli_query($conn, $sql_check_batiment);
    if (mysqli_num_rows($result_check_batiment) > 0) {
        // Insérer les nouvelles données du capteur dans la table 'Capteur' // Insert the new sensor data into the 'Capteur' table
        $sql = "INSERT INTO Capteur (NomCapteur, TypeCapteur, Unite, NomSalle) VALUES ('$NomCapteur', '$TypeCapteur', '$Unite', '$NomSalle')";
        if (mysqli_query($conn, $sql)) {
            // Afficher un message de succès si le capteur est ajouté avec succès // Display success message if the sensor is successfully added
            echo "Le capteur a été ajouté avec succès.";
        } else {
            // Afficher un message d'erreur en cas de problème lors de l'ajout du capteur // Display error message if there is an issue with adding the sensor
            echo "Erreur lors de l'ajout du capteur : " . mysqli_error($conn);
        }
    } else {
        // Afficher un message d'erreur si le 'BatID' spécifié n'existe pas // Display error message if the specified 'BatID' does not exist
        echo "Le bâtiment avec l'ID $BatID n'existe pas. Veuillez ajouter d'abord le bâtiment.";
    }
}

// Vérifier si le formulaire 'supprimer_capteur' a été soumis // Check if the 'supprimer_capteur' form has been submitted
if (isset($_POST['supprimer_capteur'])) {
    // Récupérer les données du formulaire // Retrieve form data
    $NomCapteur = $_POST['NomCapteur'];

    // Supprimer les mesures du capteur associées au 'NomCapteur' spécifié // Delete the sensor measurements associated with the specified 'NomCapteur'
    $sql_delete_mesures = "DELETE FROM Mesure WHERE NomCapteur = '$NomCapteur'";
    if (mysqli_query($conn, $sql_delete_mesures)) {
        // Supprimer le capteur de la table 'Capteur' // Delete the sensor from the 'Capteur' table
        $sql_delete_capteur = "DELETE FROM Capteur WHERE NomCapteur = '$NomCapteur'";
        if (mysqli_query($conn, $sql_delete_capteur)) {
            // Afficher un message de succès si le capteur est supprimé avec succès // Display success message if the sensor is successfully deleted
            echo "Le capteur a été supprimé avec succès.";
        } else {
            // Afficher un message d'erreur en cas de problème lors de la suppression du capteur // Display error message if there is an issue with deleting the sensor
            echo "Erreur lors de la suppression du capteur : " . mysqli_error($conn);
        }
    } else {
        // Afficher un message d'erreur en cas de problème lors de la suppression des mesures associées // Display error message if there is an issue with deleting the associated measurements
        echo "Erreur lors de la suppression des mesures : " . mysqli_error($conn);
    }
}

// Sélectionner tous les capteurs de la table 'Capteur' // Select all sensors from the 'Capteur' table
$sql_select_capteurs = "SELECT NomCapteur, TypeCapteur, NomSalle FROM Capteur";
$result_capteurs = mysqli_query($conn, $sql_select_capteurs);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter/Supprimer des capteurs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="author" content="ALAMI, " />
    <meta name="description" content="SAE_23" />
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
                    <li><a href="ajout_suppr_bat.php">Ajout/Suppression de bâtiment</a></li>
                    <li><a href="gestion_de_projet.html">Gestion de projet</a></li>
                    <li><a href="deconnexion.php">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Afficher le titre // Display the title -->
    <h1>Ajouter/Supprimer des capteurs</h1>

    <!-- Section pour ajouter un capteur // Section for adding a sensor -->
    <section class="bulle">
        <h2>Ajouter un capteur</h2>

        <!-- Formulaire pour ajouter un capteur // Form for adding a sensor -->
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <!-- Champs de saisie pour les détails du capteur // Input fields for sensor details -->
            <label for="NomCapteur">Nom Capteur:</label>
            <input type="text" id="NomCapteur" name="NomCapteur" required><br>

            <label for="TypeCapteur">Type:</label>
            <input type="text" id="TypeCapteur" name="TypeCapteur" required><br>

            <label for="Unite">Unité:</label>
            <input type="text" id="Unite" name="Unite" required><br>

            <label for="BatID">ID Bâtiment:</label>
            <input type="text" id="BatID" name="BatID" required><br>

            <label for="NomSalle">Nom Salle:</label>
            <input type="text" id="NomSalle" name="NomSalle" required><br>

            <!-- Bouton de soumission pour ajouter le capteur // Submit button for adding the sensor -->
            <input type="submit" name="ajouter_capteur" value="Ajouter Capteur">
        </form>
    </section>

    <!-- Section pour supprimer un capteur // Section for deleting a sensor -->
    <section class="bulle">
        <h2>Supprimer un capteur</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <!-- Liste déroulante pour choisir un capteur à supprimer // Select dropdown for choosing a sensor to delete -->
            <label for="NomCapteur_supprimer">Sélectionnez un capteur:</label>
            <select class="bouton_selec" id="NomCapteur_supprimer" name="NomCapteur" required>
                <!-- Afficher les options pour chaque capteur récupéré de la base de données // Display options for each sensor retrieved from the database -->
                <?php
                while ($row = mysqli_fetch_assoc($result_capteurs)) {
                    echo "<option value='" . $row['NomCapteur'] . "'>" . $row['NomCapteur'] . " - " . $row['NomSalle'] . "</option>";
                }
                ?>
            </select><br>

            <!-- Bouton de soumission pour supprimer le capteur sélectionné // Submit button for deleting the selected sensor -->
            <input type="submit" name="supprimer_capteur" value="Supprimer Capteur"><br><br><br>
        </form>
    </section>

    <section class="bulle">
        <h2>Afficher les données des capteurs</h2>
        <form method="post" action="">
            <label for="num_rows">Nombre de lignes à afficher:</label>
            <select name="num_rows" id="num_rows">
                <option value="6" <?php if (isset($_POST['num_rows']) && $_POST['num_rows'] == 6) echo 'selected'; ?>>6</option>
                <option value="12" <?php if ((isset($_POST['num_rows']) && $_POST['num_rows'] == 12) || !isset($_POST['num_rows'])) echo 'selected'; ?>>12</option>
                <option value="24" <?php if (isset($_POST['num_rows']) && $_POST['num_rows'] == 24) echo 'selected'; ?>>24</option>
                <option value="48" <?php if (isset($_POST['num_rows']) && $_POST['num_rows'] == 48) echo 'selected'; ?>>48</option>
                <option value="all" <?php if (isset($_POST['num_rows']) && $_POST['num_rows'] == 'all') echo 'selected'; ?>>Tout afficher</option>
            </select>
            <input type="submit" value="Afficher">
        </form>
    </section>

    <section class="bulle">
        <!-- Afficher les données des capteurs dans un tableau // Display the sensor data in a table -->
        <table id="data-table">
            <?php
            // Déterminer le nombre de lignes à afficher // Determine the number of rows to display
            $num_rows = isset($_POST['num_rows']) ? $_POST['num_rows'] : 12;

            // Sélectionner les données des capteurs à partir de plusieurs tables et les afficher sous forme de tableau // Select sensor data from multiple tables and display it in a table format
            $sql = "SELECT Batiment.NomBat AS Batiment, Salle.NomSalle AS Salle, Capteur.TypeCapteur AS Type, Capteur.Unite, Mesure.Date, Mesure.Horaire, Mesure.Valeur
                    FROM Capteur
                    JOIN Mesure ON Capteur.NomCapteur = Mesure.NomCapteur
                    JOIN Salle ON Capteur.NomSalle = Salle.NomSalle
                    JOIN Batiment ON Salle.BatID = Batiment.BatID
                    ORDER BY Mesure.Date DESC, Mesure.Horaire DESC";

            if ($num_rows != 'all') {
                $sql .= " LIMIT $num_rows";
            }

            $result = mysqli_query($conn, $sql);

            if ($result && mysqli_num_rows($result) > 0) {
                // Afficher la ligne d'en-tête du tableau // Display table header row
                echo "<table>";
                echo "<tr>";
                echo "<th>Batiment</th>";
                echo "<th>Salle</th>";
                echo "<th>Type</th>";
                echo "<th>Date</th>";
                echo "<th>Horaire</th>";
                echo "<th>Valeur</th>";
                echo "</tr>";

                // Afficher les lignes du tableau avec les données des capteurs // Display table rows with sensor data
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
                // Afficher un message si aucune donnée n'est disponible // Display a message if no data is available
                echo "<tr><td colspan='7'>No data available.</td></tr>";
            }
            ?>
        </table>
    </section>

</body>
</html>
