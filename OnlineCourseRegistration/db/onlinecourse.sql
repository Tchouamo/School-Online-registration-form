-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 01 déc. 2024 à 15:13
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `onlinecourse`
--

-- --------------------------------------------------------

--
-- Structure de la table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `creationDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `updationDate` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `creationDate`, `updationDate`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', '2020-01-24 16:21:18', '03-06-2020 07:09:07 PM');

-- --------------------------------------------------------

--
-- Structure de la table `classroom`
--

CREATE TABLE `classroom` (
  `id` int(11) NOT NULL,
  `classroom` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `creationDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `classroom`
--

INSERT INTO `classroom` (`id`, `classroom`, `creationDate`) VALUES
(4, 'MBA 8', '2024-11-19 14:19:21'),
(5, 'S11', '2024-11-24 07:47:42'),
(1, 'S9', '2024-11-19 13:57:02');

-- --------------------------------------------------------

--
-- Structure de la table `course`
--

CREATE TABLE `course` (
  `id` int(11) NOT NULL,
  `courseCode` varchar(255) NOT NULL,
  `courseName` varchar(255) NOT NULL,
  `courseUnit` int(11) NOT NULL,
  `passinggrade` varchar(3) NOT NULL,
  `level` varchar(255) NOT NULL,
  `creationDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `updationDate` varchar(255) NOT NULL,
  `teacherAssigned` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `course`
--

INSERT INTO `course` (`id`, `courseCode`, `courseName`, `courseUnit`, `passinggrade`, `level`, `creationDate`, `updationDate`, `teacherAssigned`) VALUES
(65, 'CS2001', 'Java1', 3, 'C', 'Freshman 1', '2024-11-24 07:48:48', '2024-12-01 13:16:22', 1),
(66, 'CS2002', 'Java 2', 4, 'B', 'Freshman 2', '2024-11-24 07:53:05', '2024-12-01 12:58:16', 2),
(68, 'CS2003', 'Java3', 3, 'A', 'Freshman 1', '2024-11-24 10:06:36', '2024-12-01 12:58:27', 3),
(69, 'CS2004', 'Java4', 3, 'B', 'Freshman 2', '2024-11-24 10:08:35', '2024-12-01 12:54:50', 4),
(67, 'MATH1234', 'Pre-Calculus', 4, 'B', 'Freshman 1', '2024-11-24 09:54:08', '2024-12-01 12:58:55', 3),
(74, 'MATH1235', 'Calculus 1', 4, 'B', 'Freshman 2', '2024-12-01 12:13:00', '2024-12-01 13:24:15', 0);

-- --------------------------------------------------------

--
-- Structure de la table `courseenrolls`
--

CREATE TABLE `courseenrolls` (
  `id` int(11) NOT NULL,
  `studentRegno` varchar(255) NOT NULL,
  `pincode` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `semester` varchar(255) NOT NULL,
  `course` varchar(255) NOT NULL,
  `enrollDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `courseschedule`
--

CREATE TABLE `courseschedule` (
  `id` int(11) NOT NULL,
  `course_id` varchar(255) NOT NULL,
  `day` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `classroom` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `courseschedule`
--

INSERT INTO `courseschedule` (`id`, `course_id`, `day`, `start_time`, `end_time`, `classroom`) VALUES
(71, 'CS2004', 'Wednesday', '00:00:00', '05:00:00', 'S11'),
(74, 'CS2002', 'Tuesday', '12:00:00', '15:00:00', 'S11'),
(75, 'CS2003', 'Monday', '00:00:00', '01:00:00', 'S9'),
(76, 'MATH1234', 'Monday', '01:00:00', '15:00:00', 'MBA 8'),
(79, 'CS2001', 'Tuesday', '00:00:00', '03:00:00', 'S9'),
(82, 'MATH1235', 'Monday', '00:00:00', '05:00:00', 'MBA 8');

-- --------------------------------------------------------

--
-- Structure de la table `course_departments`
--

CREATE TABLE `course_departments` (
  `id` int(11) NOT NULL,
  `course_id` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `department_id` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `course_departments`
--

INSERT INTO `course_departments` (`id`, `course_id`, `department_id`) VALUES
(22, 'CS2004', 'Law'),
(25, 'CS2002', 'MET'),
(26, 'CS2003', 'MET'),
(27, 'MATH1234', 'MET'),
(28, 'MATH1234', 'Law'),
(32, 'CS2001', 'Law'),
(37, 'MATH1235', 'MET'),
(38, 'MATH1235', 'Law');

-- --------------------------------------------------------

--
-- Structure de la table `department`
--

CREATE TABLE `department` (
  `id` int(11) NOT NULL,
  `department` varchar(255) NOT NULL,
  `creationDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `updationDate` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `department`
--

INSERT INTO `department` (`id`, `department`, `creationDate`, `updationDate`) VALUES
(12, 'Law', '2024-11-24 07:46:46', '2024-11-30 23:05:03'),
(11, 'MET', '2024-11-23 23:30:27', '');

-- --------------------------------------------------------

--
-- Structure de la table `lecturer`
--

CREATE TABLE `lecturer` (
  `lecturerId` int(11) NOT NULL,
  `lecturerName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `lecturer`
--

INSERT INTO `lecturer` (`lecturerId`, `lecturerName`) VALUES
(1, 'Severin Kakeu'),
(2, 'Mekontso Hermann'),
(3, 'Gnentedem'),
(4, 'Shey Augustine');

-- --------------------------------------------------------

--
-- Structure de la table `level`
--

CREATE TABLE `level` (
  `id` int(11) NOT NULL,
  `level` varchar(255) NOT NULL,
  `order` int(11) NOT NULL,
  `creationDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `updationDate` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `level`
--

INSERT INTO `level` (`id`, `level`, `order`, `creationDate`, `updationDate`) VALUES
(30, 'Freshman 1', 1, '2024-11-30 21:51:45', ''),
(35, 'Freshman 2', 2, '2024-11-30 21:53:11', ''),
(34, 'Junior 1', 5, '2024-11-30 21:52:46', ''),
(20, 'Junior 2', 6, '2024-11-23 21:23:22', ''),
(17, 'Sophomore 1', 3, '2024-11-23 21:23:22', ''),
(18, 'Sophomore 2', 4, '2024-11-23 21:23:22', '2024-11-30 22:33:29');

-- --------------------------------------------------------

--
-- Structure de la table `notification`
--

CREATE TABLE `notification` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('INFORMATION','REMINDER','EMERGENCY','') NOT NULL,
  `department_id` varchar(255) DEFAULT NULL,
  `semester_id` varchar(255) DEFAULT NULL,
  `studentRegno` varchar(255) DEFAULT NULL,
  `is_for_all` varchar(5) DEFAULT NULL,
  `level` varchar(255) NOT NULL,
  `creationDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `updationDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `recipient_type` enum('specific','department','all','level') DEFAULT NULL,
  `sent_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `notification`
--

INSERT INTO `notification` (`id`, `title`, `message`, `type`, `department_id`, `semester_id`, `studentRegno`, `is_for_all`, `level`, `creationDate`, `updationDate`, `is_read`, `recipient_type`, `sent_date`) VALUES
(76, 'Update', 'Updated registration deadline: 2024-12-11', 'INFORMATION', NULL, 'FALL 2024', 'S001', NULL, '', '2024-12-01 13:07:18', '2024-12-01 13:28:53', 1, 'specific', '2024-12-01'),
(80, 'Update', 'Updated registration deadline: 2024-12-10', 'INFORMATION', NULL, 'FALL 2024', 'S001', NULL, '', '2024-12-01 13:28:00', '2024-12-01 14:09:17', 0, 'specific', '2024-12-01'),
(81, 'bitchhh', 'hjw', 'INFORMATION', NULL, 'FALL 2024', 'S001', NULL, '', '2024-12-01 13:29:43', '2024-12-01 13:29:58', 1, 'specific', NULL),
(84, 'kjhgf', 'bhgyfd', 'EMERGENCY', NULL, 'FALL 2024', NULL, NULL, 'Freshman 1', '2024-12-01 13:45:13', '2024-12-01 14:08:08', 1, 'level', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `prerequisites`
--

CREATE TABLE `prerequisites` (
  `id` int(11) NOT NULL,
  `course_id` varchar(255) NOT NULL,
  `prerequisite_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `prerequisites`
--

INSERT INTO `prerequisites` (`id`, `course_id`, `prerequisite_id`) VALUES
(15, 'CS2002', 'CS2001'),
(19, 'MATH1235', 'MATH1234');

-- --------------------------------------------------------

--
-- Structure de la table `semester`
--

CREATE TABLE `semester` (
  `id` int(11) NOT NULL,
  `semester` varchar(255) NOT NULL,
  `creationDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `updationDate` varchar(255) NOT NULL,
  `sdate` date NOT NULL,
  `edate` date NOT NULL,
  `status` enum('ongoing','concluded') DEFAULT 'ongoing',
  `registration_deadline` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `semester`
--

INSERT INTO `semester` (`id`, `semester`, `creationDate`, `updationDate`, `sdate`, `edate`, `status`, `registration_deadline`) VALUES
(14, 'FALL 2024', '2024-11-18 11:32:52', '2024-12-01 15:09:17', '2024-09-10', '2024-12-12', 'ongoing', '2024-12-10');

-- --------------------------------------------------------

--
-- Structure de la table `session`
--

CREATE TABLE `session` (
  `id` int(11) NOT NULL,
  `session` varchar(255) NOT NULL,
  `creationDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `students`
--

CREATE TABLE `students` (
  `StudentRegno` varchar(50) NOT NULL,
  `studentPhoto` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `studentName` varchar(255) NOT NULL,
  `level` varchar(255) NOT NULL,
  `pincode` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `cgpa` decimal(10,2) NOT NULL,
  `creationdate` timestamp NOT NULL DEFAULT current_timestamp(),
  `updationDate` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `students`
--

INSERT INTO `students` (`StudentRegno`, `studentPhoto`, `password`, `studentName`, `level`, `pincode`, `department`, `cgpa`, `creationdate`, `updationDate`) VALUES
('S001', 'aaa1.png', '81dc9bdb52d04dc20036dbd8313ed055', 'Jean Dupont', 'Freshman 1', '829548', 'Law', 0.00, '2024-11-30 22:56:54', '2024-11-30 23:56:54');

-- --------------------------------------------------------

--
-- Structure de la table `studentsrecord`
--

CREATE TABLE `studentsrecord` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `place_of_birth` varchar(50) DEFAULT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `enrollment_number` varchar(50) NOT NULL,
  `level` varchar(255) NOT NULL DEFAULT 'Freshman 1',
  `program` varchar(255) DEFAULT NULL,
  `year_of_enrollment` int(11) DEFAULT NULL,
  `status` enum('Active','Graduated','Dropped') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `studentsrecord`
--

INSERT INTO `studentsrecord` (`id`, `first_name`, `last_name`, `date_of_birth`, `place_of_birth`, `gender`, `phone_number`, `email`, `address`, `city`, `postal_code`, `country`, `enrollment_number`, `level`, `program`, `year_of_enrollment`, `status`, `created_at`, `updated_at`) VALUES
(77, 'Jean', 'Dupont', '1998-05-15', 'Yaoundé', 'Male', '657483920', 'jean.dupont@email.com', '123 Rue de la Paix', 'Yaoundé', '1234', 'Cameroon', 'S001', 'Freshman 1', 'Law', 2024, 'Active', '2024-11-30 22:56:15', '2024-11-30 22:56:15'),
(78, 'Monique', 'Mvoula', '2000-01-10', 'Bamenda', 'Female', '657483923', 'monique.mvoula@email.com', '321 Rue des Hibiscus', 'Bamenda', '1122', 'Cameroon', 'S004', 'Freshman 1', 'MET', 2024, 'Active', '2024-11-30 22:56:15', '2024-11-30 22:56:15'),
(79, 'Valerie', 'Kengne', '2000-03-29', 'Bamenda', 'Female', '657483927', 'valerie.kengne@email.com', '5678 Rue des Palmiers', 'Bamenda', '9900', 'Cameroon', 'S008', 'Freshman 1', 'MET', 2024, 'Active', '2024-11-30 22:56:15', '2024-11-30 22:56:15');

-- --------------------------------------------------------

--
-- Structure de la table `student_courses`
--

CREATE TABLE `student_courses` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `course_id` varchar(255) NOT NULL,
  `semester` varchar(255) NOT NULL,
  `grade` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `student_semesters`
--

CREATE TABLE `student_semesters` (
  `id` int(11) NOT NULL,
  `studentRegno` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `semester` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `status` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'not enrolled',
  `date_recorded` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `userlog`
--

CREATE TABLE `userlog` (
  `id` int(11) NOT NULL,
  `studentRegno` varchar(255) NOT NULL,
  `userip` binary(16) NOT NULL,
  `loginTime` timestamp NOT NULL DEFAULT current_timestamp(),
  `logout` varchar(255) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `userlog`
--

INSERT INTO `userlog` (`id`, `studentRegno`, `userip`, `loginTime`, `logout`, `status`) VALUES
(1, 'ENR001', 0x3a3a3100000000000000000000000000, '2024-11-20 20:20:08', '21-11-2024 02:06:24 AM', 1),
(2, 'ENR002', 0x3a3a3100000000000000000000000000, '2024-11-20 20:36:37', '21-11-2024 02:07:53 AM', 1),
(3, 'ENR002', 0x3a3a3100000000000000000000000000, '2024-11-20 20:38:11', '', 1),
(4, 'ENR004', 0x3a3a3100000000000000000000000000, '2024-11-20 20:44:18', '', 1),
(5, 'ENR004', 0x3a3a3100000000000000000000000000, '2024-11-23 00:03:42', '23-11-2024 06:46:51 AM', 1),
(6, 'LAW12345', 0x3a3a3100000000000000000000000000, '2024-11-23 21:32:36', '24-11-2024 03:14:06 AM', 1),
(7, 'LAW12345', 0x3a3a3100000000000000000000000000, '2024-11-23 21:44:14', '24-11-2024 03:17:22 AM', 1),
(8, 'LAW12345', 0x3a3a3100000000000000000000000000, '2024-11-23 21:47:29', '24-11-2024 03:18:20 AM', 1),
(9, 'LAW12345', 0x3a3a3100000000000000000000000000, '2024-11-23 21:48:27', '24-11-2024 03:22:55 AM', 1),
(10, 'LAW12345', 0x3a3a3100000000000000000000000000, '2024-11-23 21:53:06', '24-11-2024 03:24:50 AM', 1),
(11, 'LAW12345', 0x3a3a3100000000000000000000000000, '2024-11-23 21:57:31', '24-11-2024 03:33:13 AM', 1),
(12, 'LAW12345', 0x3a3a3100000000000000000000000000, '2024-11-23 22:04:42', '24-11-2024 03:34:54 AM', 1),
(13, 'LAW12345', 0x3a3a3100000000000000000000000000, '2024-11-23 22:05:00', '24-11-2024 03:39:44 AM', 1),
(14, 'LAW12345', 0x3a3a3100000000000000000000000000, '2024-11-23 22:09:51', '24-11-2024 03:41:00 AM', 1),
(15, 'LAW12345', 0x3a3a3100000000000000000000000000, '2024-11-23 22:11:07', '24-11-2024 03:44:28 AM', 1),
(16, 'LAW12345', 0x3a3a3100000000000000000000000000, '2024-11-23 22:14:33', '24-11-2024 03:52:28 AM', 1),
(17, 'LAW12345', 0x3a3a3100000000000000000000000000, '2024-11-23 22:22:38', '24-11-2024 03:53:34 AM', 1),
(18, 'LAW12345', 0x3a3a3100000000000000000000000000, '2024-11-23 22:23:42', '24-11-2024 03:56:47 AM', 1),
(19, 'LAW12345', 0x3a3a3100000000000000000000000000, '2024-11-23 22:27:44', '24-11-2024 04:01:57 AM', 1),
(20, 'LAW12345', 0x3a3a3100000000000000000000000000, '2024-11-23 22:32:03', '24-11-2024 04:03:37 AM', 1),
(21, 'LAW12345', 0x3a3a3100000000000000000000000000, '2024-11-23 22:33:43', '24-11-2024 04:06:07 AM', 1),
(22, 'LAW12345', 0x3a3a3100000000000000000000000000, '2024-11-23 22:36:14', '24-11-2024 04:22:18 AM', 1),
(23, 'LAW12345', 0x3a3a3100000000000000000000000000, '2024-11-23 22:56:14', '24-11-2024 04:26:57 AM', 1),
(24, 'LAW12345', 0x3a3a3100000000000000000000000000, '2024-11-23 22:57:04', '24-11-2024 04:28:24 AM', 1),
(25, 'LAW12345', 0x3a3a3100000000000000000000000000, '2024-11-23 22:58:30', '24-11-2024 04:39:15 AM', 1),
(26, 'LAW12345', 0x3a3a3100000000000000000000000000, '2024-11-23 23:09:21', '24-11-2024 04:40:10 AM', 1),
(27, 'LAW12345', 0x3a3a3100000000000000000000000000, '2024-11-23 23:10:24', '', 1),
(28, 'LAW12345', 0x3a3a3100000000000000000000000000, '2024-11-24 07:49:48', '24-11-2024 02:30:18 PM', 1),
(29, 'LAW12345', 0x3a3a3100000000000000000000000000, '2024-11-24 09:00:27', '', 1),
(30, 'LAW12345', 0x3a3a3100000000000000000000000000, '2024-11-24 13:47:59', '24-11-2024 09:06:49 PM', 1),
(31, 'LAW12345', 0x3a3a3100000000000000000000000000, '2024-11-25 11:09:50', '', 1),
(32, 'S001', 0x3a3a3100000000000000000000000000, '2024-11-30 22:57:00', '01-12-2024 04:34:19 AM', 1),
(33, 'S001', 0x3a3a3100000000000000000000000000, '2024-11-30 23:04:29', '01-12-2024 04:48:02 AM', 1),
(34, 'S001', 0x3a3a3100000000000000000000000000, '2024-11-30 23:18:08', '01-12-2024 04:53:09 AM', 1),
(35, 'S001', 0x3a3a3100000000000000000000000000, '2024-11-30 23:23:14', '01-12-2024 04:55:12 AM', 1),
(36, 'S001', 0x3a3a3100000000000000000000000000, '2024-11-30 23:25:17', '01-12-2024 04:56:09 AM', 1),
(37, 'S001', 0x3a3a3100000000000000000000000000, '2024-11-30 23:26:15', '01-12-2024 05:04:21 AM', 1),
(38, 'S001', 0x3a3a3100000000000000000000000000, '2024-11-30 23:34:28', '01-12-2024 05:10:42 AM', 1),
(39, 'S001', 0x3a3a3100000000000000000000000000, '2024-11-30 23:40:49', '01-12-2024 05:14:20 AM', 1),
(40, 'S001', 0x3a3a3100000000000000000000000000, '2024-11-30 23:44:30', '01-12-2024 05:18:39 AM', 1),
(41, 'S001', 0x3a3a3100000000000000000000000000, '2024-11-30 23:48:43', '01-12-2024 05:21:02 AM', 1),
(42, 'S001', 0x3a3a3100000000000000000000000000, '2024-11-30 23:51:17', '01-12-2024 05:24:38 AM', 1),
(43, 'S001', 0x3a3a3100000000000000000000000000, '2024-11-30 23:55:24', '01-12-2024 05:27:26 AM', 1),
(44, 'S001', 0x3a3a3100000000000000000000000000, '2024-11-30 23:57:34', '01-12-2024 05:28:46 AM', 1),
(45, 'S001', 0x3a3a3100000000000000000000000000, '2024-11-30 23:58:51', '01-12-2024 05:29:28 AM', 1),
(46, 'S001', 0x3a3a3100000000000000000000000000, '2024-11-30 23:59:33', '01-12-2024 05:30:26 AM', 1),
(47, 'S001', 0x3a3a3100000000000000000000000000, '2024-12-01 00:00:32', '', 1),
(48, 'S001', 0x3a3a3100000000000000000000000000, '2024-12-01 08:03:44', '01-12-2024 01:35:17 PM', 1),
(49, 'S001', 0x3a3a3100000000000000000000000000, '2024-12-01 08:05:23', '01-12-2024 05:14:42 PM', 1),
(50, 'S001', 0x3a3a3100000000000000000000000000, '2024-12-01 11:44:49', '01-12-2024 06:28:58 PM', 1),
(51, 'S001', 0x3a3a3100000000000000000000000000, '2024-12-01 12:59:13', '01-12-2024 06:30:23 PM', 1),
(52, 'S001', 0x3a3a3100000000000000000000000000, '2024-12-01 13:00:28', '01-12-2024 06:33:16 PM', 1),
(53, 'S001', 0x3a3a3100000000000000000000000000, '2024-12-01 13:03:22', '', 1),
(54, 'S001', 0x3a3a3100000000000000000000000000, '2024-12-01 13:07:18', '01-12-2024 06:55:59 PM', 1),
(55, 'S001', 0x3a3a3100000000000000000000000000, '2024-12-01 13:26:11', '01-12-2024 06:57:54 PM', 1),
(56, 'S001', 0x3a3a3100000000000000000000000000, '2024-12-01 13:28:00', '01-12-2024 07:15:42 PM', 1),
(57, 'S001', 0x3a3a3100000000000000000000000000, '2024-12-01 13:45:46', '01-12-2024 07:19:59 PM', 1),
(58, 'S001', 0x3a3a3100000000000000000000000000, '2024-12-01 13:50:03', '', 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `classroom`
--
ALTER TABLE `classroom`
  ADD PRIMARY KEY (`classroom`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`courseCode`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `level` (`level`);

--
-- Index pour la table `courseenrolls`
--
ALTER TABLE `courseenrolls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `courseenrolls_ibfk_1` (`studentRegno`),
  ADD KEY `courseenrolls_ibfk_2` (`department`),
  ADD KEY `courseenrolls_ibfk_3` (`semester`);

--
-- Index pour la table `courseschedule`
--
ALTER TABLE `courseschedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `courseschedule_ibfk_2` (`classroom`);

--
-- Index pour la table `course_departments`
--
ALTER TABLE `course_departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_departments_ibfk_1` (`department_id`),
  ADD KEY `course_departments_ibfk_2` (`course_id`);

--
-- Index pour la table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`department`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `lecturer`
--
ALTER TABLE `lecturer`
  ADD PRIMARY KEY (`lecturerId`);

--
-- Index pour la table `level`
--
ALTER TABLE `level`
  ADD PRIMARY KEY (`level`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `order` (`order`);

--
-- Index pour la table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `semeter_id` (`semester_id`),
  ADD KEY `studentRegno` (`studentRegno`);

--
-- Index pour la table `prerequisites`
--
ALTER TABLE `prerequisites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prerequisites_ibfk_1` (`course_id`),
  ADD KEY `prerequisites_ibfk_2` (`prerequisite_id`);

--
-- Index pour la table `semester`
--
ALTER TABLE `semester`
  ADD PRIMARY KEY (`semester`),
  ADD UNIQUE KEY `semester` (`semester`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`StudentRegno`),
  ADD KEY `students_ibfk_2` (`level`);

--
-- Index pour la table `studentsrecord`
--
ALTER TABLE `studentsrecord`
  ADD PRIMARY KEY (`enrollment_number`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `studentsrecord_ibfk_1` (`program`),
  ADD KEY `studentsrecord_ibfk_2` (`level`);

--
-- Index pour la table `student_courses`
--
ALTER TABLE `student_courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_courses_ibfk_1` (`student_id`),
  ADD KEY `student_courses_ibfk_2` (`course_id`);

--
-- Index pour la table `student_semesters`
--
ALTER TABLE `student_semesters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_semesters_ibfk_1` (`semester`),
  ADD KEY `student_semesters_ibfk_2` (`studentRegno`);

--
-- Index pour la table `userlog`
--
ALTER TABLE `userlog`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `classroom`
--
ALTER TABLE `classroom`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `course`
--
ALTER TABLE `course`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT pour la table `courseenrolls`
--
ALTER TABLE `courseenrolls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT pour la table `courseschedule`
--
ALTER TABLE `courseschedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT pour la table `course_departments`
--
ALTER TABLE `course_departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT pour la table `department`
--
ALTER TABLE `department`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `lecturer`
--
ALTER TABLE `lecturer`
  MODIFY `lecturerId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `level`
--
ALTER TABLE `level`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT pour la table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT pour la table `prerequisites`
--
ALTER TABLE `prerequisites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `semester`
--
ALTER TABLE `semester`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `studentsrecord`
--
ALTER TABLE `studentsrecord`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT pour la table `student_courses`
--
ALTER TABLE `student_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `student_semesters`
--
ALTER TABLE `student_semesters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `userlog`
--
ALTER TABLE `userlog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `courseenrolls`
--
ALTER TABLE `courseenrolls`
  ADD CONSTRAINT `courseenrolls_ibfk_1` FOREIGN KEY (`studentRegno`) REFERENCES `students` (`StudentRegno`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `courseenrolls_ibfk_2` FOREIGN KEY (`department`) REFERENCES `department` (`department`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `courseenrolls_ibfk_3` FOREIGN KEY (`semester`) REFERENCES `semester` (`semester`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `courseschedule`
--
ALTER TABLE `courseschedule`
  ADD CONSTRAINT `courseschedule_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`courseCode`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `courseschedule_ibfk_2` FOREIGN KEY (`classroom`) REFERENCES `classroom` (`classroom`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `course_departments`
--
ALTER TABLE `course_departments`
  ADD CONSTRAINT `course_departments_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `department` (`department`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `course_departments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `course` (`courseCode`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`studentRegno`) REFERENCES `students` (`StudentRegno`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `notification_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `department` (`department`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `notification_ibfk_3` FOREIGN KEY (`semester_id`) REFERENCES `semester` (`semester`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `prerequisites`
--
ALTER TABLE `prerequisites`
  ADD CONSTRAINT `prerequisites_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`courseCode`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `prerequisites_ibfk_2` FOREIGN KEY (`prerequisite_id`) REFERENCES `course` (`courseCode`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`StudentRegno`) REFERENCES `studentsrecord` (`enrollment_number`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `students_ibfk_2` FOREIGN KEY (`level`) REFERENCES `level` (`level`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `studentsrecord`
--
ALTER TABLE `studentsrecord`
  ADD CONSTRAINT `studentsrecord_ibfk_1` FOREIGN KEY (`program`) REFERENCES `department` (`department`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `studentsrecord_ibfk_2` FOREIGN KEY (`level`) REFERENCES `level` (`level`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `student_courses`
--
ALTER TABLE `student_courses`
  ADD CONSTRAINT `student_courses_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `studentsrecord` (`enrollment_number`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `student_courses_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `course` (`courseCode`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `student_semesters`
--
ALTER TABLE `student_semesters`
  ADD CONSTRAINT `student_semesters_ibfk_1` FOREIGN KEY (`semester`) REFERENCES `semester` (`semester`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `student_semesters_ibfk_2` FOREIGN KEY (`studentRegno`) REFERENCES `students` (`StudentRegno`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
