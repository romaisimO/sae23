#!/opt/lampp/bin/php
<?php
$idBd = mysqli_connect("localhost", "root", "passroot", "sae23")
    or die("Echec de la connexion à la base de données"); // Connect to the database or display an error message if the connection fails // Connexion à la base de données ou affichage d'un message d'erreur en cas d'échec

while (true) { // Infinite loop to continuously fetch and process data // Boucle infinie pour récupérer et traiter les données en continu
    $mqtt_broker = "mqtt.iut-blagnac.fr"; // MQTT broker URL // URL du broker MQTT
    $mqtt_topic = "AM107/by-room/+/data"; // MQTT topic to subscribe to // Sujet MQTT à s'abonner

    // Execute the MQTT subscription command and capture the JSON output // Exécuter la commande d'abonnement MQTT et capturer la sortie JSON
    $rcpjson = shell_exec("mosquitto_sub -h mqtt.iut-blagnac.fr -t AM107/by-room/+/data -C 1");

    // Decode the JSON data into an associative array // Décoder les données JSON en un tableau associatif
    $jsondec = json_decode($rcpjson, true);

    // Extract data from the JSON array // Extraire les données du tableau JSON
    $building = $jsondec[1]["Building"];
    $room = $jsondec[1]["room"];
    $temp = $jsondec[0]["temperature"];
    $co2 = $jsondec[0]["co2"];
    $humidity = $jsondec[0]["humidity"];
    $floor = $jsondec[1]["floor"];
    $deviceName = $jsondec[1]["deviceName"];

    // Define capacities for different room types // Définir les capacités pour différents types de salles
    $capaTD = 30;
    $capaTP = 17;
    $capaDF = 40;

    // Define sensor names and types // Définir les noms et types de capteurs
    $nomcaptmp = $deviceName . "Temp";
    $typcapt1 = "temperature";
    $typcapt2 = "humidite";
    $typcapt3 = "CO2";
    $nomcapco2 = $deviceName . "Co2";
    $nomcaphumi = $deviceName . "Humi";
    $unitemp = "degres";
    $unico2 = "ppm";
    $unihumi = "%";

    // Display the temperature and room information // Afficher la température et les informations de la salle
    echo 'il fait ' . $temp . ' dans la salle ' . $room . '</br>';
    echo $nomcaptmp;

    // Check if the building is not outside // Vérifier si le bâtiment n'est pas à l'extérieur
    if ($building != "Outside") {

        // Determine the room type and capacity based on the building and floor // Déterminer le type et la capacité de la salle en fonction du bâtiment et de l'étage
        if ($building == "E" || $building == "B" || $building == "C") {
            if ($floor == "1" || $floor == "2") {
                $typeSalle = 'TP';
                $capacite = $capaTP;
                echo 'La salle est une salle de TP.<br>';
            } else {
                $typeSalle = 'TD';
                $capacite = $capaTD;
                echo 'La salle est une salle de TD.<br>';
            }
        } else {
            $typeSalle = 'CmOuSALLE';
            $capacite = $capaDF;
        }

        // SQL query to insert or update the room information // Requête SQL pour insérer ou mettre à jour les informations de la salle
        $requete1 = "INSERT INTO `Salle` (`NomSalle`, `TypeSalle`, `Capacite`, `BatID`) VALUES ('$room', '$typeSalle', '$capacite', '$building') ON DUPLICATE KEY UPDATE
                    TypeSalle = VALUES(TypeSalle),
                    Capacite = VALUES(Capacite),
                    BatID = VALUES(BatID);";
        
        // SQL queries to insert or update sensor information // Requêtes SQL pour insérer ou mettre à jour les informations des capteurs
        $rqcaptemp = "INSERT INTO `Capteur` (`NomCapteur`, `TypeCapteur`, `Unite`, `NomSalle`) VALUES ('$nomcaptmp', '$typcapt1', '$unitemp', '$room') ON DUPLICATE KEY UPDATE
                    TypeCapteur = VALUES(TypeCapteur),
                    Unite = VALUES(Unite),
                    NomSalle = VALUES(NomSalle);";

        $rqcaptCo2 = "INSERT INTO `Capteur` (`NomCapteur`, `TypeCapteur`, `Unite`, `NomSalle`) VALUES ('$nomcapco2', '$typcapt3', '$unico2', '$room') ON DUPLICATE KEY UPDATE
                    TypeCapteur = VALUES(TypeCapteur),
                    Unite = VALUES(Unite),
                    NomSalle = VALUES(NomSalle);";

        $rqcapthumi = "INSERT INTO `Capteur` (`NomCapteur`, `TypeCapteur`, `Unite`, `NomSalle`) VALUES ('$nomcaphumi', '$typcapt2', '$unihumi', '$room') ON DUPLICATE KEY UPDATE
                    TypeCapteur = VALUES(TypeCapteur),
                    Unite = VALUES(Unite),
                    NomSalle = VALUES(NomSalle);";

        // SQL queries to insert measurements // Requêtes SQL pour insérer les mesures
        $rqvaltemp = "INSERT INTO Mesure (Date, Horaire, Valeur, NomCapteur)
                    VALUES (CURDATE(), CURTIME(), '$temp', '$nomcaptmp')";

        $rqvalco2 = "INSERT INTO Mesure (Date, Horaire, Valeur, NomCapteur)
                    VALUES (CURDATE(), CURTIME(), '$co2', '$nomcapco2')";

        $rqvalhumi = "INSERT INTO Mesure (Date, Horaire, Valeur, NomCapteur)
                    VALUES (CURDATE(), CURTIME(), '$humidity', '$nomcaphumi')";

        // Execute the SQL queries and check for errors // Exécuter les requêtes SQL et vérifier les erreurs
        if (!mysqli_query($idBd, $requete1)) {
            echo "Erreur : " . mysqli_error($idBd) . "<br>";
        }
        if (!mysqli_query($idBd, $rqcaptemp)) {
            echo "Erreur : " . mysqli_error($idBd) . "<br>";
        }
        if (!mysqli_query($idBd, $rqcaptCo2)) {
            echo "Erreur : " . mysqli_error($idBd) . "<br>";
        }
        if (!mysqli_query($idBd, $rqcapthumi)) {
            echo "Erreur : " . mysqli_error($idBd) . "<br>";
        }
        if (!mysqli_query($idBd, $rqvaltemp)) {
            echo "Erreur : " . mysqli_error($idBd) . "<br>";
        }
        if (!mysqli_query($idBd, $rqvalco2)) {
            echo "Erreur : " . mysqli_error($idBd) . "<br>";
        }
        if (!mysqli_query($idBd, $rqvalhumi)) {
            echo "Erreur : " . mysqli_error($idBd) . "<br>";
        }
    }
}

// Fermer la connexion à la base de données // Close the database connection
mysqli_close($idBd);
?>
