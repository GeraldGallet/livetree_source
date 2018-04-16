#------------------------------------------------------------
#        Script MySQL.
#------------------------------------------------------------

DROP DATABASE IF EXISTS `projet_LiveTree`;
CREATE DATABASE IF NOT EXISTS `projet_LiveTree` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `projet_LiveTree`;

#------------------------------------------------------------
# Table: user
#------------------------------------------------------------

CREATE TABLE user(
        id_user      int (11) Auto_increment  NOT NULL ,
        email        Varchar (100) NOT NULL ,
        password     Varchar (100) NOT NULL ,
        first_name   Varchar (50) NOT NULL ,
        last_name    Varchar (50) NOT NULL ,
        activated    Bool NOT NULL ,
        phone_number Int NOT NULL ,
        id_status    Varchar (50) NOT NULL ,
        indicative   Varchar (5) NOT NULL ,
        PRIMARY KEY (id_user ) ,
        UNIQUE (email )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: borne
#------------------------------------------------------------

CREATE TABLE borne(
        id_borne int (11) Auto_increment  NOT NULL ,
        name     Varchar (100) NOT NULL ,
        place    Varchar (100) NOT NULL ,
        id_place Int NOT NULL ,
        PRIMARY KEY (id_borne )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: company_car
#------------------------------------------------------------

CREATE TABLE company_car(
        id_company_car int (11) Auto_increment  NOT NULL ,
        model          Varchar (100) NOT NULL ,
        power          Int NOT NULL ,
        name           Varchar (100) NOT NULL ,
        id_facility    Int NOT NULL ,
        PRIMARY KEY (id_company_car )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: place
#------------------------------------------------------------

CREATE TABLE place(
        id_place    int (11) Auto_increment  NOT NULL ,
        name        Varchar (30) NOT NULL ,
        address     Varchar (100) NOT NULL ,
        id_facility Int NOT NULL ,
        PRIMARY KEY (id_place )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: facility
#------------------------------------------------------------

CREATE TABLE facility(
        id_facility   int (11) Auto_increment  NOT NULL ,
        name          Varchar (100) NOT NULL ,
        address       Varchar (100) NOT NULL ,
        complementary Varchar (200) NOT NULL ,
        PRIMARY KEY (id_facility ) ,
        UNIQUE (name )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: personal_car
#------------------------------------------------------------

CREATE TABLE personal_car(
        id_personal_car int (11) Auto_increment  NOT NULL ,
        model           Varchar (100) NOT NULL ,
        power           Int NOT NULL ,
        name            Varchar (100) NOT NULL ,
        id_user         Int NOT NULL ,
        PRIMARY KEY (id_personal_car )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: resa_borne
#------------------------------------------------------------

CREATE TABLE resa_borne(
        id_resa    int (11) Auto_increment  NOT NULL ,
        date_resa  Date NOT NULL ,
        start_time Time NOT NULL ,
        end_time   Time NOT NULL ,
        charge     Float NOT NULL ,
        id_user    Int NOT NULL ,
        id_place   Int NOT NULL ,
        PRIMARY KEY (id_resa )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: resa_car
#------------------------------------------------------------

CREATE TABLE resa_car(
        id_resa        int (11) Auto_increment  NOT NULL ,
        date_start     Date NOT NULL ,
        start_time     Time NOT NULL ,
        end_time       Time NOT NULL ,
        reason_details Varchar (200) ,
        km_start       Int ,
        km_end         Int ,
        km_planned     Int NOT NULL ,
        date_end       Date NOT NULL ,
        id_user        Int NOT NULL ,
        id_company_car Int NOT NULL ,
        id_reason      Varchar (50) NOT NULL ,
        id_state       Int NOT NULL ,
        PRIMARY KEY (id_resa )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: status
#------------------------------------------------------------

CREATE TABLE status(
        id_status Varchar (50) NOT NULL ,
        rights    Int NOT NULL ,
        PRIMARY KEY (id_status )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: reason
#------------------------------------------------------------

CREATE TABLE reason(
        id_reason Varchar (50) NOT NULL ,
        infos     Varchar (200) ,
        PRIMARY KEY (id_reason )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: domain
#------------------------------------------------------------

CREATE TABLE domain(
        id_domain int (11) Auto_increment  NOT NULL ,
        domain    Varchar (25) NOT NULL ,
        PRIMARY KEY (id_domain ) ,
        UNIQUE (domain )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: phone_indicative
#------------------------------------------------------------

CREATE TABLE phone_indicative(
        indicative Varchar (5) NOT NULL ,
        country    Varchar (25) NOT NULL ,
        PRIMARY KEY (indicative )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: state
#------------------------------------------------------------

CREATE TABLE state(
        id_state   int (11) Auto_increment  NOT NULL ,
        front      Bool NOT NULL ,
        back       Bool NOT NULL ,
        left_side  Bool NOT NULL ,
        right_side Bool NOT NULL ,
        inside     Bool NOT NULL ,
        commentary Varchar (280) ,
        id_resa    Int ,
        PRIMARY KEY (id_state )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: email_validate
#------------------------------------------------------------

CREATE TABLE email_validate(
        token           Varchar (50) NOT NULL ,
        expiration_time Datetime NOT NULL ,
        id_user         Float NOT NULL ,
        email           Varchar (25) NOT NULL ,
        PRIMARY KEY (token )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: has_access
#------------------------------------------------------------

CREATE TABLE has_access(
        id_user  Int NOT NULL ,
        id_place Int NOT NULL ,
        PRIMARY KEY (id_user ,id_place )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: has_domain
#------------------------------------------------------------

CREATE TABLE has_domain(
        id_facility Int NOT NULL ,
        id_domain   Int NOT NULL ,
        PRIMARY KEY (id_facility ,id_domain )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: work
#------------------------------------------------------------

CREATE TABLE work(
        id_user     Int NOT NULL ,
        id_facility Int NOT NULL ,
        PRIMARY KEY (id_user ,id_facility )
)ENGINE=InnoDB;

ALTER TABLE user ADD CONSTRAINT FK_user_id_status FOREIGN KEY (id_status) REFERENCES status(id_status);
ALTER TABLE user ADD CONSTRAINT FK_user_indicative FOREIGN KEY (indicative) REFERENCES phone_indicative(indicative);
ALTER TABLE borne ADD CONSTRAINT FK_borne_id_place FOREIGN KEY (id_place) REFERENCES place(id_place);
ALTER TABLE company_car ADD CONSTRAINT FK_company_car_id_facility FOREIGN KEY (id_facility) REFERENCES facility(id_facility);
ALTER TABLE place ADD CONSTRAINT FK_place_id_facility FOREIGN KEY (id_facility) REFERENCES facility(id_facility);
ALTER TABLE personal_car ADD CONSTRAINT FK_personal_car_id_user FOREIGN KEY (id_user) REFERENCES user(id_user);
ALTER TABLE resa_borne ADD CONSTRAINT FK_resa_borne_id_user FOREIGN KEY (id_user) REFERENCES user(id_user);
ALTER TABLE resa_borne ADD CONSTRAINT FK_resa_borne_id_place FOREIGN KEY (id_place) REFERENCES place(id_place);
ALTER TABLE resa_car ADD CONSTRAINT FK_resa_car_id_user FOREIGN KEY (id_user) REFERENCES user(id_user);
ALTER TABLE resa_car ADD CONSTRAINT FK_resa_car_id_company_car FOREIGN KEY (id_company_car) REFERENCES company_car(id_company_car);
ALTER TABLE resa_car ADD CONSTRAINT FK_resa_car_id_reason FOREIGN KEY (id_reason) REFERENCES reason(id_reason);
ALTER TABLE resa_car ADD CONSTRAINT FK_resa_car_id_state FOREIGN KEY (id_state) REFERENCES state(id_state);
ALTER TABLE state ADD CONSTRAINT FK_state_id_resa FOREIGN KEY (id_resa) REFERENCES resa_car(id_resa);
ALTER TABLE has_access ADD CONSTRAINT FK_has_access_id_user FOREIGN KEY (id_user) REFERENCES user(id_user);
ALTER TABLE has_access ADD CONSTRAINT FK_has_access_id_place FOREIGN KEY (id_place) REFERENCES place(id_place);
ALTER TABLE has_domain ADD CONSTRAINT FK_has_domain_id_facility FOREIGN KEY (id_facility) REFERENCES facility(id_facility);
ALTER TABLE has_domain ADD CONSTRAINT FK_has_domain_id_domain FOREIGN KEY (id_domain) REFERENCES domain(id_domain);
ALTER TABLE work ADD CONSTRAINT FK_work_id_user FOREIGN KEY (id_user) REFERENCES user(id_user);
ALTER TABLE work ADD CONSTRAINT FK_work_id_facility FOREIGN KEY (id_facility) REFERENCES facility(id_facility);


INSERT INTO `status` (`id_status`, `rights`) VALUES ('Visiteur', '0');
INSERT INTO `status` (`id_status`, `rights`) VALUES ('Professeur', '1');
INSERT INTO `status` (`id_status`, `rights`) VALUES ('Etudiant', '1');
INSERT INTO `status` (`id_status`, `rights`) VALUES ('Salarié', '1');
INSERT INTO `status` (`id_status`, `rights`) VALUES ('Admin', '2');
INSERT INTO `status` (`id_status`, `rights`) VALUES ('Super-Admin', '3');

INSERT INTO `facility` (`id_facility`, `name`, `address`, `complementary`) VALUES (NULL, 'Yncréa HDF', '29 Boulevard Vauban, 59800 Lille', '');
INSERT INTO `facility` (`id_facility`, `name`, `address`, `complementary`) VALUES (NULL, 'ICL', '60 Boulevard Vauban, 59800 Lille', '');
INSERT INTO `facility` (`id_facility`, `name`, `address`, `complementary`) VALUES (NULL, 'IESEG', '3 Rue de la Digue, 59800 Lille', '');

INSERT INTO `domain` (`id_domain`, `domain`) VALUES (NULL, 'yncrea.fr');
INSERT INTO `domain` (`id_domain`, `domain`) VALUES (NULL, 'ieseg.fr');
INSERT INTO `domain` (`id_domain`, `domain`) VALUES (NULL, 'univ-catholille.fr');

INSERT INTO `has_domain` (`id_facility`, `id_domain`) VALUES ('1', '1');
INSERT INTO `has_domain` (`id_facility`, `id_domain`) VALUES ('2', '3');
INSERT INTO `has_domain` (`id_facility`, `id_domain`) VALUES ('3', '2');

INSERT INTO `place` (`id_place`, `name`, `address`, `id_facility`) VALUES (NULL, 'Parking Yncréa', '29 Boulevard Vauban, 59800 Lille', '1');
INSERT INTO `place` (`id_place`, `name`, `address`, `id_facility`) VALUES (NULL, 'Parking IESEG', '3 Rue de la Digue, 59800 Lille', '3');
INSERT INTO `place` (`id_place`, `name`, `address`, `id_facility`) VALUES (NULL, 'Parking P1', '60 Boulevard Vauban, 59800 Lille', '2');

INSERT INTO `phone_indicative` (`indicative`, `country`) VALUES ('+32', 'Angleterre');
INSERT INTO `phone_indicative` (`indicative`, `country`) VALUES ('+33', 'France');

INSERT INTO `reason` (`id_reason`, `infos`) VALUES ('Visite', 'Visite chez un partenaire ou dans une entreprise');
INSERT INTO `reason` (`id_reason`, `infos`) VALUES ('Représentation', 'Lorsqu\'on réserve un véhicule pour aller dans un salon ou autre pour représenter l\'établissement');
