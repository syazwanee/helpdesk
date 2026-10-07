-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 05, 2026 at 04:11 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `helpdesk_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `application_issues`
--

CREATE TABLE `application_issues` (
  `app_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `application_name` enum('VESALIUS (HIS)','QMED','CHUPP','PACSYS (TV)','WINDOWS ISSUE','MICROSOFT OFFICE','LAB RESULT','RIS / PACS (RADIOLOGY)','RIS / PACS CV (CARDIOLOGY)','LIS','UBS','QTMS/QPAY/SPHERE','EMAIL','SERVER / CLOUD','SHAREHOLDERS SYSTEM','REQUEST REPORT','NEW REQUEST','Other') NOT NULL,
  `problem_description` text NOT NULL,
  `attachment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hardware_issues`
--

CREATE TABLE `hardware_issues` (
  `hardware_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `hardware_type` enum('PC/Notebook','Printer / Fax / Scan','Telephone','POC Terminal / Arm','Door Access','CCTV','QMS Device','Nurse Call','Network','New Request') NOT NULL,
  `problem_description` text NOT NULL,
  `attachment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `request_type` enum('Hardware','Application') NOT NULL,
  `severity` tinyint(4) NOT NULL,
  `status` enum('Pending','In Progress','Resolve','Closed') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `staff_name` varchar(100) NOT NULL,
  `closed_by` varchar(100) DEFAULT NULL,
  `area` varchar(100) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `anydesk_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_admin_notes`
--

CREATE TABLE `ticket_admin_notes` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `note` text NOT NULL,
  `created_by` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `password`, `role`, `created_at`) VALUES
