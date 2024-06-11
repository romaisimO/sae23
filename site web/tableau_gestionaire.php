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

// Define default number of rows to display
$num_rows_to_display = 12; // Set default to 12
$show_all = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the number of rows to display from the form input
    $num_rows_to_display = $_POST['num_rows'];
    if ($num_rows_to_display == 'all') {
        $show_all = true;
    } else {
        $num_rows_to_display = (int)$num_rows_to_display;
    }
}
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
        <table id="data-table">
            <?php
            // SQL query to retrieve measurements for a specific user (filtered by login)
            $sql = "SELECT Batiment.NomBat AS Batiment, Salle.NomSalle AS Salle, Capteur.TypeCapteur AS Type, Capteur.Unite, Mesure.Date, Mesure.Horaire, Mesure.Valeur
                    FROM Capteur
                    JOIN Mesure ON Capteur.NomCapteur = Mesure.NomCapteur
                    JOIN Salle ON Capteur.NomSalle = Salle.NomSalle
                    JOIN Batiment ON Salle.BatID = Batiment.BatID
                    WHERE Batiment.GestioLog = '" . $login . "'
                    ORDER BY Mesure.Date DESC, Mesure.Horaire DESC";
            
            // Apply the limit if not showing all rows
            if (!$show_all) {
                $sql .= " LIMIT $num_rows_to_display";
            }

            $result = $conn->query($sql);

            // Generate the HTML table with the retrieved data
            if ($result->num_rows > 0) {
                // Table header
                echo "<table>";
                echo "<tr>";
                echo "<th>Batiment</th>";
                echo "<th>Salle</th>";
                echo "<th>Type</th>";
                echo "<th>Date</th>";
                echo "<th>Horaire</th>";
                echo "<th>Valeur</th>";
                echo "</tr>";

                // Table data
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['Batiment'] . "</td>";
                    echo "<td>" . $row['Salle'] . "</td>";
                    echo "<td>" . $row['Type'] . "</td>";
                    echo "<td>" . $row['Date'] . "</td>";
                    echo "<td>" . $row['Horaire'] . "</td>";
                    echo "<td>" . $row['Valeur'] . " " . $row['Unite'] . "</td>";  // Append unit to value
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<tr><td colspan='7'>No data available.</td></tr>";
                // Display a message if no data is found
            }

            // Close the database connection
            $conn->close();
            ?>
        </table>
    </section>

    <section class="bulle">
        <h2>Métriques des capteurs</h2>
        <?php
        include 'config.php';

        // Reconnect to the database
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // SQL query to calculate metrics for a specific user (filtered by login)
        $sql = "SELECT Capteur.TypeCapteur, Capteur.Unite, AVG(Mesure.Valeur) AS Moyenne, MIN(Mesure.Valeur) AS Minimum, MAX(Mesure.Valeur) AS Maximum
                FROM Capteur
                JOIN Mesure ON Capteur.NomCapteur = Mesure.NomCapteur
                JOIN Salle ON Capteur.NomSalle = Salle.NomSalle
                JOIN Batiment ON Salle.BatID = Batiment.BatID
                WHERE Batiment.GestioLog = '" . $login . "'
                GROUP BY Capteur.TypeCapteur, Capteur.Unite";


        // Execute the SQL query
        $result = $conn->query($sql);

        // Check if any results are returned
        if ($result->num_rows > 0) {
            // Loop through the results and display the values
            while ($row = $result->fetch_assoc()) {
                echo "<p>Type de Capteur: " . $row["TypeCapteur"] . "<br>";
                echo "Moyenne: " . $row["Moyenne"] . " " . $row["Unite"] . "<br>";  // Append unit to average value
                echo "Minimum: " . $row["Minimum"] . " " . $row["Unite"] . "<br>";  // Append unit to minimum value
                echo "Maximum: " . $row["Maximum"] . " " . $row["Unite"] . "</p><br>";  // Append unit to maximum value
            }
        } else {
            echo "<p>No results found.</p>";
        }


        // Close the database connection
        $conn->close();
        ?>
    </section>

    </body>
</html>
