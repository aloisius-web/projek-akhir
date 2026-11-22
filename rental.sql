-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 22, 2026 at 01:47 PM
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
-- Database: `rental`
--

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
(1, 'MPV'),
(2, 'SUV'),
(3, 'City Car');

-- --------------------------------------------------------

--
-- Table structure for table `mobil`
--

CREATE TABLE `mobil` (
  `id_mobil` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `nama_mobil` varchar(100) NOT NULL,
  `transmisi` enum('manual','matic') NOT NULL,
  `kursi` int(11) NOT NULL,
  `harga_harian` int(11) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mobil`
--

INSERT INTO `mobil` (`id_mobil`, `id_kategori`, `nama_mobil`, `transmisi`, `kursi`, `harga_harian`, `foto`, `deskripsi`, `aktif`) VALUES
(1, 1, 'Toyota Avanza', 'manual', 7, 350000, 'mobil_6afab192ed4d5.jpg', 'Mobil keluarga yang nyaman dan muat banyak penumpang.', 1),
(2, 1, 'Daihatsu Xenia', 'manual', 7, 330000, 'mobil_6afab3c9a1dbb.jpeg', 'MPV ekonomis dan irit untuk perjalanan keluarga.', 1),
(3, 1, 'Suzuki Ertiga', 'matic', 6, 380000, 'mobil_6afab420a7e56.jpg', 'MPV matic yang nyaman untuk perjalanan jauh', 1);

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_mobil` int(11) NOT NULL,
  `tgl_mulai` date NOT NULL,
  `tgl_selesai` date NOT NULL,
  `total_harga` int(11) NOT NULL,
  `status_pesanan` enum('menunggu','diterima','ditolak','selesai','dibatalkan') NOT NULL DEFAULT 'menunggu',
  `metode_bayar` enum('transfer','di_tempat') DEFAULT NULL,
  `status_bayar` enum('belum_bayar','menunggu_verifikasi','lunas') NOT NULL DEFAULT 'belum_bayar',
  `bukti_bayar` varchar(255) DEFAULT NULL,
  `tgl_pesan` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `id_user`, `id_mobil`, `tgl_mulai`, `tgl_selesai`, `total_harga`, `status_pesanan`, `metode_bayar`, `status_bayar`, `bukti_bayar`, `tgl_pesan`) VALUES
(1, 3, 2, '2026-11-23', '2026-12-01', 2970000, 'menunggu', NULL, 'belum_bayar', NULL, '2026-11-22 12:11:29');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('pelanggan','admin') NOT NULL DEFAULT 'pelanggan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama`, `email`, `no_hp`, `password`, `role`) VALUES
(1, 'admin', 'admin@gmail.com', '08123456789', '$2y$10$p/e.3OTBJ6c0OoZMbm20/OBNtEVDG8Pze6.COcrabC8jU6oR1gx0e', 'admin'),
(2, 'poe', 'poe@gmail.com', '08', '$2y$10$8JoVWTE77x5a8GxFwffqj.phjsdBSHZC/m8PdpEmxwomF0Kg8HuR.', 'pelanggan'),
(3, 'manusiaa', 'manusia@gmail.com', '0812345678', '$2y$10$3clvCaSRu2P9l39poHAmdudEs6UsY6qiDhTlO9eb0QrL0gDY6Vw8i', 'pelanggan');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `mobil`
--
ALTER TABLE `mobil`
  ADD PRIMARY KEY (`id_mobil`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_mobil` (`id_mobil`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `mobil`
--
ALTER TABLE `mobil`
  MODIFY `id_mobil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `mobil`
--
ALTER TABLE `mobil`
  ADD CONSTRAINT `mobil_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`);

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `pesanan_ibfk_2` FOREIGN KEY (`id_mobil`) REFERENCES `mobil` (`id_mobil`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