(27, 'admin', '$2y$10$hj5k.D1NrZ863gEB0OoA..FledS/6XGv1CtWKdxyAhbMhWlYRGmj6', 'admin', '2025-10-02 07:55:58'),
(28, 'staff', '$2y$10$6Q2.o.VfTkOpLhbd5oRE1ONQIuvrsBvZFd1I5M97tQEPF/AgJ3XFm', 'user', '2025-10-02 07:56:51'),
(61, 'Paeds Ward', '$2y$10$w/SEJfQ2g1aXWZK9ysRCFebAC.us0B37aBkLyhH.1MwMBjIQ3Tq/e', 'user', '2025-10-24 07:26:19'),
(62, 'O&G Ward', '$2y$10$vjljLGD.lKHHKXC5gbOYfeH1gWtBNhDVKww9niMMcgc8AxhV8j6Qy', 'user', '2025-10-24 07:26:19'),
(63, 'Medical Ward', '$2y$10$oJQvJ/gFYPFLlvLDVY7fTOH1rUPZug3.b.uKhSwc3WXvPrAniPTEC', 'user', '2025-10-24 07:26:19'),
(64, 'Surgical Ward', '$2y$10$tCGb/GEM7DhTm05xAcWWPuWpuHDqJ59qoq56UmjnNQ0RCct7WrgRe', 'user', '2025-10-24 07:26:19'),
(65, 'Nursery', '$2y$10$OK8zEZmAuUsWzSOTr6TS/OW/MG3t/hF3BIQnhi0E2SVyyrg/gauR.', 'user', '2025-10-24 07:26:19'),
(66, 'ICU', '$2y$10$YaiDIAQpEsuhTRUX6CNCIe19qDn6pzGdxQQODj9s62YbEdvJeaz4y', 'user', '2025-10-24 07:26:19'),
(67, 'Haemodialysis', '$2y$10$xHZuSUtu8NL5sK.w45gTc.jnfYSXw1IPPhFLsYa6Kh3tyCmZyhxP2', 'user', '2025-10-24 07:26:19'),
(68, 'Endoscopy', '$2y$10$Yz3ZADdYggMhdQhO7toMM.WsH6l0pqNxn7FIITwMH1bad2Ca2arhe', 'user', '2025-10-24 07:26:19'),
(69, 'Health Screening', '$2y$10$Y4H2Ji2VNBY/oazFN3fhGu5EhRvjc/2d7Xtih.gtq86ful/8fZ2Ce', 'user', '2025-10-24 07:26:19'),
(70, 'Labour Room', '$2y$10$gMVXVd7MWCsmfsTzUEfDT.7VHkfxRf2QXhYs0vbn0rBQaiIUHUlky', 'user', '2025-10-24 07:26:19'),
(71, 'SCN', '$2y$10$lxecXGv.k5mTudXUOiqUtekjH.5tiVvtGgvbGjCStE86/JeDoi8.e', 'user', '2025-10-24 07:26:19'),
(72, 'A&E', '$2y$10$tBOGIyfer8wNTZQyYAXWkeGQzMwL9ovdLMLVovQsFh0wiD1SZXOSW', 'user', '2025-10-24 07:26:19'),
(73, 'Operation Theatre', '$2y$10$R0Xk8Az9fc5HtBgnKMo7.u6Gk9/oV7qvQJHKgFJndie5F7xuvQYxW', 'user', '2025-10-24 07:26:19'),
(74, 'CSSD', '$2y$10$C3z6bWBgx.m57csiiVpP9.t7oXRLApGAn1VoVb1NrL./Tv.cMJQu.', 'user', '2025-10-24 07:26:19'),
(75, 'ICL', '$2y$10$EkRKemEf9bXpRVExilAVKuw.32Q2t/CLpHYdYyKiHfvbkhsrqH5S6', 'user', '2025-10-24 07:26:19'),
(76, 'Administration', '$2y$10$dwOzezebconGu7L09WInbezNPnLL5y1aAq6Sev13rK3oiD4KbBkxO', 'user', '2025-10-24 07:26:19'),
(77, 'Laboratory', '$2y$10$IIzc6PlWcAtd3xZM88R1bOD5MY89gGhXQXS7Vkys9.YWXmmtjJs6m', 'user', '2025-10-24 07:26:19'),
(78, 'Radiology', '$2y$10$FGCExL/18DsXa2l97fFoq.WLxS4AJvJkq8lQFIe5iu3ifh9ApZOFW', 'user', '2025-10-24 07:26:19'),
(79, 'Rehabilitation', '$2y$10$DYWCH138cRjjVKwhRM3Dk.L7llgu7m9oAPUSWCuEA.J4DrFmzilym', 'user', '2025-10-24 07:26:19'),
(80, 'Pharmacy', '$2y$10$0zG8oPlLFPGPlnfwPAvcEu6EfWqLRlxRXQdtSTyJ88k1kNCcNfeka', 'user', '2025-10-24 07:26:19'),
(81, 'Support Service', '$2y$10$3Q7ck0nmjIe8YBiY7vPBoOHRdgrNA8fL6KEVI.tyao.u2Km7l2qjG', 'user', '2025-10-24 07:26:19'),
(82, 'Medical Record ', '$2y$10$e8Tkq6WOzOywoAdW93VlvOU.XhzcHbtVH31WdZZSdN3AytUByuX8S', 'user', '2025-10-24 07:26:19'),
(83, 'Biomedical ', '$2y$10$Su.dkWdeiOyHLM6C3MXnZepelC5uoyHYZ2XjL.BScDsKGczIFGot6', 'user', '2025-10-24 07:26:19'),
(84, 'Facilities Department', '$2y$10$YDeOBNDI5.P3iriWnT36Zu6iQJQkf3RBdHh5Di7GszVEznTWovH2O', 'user', '2025-10-24 07:26:19'),
(85, 'Customer Relation Department', '$2y$10$EdureyGtmb8HbUPbk0pfo.SfYR0msZOJSKLHcZSBqifETfxPhd/CW', 'user', '2025-10-24 07:26:19'),
(86, 'Patient Care Department', '$2y$10$JnD03l1/G1aXkrynnY8MHuC0adKnyHiNB.Kp9ChhpcckkTISgXf3C', 'user', '2025-10-24 07:26:19'),
(87, 'Human Resource Department', '$2y$10$CDNjIzwbFBf2zYVFQXC2VeqC8WhtSj3ICnN4Mz7z66sPnAOHw/iuG', 'user', '2025-10-24 07:26:19'),
(88, 'Specialist Clinic Department', '$2y$10$gj2sHK5u8J5sLNFdhwrku.SebEB4V7gZVAWdCkHqdyC/t1wWtzlOG', 'user', '2025-10-24 07:26:19'),
(89, 'Quality & Regulatory Department', '$2y$10$dkiM4SojBmYxLV50Tuebp.6u3IP1UjrZ8h00MJGxp8rzystkzqodO', 'user', '2025-10-24 07:26:19'),
(90, 'Marketing & Communication Department', '$2y$10$mEo7T/ZwbEi3I98mSQX51./AuMRLj4lCPiVJLTtcoOPbG.JWppXVm', 'user', '2025-10-24 07:26:19'),
(91, 'Account & Finance Department', '$2y$10$d5PKw.KGTY7BdCX/eteic.eCitWH/FWYIOkyulC7Ed8xK2dS8zhpq', 'user', '2025-10-24 07:26:19'),
(92, 'MIG TEC', '$2y$10$WZHmNGvGW8F7H83GCHToWONNnU.AVfFX2Aqv2VGA2ed2cPMwBLN7q', 'user', '2025-10-24 07:26:19'),
(93, 'MIG Property', '$2y$10$NInc/rMgKjtUYUutRFrVPuESFP4qsN7cX7PhF8.bGNSKk3vGajNhu', 'user', '2025-10-24 07:26:19'),
(94, 'MIG IT', '$2y$10$ONQxYrLdVrecte/XfovONupun.eaeQGo6DpkCbW4Dxqh8N6ICKzs2', 'user', '2025-10-24 07:26:19'),
(95, 'Legal & Secretarial Department', '$2y$10$rzfsHgSj9QSv2SBd9hU7u.GTNTv8rrUnq4mvXQldfn2CotYOwuwry', 'user', '2025-10-24 07:26:19'),
(96, 'Group Transformation Department', '$2y$10$KgUKFEK7tkNt7bR39jsWdu.fMzQLfuBkSQA6t48gt/1NGQ.rq9iAi', 'user', '2025-10-24 07:26:19'),
(97, 'Management', '$2y$10$kYVf/f6QmBeJo1CcI9rPL.BM9fTyPU0lgjrt4QY9iLeexkrj4RWOu', 'user', '2025-10-24 07:26:19'),
(98, 'Consultant', '$2y$10$ZjCCxMNdSRrQRMVkaCEjRucsVvIA.lA4PmvRp95LSTWiS2gNo5gja', 'user', '2025-10-24 07:26:19'),
(99, 'CEO Office', '$2y$10$dqE4iOknThugqJRSYwPxMOmOU9iYg.0T.bJEYN3K21gK/C4DPpEf.', 'user', '2025-10-24 07:26:19'),
(100, 'Procurement', '$2y$10$f4TykejAHnNl.KjhTzOTTuGTBwaXtUo4rBKd.BkApqTqWxC1GYfG.', 'user', '2025-10-24 07:26:19'),
(101, 'Medical Officer', '$2y$10$g1Dq5vlGBCCIS.m7ZBLueuBexRDh8ZugHsZ37DM8I4C.WH2Zhuhhu', 'user', '2025-10-24 07:26:19'),
(102, 'Safety & Security Department', '$2y$10$7wZNtX46EVfXakhFJ1pp5eMKYJG/uyL9aQeuwfFY.6rX5kUYSTQxy', 'user', '2025-10-24 07:26:19'),
(103, 'Audiology', '$2y$10$Lf65L0LiOjU32Z9JgzAPuOhvf6Bc13TmovsOuNqf/3p21HSCeOgxK', 'user', '2025-10-24 07:26:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `application_issues`
--
ALTER TABLE `application_issues`
  ADD PRIMARY KEY (`app_id`),
  ADD KEY `ticket_id` (`ticket_id`);

--
-- Indexes for table `hardware_issues`
--
ALTER TABLE `hardware_issues`
  ADD PRIMARY KEY (`hardware_id`),
  ADD KEY `ticket_id` (`ticket_id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`ticket_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `ticket_admin_notes`
--
ALTER TABLE `ticket_admin_notes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `application_issues`
--
ALTER TABLE `application_issues`
  MODIFY `app_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `hardware_issues`
--
ALTER TABLE `hardware_issues`
  MODIFY `hardware_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=182;

--
-- AUTO_INCREMENT for table `ticket_admin_notes`
--
ALTER TABLE `ticket_admin_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `application_issues`
--
ALTER TABLE `application_issues`
  ADD CONSTRAINT `application_issues_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`ticket_id`) ON DELETE CASCADE;

--
-- Constraints for table `hardware_issues`
--
ALTER TABLE `hardware_issues`
  ADD CONSTRAINT `hardware_issues_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`ticket_id`) ON DELETE CASCADE;

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
