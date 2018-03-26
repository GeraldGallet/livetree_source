#------------------------------------------------------------
#        Script MySQL.
#------------------------------------------------------------

DROP DATABASE IF EXISTS `projet_LiveTree`; 
CREATE DATABASE IF NOT EXISTS `projet_LiveTree` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `projet_LiveTree`;


#------------------------------------------------------------
# Table: Utilisateur
#------------------------------------------------------------


CREATE TABLE Utilisateur(
        id_utilisateur int (11) Auto_increment  NOT NULL ,
        nom            Varchar (25) NOT NULL ,
        prenom         Varchar (25) NOT NULL ,
        mdp            Varchar (25) NOT NULL ,
        PRIMARY KEY (id_utilisateur )
)ENGINE=InnoDB;
