#!/opt/lampp/bin/php
<?
$idBd = mysqli_connect("localhost", "root", "passroot", "sae23")
    or die("Echec de la connexio a la base de données");

while (true) {
    $mqtt_broker = "mqtt.iut-blagnac.fr";
    $mqtt_topic = "AM107/by-room/+/data";

    $rcpjson = shell_exec("mosquitto_sub -h mqtt.iut-blagnac.fr -t AM107/by-room/+/data -C 1");

    $jsondec = json_decode($rcpjson, true);

    $building = $jsondec[1]["Building"];
    $room = $jsondec[1]["room"];
    $temp = $jsondec[0]["temperature"];
    $co2 = $jsondec[0]["co2"];
    $humidity = $jsondec[0]["humidity"];
    $floor = $jsondec[1]["floor"];
    $deviceName = $jsondec[1]["deviceName"];

    $capaTD = 30;
    $capaTP = 17;
    $capaDF = 40;

    $nomcaptmp = $deviceName . "Temp";
    $typcapt1 = "temperature";
    $typcapt2 = "humidite";
    $typcapt3 = "CO2";
    $nomcapco2 = $deviceName . "Co2";
    $nomcaphumi = $deviceName . "Humi";
    $unitemp = "degres";
    $unico2 = "ppm";
    $unihumi = "%";

    echo 'il fait ' . $temp . ' dans la salle ' . $room . '</br>';
    echo $nomcaptmp;


    if ($building != "Outside") {

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


        // Requête SQL pour insérer ou mettre à jour la salle
        $requete1 = "INSERT INTO `Salle` (`NomSalle`, `TypeSalle`, `Capacite`, `BatID`) VALUES ('$room', '$typeSalle', '$capacite', '$building') ON DUPLICATE KEY UPDATE
                	NomSalle = VALUES(NomSalle),
                	TypeSalle = VALUES(TypeSalle),
                	Capacite = VALUES(Capacite),
                	BatID = VALUES(BatID);";
        $rqcaptemp = "INSERT INTO `Capteur` (`NomCapteur`, `TypeCapteur`, `Unite`, `NomSalle`) VALUES ('$nomcaptmp', '$typcapt1', '$unitemp', '$room') ON DUPLICATE KEY UPDATE
        	NomCapteur = VALUES(NomCapteur),
        	TypeCapteur = VALUES(TypeCapteur),
        	Unite = VALUES(Unite),
        	NomSalle = VALUES(NomSalle);";

        $rqcaptCo2 = "INSERT INTO `Capteur` (`NomCapteur`, `TypeCapteur`, `Unite`, `NomSalle`) VALUES ('$nomcapco2', '$typcapt3', '$unico2', '$room') ON DUPLICATE KEY UPDATE
    	NomCapteur = VALUES(NomCapteur),
    	TypeCapteur = VALUES(TypeCapteur),
    	Unite = VALUES(Unite),
    	NomSalle = VALUES(NomSalle);";

        $rqcapthumi = "INSERT INTO `Capteur` (`NomCapteur`, `TypeCapteur`, `Unite`, `NomSalle`) VALUES ('$nomcaphumi', '$typcapt2', '$unihumi', '$room') ON DUPLICATE KEY UPDATE
    	NomCapteur = VALUES(NomCapteur),
    	TypeCapteur = VALUES(TypeCapteur),
    	Unite = VALUES(Unite),
    	NomSalle = VALUES(NomSalle);";

        $rqvaltemp = "INSERT INTO Mesure (Date, Horaire, Valeur, NomCapteur)
                   	VALUES (CURDATE(), CURTIME(), '$temp', '$nomcaptmp')";

        $rqvalco2 = "INSERT INTO Mesure (Date, Horaire, Valeur, NomCapteur)
                   	VALUES (CURDATE(), CURTIME(), '$co2', '$nomcapco2')";

        $rqvalhumi = "INSERT INTO Mesure (Date, Horaire, Valeur, NomCapteur)
                   	VALUES (CURDATE(), CURTIME(), '$humidity', '$nomcaphumi')";
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
mysqli_close($idBd);

?>