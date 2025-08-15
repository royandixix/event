-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 16, 2025 at 12:54 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `penaftaran`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `id_event` int(11) NOT NULL,
  `judul_event` varchar(255) NOT NULL,
  `deskripsi_event` text NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `lokasi_event` varchar(255) NOT NULL,
  `poster_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `harga_event` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`id_event`, `judul_event`, `deskripsi_event`, `tanggal_mulai`, `tanggal_selesai`, `lokasi_event`, `poster_path`, `created_at`, `harga_event`) VALUES
(3, 'mobil', 'pendaftaran unutk mobil', '2025-08-13', '2025-08-13', 'makassar', '1755237799_ea776-2020-03-04_showroom-event.jpg', '2025-08-15 06:03:19', 5000000000);

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `id_invoice` int(11) NOT NULL,
  `nomor_invoice` varchar(50) NOT NULL,
  `id_event` int(11) NOT NULL,
  `id_manajer` int(11) DEFAULT NULL,
  `id_peserta` int(11) DEFAULT NULL,
  `total_harga` bigint(20) NOT NULL,
  `kode_unik` int(11) NOT NULL,
  `total_transfer` bigint(20) NOT NULL,
  `bank_tujuan` varchar(50) NOT NULL,
  `no_rekening` varchar(50) NOT NULL,
  `nama_pemilik_rekening` varchar(100) NOT NULL,
  `gambar_bank` varchar(255) DEFAULT NULL,
  `status` enum('pending','lunas','batal') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `slot_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice`
--

INSERT INTO `invoice` (`id_invoice`, `nomor_invoice`, `id_event`, `id_manajer`, `id_peserta`, `total_harga`, `kode_unik`, `total_transfer`, `bank_tujuan`, `no_rekening`, `nama_pemilik_rekening`, `gambar_bank`, `status`, `created_at`, `slot_id`) VALUES
(2, 'SSM/INV-PADDOCK/08/2025/000003', 3, NULL, NULL, 5000000000, 615, 5000000615, 'Mandiri', '1330012345678', 'manusia', '1755237799_ag-branding-logo-2.png', 'pending', '2025-08-15 06:03:19', 0),
(3, 'SSM/INV-PADDOCK/08/2025/975974', 3, NULL, NULL, 5000000000, 573, 5000000573, 'Mandiri', '1330012345678', 'manusia', NULL, 'pending', '2025-08-15 20:34:41', 66),
(4, 'SSM/INV-PADDOCK/08/2025/048800', 3, NULL, NULL, 5000000000, 570, 5000000570, 'Mandiri', '1330012345678', 'manusia', NULL, 'pending', '2025-08-15 22:28:40', 71),
(5, 'SSM/INV-PADDOCK/08/2025/328087', 3, NULL, NULL, 5000000000, 609, 5000000609, 'Mandiri', '1330012345678', 'manusia', '1755237799_ag-branding-logo-2.png', 'pending', '2025-08-15 22:34:15', 63),
(6, 'SSM/INV-PADDOCK/08/2025/973437', 3, NULL, NULL, 5000000000, 535, 5000000535, 'Mandiri', '1330012345678', 'manusia', '1755237799_ag-branding-logo-2.png', 'pending', '2025-08-15 22:37:38', 62),
(7, 'SSM/INV-PADDOCK/08/2025/762985', 3, NULL, NULL, 5000000000, 622, 5000000622, 'Mandiri', '1330012345678', 'manusia', '1755237799_ag-branding-logo-2.png', 'pending', '2025-08-15 22:43:48', 68),
(8, 'SSM/INV-PADDOCK/08/2025/915790', 3, NULL, NULL, 5000000000, 703, 5000000703, 'Mandiri', '1330012345678', 'manusia', '1755237799_ag-branding-logo-2.png', 'pending', '2025-08-15 22:46:28', 65),
(9, 'SSM/INV-PADDOCK/08/2025/106418', 3, NULL, NULL, 5000000000, 179, 5000000179, 'Mandiri', '1330012345678', 'manusia', '1755237799_ag-branding-logo-2.png', 'pending', '2025-08-15 22:47:47', 64);

-- --------------------------------------------------------

--
-- Table structure for table `manajer`
--

CREATE TABLE `manajer` (
  `id_manajer` int(11) NOT NULL,
  `nama_manajer` varchar(255) NOT NULL,
  `nama_tim` varchar(255) NOT NULL,
  `foto_manajer` varchar(255) NOT NULL,
  `asal_provinsi` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `whatsapp` varchar(50) NOT NULL,
  `voucher` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `manajer`
--

INSERT INTO `manajer` (`id_manajer`, `nama_manajer`, `nama_tim`, `foto_manajer`, `asal_provinsi`, `email`, `whatsapp`, `voucher`, `created_at`) VALUES
(1, 'jokowi', 'garuda', 'manajer_1755170892.jpg', 'sulawesi barat', 'barat@gmail.com', '081678990111', '', '2025-08-14 11:28:12'),
(2, 'prabowo', 'prabowow', 'manajer_1755298374.jpg', 'sulawesi barat', 'barat@gmail.com', '081678990111', '', '2025-08-15 22:52:54');

-- --------------------------------------------------------

--
-- Table structure for table `manajer_kelas`
--

CREATE TABLE `manajer_kelas` (
  `id_kelas` int(11) NOT NULL,
  `manajer_id` int(11) NOT NULL,
  `kelas` varchar(100) NOT NULL,
  `warna_kendaraan` varchar(50) NOT NULL,
  `tipe_kendaraan` varchar(100) NOT NULL,
  `nomor_polisi` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `manajer_kelas`
--

INSERT INTO `manajer_kelas` (`id_kelas`, `manajer_id`, `kelas`, `warna_kendaraan`, `tipe_kendaraan`, `nomor_polisi`) VALUES
(1, 1, 'B', 'Merah', 'Mio', 'C 1234 XYA'),
(2, 2, 'B', 'Hitam', 'Pajero', 'C 1234 XYA');

-- --------------------------------------------------------

--
-- Table structure for table `paddock_booking`
--

CREATE TABLE `paddock_booking` (
  `id_booking` int(11) NOT NULL,
  `slot_id` int(11) NOT NULL,
  `nama_pesanan` varchar(100) NOT NULL,
  `nama_tim` varchar(100) NOT NULL,
  `nomor_wa` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `paddock_booking`
--

INSERT INTO `paddock_booking` (`id_booking`, `slot_id`, `nama_pesanan`, `nama_tim`, `nomor_wa`, `created_at`) VALUES
(4, 61, 'coding', 'coding', '081347018612', '2025-08-15 06:17:09'),
(5, 75, 'orang', 'orang', '081347018615', '2025-08-15 06:24:31'),
(6, 73, 'coba', 'coba', '081348901234', '2025-08-15 06:30:26'),
(7, 70, 'test', 'test', '081347018619', '2025-08-15 06:35:47'),
(8, 74, 'gas', 'gas', '081347018611', '2025-08-15 06:38:16'),
(9, 66, 'kedrik lamar', 'lamar', '0814456789001', '2025-08-15 20:34:41'),
(10, 71, 'gengs', 'gegege', '081347018611', '2025-08-15 22:28:40'),
(11, 63, 'demon slayer', 'demon slayer', '081723456789088', '2025-08-15 22:34:15'),
(12, 62, 'ulagan', 'ulagana', '081245666789', '2025-08-15 22:37:38'),
(13, 68, 'sukses mobil ', 'mobil', '985345678921', '2025-08-15 22:43:48'),
(14, 65, 'sukses mobil', 'mobil', '085348911212', '2025-08-15 22:46:28'),
(15, 64, 'suda bagus', 'suda bagus', '984545636464', '2025-08-15 22:47:47');

-- --------------------------------------------------------

--
-- Table structure for table `paddock_slot`
--

CREATE TABLE `paddock_slot` (
  `id_slot` int(11) NOT NULL,
  `id_event` int(11) NOT NULL,
  `nomor_slot` varchar(10) NOT NULL,
  `status` enum('kosong','terisi') DEFAULT 'kosong',
  `manajer_id` int(11) DEFAULT NULL,
  `peserta_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `paddock_slot`
