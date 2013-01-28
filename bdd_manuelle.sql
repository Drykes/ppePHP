#Ma base de données : 

DROP TABLE IF EXISTS PERSONNE;
CREATE TABLE PERSONNE(
        id_personne                    int(11) Auto_increment  NOT NULL ,
        nom_personne                   Varchar (25) NOT NULL ,
        prenom_personne                Varchar (25) NOT NULL ,
        mdp_personne                   Varchar (50) NOT NULL ,
        login_personne                 Varchar (25) NOT NULL ,
        mail_personne                  Varchar (25) NOT NULL ,
		administrateur		       Bool  NOT NULL ,
        id_type_personne Int  NOT NULL ,
        PRIMARY KEY (id_personne)
)ENGINE=InnoDB;

DROP TABLE IF EXISTS MATERIEL;
CREATE TABLE MATERIEL(
        id_materiel                    int(11) Auto_increment  NOT NULL ,
        date_circulation               Date  NOT NULL ,
        garantie                        Int  NOT NULL ,
        id_personne         Int  NOT NULL ,
        id_marque              Int  NOT NULL ,
        id_type_materiel Int  NOT NULL ,
        PRIMARY KEY (id_materiel)
)ENGINE=InnoDB;

DROP TABLE IF EXISTS LICENCE_DUREE;
CREATE TABLE LICENCE_DUREE(
        id_licence_duree      int(11) Auto_increment  NOT NULL ,
	nom_version           Varchar(25) NOT NULL ,
        debut_licence         Date  NOT NULL ,
        duree_licence         Int  NOT NULL ,
        id_personne Int  NOT NULL ,
        id_materiel  Int  NULL ,
        PRIMARY KEY (id_licence_duree)
)ENGINE=InnoDB;

DROP TABLE IF EXISTS MARQUE;
CREATE TABLE MARQUE(
        id_marque  int(11) Auto_increment  NOT NULL ,
        nom_marque Varchar (25) NOT NULL ,
        PRIMARY KEY (id_marque)
)ENGINE=InnoDB;

DROP TABLE IF EXISTS TYPE_MATERIEL;
CREATE TABLE TYPE_MATERIEL(
        id_type_materiel int(11) Auto_increment  NOT NULL ,
        type_materiel    Varchar (25) NOT NULL ,
        PRIMARY KEY (id_type_materiel)
)ENGINE=InnoDB;

DROP TABLE IF EXISTS TYPE_PERSONNE;
CREATE TABLE TYPE_PERSONNE(
        id_type_personne int(11) Auto_increment  NOT NULL ,
        type_personne    Varchar (25) NOT NULL ,
        PRIMARY KEY (id_type_personne)
)ENGINE=InnoDB;

DROP TABLE IF EXISTS LICENCE_VERSION;
CREATE TABLE LICENCE_VERSION(
        id_licence_version    int(11) Auto_increment  NOT NULL ,
        nom_version           Varchar(25) NOT NULL ,
        id_materiel  Int  NULL ,
        id_personne Int  NOT NULL ,
        PRIMARY KEY (id_licence_version)
)ENGINE=InnoDB;


ALTER TABLE PERSONNE ADD FOREIGN KEY (id_type_personne) REFERENCES TYPE_PERSONNE (id_type_personne);
ALTER TABLE MATERIEL ADD FOREIGN KEY (id_personne) REFERENCES PERSONNE(id_personne);
ALTER TABLE MATERIEL ADD FOREIGN KEY (id_marque) REFERENCES MARQUE(id_marque);
ALTER TABLE MATERIEL ADD FOREIGN KEY (id_type_materiel) REFERENCES TYPE_MATERIEL(id_type_materiel);
ALTER TABLE LICENCE_DUREE ADD FOREIGN KEY (id_personne) REFERENCES PERSONNE(id_personne);
ALTER TABLE LICENCE_DUREE ADD FOREIGN KEY (id_materiel) REFERENCES MATERIEL(id_materiel);
ALTER TABLE LICENCE_VERSION ADD FOREIGN KEY (id_materiel) REFERENCES MATERIEL(id_materiel);
ALTER TABLE LICENCE_VERSION ADD FOREIGN KEY (id_personne) REFERENCES PERSONNE(id_personne);



/*INSERTIONS DES DONNEES*/

INSERT INTO TYPE_PERSONNE VALUES 
(default,"Informaticien"),
(default,"Gestionnaire");


INSERT INTO `PERSONNE` VALUES(default, 'AVAKOV', 'David', '7d49e943dda48fc707856282481f9a86', 'david', 'avakov@hotmail.fr', 1, 2);
INSERT INTO `PERSONNE` VALUES(default, 'Pires Lopes', 'Lucas', '225a271f3f85d9f0f96af2fa41f90d50', 'lucas', 'lucas.pireslopes@gmail.com', 1, 1);
INSERT INTO `PERSONNE` VALUES(default, 'Chhay', 'Thierry', '2877feac0298359a58361c03e83b9a3c', 'thierry', 'chhay.thierry@gmail.com', 1, 1);
INSERT INTO `PERSONNE` VALUES(default, 'admin', 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin', 'admin@admin.admin', 1, 1);


INSERT INTO TYPE_MATERIEL VALUES 
(default, "Ordinateur"),
(default, "Tablette"),
(default, "Imprimante");


INSERT INTO `MARQUE` VALUES(default, 'ASUS');
INSERT INTO `MARQUE` VALUES(default, 'HP');
INSERT INTO `MARQUE` VALUES(default, 'Apple');


INSERT INTO `MATERIEL` VALUES(default, '1999-02-14', 3, 1, 2, 1);
INSERT INTO `MATERIEL` VALUES(default, '2004-02-14', 2, 2, 1, 2);
INSERT INTO `MATERIEL` VALUES(default, '2007-11-02', 1, 1, 2, 3);
INSERT INTO `MATERIEL` VALUES(default, '2012-10-10', 2, 1, 1, 3);


INSERT INTO `LICENCE_DUREE` VALUES(default, 'Bitdefender', '2011-12-07', 2, 2, 2);
INSERT INTO `LICENCE_DUREE` VALUES(default, 'Bitdefender', '2012-10-17', 2, 1, 3);
INSERT INTO `LICENCE_DUREE` VALUES(default, 'Norton', '2010-10-30', 1, 1, NULL);


INSERT INTO `LICENCE_VERSION` VALUES(default, 'Ubuntu 12', NULL, 3);
