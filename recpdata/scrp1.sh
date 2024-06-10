#!/bin/bash

# Configuration du broker MQTT
mqtt_broker="mqtt.iut-blagnac.fr"
mqtt_topic="AM107/by-room/+/data"

# Variables de connexion MySQL
mysql_user="root"
mysql_password="passroot"
mysql_database="sae23"




# Fonction pour insérer les données dans la base de données
insert_data() {
    local json_data=$1
    #variable positionnelle
    
    # Extraire les champs du JSON pour les capteurs
    sensors=$(echo "$json_data" | jq -r '.[0] | to_entries | .[] | select(.key != "Latitude" and .key != "Langitude")')
    #{"key": "temperature", "value": 24.3} le to_entries permet de faire la transformation du json // .[] prend chaque élément individuellement. // lat et langi exclu
    # Extraire les champs du JSON pour les informations de la salle et du bâtiment
    latitude=$(echo "$json_data" | jq -r '.[0].Latitude')
    longitude=$(echo "$json_data" | jq -r '.[0].Langitude')
    deviceName=$(echo "$json_data" | jq -r '.[1].deviceName')
    devEUI=$(echo "$json_data" | jq -r '.[1].devEUI')
    room=$(echo "$json_data" | jq -r '.[1].room')
    floor=$(echo "$json_data" | jq -r '.[1].floor')
    building=$(echo "$json_data" | jq -r '.[1].Building')

    deviceName=$(echo "$json_data" | jq -r '.[1].deviceName')
    devEUI=$(echo "$json_data" | jq -r '.[1].devEUI')
    room=$(echo "$json_data" | jq -r '.[1].room')
    floor=$(echo "$json_data" | jq -r '.[1].floor')
    building=$(echo "$json_data" | jq -r '.[1].Building')

    # Insérer les données dans les tables MySQL en vérifiant si elles existent déjà

    # le -e exécuter directement
    #pour le -sse s : silence, -s : upprime les noms de colonnes de la sortie, -e excute.
    # Insertion dans la table Bâtiment
    mysql -u "$mysql_user" -p"$mysql_password" -D "$mysql_database" -e "
    INSERT INTO Batiment (BatID)
    SELECT '$building' WHERE NOT EXISTS (SELECT 1 FROM Batiment WHERE BatID = '$building');
    "
    
    # Récupérer l'ID du bâtiment
    BatID=$(mysql -u "$mysql_user" -p"$mysql_password" -D "$mysql_database" -sse "
    SELECT BatID FROM Batiment WHERE NomBat = '$building';
    ")

    # Insertion dans la table Salle
    mysql -u "$mysql_user" -p"$mysql_password" -D "$mysql_database" -e "
    INSERT INTO Salle (NomSalle, TypeSalle, Capacité, BatID)
    SELECT '$room', 'Type', 0, $BatID WHERE NOT EXISTS (SELECT 1 FROM Salle WHERE NomSalle = '$room');
    "

    # Insertion dans la table Capteur
    mysql -u "$mysql_user" -p"$mysql_password" -D "$mysql_database" -e "
    INSERT INTO Capteur (NomCapteur, TypeCapteur, Unité, NomSalle)
    SELECT '$deviceName', 'Type', 'Unit', '$room' WHERE NOT EXISTS (SELECT 1 FROM Capteur WHERE NomCapteur = '$deviceName');
    "
    #parcours du json pour les capteurs et extraction de la key de la valeur et de la valeur
    while IFS= read -r sensor; do
        key=$(echo "$sensor" | jq -r '.key')
        value=$(echo "$sensor" | jq -r '.value')

        # Insertion dans la table Mesure pour chaque capteur
        mysql -u "$mysql_user" -p"$mysql_password" -D "$mysql_database" -e "
        INSERT INTO Mesure (NomMesure, Date, Horaire, Valeur, NomCapteur)
        SELECT '{$key}_$deviceName', CURDATE(), CURTIME(), $value, '$deviceName' WHERE NOT EXISTS (SELECT 1 FROM Mesure WHERE NomMesure = '$key' AND Date = CURDATE() AND Horaire = CURTIME());
        "
    done <<< "$sensors"
}

# S'abonner au topic MQTT et traiter les messages
mosquitto_sub -h "$mqtt_broker" -t "$mqtt_topic" -C 1 | while read -r message
do
    insert_data "$message"
done

