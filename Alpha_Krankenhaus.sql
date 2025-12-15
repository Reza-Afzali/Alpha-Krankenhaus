-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 15. Dez 2025 um 16:03
-- Server-Version: 10.4.32-MariaDB
-- PHP-Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `alphahospital`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `first_name` varchar(120) NOT NULL,
  `last_name` varchar(120) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `appt_date` date NOT NULL,
  `appt_time` varchar(50) NOT NULL,
  `department` varchar(120) DEFAULT NULL,
  `doctor` varchar(120) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'Pending',
  `approved_by_user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `appointments`
--

INSERT INTO `appointments` (`id`, `first_name`, `last_name`, `email`, `phone`, `appt_date`, `appt_time`, `department`, `doctor`, `reason`, `created_at`, `status`, `approved_by_user_id`) VALUES
(43, 'muhire', 't', 'muhire@gmail.com', '4555555555', '2025-12-07', '12:12', 'Cardiology', 'Dr. Smith', 'ty', '2025-12-08 19:58:05', 'Approved', 15),
(44, 'mugisha', 'Jack', 'jack@gmail.com', '4555555555', '2025-12-07', '10:22', 'Pediatrics', 'Dr. Smith', 'ui uj', '2025-12-08 19:59:29', 'Approved', 15),
(45, 'John', 'Rukundo', 'john@gmail.com', '576345436534675', '2025-12-19', '01:36', 'Pediatrics', 'Dr. Lee', 'fdvhgshvgfsdf', '2025-12-09 11:07:57', 'Approved', 15),
(102, 'dfdsfdsf', 'dsfdsfds', 'ds@gmail.com', '4555555555', '2025-12-25', '8:45', 'Pediatrics', 'Dr. J. Muller', 'dfdsfdsf', '2025-12-10 16:39:07', 'Approved', 15),
(119, 'ljhag', 'öjhgkjy', 'bkjnl@gmail.com', '0987658790', '2025-12-10', '15:00', 'Pediatrics', 'Dr. J. Muller', 'trrziuzui', '2025-12-12 07:48:06', 'Approved', NULL),
(120, 'Hani', 'labib', 'hani@gmail.com', '0134785983475', '2025-12-13', '11:45', 'Pediatrics', 'Dr. M. Sana', 'fehwdhwjkrhwekr', '2025-12-12 09:45:12', 'Approved', NULL),
(121, 'khgds', 'shgsfs', 'fcccccccccccc@gmail.com', '876575656', '2025-12-09', '19:00', 'Pediatrics', '', 'hjhjgfhgff', '2025-12-12 10:17:51', 'Approved', NULL),
(122, 'zia', 'nader', 'zia@gmail.com', '03248727492837', '2025-12-17', '13:45', 'Surgery', 'Dr. M. Sana', 'ich', '2025-12-15 14:55:21', 'Pending', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `department`
--

CREATE TABLE `department` (
  `DepartmentID` int(11) NOT NULL,
  `DepartmentName` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `department`
--

INSERT INTO `department` (`DepartmentID`, `DepartmentName`) VALUES
(1, 'Cardiology'),
(2, 'Neurology'),
(3, 'Pediatrics');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `doctor`
--

CREATE TABLE `doctor` (
  `DoctorID` int(11) NOT NULL,
  `DoctorName` varchar(100) NOT NULL,
  `Specialty` varchar(100) DEFAULT NULL,
  `DepartmentID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `doctor`
--

INSERT INTO `doctor` (`DoctorID`, `DoctorName`, `Specialty`, `DepartmentID`) VALUES
(101, 'Dr. Smith', 'Heart Specialist', 1),
(102, 'Dr. Johnson', 'Brain Surgeon', 2),
(103, 'Dr. Lee', 'Child Specialist', 3);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `messages`
--

