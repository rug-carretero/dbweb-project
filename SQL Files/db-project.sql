-- phpMyAdmin SQL Dump
-- version 4.0.4.2
-- http://www.phpmyadmin.net
--
-- Machine: localhost
-- Genereertijd: 31 jan 2014 om 13:41
-- Serverversie: 5.6.13
-- PHP-versie: 5.4.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databank: `db-project`
--
CREATE DATABASE IF NOT EXISTS `db-project` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `db-project`;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `answer`
--

CREATE TABLE IF NOT EXISTS `answer` (
  `answerID` int(20) NOT NULL AUTO_INCREMENT,
  `questionID` int(20) NOT NULL,
  `userID` int(20) NOT NULL,
  `projectID` int(20) NOT NULL,
  `answerMulti` int(1) NOT NULL,
  `answerOpen` text NOT NULL,
  PRIMARY KEY (`answerID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Gegevens worden uitgevoerd voor tabel `answer`
--

INSERT INTO `answer` (`answerID`, `questionID`, `userID`, `projectID`, `answerMulti`, `answerOpen`) VALUES
(1, 1, 2, 1, 1, ''),
(2, 2, 2, 1, 1, ''),
(3, 3, 2, 1, 0, 'No');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `interests`
--

CREATE TABLE IF NOT EXISTS `interests` (
  `interestID` int(50) NOT NULL AUTO_INCREMENT,
  `userID` int(50) NOT NULL,
  `sports` tinyint(1) NOT NULL,
  `politics` tinyint(1) NOT NULL,
  `movies` tinyint(1) NOT NULL,
  `weather` tinyint(1) NOT NULL,
  PRIMARY KEY (`interestID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Gegevens worden uitgevoerd voor tabel `interests`
--

INSERT INTO `interests` (`interestID`, `userID`, `sports`, `politics`, `movies`, `weather`) VALUES
(5, 1, 0, 0, 0, 0),
(6, 2, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `joinproject`
--

CREATE TABLE IF NOT EXISTS `joinproject` (
  `joinID` int(20) NOT NULL AUTO_INCREMENT,
  `userID` int(20) NOT NULL,
  `projectID` int(20) NOT NULL,
  PRIMARY KEY (`joinID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Gegevens worden uitgevoerd voor tabel `joinproject`
--

INSERT INTO `joinproject` (`joinID`, `userID`, `projectID`) VALUES
(1, 2, 1);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `loginlog`
--

CREATE TABLE IF NOT EXISTS `loginlog` (
  `logID` int(20) NOT NULL AUTO_INCREMENT,
  `userID` int(20) NOT NULL,
  `username` varchar(30) NOT NULL,
  `sessieID` int(6) NOT NULL,
  PRIMARY KEY (`logID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Gegevens worden uitgevoerd voor tabel `loginlog`
--

INSERT INTO `loginlog` (`logID`, `userID`, `username`, `sessieID`) VALUES
(1, 2, 'owner', 921420);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `project`
--

CREATE TABLE IF NOT EXISTS `project` (
  `projectID` int(50) NOT NULL AUTO_INCREMENT,
  `userID` int(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `beginDate` datetime NOT NULL,
  `endDate` datetime NOT NULL,
  `agePref` int(2) NOT NULL,
  `locationPref` varchar(2) NOT NULL,
  `nationalityPref` varchar(2) NOT NULL,
  `interests` varchar(255) NOT NULL,
  PRIMARY KEY (`projectID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Gegevens worden uitgevoerd voor tabel `project`
--

INSERT INTO `project` (`projectID`, `userID`, `name`, `beginDate`, `endDate`, `agePref`, `locationPref`, `nationalityPref`, `interests`) VALUES
(1, 2, 'Maths', '2014-01-31 23:59:59', '2014-02-25 23:59:59', 0, '', '', ''),
(2, 2, 'Supermarkets', '2014-01-31 23:59:59', '2014-02-25 23:59:59', 0, '', '', '');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `questions`
--

CREATE TABLE IF NOT EXISTS `questions` (
  `questionID` int(255) NOT NULL AUTO_INCREMENT,
  `userID` int(20) NOT NULL,
  `projectID` int(11) NOT NULL,
  `type` enum('multi','open') NOT NULL,
  `title` varchar(255) NOT NULL,
  `option1` varchar(255) NOT NULL,
  `option2` varchar(255) NOT NULL,
  `option3` varchar(255) NOT NULL,
  `option4` varchar(255) NOT NULL,
  PRIMARY KEY (`questionID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Gegevens worden uitgevoerd voor tabel `questions`
--

INSERT INTO `questions` (`questionID`, `userID`, `projectID`, `type`, `title`, `option1`, `option2`, `option3`, `option4`) VALUES
(1, 2, 1, 'multi', 'What is 4+4?', '6', '7', '8', '9'),
(2, 2, 1, 'multi', 'Is 97 - 52 = 40', 'Yes', 'No', '', ''),
(3, 2, 1, 'open', 'Any comments?', '', '', '', ''),
(4, 2, 2, 'multi', 'Best supermarket?', 'Albert Heijn', 'C1000', 'Super de Boer', 'Jumbo');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `userID` int(255) NOT NULL AUTO_INCREMENT,
  `groupID` enum('1','2') NOT NULL,
  `username` varchar(200) NOT NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(100) NOT NULL,
  `email` varchar(250) NOT NULL,
  `age` smallint(4) NOT NULL,
  `gender` enum('F','M') NOT NULL,
  `location` varchar(2) DEFAULT NULL,
  `nationality` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`userID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Gegevens worden uitgevoerd voor tabel `users`
--

INSERT INTO `users` (`userID`, `groupID`, `username`, `password`, `salt`, `email`, `age`, `gender`, `location`, `nationality`) VALUES
(1, '1', 'user', '426628f2b19f69052cb1e602a945da2b3f2b42b8966e8e5e10e422d9fac2427dcda5d4c89a1f63a6a3b3dabd55602db3dab69cb0b02dee66f1559d828890a025', '78605688352eba3385902d1.97619642', 'user@user.com', 0, 'F', NULL, NULL),
(2, '2', 'owner', 'ed649510dd70cf85f01b9b665f161d6246f006047995e6d0e3b99692006a2d2dcffee92f07be2ad8a8448f2cb85e0f66f9e82b81fcdef6aa38a8257140b987e8', '200628980052eba386e38385.96117946', 'owner@owner.com', 0, 'F', NULL, NULL);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
