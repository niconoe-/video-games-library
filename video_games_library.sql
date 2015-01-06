-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Lun 08 Décembre 2014 à 22:19
-- Version du serveur :  5.6.20-log
-- Version de PHP :  5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `video_games_library`
--

-- --------------------------------------------------------

--
-- Structure de la table `game`
--

CREATE TABLE IF NOT EXISTS `game` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `idPlatform` int(10) unsigned NOT NULL,
  `releaseDate` date DEFAULT NULL,
  `overview` longtext,
  `ESRB` varchar(255) DEFAULT NULL,
  `players` varchar(255) DEFAULT NULL,
  `isCoop` tinyint(1) unsigned DEFAULT NULL,
  `youtube` mediumtext,
  `publisher` varchar(255) DEFAULT NULL,
  `developer` varchar(255) DEFAULT NULL,
  `rating` decimal(8,6) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `games_genres`
--

CREATE TABLE IF NOT EXISTS `games_genres` (
  `idGame` int(10) unsigned NOT NULL,
  `idGenre` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `games_images`
--

CREATE TABLE IF NOT EXISTS `games_images` (
  `idGame` int(10) unsigned NOT NULL,
  `idImage` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `genre`
--

CREATE TABLE IF NOT EXISTS `genre` (
`id` int(10) unsigned NOT NULL,
  `genre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `image`
--

CREATE TABLE IF NOT EXISTS `image` (
`id` int(10) unsigned NOT NULL,
  `category` enum('boxartFront','boxartBack','banner','logo','fanart','screenshot','consoleArt','controllerArt') NOT NULL,
  `scaleType` enum('original','thumb') NOT NULL,
  `width` int(10) unsigned DEFAULT NULL,
  `height` int(10) unsigned DEFAULT NULL,
  `relativeURL` varchar(255) NOT NULL,
  `dataPicture` longblob
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `platform`
--

CREATE TABLE IF NOT EXISTS `platform` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `overview` longtext,
  `developer` varchar(255) DEFAULT NULL,
  `manufacturer` varchar(255) DEFAULT NULL,
  `cpu` varchar(255) DEFAULT NULL,
  `memory` varchar(255) DEFAULT NULL,
  `sound` varchar(255) DEFAULT NULL,
  `display` varchar(255) DEFAULT NULL,
  `media` varchar(255) DEFAULT NULL,
  `maxController` int(10) unsigned DEFAULT NULL,
  `youtube` mediumtext,
  `rating` decimal(8,6) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `platforms_images`
--

CREATE TABLE IF NOT EXISTS `platforms_images` (
  `idPlatform` int(10) unsigned NOT NULL,
  `idImage` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `update`
--

CREATE TABLE IF NOT EXISTS `update` (
`id` int(10) unsigned NOT NULL,
  `timestampAtRun` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `game`
--
ALTER TABLE `game`
 ADD PRIMARY KEY (`id`), ADD KEY `title` (`title`);

--
-- Index pour la table `games_genres`
--
ALTER TABLE `games_genres`
 ADD PRIMARY KEY (`idGame`,`idGenre`);

--
-- Index pour la table `games_images`
--
ALTER TABLE `games_images`
 ADD PRIMARY KEY (`idGame`,`idImage`);

--
-- Index pour la table `genre`
--
ALTER TABLE `genre`
 ADD PRIMARY KEY (`id`);

--
-- Index pour la table `image`
--
ALTER TABLE `image`
 ADD PRIMARY KEY (`id`), ADD KEY `category` (`category`);

--
-- Index pour la table `platform`
--
ALTER TABLE `platform`
 ADD PRIMARY KEY (`id`), ADD KEY `name` (`name`);

--
-- Index pour la table `platforms_images`
--
ALTER TABLE `platforms_images`
 ADD PRIMARY KEY (`idPlatform`,`idImage`);

--
-- Index pour la table `update`
--
ALTER TABLE `update`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `genre`
--
ALTER TABLE `genre`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `image`
--
ALTER TABLE `image`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `update`
--
ALTER TABLE `update`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
