-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 09, 2023 at 05:33 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `financial_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `borrowers`
--

CREATE TABLE `borrowers` (
  `ninNumber` varchar(20) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `title` varchar(10) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `location` varchar(100) NOT NULL,
  `district` varchar(50) NOT NULL,
  `group_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `borrowers`
--

INSERT INTO `borrowers` (`ninNumber`, `firstname`, `lastname`, `title`, `gender`, `phone`, `email`, `location`, `district`, `group_id`, `created_at`) VALUES
(' gghfghuytyy', 'Lwanga', 'kaye', 'bvc', 'male', '0704038726', 'kayegrace14@gmail.com', 'kampala', 'Kampala', 1, '2023-06-08 16:18:38'),
('123344', 'Tara', 'Cynthis', 'htr', 'female', '7656754564', 'example@ex.com', 'MA', 'Bugema', 1, '2023-06-08 16:18:38'),
('4ertefh55', 'senabulya', 'Akram', 'Mr', 'male', '08676565', 'jsdhshf@email.com', 'kamwokya', 'Gulu', 1, '2023-06-08 16:18:38'),
('4ertefh5ttt', 'senabulya', 'Akram', 'Mr', 'male', '08676565', 'jsdhshf@email.com', 'kamwokya', 'Gulu', 1, '2023-06-08 16:18:38'),
('4ertefh5ttt54', 'senabulya', 'Akram', 'Mr', 'male', '08676565', 'jsdhshf@email.com', 'kamwokya', 'Gulu', 1, '2023-06-08 16:18:38'),
('4ertefhf', 'senabulya', 'Akram', 'Mr', 'male', '08676565', 'jsdhshf@email.com', 'kamwokya', 'Gulu', 1, '2023-06-08 16:18:38'),
('655tfggfvhgvgh', 'Lwanga', 'kaye', 'ghf', 'male', '0704038726', 'kayegrace14@gmail.com', 'tery', '6767', 1, '2023-06-08 16:18:38'),
('6576546546hjbh', 'Lwanga', 'kaye', 'gytr', 'female', '0704038726', 'kayegrace14@gmail.com', 'yguyrt', 'uret', 1, '2023-06-08 16:18:38'),
('CBygdgygsdg', 'Blair ', 'Mujjabi', 'Mr', 'male', '0788435466', 'kayegrace14@gmail.com', 'MA', 'Bugema', 2, '2023-06-08 16:18:38'),
('CMEHETYT', 'Mubede ', 'Enoch', 'Btt', 'male', '0704038893', 'kayegrace14@gmail.com', 'Nansana', 'Wakiso', 2, '2023-06-08 16:37:49'),
('hgfnzvbx', 'Kaye', 'Grace', 'sytury', 'male', '0704038726', 'kayegrace14@gmail.com', 'home/adventur', 'Bujiri', 1, '2023-06-08 16:18:38'),
('hgfnzytttfgf', 'Kaye', 'Grace', 'sytury', 'male', '0704038726', 'kayegrace14@gmail.com', 'home/adventur', 'Bujiri', 1, '2023-06-08 16:18:38'),
('Jkjfuefit', 'Lwanga', 'kaye', 'yr7', 'male', '07040385768', 'kayegrace14@gmail.com', 'home/adventur', 'Bugema', 3, '2023-06-08 16:31:34'),
('qwerty', 'Nyakana', 'Francis', 'Mr', 'male', '0704038726', 'k4@gmail.com', 'kampala', '6767', 1, '2023-06-08 16:18:38');

-- --------------------------------------------------------

--
-- Table structure for table `borrower_groups`
--

CREATE TABLE `borrower_groups` (
  `id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `location` varchar(256) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `leader` varchar(256) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `borrower_groups`
--

INSERT INTO `borrower_groups` (`id`, `name`, `location`, `contact`, `leader`, `created_at`) VALUES
(1, 'Twekembe Saaco', 'Ntinda', '1234567890', 'Ritah Trou', '2023-06-08 16:41:02'),
(2, 'Twekembe 2', 'kampala', '0778457832', 'Trial Kenny', '2023-06-08 16:41:02'),
(3, 'Twakala Sacco', 'Mpigi, Mpigi Town Council', '0778457832', 'Kalema Tonny', '2023-06-08 16:41:02'),
(4, 'Twekembe Sa', 'Mpigi, Mpigi Town Council', '0783545756', 'Senkondo Jimmy', '2023-06-08 16:41:02');

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `loan_number` int(12) NOT NULL,
  `loan_type` varchar(30) NOT NULL,
  `borrower` varchar(20) NOT NULL,
  `distributed_by` varchar(30) NOT NULL,
  `amount_requested` int(12) NOT NULL,
  `amount_approved` int(12) NOT NULL,
  `processing_fee` double NOT NULL DEFAULT 15000,
  `application_fee` double(10,0) NOT NULL DEFAULT 5000,
  `loan_period` int(12) NOT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT 0,
  `interest_rate` int(12) NOT NULL,
  `release_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`loan_number`, `loan_type`, `borrower`, `distributed_by`, `amount_requested`, `amount_approved`, `processing_fee`, `application_fee`, `loan_period`, `approved`, `interest_rate`, `release_date`) VALUES
(1, 'Business Loan', '123344', 'Cash', 33400, 33400, 15000, 5000, 1, 0, 5, '2023-06-09');

-- --------------------------------------------------------

--
-- Table structure for table `loan_repayements`
--

CREATE TABLE `loan_repayements` (
  `id` int(12) NOT NULL,
  `amount` int(12) NOT NULL,
  `borrower` varchar(256) NOT NULL,
  `loan` int(12) NOT NULL,
  `payement_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `adminID` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `title` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`adminID`, `firstname`, `lastname`, `title`, `username`, `password`) VALUES
(1, 'Wabwiire', 'Edrick', 'main_admin', 'edrick', '22ac3b2b220e5c94de8390d057f83667'),
(2, 'field', 'officer', 'field_officer', 'field', '06e3d36fa30cea095545139854ad1fb9'),
(3, 'Drake', 'Hunter', 'sub_admin', 'subadmin', 'abdb392f09c7376fe5ce059f045de38b');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `borrowers`
--
ALTER TABLE `borrowers`
  ADD PRIMARY KEY (`ninNumber`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `borrower_groups`
--
ALTER TABLE `borrower_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`loan_number`),
  ADD KEY `borrower` (`borrower`);

--
-- Indexes for table `loan_repayements`
--
ALTER TABLE `loan_repayements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `borrower` (`borrower`),
  ADD KEY `loan` (`loan`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`adminID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `borrower_groups`
--
ALTER TABLE `borrower_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `loan_number` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `loan_repayements`
--
ALTER TABLE `loan_repayements`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `adminID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `borrowers`
--
ALTER TABLE `borrowers`
  ADD CONSTRAINT `borrowers_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `borrower_groups` (`id`);

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `loans_ibfk_1` FOREIGN KEY (`borrower`) REFERENCES `borrowers` (`ninNumber`);

--
-- Constraints for table `loan_repayements`
--
ALTER TABLE `loan_repayements`
  ADD CONSTRAINT `loan_repayements_ibfk_1` FOREIGN KEY (`borrower`) REFERENCES `borrowers` (`ninNumber`),
  ADD CONSTRAINT `loan_repayements_ibfk_2` FOREIGN KEY (`loan`) REFERENCES `loans` (`loan_number`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
