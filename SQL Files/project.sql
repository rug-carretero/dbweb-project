-- phpMyAdmin SQL Dump
-- version 4.0.4.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 27, 2014 at 01:15 PM
-- Server version: 5.6.13
-- PHP Version: 5.4.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `project`
--
CREATE DATABASE IF NOT EXISTS `project` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `project`;

-- --------------------------------------------------------

--
-- Table structure for table `interests`
--

CREATE TABLE IF NOT EXISTS `interests` (
  `interestID` int(50) NOT NULL AUTO_INCREMENT,
  `userID` int(50) NOT NULL,
  `sports` tinyint(1) NOT NULL,
  `politics` tinyint(1) NOT NULL,
  `movies` tinyint(1) NOT NULL,
  PRIMARY KEY (`interestID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `interests`
--

INSERT INTO `interests` (`interestID`, `userID`, `sports`, `politics`, `movies`) VALUES
(1, 1, 1, 1, 1),
(2, 2, 0, 0, 0),
(3, 3, 0, 0, 0),
(4, 4, 0, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `joinproject`
--

CREATE TABLE IF NOT EXISTS `joinproject` (
  `joinID` int(20) NOT NULL AUTO_INCREMENT,
  `userID` int(20) NOT NULL,
  `projectID` int(20) NOT NULL,
  PRIMARY KEY (`joinID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `joinproject`
--

INSERT INTO `joinproject` (`joinID`, `userID`, `projectID`) VALUES
(1, 5, 1),
(2, 5, 1),
(3, 1, 5),
(4, 1, 5);

-- --------------------------------------------------------

--
-- Table structure for table `multichoiseanswers`
--

CREATE TABLE IF NOT EXISTS `multichoiseanswers` (
  `mcID` int(255) NOT NULL AUTO_INCREMENT,
  `projectID` int(255) NOT NULL,
  `questionID` int(255) NOT NULL,
  `option1` varchar(255) NOT NULL,
  `option2` varchar(255) NOT NULL,
  `option3` varchar(255) NOT NULL,
  `option4` varchar(255) NOT NULL,
  PRIMARY KEY (`mcID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `project`
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
  `reward` int(10) NOT NULL,
  `interests` varchar(255) NOT NULL,
  PRIMARY KEY (`projectID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `project`
--

INSERT INTO `project` (`projectID`, `userID`, `name`, `beginDate`, `endDate`, `agePref`, `locationPref`, `nationalityPref`, `reward`, `interests`) VALUES
(1, 4, 'Koekoek', '2014-01-01 00:00:00', '2014-02-02 00:00:00', 1, '1', '0', 0, 'sports'),
(2, 4, 'Koekoek', '2014-01-01 00:00:00', '2014-02-02 00:00:00', 1, '1', '0', 0, 'sports'),
(3, 1, '''Test project 1''', '2014-01-01 00:00:00', '2014-02-02 00:00:00', 1, '1', '0', 0, 'sports,politics'),
(4, 1, 'Test project 1', '2014-01-01 00:00:00', '2014-02-02 00:00:00', 3, 'UK', 'NL', 0, 'sports,politics,movies'),
(5, 1, 'Test project 1', '2014-01-01 00:00:00', '2014-01-31 00:00:00', 0, '', '', 0, 'sports,politics,movies');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE IF NOT EXISTS `questions` (
  `questionID` int(255) NOT NULL AUTO_INCREMENT,
  `projectID` int(11) NOT NULL,
  `type` enum('multi','open') NOT NULL,
  `title` varchar(255) NOT NULL,
  `option1` varchar(255) NOT NULL,
  `option2` varchar(255) NOT NULL,
  `option3` varchar(255) NOT NULL,
  `option4` varchar(255) NOT NULL,
  PRIMARY KEY (`questionID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`questionID`, `projectID`, `type`, `title`, `option1`, `option2`, `option3`, `option4`) VALUES
(1, 0, 'open', '''What is 4+4?''', '', '', '', ''),
(2, 1, 'open', 'What is 4+4?', '', '', '', ''),
(3, 1, 'multi', 'What is 1-1?', '1', '0', '', ''),
(4, 1, 'multi', 'What is 5+5?', '5', '10', '15', '20'),
(5, 5, 'multi', 'What is 4+4?', '5', '10', '15', '20');

-- --------------------------------------------------------

--
-- Table structure for table `users`
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `groupID`, `username`, `password`, `salt`, `email`, `age`, `gender`, `location`, `nationality`) VALUES
(1, '2', 'carretero', 'cf1f05f5583022cf8376fd851670b43fcbdddfb6f46b435b3010af0c3aa142ec984b724d6f5e1195c36701ba83b23c95d19250e988b339f48fa27c3d37f74352', '21204839552a84bedf2ed52.46353070', 'thomcarretero@gmail.com', 1991, 'M', 'UK', 'BE'),
(2, '1', 'ynte', '1c01b5081f9e68975fb87eace0e41a2242f2fe3ed4ec48bed7e81cffaf4201965e0b1c095dbd927d374c3fc4a537d4052fce6df1e7a28a9dcbcd15b8c8ceb45b', '39420814852a8807a0b6c16.60501870', 's2253216@student.rug.nl', 1993, 'M', 'NL', 'Dutch'),
(4, '2', 'koekoek', 'e4be3f6b38e7a876527bf54eb260359de4717cfca14449b4efa5abd078460a319d6cf01568459656d33f5be5559e603b35c897ea49bb7f081f797cdf02adfc4f', '86784971252b492bae08532.81648136', 'thomcarasfafretero@gmail.com', 1970, 'F', 'NL', 'Dutch');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
