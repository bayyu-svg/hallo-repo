-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 26, 2025 at 01:17 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `monitoring_operasional`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','manager') NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `nip` varchar(30) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `alamat` text,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `password`, `role`, `nama_lengkap`, `nip`, `no_hp`, `alamat`, `foto`, `created_at`) VALUES
(1, 'admin', 'fe01ce2a7fbac8fafaed7c982a04e229', 'admin', 'Administrator Sistem', '1234567890', '081234567890', 'Denpasar, Bali', 'uploads/profile/1766673862_gambar_telkom.jpg', '2025-12-24 00:17:55'),
(2, 'manager', 'fe01ce2a7fbac8fafaed7c982a04e229', 'manager', 'Manager', '1234567890', '081234567890', 'Denpasar, Bali', 'uploads/profile/1766671627_1000_F_250085179_lMeGgEMtB4bsRPORTfHYVF6BZ4rZM0Xx.jpg', '2025-12-25 14:05:15');

-- --------------------------------------------------------

--
-- Table structure for table `banks`
--

CREATE TABLE `banks` (
  `bank_id` int NOT NULL,
  `nama_bank` varchar(100) DEFAULT NULL,
  `pemilik_rekening` varchar(100) DEFAULT NULL,
  `nomor_rekening` varchar(50) DEFAULT NULL,
  `saldo` decimal(15,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `banks`
--

INSERT INTO `banks` (`bank_id`, `nama_bank`, `pemilik_rekening`, `nomor_rekening`, `saldo`, `created_at`) VALUES
(1, 'Bank BRI', 'PT Telkom Akses', '1234567890123456', 10000000.00, '2025-12-25 07:24:11'),
(2, 'Bank BCA', 'PT Telkom Akses', '9876543210987654', 25000000.00, '2025-12-25 07:24:11'),
(3, 'Bank Mandiri', 'PT Telkom Akses', '1122334455667788', 15000000.00, '2025-12-25 07:24:11'),
(4, 'Bank Mandiri', 'DEMO', '1234576437476', 20000000.00, '2025-12-25 12:50:30');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int NOT NULL,
  `bank_id` int DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `keterangan` text,
  `nominal` decimal(15,2) DEFAULT NULL,
  `tipe` enum('harian','mingguan') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `bank_id`, `tanggal`, `nama`, `keterangan`, `nominal`, `tipe`, `created_at`) VALUES
(1, 1, '2025-12-25', 'DEMO', 'Beli Kabel', 2000000.00, 'harian', '2025-12-25 07:30:56'),
(3, 2, '2025-12-25', 'COBA', 'beli tang printing', 350000.00, 'harian', '2025-12-25 08:02:51'),
(4, 1, '2025-12-26', 'DEMO2', 'Biaya Operasional Harian', 500000.00, 'harian', '2025-12-25 23:31:40'),
(6, 1, '2025-12-26', 'DEMO', 'Pengeluaran Mingguan', 2850000.00, 'mingguan', '2025-12-26 00:25:57');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_files`
--

CREATE TABLE `transaction_files` (
  `file_id` int NOT NULL,
  `transaction_id` int DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_type` varchar(20) DEFAULT NULL,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transaction_files`
--

INSERT INTO `transaction_files` (`file_id`, `transaction_id`, `file_name`, `file_path`, `file_type`, `uploaded_at`) VALUES
(1, 1, 'Fundamentals Marketing (1) (1).pdf', 'uploads/mingguan/1766656987_Fundamentals Marketing (1) (1).pdf', 'application/pdf', '2025-12-25 10:03:07'),
(2, 1, 'Fundamentals Marketing (1) (1).pdf', 'uploads/mingguan/1766656987_Fundamentals Marketing (1) (1).pdf', 'application/pdf', '2025-12-25 10:03:07'),
(3, 3, 'Salinan dari MINI TASK.pdf', 'uploads/mingguan/1766666144_Salinan dari MINI TASK.pdf', 'application/pdf', '2025-12-25 12:35:44'),
(5, 6, 'Fundamentals Marketing (1) (1) (1).pdf', 'uploads/mingguan/1766708757_Fundamentals Marketing (1) (1) (1).pdf', 'application/pdf', '2025-12-26 00:25:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `banks`
--
ALTER TABLE `banks`
  ADD PRIMARY KEY (`bank_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `bank_id` (`bank_id`);

--
-- Indexes for table `transaction_files`
--
ALTER TABLE `transaction_files`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `transaction_id` (`transaction_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `banks`
--
ALTER TABLE `banks`
  MODIFY `bank_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `transaction_files`
--
ALTER TABLE `transaction_files`
  MODIFY `file_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`bank_id`) REFERENCES `banks` (`bank_id`);

--
-- Constraints for table `transaction_files`
--
ALTER TABLE `transaction_files`
  ADD CONSTRAINT `transaction_files_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`transaction_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
