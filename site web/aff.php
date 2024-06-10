<!DOCTYPE html>
<html lang="fr">
<head>
    <title>SAE 23</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="DSM">
    <meta name="description" content="SAE 23">
    <meta name="keywords" content="SAE 23">
</head>
<body>
<section class="bulle">
    <h2>Affichage des dernières mesures:</h2>
    <?php
    include 'config.php';

    // SQL query to retrieve the latest measurements for each sensor
    $sql = "SELECT Batiment.NomBat AS Batiment, Salle.NomSalle AS Salle, Capteur.TypeCapteur AS Type, Capteur.Unite, Mesure.Date, Mesure.Horaire, Mesure.Valeur FROM Capteur JOIN ( SELECT NomCapteur, MAX(NomMesure) AS LastMesureID FROM Mesure GROUP BY NomCapteur ) AS LastMesure ON Capteur.NomCapteur = LastMesure.NomCapteur JOIN Mesure ON LastMesure.LastMesureID = Mesure.NomMesure JOIN Salle ON Capteur.NomSalle = Salle.NomSalle JOIN Batiment ON Salle.BatID = Batiment.BatID ORDER BY Mesure.Date DESC, Mesure.Horaire DESC";

    // Execute the SQL query
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
        echo "<p>Pas de données disponibles.</p>"; 
        // Display a message if no data is found
    }

    // Close the database connection
    $conn->close();
    ?>
</section>

</body>
</html>
