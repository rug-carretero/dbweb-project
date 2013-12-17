-- phpMyAdmin SQL Dump
-- version 4.0.4.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 17, 2013 at 11:21 AM
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `groupID`, `username`, `password`, `salt`, `email`, `age`, `gender`, `location`, `nationality`) VALUES
(1, '1', 'carretero', 'b9facfc27374df152637590ab63425215a1967c2056bbc5e094ab3082b6007570ed0fbd888d6900064e637b6d649614f36fa29678e89700425013b9ea44b73f0', '21204839552a84bedf2ed52.46353070', 'thomcarretero@gmail.com', 1991, 'M', 'NL', 'Dutch'),
(2, '1', 'ynte', '1c01b5081f9e68975fb87eace0e41a2242f2fe3ed4ec48bed7e81cffaf4201965e0b1c095dbd927d374c3fc4a537d4052fce6df1e7a28a9dcbcd15b8c8ceb45b', '39420814852a8807a0b6c16.60501870', 's2253216@student.rug.nl', 1993, 'M', 'NL', 'Dutch');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