--

INSERT INTO `paddock_slot` (`id_slot`, `id_event`, `nomor_slot`, `status`, `manajer_id`, `peserta_id`, `created_at`) VALUES
(61, 3, 'A1', 'terisi', NULL, NULL, '2025-08-15 06:16:06'),
(62, 3, 'A2', 'terisi', NULL, NULL, '2025-08-15 06:16:06'),
(63, 3, 'A3', 'terisi', NULL, NULL, '2025-08-15 06:16:06'),
(64, 3, 'A2', 'terisi', NULL, NULL, '2025-08-15 06:16:06'),
(65, 3, 'A3', 'terisi', NULL, NULL, '2025-08-15 06:16:06'),
(66, 3, 'A4', 'terisi', NULL, NULL, '2025-08-15 06:16:06'),
(67, 3, 'A5', 'kosong', NULL, NULL, '2025-08-15 06:16:06'),
(68, 3, 'A6', 'terisi', NULL, NULL, '2025-08-15 06:16:06'),
(69, 3, 'A7', 'kosong', NULL, NULL, '2025-08-15 06:16:06'),
(70, 3, 'A8', 'terisi', NULL, NULL, '2025-08-15 06:16:06'),
(71, 3, 'A9', 'terisi', NULL, NULL, '2025-08-15 06:16:06'),
(72, 3, 'A10', 'kosong', NULL, NULL, '2025-08-15 06:16:06'),
(73, 3, 'A11', 'terisi', NULL, NULL, '2025-08-15 06:16:06'),
(74, 3, 'A12', 'terisi', NULL, NULL, '2025-08-15 06:16:06'),
(75, 3, 'A13', 'terisi', NULL, NULL, '2025-08-15 06:16:06');

