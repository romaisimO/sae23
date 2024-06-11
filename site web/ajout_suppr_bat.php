<?php
include 'config.php';

session_start(); // Start the session

if (isset($_SESSION['login'])) {
    // User is logged in
    $login = $_SESSION['login'];
} else {
    // User is not logged in
    // Redirect to the login page
    header('Location: connexion.php');
    exit(); // Make sure to exit after redirect
}

// Connect to the database
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    // Display an error message if the database connection fails
    die("La connexion à la base de données a échoué : " . mysqli_connect_error());
}

// Handling the building addition form
if (isset($_POST['ajouter_batiment'])) {
    $BatID = $_POST['BatID'];
    $NomBat = $_POST['NomBat'];
    $GestioLog = $_POST['GestioLog'];
    $MdpGestio = $_POST['MdpGestio'];

    $sql = "INSERT INTO Batiment (BatID, NomBat, GestioLog, MdpGestio) VALUES ('$BatID', '$NomBat', '$GestioLog', '$MdpGestio')";
    if (mysqli_query($conn, $sql)) {
        echo "Le bâtiment a été ajouté avec succès.";
    } else {
        echo "Erreur lors de l'ajout du bâtiment : " . mysqli_error($conn);
    }
}

// Handling the building deletion form
if (isset($_POST['supprimer_batiment'])) {
    $BatID = $_POST['BatID'];

    // Check if the building contains sensors before deleting it
    $sql_check_capteurs = "SELECT * FROM Capteur WHERE NomSalle IN (SELECT NomSalle FROM Salle WHERE BatID = '$BatID')";
    $result_check_capteurs = mysqli_query($conn, $sql_check_capteurs);
    if (mysqli_num_rows($result_check_capteurs) > 0) {
        echo "Le bâtiment avec l'ID $BatID contient des capteurs. Veuillez supprimer d'abord les capteurs.";
    } else {
        // Delete the building
        $sql_delete_batiment = "DELETE FROM Batiment WHERE BatID = '$BatID'";
        if (mysqli_query($conn, $sql_delete_batiment)) {
            echo "Le bâtiment a été supprimé avec succès.";
        } else {
            echo "Erreur lors de la suppression du bâtiment : " . mysqli_error($conn);
        }
    }
}

// Handling the room addition form
if (isset($_POST['ajouter_salle'])) {
    $NomSalle = $_POST['NomSalle'];
    $TypeSalle = $_POST['TypeSalle'];
    $Capacite = $_POST['Capacite'];
    $BatID = $_POST['BatID'];

    $sql = "INSERT INTO Salle (NomSalle, TypeSalle, Capacite, BatID) VALUES ('$NomSalle', '$TypeSalle', '$Capacite', '$BatID')";
    if (mysqli_query($conn, $sql)) {
        echo "La salle a été ajoutée avec succès.";
    } else {
        echo "Erreur lors de l'ajout de la salle : " . mysqli_error($conn);
    }
}

// Handling the room deletion form
if (isset($_POST['supprimer_salle'])) {
    $NomSalle = $_POST['NomSalle'];

    // Check if the room contains sensors before deleting it
    $sql_check_capteurs = "SELECT * FROM Capteur WHERE NomSalle = '$NomSalle'";
    $result_check_capteurs = mysqli_query($conn, $sql_check_capteurs);
    if (mysqli_num_rows($result_check_capteurs) > 0) {
        echo "La salle avec le nom $NomSalle contient des capteurs. Veuillez supprimer d'abord les capteurs.";
    } else {
        // Delete the room
        $sql_delete_salle = "DELETE FROM Salle WHERE NomSalle = '$NomSalle'";
        if (mysqli_query($conn, $sql_delete_salle)) {
            echo "La salle a été supprimée avec succès.";
        } else {
            echo "Erreur lors de la suppression de la salle : " . mysqli_error($conn);
        }
    }
}

// Retrieve the list of buildings for selection
$sql_select_batiments = "SELECT BatID, NomBat FROM Batiment";
$result_batiments = mysqli_query($conn, $sql_select_batiments);

