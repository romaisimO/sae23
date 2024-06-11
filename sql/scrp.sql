-- Creation of the database
CREATE DATABASE IF NOT EXISTS sae23;
USE sae23;

-- Creation of the table Building
CREATE TABLE Batiment (
    BatID VARCHAR(10) NOT NULL PRIMARY KEY,
    NomBat VARCHAR(20) NOT NULL,
    GestioLog VARCHAR(50) NOT NULL,
    MdpGestio VARCHAR(50) NOT NULL
);

-- Creation of the Salle table
CREATE TABLE Salle (
    NomSalle VARCHAR(10) PRIMARY KEY,
    TypeSalle VARCHAR(10),
    Capacite TINYINT,
    BatID VARCHAR(10),
    FOREIGN KEY (BatID) REFERENCES Batiment(BatID)
);

-- Creating the Sensor Table
CREATE TABLE Capteur (
    NomCapteur VARCHAR(50) PRIMARY KEY,
    TypeCapteur VARCHAR(20),
    Unite VARCHAR(20),
    NomSalle VARCHAR(10),
    FOREIGN KEY (NomSalle) REFERENCES Salle(NomSalle)
);

-- Creation of the Measurement table
CREATE TABLE Mesure (
    NomMesure INT AUTO_INCREMENT PRIMARY KEY,
    Date DATE,
    Horaire TIME,
    Valeur FLOAT,
    NomCapteur VARCHAR(50),
    FOREIGN KEY (NomCapteur) REFERENCES Capteur(NomCapteur)
);

-- Creation of the Administration table
CREATE TABLE Administration (
    Login VARCHAR(50) PRIMARY KEY,
    Mdp VARCHAR(50) NOT NULL
);

-- Insert login and password --
INSERT INTO Batiment (BatID, NomBat,GestioLog, MdpGestio) VALUES 
('A', 'Administratif', 'gestBatA','passBatA' ),
('B', 'INFO_GIM', 'gestBatB','passBatB' ),
('C', 'Recherche', 'gestBatC','passBatC' ),
('E', 'R&T', 'gestBatE','passBatE' );

-- Insert log mdp for admin
INSERT INTO Administration (Login, Mdp) VALUES 
('admin', 'admin');
