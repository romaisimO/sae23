<!DOCTYPE html>
<html lang="fr">

<head>
    <title>SAE 23</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="DSM">
    <meta name="description" content="SAE 23">
    <meta name="keywords" content="SAE 23">
    <link rel="stylesheet" href="/styles/style.css" />
    <link rel="stylesheet" href="/styles/rwd.css" />
    <link rel="stylesheet" href="/styles/style2.css" />
</head>

<body>
    <section class="bulle">
        <h2>Affichage des dernières mesures:</h2>
        <?php
        include 'config.php';

        // Connect to the database using procedural style
        $conn = mysqli_connect($servername, $username, $password, $dbname);
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // SQL query to retrieve the latest measurements for each sensor
        $sql = "SELECT Batiment.NomBat AS Batiment, Salle.NomSalle AS Salle, Capteur.TypeCapteur AS Type, Capteur.Unite, Mesure.Date, Mesure.Horaire, Mesure.Valeur 
                FROM Capteur 
                JOIN (
                    SELECT NomCapteur, MAX(NomMesure) AS LastMesureID 
                    FROM Mesure 
                    GROUP BY NomCapteur
                ) AS LastMesure ON Capteur.NomCapteur = LastMesure.NomCapteur 
                JOIN Mesure ON LastMesure.LastMesureID = Mesure.NomMesure 
                JOIN Salle ON Capteur.NomSalle = Salle.NomSalle 
                JOIN Batiment ON Salle.BatID = Batiment.BatID 
                ORDER BY Mesure.Date DESC, Mesure.Horaire DESC";

        // Execute the SQL query
        $result = mysqli_query($conn, $sql);

        // Generate the HTML table with the retrieved data
        if (mysqli_num_rows($result) > 0) {
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
            while ($row = mysqli_fetch_assoc($result)) {
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
        mysqli_close($conn);
        ?>
    </section>
    <footer>
        <aside id="last">
            <p>Validation de la page HTML5 - CSS3</p>

            <a href="https://validator.w3.org/nu/?doc=http%3A%2F%2F127.0.0.1%3A3000%2Findex.html#file" target="_blank">
                <img class="image-responsive" src="./images/html5-validator-badge-blue.png" alt="HTML5 Valide !" />
            </a>

            <a href="https://validator.w3.org/nu/?doc=http%3A%2F%2F127.0.0.1%3A3000%2Findex.html#file" target="_blank">
                <img class="image-responsive" src="http://jigsaw.w3.org/css-validator/images/vcss-blue"
                    alt="CSS Valide !" />
            </a>
        </aside>

        <ul class="IUT">
            <li>IUT de Blagnac</li>
            <li>Département Réseaux et Télécommunications</li>
            <li><a href="mentions-légales.html">Mentions légales</a></li>
        </ul>
    </footer>
</body>

</html>