INSERT INTO `messages` (`id`, `name`, `email`, `subject`, `message`, `created_at`) VALUES
(14, 'Kommissionieren', 'emilemarc2@gmail.com', 'Billing Question', 'hj', '2025-12-08 18:13:13'),
(15, 'Emile Jean Marc Rukundo', 'emilemarc55@gmail.com', 'Billing Question', 'ty', '2025-12-08 19:14:50'),
(17, 'ahmad', 'ahmad@gmail.com', 'Abrechnungsfrage', 'Ich habe schon bei Sie zwei mal getroffen.', '2025-12-15 14:52:01');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `dob`, `email`, `role`, `password_hash`, `created_at`) VALUES
(22, 'Reza', '1992-01-11', 'reza@gmail.com', 'Admin', '$2y$10$62sfQGfUiouWOYB8iFZUluSpCbQFvTLJ7e75MxNvsGxhZMbJcbVf6', '2025-12-11 12:52:20'),
(24, 'ahmad', '1995-12-12', 'ahmad@gmail.com', 'Staff', '$2y$10$dX3huyQhyKaVrQ4kMMchMeSsD0UrAdHJ9Z2F.R.r5q4fiZDWT6ReW', '2025-12-11 13:14:14'),
(25, 'mahmood', '2025-12-17', 'mahmood@gmail.com', 'Admin', '$2y$10$tyl0x40eDv7LwfC4iFkV9eL76J.HrkrwxACiEYwDvm/LJh/Iq.J/O', '2025-12-11 21:38:01'),
(26, 'mohammed', '1998-12-12', 'mohammed@gmail.com', 'Doctor', '$2y$10$oeyZaZeDJMcOvQ/Evs4WUedsxXsfGD9oAM6pdoC2aDIYeDYV8cgLa', '2025-12-12 07:55:44'),
(27, 'hamid', '2002-12-12', 'hamid@gmail.com', 'Doctor', '$2y$10$mjb4z0g.da4LJTCILoVc3eEoHdOimOFOOmISG4vjLVEx29iG6hlKG', '2025-12-12 09:06:11'),
(28, 'rowaida', '2000-12-12', 'rowaida@gmail.com', 'Doctor', '$2y$10$eMTF7tWhoN2afKj8s1rWkOTU4sId7IDK3Flo/IVWGdrST4SYUhhXS', '2025-12-12 09:34:08'),
(29, 'emile', '2025-12-10', 'g@g.com', 'Staff', '$2y$10$JA1LAoz2JlDKVc9Uu6bcS.YGuZzSKiLDL3tdlFk0Ww4JTTpV1aEca', '2025-12-12 09:43:50'),
(30, 'emilemarc', '1999-12-12', 'emilemarc96@gmail.com', 'Admin', '$2y$10$GctV779.ZS90YfNlAZ3xVuR2Hxf0LWQIp2CRBiRSPyJVTB.OeaeDS', '2025-12-12 10:02:21'),
(31, 'reza', '1999-12-12', 'rezas@gmail.com', 'Admin', '$2y$10$vVoMwS5Mt2wCbysB5h92kOmuWUnVC8624iNE8z/K0/ymwIeagtN86', '2025-12-12 10:04:54'),
(32, 'emile marc', '2025-12-18', 'emile@gmail.com', 'Admin', '$2y$10$yFqjp3EwyQSBIVxQYN93COgErk434.99QSjM4A82gqkugPHUGJZoW', '2025-12-12 10:25:39'),
(33, 'ali', '2003-02-15', 'ali@gmail.com', 'Doctor', '$2y$10$pmG874z.UYXGa8DeuF7Hk.QagxL0QS5VovokSXKy49xJmaXTvr.2G', '2025-12-15 14:54:08');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`DepartmentID`);

--
-- Indizes für die Tabelle `doctor`
--
ALTER TABLE `doctor`
  ADD PRIMARY KEY (`DoctorID`),
  ADD KEY `DepartmentID` (`DepartmentID`);

--
-- Indizes für die Tabelle `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT für Tabelle `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `doctor`
--
ALTER TABLE `doctor`
  ADD CONSTRAINT `doctor_ibfk_1` FOREIGN KEY (`DepartmentID`) REFERENCES `department` (`DepartmentID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
