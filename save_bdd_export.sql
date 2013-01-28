-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 01, 2012 at 05:49 AM
-- Server version: 5.1.57
-- PHP Version: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `a5300961_gsb`
--

-- --------------------------------------------------------

--
-- Table structure for table `LICENCE_DUREE`
--

CREATE TABLE `LICENCE_DUREE` (
  `id_licence_duree` int(11) NOT NULL AUTO_INCREMENT,
  `nom_version` varchar(25) COLLATE latin1_general_ci NOT NULL,
  `debut_licence` date NOT NULL,
  `duree_licence` int(11) NOT NULL,
  `id_personne` int(11) NOT NULL,
  `id_materiel` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_licence_duree`),
  KEY `id_personne` (`id_personne`),
  KEY `id_materiel` (`id_materiel`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `LICENCE_DUREE`
--

INSERT INTO `LICENCE_DUREE` VALUES(1, 'Bitdefender', '2011-12-07', 2, 2, 2);
INSERT INTO `LICENCE_DUREE` VALUES(2, 'Bitdefender', '2012-10-17', 2, 1, 3);
INSERT INTO `LICENCE_DUREE` VALUES(3, 'Norton', '2010-10-30', 1, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `LICENCE_VERSION`
--

CREATE TABLE `LICENCE_VERSION` (
  `id_licence_version` int(11) NOT NULL AUTO_INCREMENT,
  `nom_version` varchar(25) COLLATE latin1_general_ci NOT NULL,
  `id_materiel` int(11) DEFAULT NULL,
  `id_personne` int(11) NOT NULL,
  PRIMARY KEY (`id_licence_version`),
  KEY `id_materiel` (`id_materiel`),
  KEY `id_personne` (`id_personne`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `LICENCE_VERSION`
--

INSERT INTO `LICENCE_VERSION` VALUES(3, 'Ubuntu 12', NULL, 3);

-- --------------------------------------------------------

--
-- Table structure for table `MARQUE`
--

CREATE TABLE `MARQUE` (
  `id_marque` int(11) NOT NULL AUTO_INCREMENT,
  `nom_marque` varchar(25) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id_marque`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `MARQUE`
--

INSERT INTO `MARQUE` VALUES(1, 'ASUS');
INSERT INTO `MARQUE` VALUES(2, 'HP');
INSERT INTO `MARQUE` VALUES(3, 'Apple');

-- --------------------------------------------------------

--
-- Table structure for table `MATERIEL`
--

CREATE TABLE `MATERIEL` (
  `id_materiel` int(11) NOT NULL AUTO_INCREMENT,
  `date_circulation` date NOT NULL,
  `garantie` int(11) NOT NULL,
  `id_personne` int(11) NOT NULL,
  `id_marque` int(11) NOT NULL,
  `id_type_materiel` int(11) NOT NULL,
  PRIMARY KEY (`id_materiel`),
  KEY `id_personne` (`id_personne`),
  KEY `id_marque` (`id_marque`),
  KEY `id_type_materiel` (`id_type_materiel`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `MATERIEL`
--

INSERT INTO `MATERIEL` VALUES(1, '1999-02-14', 3, 1, 2, 1);
INSERT INTO `MATERIEL` VALUES(2, '2004-02-14', 2, 2, 1, 2);
INSERT INTO `MATERIEL` VALUES(3, '2007-11-02', 1, 1, 2, 3);
INSERT INTO `MATERIEL` VALUES(4, '2012-10-10', 2, 1, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `PERSONNE`
--

CREATE TABLE `PERSONNE` (
  `id_personne` int(11) NOT NULL AUTO_INCREMENT,
  `nom_personne` varchar(25) COLLATE latin1_general_ci NOT NULL,
  `prenom_personne` varchar(25) COLLATE latin1_general_ci NOT NULL,
  `mdp_personne` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `login_personne` varchar(25) COLLATE latin1_general_ci NOT NULL,
  `mail_personne` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `administrateur` tinyint(1) NOT NULL,
  `id_type_personne` int(11) NOT NULL,
  PRIMARY KEY (`id_personne`),
  KEY `id_type_personne` (`id_type_personne`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `PERSONNE`
--

INSERT INTO `PERSONNE` VALUES(1, 'AVAKOV', 'David', '7d49e943dda48fc707856282481f9a86', 'david', 'avakov@hotmail.fr', 1, 2);
INSERT INTO `PERSONNE` VALUES(2, 'Pires Lopes', 'Lucas', '225a271f3f85d9f0f96af2fa41f90d50', 'lucas', 'lucas.pireslopes@gmail.com', 1, 1);
INSERT INTO `PERSONNE` VALUES(3, 'Chhay', 'Thierry', '2877feac0298359a58361c03e83b9a3c', 'thierry', 'chhay.thierry@gmail.com', 1, 1);
INSERT INTO `PERSONNE` VALUES(4, 'admin', 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin', 'admin@admin.admin', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `TYPE_MATERIEL`
--

CREATE TABLE `TYPE_MATERIEL` (
  `id_type_materiel` int(11) NOT NULL AUTO_INCREMENT,
  `type_materiel` varchar(25) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id_type_materiel`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `TYPE_MATERIEL`
--

INSERT INTO `TYPE_MATERIEL` VALUES(1, 'Ordinateur');
INSERT INTO `TYPE_MATERIEL` VALUES(2, 'Tablette');
INSERT INTO `TYPE_MATERIEL` VALUES(3, 'Imprimante');

-- --------------------------------------------------------

--
-- Table structure for table `TYPE_PERSONNE`
--

CREATE TABLE `TYPE_PERSONNE` (
  `id_type_personne` int(11) NOT NULL AUTO_INCREMENT,
  `type_personne` varchar(25) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id_type_personne`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `TYPE_PERSONNE`
--

INSERT INTO `TYPE_PERSONNE` VALUES(1, 'Informaticien');
INSERT INTO `TYPE_PERSONNE` VALUES(2, 'Gestionnaire');