-- --------------------------------------------------------

--
-- Table structure for table `peserta`
--

CREATE TABLE `peserta` (
  `id_peserta` int(11) NOT NULL,
  `nama_peserta` varchar(255) NOT NULL,
  `nama_tim` varchar(255) NOT NULL,
  `foto_peserta` varchar(255) NOT NULL,
  `asal_provinsi` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `whatsapp` varchar(50) NOT NULL,
  `voucher` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `peserta`
--

INSERT INTO `peserta` (`id_peserta`, `nama_peserta`, `nama_tim`, `foto_peserta`, `asal_provinsi`, `email`, `whatsapp`, `voucher`, `created_at`) VALUES
(1, 'royandi', 'undipa', 'foto_peserta_1755095135.png', 'sulawesi barat', 'randiroyandi@gmail.com', '081347018612', '', '2025-08-13 14:25:35'),
(2, 'royandi', 'undipa', 'foto_peserta_1755180247.png', 'sulawesi barat', 'admin@gmail.com', '345678908', '', '2025-08-14 14:04:07');

-- --------------------------------------------------------

--
-- Table structure for table `peserta_kelas`
--

CREATE TABLE `peserta_kelas` (
  `id_kelas` int(11) NOT NULL,
  `peserta_id` int(11) NOT NULL,
  `kelas` varchar(100) NOT NULL,
  `warna_kendaraan` varchar(50) NOT NULL,
  `tipe_kendaraan` varchar(100) NOT NULL,
  `nomor_polisi` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `peserta_kelas`
--

INSERT INTO `peserta_kelas` (`id_kelas`, `peserta_id`, `kelas`, `warna_kendaraan`, `tipe_kendaraan`, `nomor_polisi`) VALUES
(1, 1, '2', 'biru', 'pajero', 'B 1234 XYZ (Jakarta)'),
(2, 2, '2', 'biru', 'mio', 'mera'),
(3, 2, '3', 'mera', 'pajero', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`id_event`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`id_invoice`),
  ADD UNIQUE KEY `nomor_invoice` (`nomor_invoice`),
  ADD KEY `id_event` (`id_event`),
  ADD KEY `id_manajer` (`id_manajer`),
  ADD KEY `id_peserta` (`id_peserta`);

--
-- Indexes for table `manajer`
--
ALTER TABLE `manajer`
  ADD PRIMARY KEY (`id_manajer`);

--
-- Indexes for table `manajer_kelas`
--
ALTER TABLE `manajer_kelas`
  ADD PRIMARY KEY (`id_kelas`),
  ADD KEY `fk_manajer` (`manajer_id`);

--
-- Indexes for table `paddock_booking`
--
ALTER TABLE `paddock_booking`
  ADD PRIMARY KEY (`id_booking`),
  ADD KEY `slot_id` (`slot_id`);

--
-- Indexes for table `paddock_slot`
--
ALTER TABLE `paddock_slot`
  ADD PRIMARY KEY (`id_slot`),
  ADD KEY `id_event` (`id_event`),
  ADD KEY `manajer_id` (`manajer_id`),
  ADD KEY `peserta_id` (`peserta_id`);

--
-- Indexes for table `peserta`
--
ALTER TABLE `peserta`
  ADD PRIMARY KEY (`id_peserta`);

--
-- Indexes for table `peserta_kelas`
--
ALTER TABLE `peserta_kelas`
  ADD PRIMARY KEY (`id_kelas`),
  ADD KEY `peserta_id` (`peserta_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `id_event` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `id_invoice` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `manajer`
--
ALTER TABLE `manajer`
  MODIFY `id_manajer` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `manajer_kelas`
--
ALTER TABLE `manajer_kelas`
  MODIFY `id_kelas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `paddock_booking`
--
ALTER TABLE `paddock_booking`
  MODIFY `id_booking` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `paddock_slot`
--
ALTER TABLE `paddock_slot`
  MODIFY `id_slot` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `peserta`
--
ALTER TABLE `peserta`
  MODIFY `id_peserta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `peserta_kelas`
--
ALTER TABLE `peserta_kelas`
  MODIFY `id_kelas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`id_event`) REFERENCES `event` (`id_event`),
  ADD CONSTRAINT `invoice_ibfk_2` FOREIGN KEY (`id_manajer`) REFERENCES `manajer` (`id_manajer`),
  ADD CONSTRAINT `invoice_ibfk_3` FOREIGN KEY (`id_peserta`) REFERENCES `peserta` (`id_peserta`);

--
-- Constraints for table `manajer_kelas`
--
ALTER TABLE `manajer_kelas`
  ADD CONSTRAINT `fk_manajer` FOREIGN KEY (`manajer_id`) REFERENCES `manajer` (`id_manajer`) ON DELETE CASCADE,
  ADD CONSTRAINT `manajer_kelas_ibfk_1` FOREIGN KEY (`manajer_id`) REFERENCES `manajer` (`id_manajer`) ON DELETE CASCADE;

--
-- Constraints for table `paddock_booking`
--
ALTER TABLE `paddock_booking`
  ADD CONSTRAINT `paddock_booking_ibfk_1` FOREIGN KEY (`slot_id`) REFERENCES `paddock_slot` (`id_slot`);

--
-- Constraints for table `paddock_slot`
--
ALTER TABLE `paddock_slot`
  ADD CONSTRAINT `paddock_slot_ibfk_1` FOREIGN KEY (`id_event`) REFERENCES `event` (`id_event`),
  ADD CONSTRAINT `paddock_slot_ibfk_2` FOREIGN KEY (`manajer_id`) REFERENCES `manajer` (`id_manajer`),
  ADD CONSTRAINT `paddock_slot_ibfk_3` FOREIGN KEY (`peserta_id`) REFERENCES `peserta` (`id_peserta`);

--
-- Constraints for table `peserta_kelas`
--
ALTER TABLE `peserta_kelas`
  ADD CONSTRAINT `peserta_kelas_ibfk_1` FOREIGN KEY (`peserta_id`) REFERENCES `peserta` (`id_peserta`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
