-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 12, 2025 at 07:03 AM
-- Server version: 10.4.13-MariaDB
-- PHP Version: 7.4.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_todolist`
--

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id_pengguna` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id_pengguna`, `nama`, `email`, `password`, `dibuat_pada`) VALUES
(11, 'amelia putri', 'amelia@gmail.com', '$2y$10$qc6HBG8wplMeG/WTmbOJZ..Avfr1Zi2QK9o9XnucVY0Y5iUuvpwgy', '2025-02-22 00:10:38'),
(16, 'amelia putri', 'adila@gmail.com', '$2y$10$PbRfE.KNeOd3b4SzwO0UIOQBrySLZ5r2PhpDnl6woGeblNb95OXqG', '2025-03-09 12:08:01');

-- --------------------------------------------------------

--
-- Table structure for table `subtasks`
--

CREATE TABLE `subtasks` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `subtasks` varchar(255) NOT NULL,
  `status` enum('pending','completed') DEFAULT 'pending',
  `deskripsi_subtugas` text DEFAULT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `subtasks`
--

INSERT INTO `subtasks` (`id`, `task_id`, `subtasks`, `status`, `deskripsi_subtugas`, `dibuat_pada`) VALUES
(25, 21, 'persiapan ukk', 'pending', 'penyerahan app', '2025-02-22 00:11:53'),
(26, 22, 'nyapu', 'completed', 'menyapu lantai', '2025-02-22 00:12:43'),
(27, 23, 'puasaaa', 'pending', 'nahan lapar', '2025-03-02 10:40:12'),
(28, 23, 'taraweh', 'pending', '23 rakaat', '2025-03-02 10:40:12'),
(32, 27, 'pra ukk', 'pending', 'pemantapan lgi', '2025-03-28 17:13:51'),
(34, 29, 'beres-beres halaman', 'completed', 'menggunting rumput panjang', '2025-04-09 20:38:34');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `task` varchar(255) NOT NULL,
  `deadline` datetime NOT NULL,
  `status` enum('pending','completed') DEFAULT 'pending',
  `deskripsi_tugas` text DEFAULT NULL,
  `prioritas` enum('rendah','sedang','tinggi') NOT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp(),
  `notifikasi` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `id_pengguna`, `task`, `deadline`, `status`, `deskripsi_tugas`, `prioritas`, `dibuat_pada`, `notifikasi`) VALUES
(21, 11, 'tugas sekolah', '2025-02-22 08:11:00', 'pending', 'pemantapan', 'rendah', '2025-02-22 00:11:53', 1),
(22, 11, 'tugas rumah', '2025-02-22 07:13:00', 'completed', 'harus selesai', 'rendah', '2025-02-22 00:12:43', 1),
(23, 11, 'tugas ramadan', '2025-03-30 17:39:00', 'pending', 'harus selesai', 'tinggi', '2025-03-02 10:40:12', 1),
(27, 11, 'sekolah lagi', '2025-03-30 00:12:00', 'pending', 'persiapan UKK', 'tinggi', '2025-03-28 17:13:51', 1),
(28, 11, 'tugas rumahan', '2025-04-20 03:14:00', 'pending', 'bantu mama', 'rendah', '2025-04-09 20:15:10', 0),
(29, 11, 'tugas rumah', '2025-04-18 03:37:00', 'completed', 'bantu ayah dan ibu', 'rendah', '2025-04-09 20:38:34', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_pengguna`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `subtasks`
--
ALTER TABLE `subtasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_task_id` (`task_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `subtasks`
--
ALTER TABLE `subtasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `subtasks`
--
ALTER TABLE `subtasks`
  ADD CONSTRAINT `fk_task_id` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subtasks_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
