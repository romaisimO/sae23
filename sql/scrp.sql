-- Création de la base de données
CREATE DATABASE IF NOT EXISTS sae23;
USE sae23;

-- Création de la table Bâtiment
CREATE TABLE Batiment (
    BatID VARCHAR(10) NOT NULL PRIMARY KEY,
    NomBat VARCHAR(20) NOT NULL,
    GestioLog VARCHAR(50) NOT NULL,
    MdpGestio VARCHAR(50) NOT NULL
);

-- Création de la table Salle
CREATE TABLE Salle (
    NomSalle VARCHAR(10) PRIMARY KEY,
    TypeSalle VARCHAR(10),
    Capacite TINYINT,
    BatID VARCHAR(10),
    FOREIGN KEY (BatID) REFERENCES Batiment(BatID)
);

-- Création de la table Capteur
CREATE TABLE Capteur (
    NomCapteur VARCHAR(50) PRIMARY KEY,
    TypeCapteur VARCHAR(20),
    Unite VARCHAR(20),
    NomSalle VARCHAR(10),
    FOREIGN KEY (NomSalle) REFERENCES Salle(NomSalle)
);

-- Création de la table Mesure
CREATE TABLE Mesure (
    NomMesure INT AUTO_INCREMENT PRIMARY KEY,
    Date DATE,
    Horaire TIME,
    Valeur FLOAT,
    NomCapteur VARCHAR(50),
    FOREIGN KEY (NomCapteur) REFERENCES Capteur(NomCapteur)
);

-- Création de la table Administration
CREATE TABLE Administration (
    Login VARCHAR(50) PRIMARY KEY,
    Mdp VARCHAR(50) NOT NULL
);

-- Insertion de login et mot de passe --
INSERT INTO Batiment (BatID, NomBat,GestioLog, MdpGestio) VALUES 
('A', 'Administratif', 'gestBatA','passBatA' ),
('B', 'INFO_GIM', 'gestBatB','passBatB' ),
('C', 'Recherche', 'gestBatC','passBatC' ),
('E', 'R&T', 'gestBatE','passBatE' );

-- isertion de log mdp pour admin
INSERT INTO Administration (Login, Mdp) VALUES 
('admin', 'admin');
