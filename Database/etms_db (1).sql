-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Feb 06, 2026 at 03:43 PM
-- Server version: 8.0.43
-- PHP Version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `etms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance_info`
--

CREATE TABLE `attendance_info` (
  `aten_id` int NOT NULL,
  `atn_user_id` int NOT NULL,
  `in_time` datetime DEFAULT NULL,
  `out_time` datetime DEFAULT NULL,
  `total_duration` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `task_info`
--

CREATE TABLE `task_info` (
  `task_id` int NOT NULL,
  `t_title` varchar(120) NOT NULL,
  `t_category` text NOT NULL,
  `t_description` text,
  `t_start_time` datetime DEFAULT NULL,
  `t_end_time` datetime DEFAULT NULL,
  `t_user_id` int NOT NULL,
  `status` int NOT NULL DEFAULT '0' COMMENT '0 = incomplete, 1 = In progress, 2 = complete'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `task_info`
--

INSERT INTO `task_info` (`task_id`, `t_title`, `t_category`, `t_description`, `t_start_time`, `t_end_time`, `t_user_id`, `status`) VALUES
(3, 'Learn MERN', 'PRACTICE', 'Learning MERN ( MongoDB, Express, React, NodeJS )', '2025-09-08 16:30:00', '2025-09-08 17:30:00', 29, 2),
(2, 'Installasi Dockerize PHP app', 'PRACTICE', 'Menjalankan aplikasi berbasis PHP MySql dengan docker', '2025-09-08 09:00:00', '2025-09-08 14:00:00', 29, 2),
(4, 'Migrasi Hokben Web App AWS from Singapura to Jakarta', 'TASK', 'Create dan Copy Snapshot Volume Web App Singapura to Jakarta regional', '2025-09-09 09:30:00', '2025-09-10 17:30:00', 29, 2),
(5, 'Migrasi Hokben Web App', 'TASK', 'Monitoring migration create and copy snapshot volume Hokben web app', '2025-09-10 09:30:00', '2025-09-10 17:30:00', 29, 2),
(6, 'Renew certificate mail.lappjk.com', 'MAINTENANCE', 'Renew certificate mail lappjk', '2025-09-19 11:00:00', '2025-09-19 11:30:00', 29, 2),
(7, 'Upgrade PostgreSQL', 'TASK', 'Upgrade PostgreSQL 11 to 15 di AWS Jakarta DEV-db-web-apps', '2025-10-01 16:30:00', '2025-10-01 17:30:00', 29, 2),
(8, 'Config pub key Jenkins to CMS', 'TASK', 'Config pub key jenkins pada server hokben.web-hokben-cms (02Bprod-hokben-web-cms) - IP 100.10.1.1.154', '2025-10-09 10:00:00', '2025-10-10 14:00:00', 29, 2),
(9, 'config pub key jenkins to mobile api', 'TASK', 'Config pub key jenkins pad server hokben.api-prod-new (03A-Prod-Hokben-MobileAPI) - IP 100.10.1.62', '2025-10-09 13:00:00', '2025-10-09 14:00:00', 29, 2),
(10, 'Config pub key Jenkins pada server webstore', 'TASK', 'Config pub key jenkins pada server webstore ( 02B-Prod-Hokben-Web-CMS ) - IP 100.10.1.154', '2025-10-09 13:30:00', '2025-10-09 14:00:00', 29, 2),
(11, 'Asep - public access link need', 'TASK', 'Asep forgotten link public access api doku at https://dockersvr.hokben.net/', '2025-10-14 17:00:00', '2025-10-14 18:30:00', 29, 2),
(12, 'Add Pub Key admin data tech', 'TASK', 'Request Pawi - add pub key ke server bastion untuk akses ke RDS hokbenapp-prod-01', '2025-10-15 13:30:00', '2025-10-15 14:00:00', 29, 2),
(13, 'install node exporter untuk grafana', 'TASK', 'install node exporter untuk grafana, store transmart, lokasari', '2025-11-11 08:30:00', '2025-11-11 12:00:00', 30, 2),
(14, 'Seting Alert Monitoring Store', 'REQUEST', 'Seting Alert Grafana to Telegram ( Hokben Store Monitoring )', '2025-11-12 10:00:00', '2025-11-12 15:00:00', 29, 2),
(15, 'Update SSL Mail LAPPJK', 'MAINTENANCE', 'update SSL Certificate mail.lappjk.com', '2025-12-18 15:00:00', '2025-12-18 15:30:00', 29, 2),
(16, 'Hardening Server Absen', 'MAINTENANCE', 'Install Antivirus Wazuh', '2025-12-23 10:00:00', '2025-12-23 10:15:00', 29, 2),
(17, 'Update SSL Certificate mail.hokben.info', 'MAINTENANCE', 'Update SSL Certificate mail.hokben.info', '2026-01-05 16:30:00', '2026-01-05 16:45:00', 29, 2),
(18, 'Update SSL Certificate mail.hokbenperformance.com', 'MAINTENANCE', 'Update SSL mail.hokbenperformance.com', '2026-01-05 16:33:00', '2026-01-05 17:17:00', 29, 2),
(19, 'Config Pipeline Jenkins ESS API Bandung', 'TASK', 'Seting Pipeline Jenkins ESS API Bandung', '2026-01-12 14:00:00', '2026-01-12 17:00:00', 29, 2),
(20, 'Config PDLC Store Eastvara Mall BSD', 'TASK', 'Config and installation PDLC on server Store Eastvara Mall BSD', '2026-01-12 17:00:00', '2026-01-12 23:00:00', 29, 2),
(21, 'Validasi Update file Git OSDS - Asep', 'TASK', 'Req to validation update git', '2026-01-15 15:00:00', '2026-01-15 15:30:00', 29, 2),
(22, 'Create Sub Domain osdsdev.hokben.net', 'TASK', 'Create sub domain osds.hokben.net  on AD', '2026-01-21 11:00:00', '2026-01-21 11:15:00', 29, 2),
(23, 'Config PDLC Store Tebet', 'FILLFULLMENT REQ', 'Config PDLC Server Store Hokben Tebet', '2026-01-21 14:05:00', '2026-01-21 14:27:00', 29, 2),
(24, 'create domain kong dev  di ip 172.18.100.108:8000 ke domain https://essdev-mgate.hokben.net', 'FILLFULLMENT REQ', 'Pointing kong dev  di ip 172.18.100.108:8000 ke domain https://essdev-mgate.hokben.net', '2026-01-29 14:00:00', '2026-01-29 19:20:00', 29, 2),
(25, 'Setting ess-mgate.hokben.net to F5', 'FILLFULLMENT REQ', 'Seting ess-mgate.hokben.net to F5', '2026-01-27 14:08:00', '2026-01-27 17:00:00', 29, 2);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admin`
--

CREATE TABLE `tbl_admin` (
  `user_id` int NOT NULL,
  `fullname` varchar(120) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `temp_password` varchar(100) DEFAULT NULL,
  `user_role` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COMMENT='2';

--
-- Dumping data for table `tbl_admin`
--

INSERT INTO `tbl_admin` (`user_id`, `fullname`, `username`, `email`, `password`, `temp_password`, `user_role`) VALUES
(1, 'Admin', 'admin', 'admin@gmail.com', 'b77324290fd9800dc894fe9f0a962233', NULL, 1),
(30, 'Harmoko', 'moko', 'harmoko@hokben.co.id', '202cb962ac59075b964b07152d234b70', '', 2),
(29, 'Dani Z', 'dani', 'dani.z@gmail.go.id', '5f4dcc3b5aa765d61d8327deb882cf99', '', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance_info`
--
ALTER TABLE `attendance_info`
  ADD PRIMARY KEY (`aten_id`);

--
-- Indexes for table `task_info`
--
ALTER TABLE `task_info`
  ADD PRIMARY KEY (`task_id`);

--
-- Indexes for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance_info`
--
ALTER TABLE `attendance_info`
  MODIFY `aten_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `task_info`
--
ALTER TABLE `task_info`
  MODIFY `task_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
