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
        phone_number Varchar (25) NOT NULL ,
        first_name   Varchar (50) NOT NULL ,
        last_name    Varchar (50) NOT NULL ,
        activated    Bool NOT NULL ,
        id_status    Varchar (50) NOT NULL ,
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
        PRIMARY KEY (id_facility )
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
        date_resa      Date NOT NULL ,
        start_time     Time NOT NULL ,
        end_time       Time NOT NULL ,
        reason         Varchar (200) ,
        km_start       Int ,
        km_end         Int ,
        km_planned     Int NOT NULL ,
        id_user        Int NOT NULL ,
        id_company_car Int NOT NULL ,
        id_reason      Varchar (50) NOT NULL ,
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
# Table: has_access
#------------------------------------------------------------

CREATE TABLE has_access(
        id_user  Int NOT NULL ,
        id_place Int NOT NULL ,
        PRIMARY KEY (id_user ,id_place )
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
ALTER TABLE borne ADD CONSTRAINT FK_borne_id_place FOREIGN KEY (id_place) REFERENCES place(id_place);
ALTER TABLE company_car ADD CONSTRAINT FK_company_car_id_facility FOREIGN KEY (id_facility) REFERENCES facility(id_facility);
ALTER TABLE place ADD CONSTRAINT FK_place_id_facility FOREIGN KEY (id_facility) REFERENCES facility(id_facility);
ALTER TABLE personal_car ADD CONSTRAINT FK_personal_car_id_user FOREIGN KEY (id_user) REFERENCES user(id_user);
ALTER TABLE resa_borne ADD CONSTRAINT FK_resa_borne_id_user FOREIGN KEY (id_user) REFERENCES user(id_user);
ALTER TABLE resa_borne ADD CONSTRAINT FK_resa_borne_id_place FOREIGN KEY (id_place) REFERENCES place(id_place);
ALTER TABLE resa_car ADD CONSTRAINT FK_resa_car_id_user FOREIGN KEY (id_user) REFERENCES user(id_user);
ALTER TABLE resa_car ADD CONSTRAINT FK_resa_car_id_company_car FOREIGN KEY (id_company_car) REFERENCES company_car(id_company_car);
ALTER TABLE resa_car ADD CONSTRAINT FK_resa_car_id_reason FOREIGN KEY (id_reason) REFERENCES reason(id_reason);
ALTER TABLE has_access ADD CONSTRAINT FK_has_access_id_user FOREIGN KEY (id_user) REFERENCES user(id_user);
ALTER TABLE has_access ADD CONSTRAINT FK_has_access_id_place FOREIGN KEY (id_place) REFERENCES place(id_place);
ALTER TABLE work ADD CONSTRAINT FK_work_id_user FOREIGN KEY (id_user) REFERENCES user(id_user);
ALTER TABLE work ADD CONSTRAINT FK_work_id_facility FOREIGN KEY (id_facility) REFERENCES facility(id_facility);