// Retrieve the list of rooms for selection
$sql_select_salles = "SELECT NomSalle, TypeSalle, Capacite, BatID FROM Salle";
$result_salles = mysqli_query($conn, $sql_select_salles);

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Ajouter/Supprimer des bâtiments et salles</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="author" content="DSM" />
    <meta name="description" content="SAE_23" />
    <meta name="keywords" content="HTML, CSS" />
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
                    <li><a href="ajout_suppr_capt.php">Ajout/Suppression de Capteurs</a></li>
                    <li><a href="gestion_de_projet.html">Gestion de projet</a></li>
                    <li><a href="deconnexion.php">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <h1>Ajouter/Supprimer des bâtiments et des salles</h1>

    <section class="bulle">
        <h2>Ajouter un bâtiment</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <label for="BatID">ID Bâtiment:</label>
            <input type="text" id="BatID" name="BatID" required><br>

            <label for="NomBat">Nom:</label>
            <input type="text" id="NomBat" name="NomBat" required><br>

            <label for="GestioLog">Utilisateur:</label>
            <input type="text" id="GestioLog" name="GestioLog" required><br>

            <label for="MdpGestio">Mot de passe:</label>
            <input type="password" id="MdpGestio" name="MdpGestio" required><br>

            <input type="submit" name="ajouter_batiment" value="Ajouter Bâtiment">
        </form>
    </section>

    <section class="bulle">
        <h2>Supprimer un bâtiment</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <label for="BatID_supprimer">Sélectionnez un bâtiment:</label>
            <select class="bouton_selec" id="BatID_supprimer" name="BatID" required>
                <?php
                while ($row = mysqli_fetch_assoc($result_batiments)) {
                    echo "<option value='" . $row['BatID'] . "'>" . $row['NomBat'] . "</option>";
                }
                ?>
            </select><br>

            <input type="submit" name="supprimer_batiment" value="Supprimer Bâtiment"><br><br><br>
        </form>
    </section>

    <section class="bulle">
        <h2>Ajouter une salle</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <label for="NomSalle">Nom Salle:</label>
            <input type="text" id="NomSalle" name="NomSalle" required><br>

            <label for="TypeSalle">Type Salle:</label>
            <input type="text" id="TypeSalle" name="TypeSalle" required><br>

            <label for="Capacite">Capacité:</label>
            <input type="number" id="Capacite" name="Capacite" required><br>

            <label for="BatID">ID Bâtiment:</label>
            <input type="text" id="BatID" name="BatID" required><br>

            <input type="submit" name="ajouter_salle" value="Ajouter Salle">
        </form>
    </section>

    <section class="bulle">
        <h2>Supprimer une salle</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <label for="NomSalle_supprimer">Sélectionnez une salle:</label>
            <select class="bouton_selec" id="NomSalle_supprimer" name="NomSalle" required>
                <?php
                while ($row = mysqli_fetch_assoc($result_salles)) {
                    echo "<option value='" . $row['NomSalle'] . "'>" . $row['NomSalle'] . "</option>";
                }
                ?>
            </select><br>

            <input type="submit" name="supprimer_salle" value="Supprimer Salle"><br><br><br>
        </form>
    </section>

    <section class="bulle">
        <h2>Afficher les salles</h2>
        <form method="post" action="">
            <label for="num_rows">Nombre de lignes à afficher:</label>
            <select name="num_rows" id="num_rows">
                <option value="6" <?php if (isset($_POST['num_rows']) && $_POST['num_rows'] == 6) echo 'selected'; ?>>6
                </option>
                <option value="12"
                    <?php if ((isset($_POST['num_rows']) && $_POST['num_rows'] == 12) || !isset($_POST['num_rows'])) echo 'selected'; ?>>
                    12</option>
                <option value="24" <?php if (isset($_POST['num_rows']) && $_POST['num_rows'] == 24) echo 'selected'; ?>>
                    24</option>
                <option value="48" <?php if (isset($_POST['num_rows']) && $_POST['num_rows'] == 48) echo 'selected'; ?>>
                    48</option>
                <option value="all"
                    <?php if (isset($_POST['num_rows']) && $_POST['num_rows'] == 'all') echo 'selected'; ?>>Tout
                    afficher</option>
            </select>
            <input type="submit" value="Afficher">
        </form>
    </section>

    <section class="bulle">
        <!-- Display the room data in a table -->
        <table id="data-table">
            <?php
            // Determine the number of rows to display
            $num_rows = isset($_POST['num_rows']) ? $_POST['num_rows'] : 12;

            // Select room data from multiple tables and display it in a table format
            $sql = "SELECT Batiment.NomBat AS Batiment, Salle.NomSalle AS Salle, Salle.TypeSalle AS Type, Salle.Capacite
                    FROM Salle
                    JOIN Batiment ON Salle.BatID = Batiment.BatID
                    ORDER BY Batiment, Salle";

            if ($num_rows != 'all') {
                $sql .= " LIMIT $num_rows";
            }

            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                // Display table header row
                echo "<table>";
                echo "<tr>";
                while ($fieldinfo = $result->fetch_field()) {
                    echo "<th>" . $fieldinfo->name . "</th>";
                }
                echo "</tr>";

                // Display table rows with room data
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['Batiment'] . "</td>";
                    echo "<td>" . $row['Salle'] . "</td>";
                    echo "<td>" . $row['Type'] . "</td>";
                    echo "<td>" . $row['Capacite'] . "</td>";
                    echo "</tr>";
                }
                echo "<table>";
            } else {
                // Display a message if no data is available
                echo "<tr><td colspan='4'>No data available.</td></tr>";
            }
            ?>
        </table>
    </section>
</body>

</html>