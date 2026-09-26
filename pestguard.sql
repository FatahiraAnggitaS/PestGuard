-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 20, 2025 at 01:59 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pestguard`
--

-- --------------------------------------------------------

--
-- Table structure for table `areas`
--

CREATE TABLE `areas` (
  `id` int(11) NOT NULL,
  `nama_area` varchar(100) DEFAULT NULL,
  `hama` varchar(50) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `status` enum('Aman','Waspada','Bahaya') DEFAULT NULL,
  `warna` varchar(20) DEFAULT NULL,
  `geometry` longtext DEFAULT NULL,
  `tanggal` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `areas`
--

INSERT INTO `areas` (`id`, `nama_area`, `hama`, `jumlah`, `status`, `warna`, `geometry`, `tanggal`) VALUES
(2, 'Sawah Ridho', 'Tikus', 50, 'Waspada', '#f1c40f', '{\"type\":\"Polygon\",\"coordinates\":[[[110.332574,-7.765925],[110.333242,-7.766016],[110.333934,-7.766106],[110.333953,-7.765604],[110.333317,-7.765516],[110.332636,-7.765447],[110.332574,-7.765925]]]}', '2025-12-19'),
(3, 'Sawah Raffan', 'Ulat', 2000, 'Bahaya', '#e74c3c', '{\"type\":\"Polygon\",\"coordinates\":[[[110.334964,-7.764137],[110.333982,-7.764015],[110.333971,-7.7647],[110.334063,-7.764785],[110.33484,-7.76495],[110.334964,-7.764137]]]}', '2025-12-19'),
(4, 'Sawah Budi', 'Ulat', 59000, 'Bahaya', '#e74c3c', '{\"type\":\"Polygon\",\"coordinates\":[[[110.334776,-7.769045],[110.334999,-7.768344],[110.335562,-7.768501],[110.335339,-7.769207],[110.334776,-7.769045]]]}', '2025-12-19'),
(5, 'Sawah Bude', 'Tikus', 5000, 'Waspada', '#f1c40f', '{\"type\":\"Polygon\",\"coordinates\":[[[110.322852,-7.793114],[110.323175,-7.79316],[110.323233,-7.793336],[110.322842,-7.793328],[110.322852,-7.793114]]]}', '2025-12-19'),
(6, 'Sawah Jarno', 'Belalang', 2000, 'Bahaya', '#e74c3c', '{\"type\":\"Polygon\",\"coordinates\":[[[110.33314,-7.762005],[110.334964,-7.76234],[110.334867,-7.763207],[110.332968,-7.763031],[110.33314,-7.762005]]]}', '2025-12-19'),
(8, 'Kraton', 'Manusia Hitam', 10000, 'Aman', '#2ecc71', '{\"type\":\"Polygon\",\"coordinates\":[[[110.358095,-7.802267],[110.366464,-7.802819],[110.366635,-7.809346],[110.358095,-7.808984],[110.358095,-7.802267]]]}', '2025-12-19'),
(9, 'Kota Gede', 'Manusia Hitam Legam', 1000000, 'Waspada', '#f1c40f', '{\"type\":\"Polygon\",\"coordinates\":[[[110.380669,-7.780561],[110.39732,-7.782687],[110.397406,-7.791105],[110.377579,-7.794167],[110.380669,-7.780561]]]}', '2025-12-19'),
(10, 'Sawah Angga', 'Walang Sangit ngit', 12000, 'Bahaya', '#e74c3c', '{\"type\":\"Polygon\",\"coordinates\":[[[110.3617,-7.827925],[110.380926,-7.828946],[110.380068,-7.84187],[110.355349,-7.84085],[110.3617,-7.827925]]]}', '2025-12-19'),
(11, 'Area Gita', 'Orang Ireng, Orang Aring', 300, 'Bahaya', '#e74c3c', '{\"type\":\"Polygon\",\"coordinates\":[[[110.365155,-7.76091],[110.368427,-7.761065],[110.368325,-7.763786],[110.365332,-7.763924],[110.363706,-7.76351],[110.365155,-7.76091]]]}', '2025-12-20'),
(12, 'Sawah Be', 'Kora Kora', 122, 'Aman', '#2ecc71', '{\"type\":\"Polygon\",\"coordinates\":[[[110.385604,-7.807943],[110.403714,-7.810069],[110.40453,-7.816234],[110.394359,-7.821421],[110.383458,-7.814873],[110.37848,-7.811046],[110.385604,-7.807943]]]}', '2025-12-20');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','petugas') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', '123', 'admin'),
(2, 'petugas', '123', 'petugas');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `areas`
--
ALTER TABLE `areas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `areas`
--
ALTER TABLE `areas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
