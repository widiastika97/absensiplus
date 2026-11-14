-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 11, 2026 at 12:35 PM
-- Server version: 11.8.9-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u538000323_db_presensi`
--

-- --------------------------------------------------------

--
-- Table structure for table `cuti_karyawan`
--

CREATE TABLE `cuti_karyawan` (
  `id` int(11) NOT NULL,
  `karyawan_id` int(11) NOT NULL,
  `target_approver_id` int(11) NOT NULL COMMENT 'ID Karyawan/Atasan/HR yang dipilih oleh pemohon untuk memproses persetujuan',
  `target_approver_name` varchar(150) NOT NULL COMMENT 'Nama lengkap atasan penyetuju saat pengajuan dibuat',
  `kategori` enum('tahunan','khusus') NOT NULL DEFAULT 'tahunan' COMMENT 'tahunan: potong kuota 12/thn, khusus: tidak potong kuota',
  `tipe_cuti` enum('tahunan','sakit','melahirkan','izin') NOT NULL DEFAULT 'tahunan',
  `label_tipe` varchar(100) NOT NULL COMMENT 'Label yang ramah dibaca (cth: Cuti Tahunan, Cuti Sakit, Cuti Melahirkan, Izin Keperluan Pribadi)',
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `total_hari` int(11) NOT NULL DEFAULT 1 COMMENT 'Jumlah hari kerja efektif pengajuan cuti',
  `potong_kuota_tahunan` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = Memotong kuota tahunan (12/thn), 0 = Bebas kuota (cuti khusus)',
  `alasan` text NOT NULL,
  `lampiran_dokumen` varchar(255) DEFAULT NULL COMMENT 'File surat dokter / bukti pendukung di uploads/documents/',
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approved_by_id` int(11) DEFAULT NULL COMMENT 'ID Admin/Manager yang memproses persetujuan',
  `approved_by_name` varchar(150) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `alasan_penolakan` text DEFAULT NULL COMMENT 'Catatan alasan jika status pengajuan ditolak',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `cuti_karyawan`
--

INSERT INTO `cuti_karyawan` (`id`, `karyawan_id`, `target_approver_id`, `target_approver_name`, `kategori`, `tipe_cuti`, `label_tipe`, `tanggal_mulai`, `tanggal_selesai`, `total_hari`, `potong_kuota_tahunan`, `alasan`, `lampiran_dokumen`, `status`, `approved_by_id`, `approved_by_name`, `approved_at`, `alasan_penolakan`, `created_at`) VALUES
(1, 2, 1, 'Administrator Utama', 'khusus', 'izin', 'Izin Urusan Pribadi / Keluarga', '2026-09-11', '2026-09-12', 2, 0, 'Upacara agama', 'cuti_2_20260910_151032_3d9fefad.jpeg', 'approved', 1, 'Administrator Utama', '2026-09-10 15:11:26', NULL, '2026-09-10 07:10:32'),
(2, 2, 1, 'Administrator Utama', 'tahunan', 'tahunan', 'Cuti Tahunan Pribadi', '2026-09-17', '2026-09-21', 4, 1, 'Acara keluarga', NULL, 'approved', 1, 'Administrator Utama', '2026-09-10 15:19:55', NULL, '2026-09-10 07:19:34');

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_libur`
--

CREATE TABLE `jadwal_libur` (
  `id` int(11) NOT NULL,
  `nama_libur` varchar(150) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `tipe_target` enum('semua','karyawan','agama') NOT NULL DEFAULT 'semua' COMMENT 'semua: Semua Karyawan (Nasional), karyawan: Karyawan Tertentu, agama: Per Agama Tertentu',
  `target_agama` enum('Islam','Kristen','Katolik','Hindu','Buddha','Konghucu','Lainnya') DEFAULT NULL,
  `target_karyawan_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Array JSON ID karyawan misal [1, 3, 4]' CHECK (json_valid(`target_karyawan_ids`)),
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `jadwal_libur`
--

INSERT INTO `jadwal_libur` (`id`, `nama_libur`, `tanggal_mulai`, `tanggal_selesai`, `tipe_target`, `target_agama`, `target_karyawan_ids`, `deskripsi`, `created_at`) VALUES
(1, 'Hari Kemerdekaan Republik Indonesia Ke-81', '2026-08-17', '2026-08-17', 'semua', NULL, NULL, 'Hari Libur Nasional memperingati Kemerdekaan RI.', '2026-09-10 02:38:10'),
(2, 'Cuti Bersama Hari Kemerdekaan', '2026-08-18', '2026-08-18', 'semua', NULL, NULL, 'Cuti bersama tahunan perayaan kemerdekaan RI.', '2026-09-10 02:38:10'),
(3, 'Hari Raya Maulid Nabi Muhammad SAW 1448 H', '2026-08-25', '2026-08-25', 'agama', 'Islam', NULL, 'Hari libur keagamaan bagi seluruh umat Islam.', '2026-09-10 02:38:10'),
(4, 'Galungan', '2026-10-01', '2026-10-01', 'agama', 'Hindu', NULL, 'Hari Raya Galungan', '2026-09-11 22:46:53');

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_shift_karyawan`
--

CREATE TABLE `jadwal_shift_karyawan` (
  `id` int(11) NOT NULL,
  `karyawan_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `jadwal_shift_karyawan`
--

INSERT INTO `jadwal_shift_karyawan` (`id`, `karyawan_id`, `shift_id`, `tanggal`, `catatan`, `created_at`) VALUES
(1, 1, 1, '2026-09-01', NULL, '2026-09-10 07:26:32'),
(2, 1, 1, '2026-09-02', NULL, '2026-09-10 07:26:32'),
(3, 1, 1, '2026-09-03', NULL, '2026-09-10 07:26:32'),
(4, 1, 1, '2026-09-04', NULL, '2026-09-10 07:26:32'),
(5, 1, 1, '2026-09-05', NULL, '2026-09-10 07:26:32'),
(6, 1, 5, '2026-09-06', NULL, '2026-09-10 07:26:32'),
(7, 1, 1, '2026-09-07', NULL, '2026-09-10 07:26:32'),
(8, 1, 1, '2026-09-08', NULL, '2026-09-10 07:26:32'),
(9, 1, 1, '2026-09-09', NULL, '2026-09-10 07:26:32'),
(10, 1, 1, '2026-09-10', NULL, '2026-09-10 07:26:32'),
(11, 1, 1, '2026-09-11', NULL, '2026-09-10 07:26:32'),
(12, 1, 1, '2026-09-12', NULL, '2026-09-10 07:26:32'),
(13, 1, 5, '2026-09-13', NULL, '2026-09-10 07:26:32'),
(14, 1, 1, '2026-09-14', NULL, '2026-09-10 07:26:32'),
(15, 1, 1, '2026-09-15', NULL, '2026-09-10 07:26:32'),
(16, 1, 1, '2026-09-16', NULL, '2026-09-10 07:26:32'),
(17, 1, 1, '2026-09-17', NULL, '2026-09-10 07:26:32'),
(18, 1, 1, '2026-09-18', NULL, '2026-09-10 07:26:32'),
(19, 1, 1, '2026-09-19', NULL, '2026-09-10 07:26:32'),
(20, 1, 5, '2026-09-20', NULL, '2026-09-10 07:26:32'),
(21, 1, 1, '2026-09-21', NULL, '2026-09-10 07:26:32'),
(22, 1, 1, '2026-09-22', NULL, '2026-09-10 07:26:32'),
(23, 1, 1, '2026-09-23', NULL, '2026-09-10 07:26:32'),
(24, 1, 1, '2026-09-24', NULL, '2026-09-10 07:26:32'),
(25, 1, 1, '2026-09-25', NULL, '2026-09-10 07:26:32'),
(26, 1, 1, '2026-09-26', NULL, '2026-09-10 07:26:32'),
(27, 1, 5, '2026-09-27', NULL, '2026-09-10 07:26:32'),
(28, 1, 1, '2026-09-28', NULL, '2026-09-10 07:26:32'),
(29, 1, 1, '2026-09-29', NULL, '2026-09-10 07:26:32'),
(30, 1, 1, '2026-09-30', NULL, '2026-09-10 07:26:32'),
(31, 2, 1, '2026-09-01', NULL, '2026-09-10 07:26:32'),
(32, 2, 1, '2026-09-02', NULL, '2026-09-10 07:26:32'),
(33, 2, 1, '2026-09-03', NULL, '2026-09-10 07:26:32'),
(34, 2, 1, '2026-09-04', NULL, '2026-09-10 07:26:32'),
(35, 2, 1, '2026-09-05', NULL, '2026-09-10 07:26:32'),
(36, 2, 5, '2026-09-06', NULL, '2026-09-10 07:26:32'),
(37, 2, 1, '2026-09-07', NULL, '2026-09-10 07:26:32'),
(38, 2, 1, '2026-09-08', NULL, '2026-09-10 07:26:32'),
(39, 2, 1, '2026-09-09', NULL, '2026-09-10 07:26:32'),
(40, 2, 1, '2026-09-10', NULL, '2026-09-10 07:26:32'),
(41, 2, 1, '2026-09-11', NULL, '2026-09-10 07:26:32'),
(42, 2, 1, '2026-09-12', NULL, '2026-09-10 07:26:32'),
(43, 2, 5, '2026-09-13', NULL, '2026-09-10 07:26:32'),
(44, 2, 1, '2026-09-14', NULL, '2026-09-10 07:26:32'),
(45, 2, 1, '2026-09-15', NULL, '2026-09-10 07:26:32'),
(46, 2, 1, '2026-09-16', NULL, '2026-09-10 07:26:32'),
(47, 2, 1, '2026-09-17', NULL, '2026-09-10 07:26:32'),
(48, 2, 1, '2026-09-18', NULL, '2026-09-10 07:26:32'),
(49, 2, 1, '2026-09-19', NULL, '2026-09-10 07:26:32'),
(50, 2, 5, '2026-09-20', NULL, '2026-09-10 07:26:32'),
(51, 2, 1, '2026-09-21', NULL, '2026-09-10 07:26:32'),
(52, 2, 1, '2026-09-22', NULL, '2026-09-10 07:26:32'),
(53, 2, 1, '2026-09-23', NULL, '2026-09-10 07:26:32'),
(54, 2, 1, '2026-09-24', NULL, '2026-09-10 07:26:32'),
(55, 2, 1, '2026-09-25', NULL, '2026-09-10 07:26:32'),
(56, 2, 1, '2026-09-26', NULL, '2026-09-10 07:26:32'),
(57, 2, 5, '2026-09-27', NULL, '2026-09-10 07:26:32'),
(58, 2, 1, '2026-09-28', NULL, '2026-09-10 07:26:32'),
(59, 2, 1, '2026-09-29', NULL, '2026-09-10 07:26:32'),
(60, 2, 1, '2026-09-30', NULL, '2026-09-10 07:26:32'),
(61, 3, 1, '2026-09-01', NULL, '2026-09-10 07:26:32'),
(62, 3, 1, '2026-09-02', NULL, '2026-09-10 07:26:32'),
(63, 3, 1, '2026-09-03', NULL, '2026-09-10 07:26:32'),
(64, 3, 1, '2026-09-04', NULL, '2026-09-10 07:26:32'),
(65, 3, 5, '2026-09-05', '', '2026-09-10 07:26:32'),
(66, 3, 5, '2026-09-06', NULL, '2026-09-10 07:26:32'),
(67, 3, 1, '2026-09-07', NULL, '2026-09-10 07:26:32'),
(68, 3, 1, '2026-09-08', NULL, '2026-09-10 07:26:32'),
(69, 3, 1, '2026-09-09', NULL, '2026-09-10 07:26:32'),
(70, 3, 1, '2026-09-10', NULL, '2026-09-10 07:26:32'),
(71, 3, 1, '2026-09-11', NULL, '2026-09-10 07:26:32'),
(72, 3, 1, '2026-09-12', NULL, '2026-09-10 07:26:32'),
(73, 3, 5, '2026-09-13', NULL, '2026-09-10 07:26:32'),
(74, 3, 1, '2026-09-14', NULL, '2026-09-10 07:26:32'),
(75, 3, 1, '2026-09-15', NULL, '2026-09-10 07:26:32'),
(76, 3, 1, '2026-09-16', NULL, '2026-09-10 07:26:32'),
(77, 3, 1, '2026-09-17', NULL, '2026-09-10 07:26:32'),
(78, 3, 1, '2026-09-18', NULL, '2026-09-10 07:26:32'),
(79, 3, 1, '2026-09-19', NULL, '2026-09-10 07:26:32'),
(80, 3, 5, '2026-09-20', NULL, '2026-09-10 07:26:32'),
(81, 3, 1, '2026-09-21', NULL, '2026-09-10 07:26:32'),
(82, 3, 1, '2026-09-22', NULL, '2026-09-10 07:26:32'),
(83, 3, 1, '2026-09-23', NULL, '2026-09-10 07:26:32'),
(84, 3, 1, '2026-09-24', NULL, '2026-09-10 07:26:32'),
(85, 3, 1, '2026-09-25', NULL, '2026-09-10 07:26:32'),
(86, 3, 1, '2026-09-26', NULL, '2026-09-10 07:26:32'),
(87, 3, 5, '2026-09-27', NULL, '2026-09-10 07:26:32'),
(88, 3, 1, '2026-09-28', NULL, '2026-09-10 07:26:32'),
(89, 3, 1, '2026-09-29', NULL, '2026-09-10 07:26:32'),
(90, 3, 1, '2026-09-30', NULL, '2026-09-10 07:26:32'),
(91, 4, 1, '2026-09-01', NULL, '2026-09-10 07:26:32'),
(92, 4, 1, '2026-09-02', NULL, '2026-09-10 07:26:32'),
(93, 4, 1, '2026-09-03', NULL, '2026-09-10 07:26:32'),
(94, 4, 1, '2026-09-04', NULL, '2026-09-10 07:26:32'),
(95, 4, 1, '2026-09-05', NULL, '2026-09-10 07:26:32'),
(96, 4, 5, '2026-09-06', NULL, '2026-09-10 07:26:32'),
(97, 4, 1, '2026-09-07', NULL, '2026-09-10 07:26:32'),
(98, 4, 1, '2026-09-08', NULL, '2026-09-10 07:26:32'),
(99, 4, 1, '2026-09-09', NULL, '2026-09-10 07:26:32'),
(100, 4, 1, '2026-09-10', NULL, '2026-09-10 07:26:32'),
(101, 4, 1, '2026-09-11', NULL, '2026-09-10 07:26:32'),
(102, 4, 1, '2026-09-12', NULL, '2026-09-10 07:26:32'),
(103, 4, 5, '2026-09-13', NULL, '2026-09-10 07:26:32'),
(104, 4, 1, '2026-09-14', NULL, '2026-09-10 07:26:32'),
(105, 4, 1, '2026-09-15', NULL, '2026-09-10 07:26:32'),
(106, 4, 1, '2026-09-16', NULL, '2026-09-10 07:26:32'),
(107, 4, 1, '2026-09-17', NULL, '2026-09-10 07:26:32'),
(108, 4, 1, '2026-09-18', NULL, '2026-09-10 07:26:32'),
(109, 4, 1, '2026-09-19', NULL, '2026-09-10 07:26:32'),
(110, 4, 5, '2026-09-20', NULL, '2026-09-10 07:26:32'),
(111, 4, 1, '2026-09-21', NULL, '2026-09-10 07:26:32'),
(112, 4, 1, '2026-09-22', NULL, '2026-09-10 07:26:32'),
(113, 4, 1, '2026-09-23', NULL, '2026-09-10 07:26:32'),
(114, 4, 1, '2026-09-24', NULL, '2026-09-10 07:26:32'),
(115, 4, 1, '2026-09-25', NULL, '2026-09-10 07:26:32'),
(116, 4, 1, '2026-09-26', NULL, '2026-09-10 07:26:32'),
(117, 4, 5, '2026-09-27', NULL, '2026-09-10 07:26:32'),
(118, 4, 1, '2026-09-28', NULL, '2026-09-10 07:26:32'),
(119, 4, 1, '2026-09-29', NULL, '2026-09-10 07:26:32'),
(120, 4, 1, '2026-09-30', NULL, '2026-09-10 07:26:32');

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `id` int(11) NOT NULL,
  `nip` varchar(50) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `departemen` varchar(100) NOT NULL,
  `jabatan` varchar(100) NOT NULL,
  `role_id` int(11) NOT NULL DEFAULT 4 COMMENT 'Foreign Key ke tabel roles',
  `agama` enum('Islam','Kristen','Katolik','Hindu','Buddha','Konghucu','Lainnya') NOT NULL DEFAULT 'Islam' COMMENT 'Agama/Kepercayaan Karyawan untuk Penyesuaian Jadwal Libur',
  `no_hp` varchar(20) DEFAULT NULL,
  `foto_profil` varchar(255) DEFAULT 'default-avatar.png',
  `jam_masuk` time DEFAULT '08:00:00',
  `jam_pulang` time DEFAULT '17:00:00',
  `kuota_cuti_tahunan` int(11) NOT NULL DEFAULT 12 COMMENT 'Jatah Kuota Saldo Awal Cuti Tahunan Karyawan per Tahun (Hari)',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`id`, `nip`, `nama`, `email`, `password`, `departemen`, `jabatan`, `role_id`, `agama`, `no_hp`, `foto_profil`, `jam_masuk`, `jam_pulang`, `kuota_cuti_tahunan`, `is_active`, `created_at`) VALUES
(1, 'ADM-001', 'Administrator Utama', 'admin@perusahaan.com', '$2y$10$1bPruyR.7jpvPablUc6LkO8q7exFIeGOMxY.5DCyzWNEi3d8r5qGG', 'Management', 'System Administrator', 1, 'Islam', '08111222333', 'default-avatar.png', '08:00:00', '17:00:00', 12, 1, '2026-09-10 02:38:10'),
(2, 'EMP-2026001', 'Budi Santoso, S.Kom', 'budi.santoso@perusahaan.co.id', '$2y$10$uN9OeJJG2/ATqaEPLJZkiO4Ol/yocG.3g0ruoYUWPOm/wwuE5Ik9m', 'IT & Engineering', 'Senior Software Engineer', 4, 'Islam', '081234567890', 'default-avatar.png', '08:00:00', '17:00:00', 12, 1, '2026-09-10 02:38:10'),
(3, 'EMP-2026002', 'Siti Rahmawati, S.E.', 'siti.rahma@perusahaan.co.id', '123456', 'Finance & Accounting', 'Finance Supervisor', 3, 'Islam', '081298765432', 'default-avatar.png', '08:00:00', '17:00:00', 12, 1, '2026-09-10 02:38:10'),
(4, 'EMP-2026003', 'Ahmad Fauzi, S.T.', 'ahmad.fauzi@perusahaan.co.id', '123456', 'Human Resources', 'HR Specialist', 2, 'Islam', '081311223344', 'default-avatar.png', '08:00:00', '17:00:00', 12, 1, '2026-09-10 02:38:10'),
(5, 'Angga', 'Tusan Adji', 'angga@gmail.com', '$2y$10$lFSUxHuEEm8GaYDCMZl8ouQ5yAYhIBXhuyBl7inHRRyfkdxcFi6he', 'IT', 'Developer', 4, 'Islam', NULL, 'default-avatar.png', '08:00:00', '17:00:00', 12, 1, '2026-09-11 22:40:08');

-- --------------------------------------------------------

--
-- Table structure for table `lokasi_kantor`
--

CREATE TABLE `lokasi_kantor` (
  `id` int(11) NOT NULL,
  `kode_lokasi` varchar(50) NOT NULL,
  `nama_lokasi` varchar(150) NOT NULL,
  `alamat` text NOT NULL,
  `kota` varchar(100) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `radius` int(11) NOT NULL DEFAULT 50 COMMENT 'Radius toleransi dalam satuan meter',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = Aktif, 0 = Nonaktif',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `lokasi_kantor`
--

INSERT INTO `lokasi_kantor` (`id`, `kode_lokasi`, `nama_lokasi`, `alamat`, `kota`, `latitude`, `longitude`, `radius`, `is_active`, `created_at`) VALUES
(1, 'PAG-AYN', 'PAG Ayani', '-', 'Denpasar', -8.60750200, 115.21351900, 100, 1, '2026-09-10 02:38:10'),
(2, 'PAG-GYR', 'PAG Gianyar', '-', 'Gianyar', -8.54662700, 115.30903400, 75, 1, '2026-09-10 02:38:10'),
(5, 'Baturiti', 'Mesaritech', 'Banjar Poyan, Luwus, Baturiti, Tabanan', 'Tabanan', -8.39613800, 115.20387300, 20, 1, '2026-09-11 22:38:59'),
(6, 'Soogud', 'Coffe', 'Denpasar', 'Denpasar', -8.64906900, 115.22308100, 100, 1, '2026-11-09 12:38:33');

-- --------------------------------------------------------

--
-- Table structure for table `master_shift`
--

CREATE TABLE `master_shift` (
  `id` int(11) NOT NULL,
  `kode_shift` varchar(50) NOT NULL,
  `nama_shift` varchar(100) NOT NULL,
  `jam_masuk` time NOT NULL,
  `jam_pulang` time NOT NULL,
  `is_night_shift` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = Shift malam / lintas hari (misal 22:00 - 06:00)',
  `is_off` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = Jadwal Libur Shift / Day Off',
  `warna_badge` varchar(30) DEFAULT 'blue' COMMENT 'emerald, blue, purple, amber, rose, cyan, slate',
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `master_shift`
--

INSERT INTO `master_shift` (`id`, `kode_shift`, `nama_shift`, `jam_masuk`, `jam_pulang`, `is_night_shift`, `is_off`, `warna_badge`, `deskripsi`, `created_at`) VALUES
(1, 'S-PAGI', 'Normal Office / Pagi', '08:00:00', '17:00:00', 0, 0, 'emerald', 'Jam kerja operasional normal 08:00 - 17:00 WITA.', '2026-09-10 02:38:10'),
(2, 'S-SIANG', 'Shift Siang', '13:00:00', '21:00:00', 0, 0, 'blue', 'Jam kerja operasional siang hingga malam WITA.', '2026-09-10 02:38:10'),
(3, 'S-MALAM', 'Shift Malam (Night)', '21:00:00', '05:00:00', 1, 0, 'purple', 'Shift malam lintas hari untuk tim teknis & support 24/7.', '2026-09-10 02:38:10'),
(4, 'S-MIDDLE', 'Shift Middle', '10:00:00', '18:00:00', 0, 0, 'amber', 'Shift fleksibel jam sibuk operasional.', '2026-09-10 02:38:10'),
(5, 'S-OFF', 'Libur Shift (Day Off)', '00:00:00', '00:00:00', 0, 1, 'slate', 'Jadwal hari libur pengganti roster kerja.', '2026-09-10 02:38:10');

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_lainnya`
--

CREATE TABLE `pengajuan_lainnya` (
  `id` int(11) NOT NULL,
  `karyawan_id` int(11) NOT NULL,
  `tipe_pengajuan` enum('terlambat','pulang_cepat') NOT NULL COMMENT 'terlambat: Terlambat Masuk, pulang_cepat: Pulang Lebih Awal',
  `label_tipe` varchar(100) NOT NULL COMMENT 'Pengajuan Terlambat Masuk / Pulang Lebih Awal',
  `tanggal` date NOT NULL COMMENT 'Tanggal Kejadian',
  `jam_estimasi` time NOT NULL COMMENT 'Estimasi Jam Datang / Jam Pulang',
  `target_approver_id` int(11) NOT NULL COMMENT 'ID Atasan / Approver yang Ditunjuk',
  `target_approver_name` varchar(150) NOT NULL,
  `alasan` text NOT NULL COMMENT 'Keterangan Alasan Keperluan / Kendala',
  `foto_bukti` varchar(255) DEFAULT NULL COMMENT 'File Foto Bukti (Wajib untuk Terlambat, Opsional untuk Pulang Awal)',
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approved_by_id` int(11) DEFAULT NULL,
  `approved_by_name` varchar(150) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `alasan_penolakan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `pengajuan_lainnya`
--

INSERT INTO `pengajuan_lainnya` (`id`, `karyawan_id`, `tipe_pengajuan`, `label_tipe`, `tanggal`, `jam_estimasi`, `target_approver_id`, `target_approver_name`, `alasan`, `foto_bukti`, `status`, `approved_by_id`, `approved_by_name`, `approved_at`, `alasan_penolakan`, `created_at`) VALUES
(1, 2, 'pulang_cepat', 'Pulang Lebih Awal', '2026-09-10', '15:13:00', 1, 'Administrator Utama', 'Acara keluarga', NULL, 'approved', 1, 'Administrator Utama', '2026-09-10 15:37:14', NULL, '2026-09-10 07:14:08');

-- --------------------------------------------------------

--
-- Table structure for table `presensi`
--

CREATE TABLE `presensi` (
  `id` int(11) NOT NULL,
  `karyawan_id` int(11) NOT NULL,
  `lokasi_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `tipe` enum('masuk','pulang') NOT NULL,
  `jam` time NOT NULL,
  `foto_selfie` varchar(255) NOT NULL COMMENT 'Nama file foto selfie webp/jpg di uploads/selfie/',
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `jarak_meter` decimal(8,2) NOT NULL COMMENT 'Jarak GPS karyawan ke titik kantor saat submit',
  `radius_diizinkan` int(11) NOT NULL COMMENT 'Radius maksimal yang diperbolehkan kantor saat itu',
  `status` enum('tepat_waktu','terlambat','lembur','dini') DEFAULT 'tepat_waktu',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `presensi`
--

INSERT INTO `presensi` (`id`, `karyawan_id`, `lokasi_id`, `tanggal`, `tipe`, `jam`, `foto_selfie`, `latitude`, `longitude`, `jarak_meter`, `radius_diizinkan`, `status`, `keterangan`, `created_at`) VALUES
(1, 1, 1, '2026-08-12', 'masuk', '08:02:23', 'user.jpg', -6.20870000, 106.82280000, 41.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-12 08:02:23'),
(2, 1, 1, '2026-08-12', 'pulang', '17:10:17', 'user.jpg', -6.20870000, 106.82280000, 68.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-12 17:10:17'),
(3, 2, 1, '2026-08-12', 'masuk', '08:03:23', 'user.jpg', -6.20870000, 106.82260000, 46.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-12 08:03:23'),
(4, 2, 1, '2026-08-12', 'pulang', '17:11:17', 'user.jpg', -6.20870000, 106.82260000, 71.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-12 17:11:17'),
(5, 3, 1, '2026-08-12', 'masuk', '08:04:23', 'user.jpg', -6.88550000, 107.61385000, 51.00, 75, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-12 08:04:23'),
(6, 3, 1, '2026-08-12', 'pulang', '17:12:17', 'user.jpg', -6.88550000, 107.61385000, 21.00, 75, 'tepat_waktu', 'Absen pulang reguler', '2026-08-12 17:12:17'),
(7, 4, 1, '2026-08-12', 'masuk', '08:05:23', 'user.jpg', -6.20870000, 106.82270000, 56.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-12 08:05:23'),
(8, 4, 1, '2026-08-12', 'pulang', '17:13:17', 'user.jpg', -6.20870000, 106.82270000, 77.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-12 17:13:17'),
(9, 1, 1, '2026-08-13', 'masuk', '08:02:16', 'user.jpg', -6.20875000, 106.82275000, 40.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-13 08:02:16'),
(10, 1, 1, '2026-08-13', 'pulang', '17:10:04', 'user.jpg', -6.20875000, 106.82275000, 66.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-13 17:10:04'),
(11, 2, 1, '2026-08-13', 'masuk', '08:03:16', 'user.jpg', -6.20875000, 106.82280000, 45.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-13 08:03:16'),
(12, 2, 1, '2026-08-13', 'pulang', '17:11:04', 'user.jpg', -6.20875000, 106.82280000, 69.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-13 17:11:04'),
(13, 3, 1, '2026-08-13', 'masuk', '08:04:16', 'user.jpg', -6.88555000, 107.61380000, 50.00, 75, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-13 08:04:16'),
(14, 3, 1, '2026-08-13', 'pulang', '18:30:12', 'user.jpg', -6.88555000, 107.61380000, 19.00, 75, 'lembur', 'Lembur menyelesaikan rekapitulasi data pekerjaan', '2026-08-13 18:30:12'),
(15, 4, 1, '2026-08-13', 'masuk', '08:05:16', 'user.jpg', -6.20875000, 106.82265000, 55.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-13 08:05:16'),
(16, 4, 1, '2026-08-13', 'pulang', '17:13:04', 'user.jpg', -6.20875000, 106.82265000, 75.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-13 17:13:04'),
(17, 1, 1, '2026-08-14', 'masuk', '08:02:09', 'user.jpg', -6.20880000, 106.82270000, 39.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-14 08:02:09'),
(18, 1, 1, '2026-08-14', 'pulang', '18:57:03', 'user.jpg', -6.20880000, 106.82270000, 64.00, 100, 'lembur', 'Lembur menyelesaikan rekapitulasi data pekerjaan', '2026-08-14 18:57:03'),
(19, 2, 1, '2026-08-14', 'masuk', '08:03:09', 'user.jpg', -6.20880000, 106.82275000, 44.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-14 08:03:09'),
(20, 2, 1, '2026-08-14', 'pulang', '17:11:51', 'user.jpg', -6.20880000, 106.82275000, 67.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-14 17:11:51'),
(21, 3, 1, '2026-08-14', 'masuk', '08:04:09', 'user.jpg', -6.88560000, 107.61400000, 49.00, 75, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-14 08:04:09'),
(22, 3, 1, '2026-08-14', 'pulang', '17:12:51', 'user.jpg', -6.88560000, 107.61400000, 17.00, 75, 'tepat_waktu', 'Absen pulang reguler', '2026-08-14 17:12:51'),
(23, 4, 1, '2026-08-14', 'masuk', '08:05:09', 'user.jpg', -6.20880000, 106.82260000, 54.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-14 08:05:09'),
(24, 4, 1, '2026-08-14', 'pulang', '17:13:51', 'user.jpg', -6.20880000, 106.82260000, 73.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-14 17:13:51'),
(25, 1, 1, '2026-08-15', 'masuk', '08:21:46', 'user.jpg', -6.20885000, 106.82265000, 38.00, 100, 'terlambat', 'Terlambat akibat kendala kemacetan lalu lintas', '2026-08-15 08:21:46'),
(26, 1, 1, '2026-08-15', 'pulang', '17:10:38', 'user.jpg', -6.20885000, 106.82265000, 62.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-15 17:10:38'),
(27, 2, 1, '2026-08-15', 'masuk', '08:03:02', 'user.jpg', -6.20885000, 106.82270000, 43.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-15 08:03:02'),
(28, 2, 1, '2026-08-15', 'pulang', '17:11:38', 'user.jpg', -6.20885000, 106.82270000, 65.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-15 17:11:38'),
(29, 3, 1, '2026-08-15', 'masuk', '08:04:02', 'user.jpg', -6.88565000, 107.61395000, 48.00, 75, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-15 08:04:02'),
(30, 3, 1, '2026-08-15', 'pulang', '17:12:38', 'user.jpg', -6.88565000, 107.61395000, 15.00, 75, 'tepat_waktu', 'Absen pulang reguler', '2026-08-15 17:12:38'),
(31, 4, 1, '2026-08-15', 'masuk', '08:05:02', 'user.jpg', -6.20885000, 106.82280000, 53.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-15 08:05:02'),
(32, 4, 1, '2026-08-15', 'pulang', '17:13:38', 'user.jpg', -6.20885000, 106.82280000, 71.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-15 17:13:38'),
(33, 1, 1, '2026-08-19', 'masuk', '08:02:34', 'user.jpg', -6.20880000, 106.82270000, 34.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-19 08:02:34'),
(34, 1, 1, '2026-08-19', 'pulang', '17:10:46', 'user.jpg', -6.20880000, 106.82270000, 54.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-19 17:10:46'),
(35, 2, 1, '2026-08-19', 'masuk', '08:03:34', 'user.jpg', -6.20880000, 106.82275000, 39.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-19 08:03:34'),
(36, 2, 1, '2026-08-19', 'pulang', '17:11:46', 'user.jpg', -6.20880000, 106.82275000, 57.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-19 17:11:46'),
(37, 3, 1, '2026-08-19', 'masuk', '08:04:34', 'user.jpg', -6.88560000, 107.61400000, 44.00, 75, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-19 08:04:34'),
(38, 3, 1, '2026-08-19', 'pulang', '17:12:46', 'user.jpg', -6.88560000, 107.61400000, 60.00, 75, 'tepat_waktu', 'Absen pulang reguler', '2026-08-19 17:12:46'),
(39, 4, 1, '2026-08-19', 'masuk', '08:05:34', 'user.jpg', -6.20880000, 106.82260000, 49.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-19 08:05:34'),
(40, 4, 1, '2026-08-19', 'pulang', '17:13:46', 'user.jpg', -6.20880000, 106.82260000, 63.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-19 17:13:46'),
(41, 1, 1, '2026-08-20', 'masuk', '08:02:27', 'user.jpg', -6.20885000, 106.82265000, 33.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-20 08:02:27'),
(42, 1, 1, '2026-08-20', 'pulang', '17:10:33', 'user.jpg', -6.20885000, 106.82265000, 52.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-20 17:10:33'),
(43, 2, 1, '2026-08-20', 'masuk', '08:03:27', 'user.jpg', -6.20885000, 106.82270000, 38.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-20 08:03:27'),
(44, 2, 1, '2026-08-20', 'pulang', '17:11:33', 'user.jpg', -6.20885000, 106.82270000, 55.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-20 17:11:33'),
(45, 3, 1, '2026-08-20', 'masuk', '08:04:27', 'user.jpg', -6.88565000, 107.61395000, 43.00, 75, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-20 08:04:27'),
(46, 3, 1, '2026-08-20', 'pulang', '17:12:33', 'user.jpg', -6.88565000, 107.61395000, 58.00, 75, 'tepat_waktu', 'Absen pulang reguler', '2026-08-20 17:12:33'),
(47, 4, 1, '2026-08-20', 'masuk', '08:05:27', 'user.jpg', -6.20885000, 106.82280000, 48.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-20 08:05:27'),
(48, 4, 1, '2026-08-20', 'pulang', '17:13:33', 'user.jpg', -6.20885000, 106.82280000, 61.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-20 17:13:33'),
(49, 1, 1, '2026-08-21', 'masuk', '08:02:20', 'user.jpg', -6.20890000, 106.82260000, 32.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-21 08:02:20'),
(50, 1, 1, '2026-08-21', 'pulang', '17:10:20', 'user.jpg', -6.20890000, 106.82260000, 50.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-21 17:10:20'),
(51, 2, 1, '2026-08-21', 'masuk', '08:40:40', 'user.jpg', -6.20890000, 106.82265000, 37.00, 100, 'terlambat', 'Terlambat akibat kendala kemacetan lalu lintas', '2026-08-21 08:40:40'),
(52, 2, 1, '2026-08-21', 'pulang', '17:11:20', 'user.jpg', -6.20890000, 106.82265000, 53.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-21 17:11:20'),
(53, 3, 1, '2026-08-21', 'masuk', '08:04:20', 'user.jpg', -6.88570000, 107.61390000, 42.00, 75, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-21 08:04:20'),
(54, 3, 1, '2026-08-21', 'pulang', '17:12:20', 'user.jpg', -6.88570000, 107.61390000, 56.00, 75, 'tepat_waktu', 'Absen pulang reguler', '2026-08-21 17:12:20'),
(55, 4, 1, '2026-08-21', 'masuk', '08:05:20', 'user.jpg', -6.20890000, 106.82275000, 47.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-21 08:05:20'),
(56, 4, 1, '2026-08-21', 'pulang', '17:13:20', 'user.jpg', -6.20890000, 106.82275000, 59.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-21 17:13:20'),
(57, 1, 1, '2026-08-22', 'masuk', '08:02:13', 'user.jpg', -6.20870000, 106.82280000, 31.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-22 08:02:13'),
(58, 1, 1, '2026-08-22', 'pulang', '17:10:07', 'user.jpg', -6.20870000, 106.82280000, 48.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-22 17:10:07'),
(59, 2, 1, '2026-08-22', 'masuk', '08:03:13', 'user.jpg', -6.20870000, 106.82260000, 36.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-22 08:03:13'),
(60, 2, 1, '2026-08-22', 'pulang', '17:11:07', 'user.jpg', -6.20870000, 106.82260000, 51.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-22 17:11:07'),
(61, 3, 1, '2026-08-22', 'masuk', '08:04:13', 'user.jpg', -6.88550000, 107.61385000, 41.00, 75, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-22 08:04:13'),
(62, 3, 1, '2026-08-22', 'pulang', '18:49:51', 'user.jpg', -6.88550000, 107.61385000, 54.00, 75, 'lembur', 'Lembur menyelesaikan rekapitulasi data pekerjaan', '2026-08-22 18:49:51'),
(63, 4, 1, '2026-08-22', 'masuk', '08:39:29', 'user.jpg', -6.20870000, 106.82270000, 46.00, 100, 'terlambat', 'Terlambat akibat kendala kemacetan lalu lintas', '2026-08-22 08:39:29'),
(64, 4, 1, '2026-08-22', 'pulang', '17:13:07', 'user.jpg', -6.20870000, 106.82270000, 57.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-22 17:13:07'),
(65, 1, 1, '2026-08-24', 'masuk', '08:02:59', 'user.jpg', -6.20880000, 106.82270000, 29.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-24 08:02:59'),
(66, 1, 1, '2026-08-24', 'pulang', '17:10:41', 'user.jpg', -6.20880000, 106.82270000, 44.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-24 17:10:41'),
(67, 2, 1, '2026-08-24', 'masuk', '08:03:59', 'user.jpg', -6.20880000, 106.82275000, 34.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-24 08:03:59'),
(68, 2, 1, '2026-08-24', 'pulang', '17:11:41', 'user.jpg', -6.20880000, 106.82275000, 47.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-24 17:11:41'),
(69, 3, 1, '2026-08-24', 'masuk', '08:04:59', 'user.jpg', -6.88560000, 107.61400000, 39.00, 75, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-24 08:04:59'),
(70, 3, 1, '2026-08-24', 'pulang', '17:12:41', 'user.jpg', -6.88560000, 107.61400000, 50.00, 75, 'tepat_waktu', 'Absen pulang reguler', '2026-08-24 17:12:41'),
(71, 4, 1, '2026-08-24', 'masuk', '08:05:59', 'user.jpg', -6.20880000, 106.82260000, 44.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-24 08:05:59'),
(72, 4, 1, '2026-08-24', 'pulang', '17:13:41', 'user.jpg', -6.20880000, 106.82260000, 53.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-24 17:13:41'),
(73, 1, 1, '2026-08-26', 'masuk', '08:35:45', 'user.jpg', -6.20890000, 106.82260000, 27.00, 100, 'terlambat', 'Terlambat akibat kendala kemacetan lalu lintas', '2026-08-26 08:35:45'),
(74, 1, 1, '2026-08-26', 'pulang', '17:10:15', 'user.jpg', -6.20890000, 106.82260000, 40.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-26 17:10:15'),
(75, 2, 1, '2026-08-26', 'masuk', '08:03:45', 'user.jpg', -6.20890000, 106.82265000, 32.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-26 08:03:45'),
(76, 2, 1, '2026-08-26', 'pulang', '17:11:15', 'user.jpg', -6.20890000, 106.82265000, 43.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-26 17:11:15'),
(77, 3, 1, '2026-08-26', 'masuk', '08:04:45', 'user.jpg', -6.88570000, 107.61390000, 37.00, 75, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-26 08:04:45'),
(78, 3, 1, '2026-08-26', 'pulang', '17:12:15', 'user.jpg', -6.88570000, 107.61390000, 46.00, 75, 'tepat_waktu', 'Absen pulang reguler', '2026-08-26 17:12:15'),
(79, 4, 1, '2026-08-26', 'masuk', '08:05:45', 'user.jpg', -6.20890000, 106.82275000, 42.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-26 08:05:45'),
(80, 4, 1, '2026-08-26', 'pulang', '18:45:15', 'user.jpg', -6.20890000, 106.82275000, 49.00, 100, 'lembur', 'Lembur menyelesaikan rekapitulasi data pekerjaan', '2026-08-26 18:45:15'),
(81, 1, 1, '2026-08-27', 'masuk', '08:02:38', 'user.jpg', -6.20870000, 106.82280000, 26.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-27 08:02:38'),
(82, 1, 1, '2026-08-27', 'pulang', '17:10:02', 'user.jpg', -6.20870000, 106.82280000, 38.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-27 17:10:02'),
(83, 2, 1, '2026-08-27', 'masuk', '08:03:38', 'user.jpg', -6.20870000, 106.82260000, 31.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-27 08:03:38'),
(84, 2, 1, '2026-08-27', 'pulang', '18:44:06', 'user.jpg', -6.20870000, 106.82260000, 41.00, 100, 'lembur', 'Lembur menyelesaikan rekapitulasi data pekerjaan', '2026-08-27 18:44:06'),
(85, 3, 1, '2026-08-27', 'masuk', '08:34:34', 'user.jpg', -6.88550000, 107.61385000, 36.00, 75, 'terlambat', 'Terlambat akibat kendala kemacetan lalu lintas', '2026-08-27 08:34:34'),
(86, 3, 1, '2026-08-27', 'pulang', '17:12:02', 'user.jpg', -6.88550000, 107.61385000, 44.00, 75, 'tepat_waktu', 'Absen pulang reguler', '2026-08-27 17:12:02'),
(87, 4, 1, '2026-08-27', 'masuk', '08:05:38', 'user.jpg', -6.20870000, 106.82270000, 41.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-27 08:05:38'),
(88, 4, 1, '2026-08-27', 'pulang', '17:13:02', 'user.jpg', -6.20870000, 106.82270000, 47.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-27 17:13:02'),
(89, 1, 1, '2026-08-28', 'masuk', '08:02:31', 'user.jpg', -6.20875000, 106.82275000, 25.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-28 08:02:31'),
(90, 1, 1, '2026-08-28', 'pulang', '17:10:49', 'user.jpg', -6.20875000, 106.82275000, 36.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-28 17:10:49'),
(91, 2, 1, '2026-08-28', 'masuk', '08:03:31', 'user.jpg', -6.20875000, 106.82280000, 30.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-28 08:03:31'),
(92, 2, 1, '2026-08-28', 'pulang', '17:11:49', 'user.jpg', -6.20875000, 106.82280000, 39.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-28 17:11:49'),
(93, 3, 1, '2026-08-28', 'masuk', '08:04:31', 'user.jpg', -6.88555000, 107.61380000, 35.00, 75, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-28 08:04:31'),
(94, 3, 1, '2026-08-28', 'pulang', '17:12:49', 'user.jpg', -6.88555000, 107.61380000, 42.00, 75, 'tepat_waktu', 'Absen pulang reguler', '2026-08-28 17:12:49'),
(95, 4, 1, '2026-08-28', 'masuk', '08:05:31', 'user.jpg', -6.20875000, 106.82265000, 40.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-28 08:05:31'),
(96, 4, 1, '2026-08-28', 'pulang', '17:13:49', 'user.jpg', -6.20875000, 106.82265000, 45.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-28 17:13:49'),
(97, 1, 1, '2026-08-29', 'masuk', '08:02:24', 'user.jpg', -6.20880000, 106.82270000, 24.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-29 08:02:24'),
(98, 1, 1, '2026-08-29', 'pulang', '17:10:36', 'user.jpg', -6.20880000, 106.82270000, 34.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-29 17:10:36'),
(99, 2, 1, '2026-08-29', 'masuk', '08:03:24', 'user.jpg', -6.20880000, 106.82275000, 29.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-29 08:03:24'),
(100, 2, 1, '2026-08-29', 'pulang', '17:11:36', 'user.jpg', -6.20880000, 106.82275000, 37.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-29 17:11:36'),
(101, 3, 1, '2026-08-29', 'masuk', '08:04:24', 'user.jpg', -6.88560000, 107.61400000, 34.00, 75, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-29 08:04:24'),
(102, 3, 1, '2026-08-29', 'pulang', '17:12:36', 'user.jpg', -6.88560000, 107.61400000, 40.00, 75, 'tepat_waktu', 'Absen pulang reguler', '2026-08-29 17:12:36'),
(103, 4, 1, '2026-08-29', 'masuk', '08:05:24', 'user.jpg', -6.20880000, 106.82260000, 39.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-29 08:05:24'),
(104, 4, 1, '2026-08-29', 'pulang', '17:13:36', 'user.jpg', -6.20880000, 106.82260000, 43.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-29 17:13:36'),
(105, 1, 1, '2026-08-31', 'masuk', '08:02:10', 'user.jpg', -6.20890000, 106.82260000, 22.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-31 08:02:10'),
(106, 1, 1, '2026-08-31', 'pulang', '17:10:10', 'user.jpg', -6.20890000, 106.82260000, 30.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-31 17:10:10'),
(107, 2, 1, '2026-08-31', 'masuk', '08:03:10', 'user.jpg', -6.20890000, 106.82265000, 27.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-31 08:03:10'),
(108, 2, 1, '2026-08-31', 'pulang', '17:11:10', 'user.jpg', -6.20890000, 106.82265000, 33.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-31 17:11:10'),
(109, 3, 1, '2026-08-31', 'masuk', '08:04:10', 'user.jpg', -6.88570000, 107.61390000, 32.00, 75, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-31 08:04:10'),
(110, 3, 1, '2026-08-31', 'pulang', '18:40:30', 'user.jpg', -6.88570000, 107.61390000, 36.00, 75, 'lembur', 'Lembur menyelesaikan rekapitulasi data pekerjaan', '2026-08-31 18:40:30'),
(111, 4, 1, '2026-08-31', 'masuk', '08:05:10', 'user.jpg', -6.20890000, 106.82275000, 37.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-08-31 08:05:10'),
(112, 4, 1, '2026-08-31', 'pulang', '17:13:10', 'user.jpg', -6.20890000, 106.82275000, 39.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-08-31 17:13:10'),
(113, 1, 1, '2026-09-01', 'masuk', '08:02:03', 'user.jpg', -6.20870000, 106.82280000, 21.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-09-01 08:02:03'),
(114, 1, 1, '2026-09-01', 'pulang', '18:39:21', 'user.jpg', -6.20870000, 106.82280000, 28.00, 100, 'lembur', 'Lembur menyelesaikan rekapitulasi data pekerjaan', '2026-09-01 18:39:21'),
(115, 2, 1, '2026-09-01', 'masuk', '08:29:39', 'user.jpg', -6.20870000, 106.82260000, 26.00, 100, 'terlambat', 'Terlambat akibat kendala kemacetan lalu lintas', '2026-09-01 08:29:39'),
(116, 2, 1, '2026-09-01', 'pulang', '17:11:57', 'user.jpg', -6.20870000, 106.82260000, 31.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-09-01 17:11:57'),
(117, 3, 1, '2026-09-01', 'masuk', '08:04:03', 'user.jpg', -6.88550000, 107.61385000, 31.00, 75, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-09-01 08:04:03'),
(118, 3, 1, '2026-09-01', 'pulang', '17:12:57', 'user.jpg', -6.88550000, 107.61385000, 34.00, 75, 'tepat_waktu', 'Absen pulang reguler', '2026-09-01 17:12:57'),
(119, 4, 1, '2026-09-01', 'masuk', '08:05:03', 'user.jpg', -6.20870000, 106.82270000, 36.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-09-01 08:05:03'),
(120, 4, 1, '2026-09-01', 'pulang', '17:13:57', 'user.jpg', -6.20870000, 106.82270000, 37.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-09-01 17:13:57'),
(121, 1, 1, '2026-09-02', 'masuk', '08:02:56', 'user.jpg', -6.20875000, 106.82275000, 20.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-09-02 08:02:56'),
(122, 1, 1, '2026-09-02', 'pulang', '17:10:44', 'user.jpg', -6.20875000, 106.82275000, 26.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-09-02 17:10:44'),
(123, 2, 1, '2026-09-02', 'masuk', '08:03:56', 'user.jpg', -6.20875000, 106.82280000, 25.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-09-02 08:03:56'),
(124, 2, 1, '2026-09-02', 'pulang', '17:11:44', 'user.jpg', -6.20875000, 106.82280000, 29.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-09-02 17:11:44'),
(125, 3, 1, '2026-09-02', 'masuk', '08:04:56', 'user.jpg', -6.88555000, 107.61380000, 30.00, 75, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-09-02 08:04:56'),
(126, 3, 1, '2026-09-02', 'pulang', '17:12:44', 'user.jpg', -6.88555000, 107.61380000, 32.00, 75, 'tepat_waktu', 'Absen pulang reguler', '2026-09-02 17:12:44'),
(127, 4, 1, '2026-09-02', 'masuk', '08:28:28', 'user.jpg', -6.20875000, 106.82265000, 35.00, 100, 'terlambat', 'Terlambat akibat kendala kemacetan lalu lintas', '2026-09-02 08:28:28'),
(128, 4, 1, '2026-09-02', 'pulang', '17:13:44', 'user.jpg', -6.20875000, 106.82265000, 35.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-09-02 17:13:44'),
(129, 1, 1, '2026-09-03', 'masuk', '08:02:49', 'user.jpg', -6.20880000, 106.82270000, 19.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-09-03 08:02:49'),
(130, 1, 1, '2026-09-03', 'pulang', '17:10:31', 'user.jpg', -6.20880000, 106.82270000, 24.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-09-03 17:10:31'),
(131, 2, 1, '2026-09-03', 'masuk', '08:03:49', 'user.jpg', -6.20880000, 106.82275000, 24.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-09-03 08:03:49'),
(132, 2, 1, '2026-09-03', 'pulang', '17:11:31', 'user.jpg', -6.20880000, 106.82275000, 27.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-09-03 17:11:31'),
(133, 4, 1, '2026-09-03', 'masuk', '08:05:49', 'user.jpg', -6.20880000, 106.82260000, 34.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-09-03 08:05:49'),
(134, 4, 1, '2026-09-03', 'pulang', '17:13:31', 'user.jpg', -6.20880000, 106.82260000, 33.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-09-03 17:13:31'),
(135, 1, 1, '2026-09-04', 'masuk', '08:02:42', 'user.jpg', -6.20885000, 106.82265000, 18.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-09-04 08:02:42'),
(136, 1, 1, '2026-09-04', 'pulang', '17:10:18', 'user.jpg', -6.20885000, 106.82265000, 22.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-09-04 17:10:18'),
(137, 2, 1, '2026-09-04', 'masuk', '08:03:42', 'user.jpg', -6.20885000, 106.82270000, 23.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-09-04 08:03:42'),
(138, 2, 1, '2026-09-04', 'pulang', '17:11:18', 'user.jpg', -6.20885000, 106.82270000, 25.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-09-04 17:11:18'),
(139, 3, 1, '2026-09-04', 'masuk', '08:04:42', 'user.jpg', -6.88565000, 107.61395000, 28.00, 75, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-09-04 08:04:42'),
(140, 3, 1, '2026-09-04', 'pulang', '17:12:18', 'user.jpg', -6.88565000, 107.61395000, 28.00, 75, 'tepat_waktu', 'Absen pulang reguler', '2026-09-04 17:12:18'),
(141, 4, 1, '2026-09-04', 'masuk', '08:05:42', 'user.jpg', -6.20885000, 106.82280000, 33.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-09-04 08:05:42'),
(142, 4, 1, '2026-09-04', 'pulang', '18:36:54', 'user.jpg', -6.20885000, 106.82280000, 31.00, 100, 'lembur', 'Lembur menyelesaikan rekapitulasi data pekerjaan', '2026-09-04 18:36:54'),
(143, 1, 1, '2026-09-05', 'masuk', '08:02:35', 'user.jpg', -6.20890000, 106.82260000, 17.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-09-05 08:02:35'),
(144, 1, 1, '2026-09-05', 'pulang', '17:10:05', 'user.jpg', -6.20890000, 106.82260000, 20.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-09-05 17:10:05'),
(145, 2, 1, '2026-09-05', 'masuk', '08:03:35', 'user.jpg', -6.20890000, 106.82265000, 22.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-09-05 08:03:35'),
(146, 2, 1, '2026-09-05', 'pulang', '18:35:45', 'user.jpg', -6.20890000, 106.82265000, 23.00, 100, 'lembur', 'Lembur menyelesaikan rekapitulasi data pekerjaan', '2026-09-05 18:35:45'),
(147, 3, 1, '2026-09-05', 'masuk', '08:04:35', 'user.jpg', -6.88570000, 107.61390000, 27.00, 75, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-09-05 08:04:35'),
(148, 3, 1, '2026-09-05', 'pulang', '17:12:05', 'user.jpg', -6.88570000, 107.61390000, 26.00, 75, 'tepat_waktu', 'Absen pulang reguler', '2026-09-05 17:12:05'),
(149, 4, 1, '2026-09-05', 'masuk', '08:05:35', 'user.jpg', -6.20890000, 106.82275000, 32.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-09-05 08:05:35'),
(150, 4, 1, '2026-09-05', 'pulang', '17:13:05', 'user.jpg', -6.20890000, 106.82275000, 29.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-09-05 17:13:05'),
(151, 1, 1, '2026-09-07', 'masuk', '08:02:21', 'user.jpg', -6.20875000, 106.82275000, 15.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-09-07 08:02:21'),
(152, 1, 1, '2026-09-07', 'pulang', '17:10:39', 'user.jpg', -6.20875000, 106.82275000, 16.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-09-07 17:10:39'),
(153, 2, 1, '2026-09-07', 'masuk', '08:03:21', 'user.jpg', -6.20875000, 106.82280000, 20.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-09-07 08:03:21'),
(154, 2, 1, '2026-09-07', 'pulang', '17:11:39', 'user.jpg', -6.20875000, 106.82280000, 19.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-09-07 17:11:39'),
(155, 3, 1, '2026-09-07', 'masuk', '08:23:33', 'user.jpg', -6.88555000, 107.61380000, 25.00, 75, 'terlambat', 'Terlambat akibat kendala kemacetan lalu lintas', '2026-09-07 08:23:33'),
(156, 3, 1, '2026-09-07', 'pulang', '17:12:39', 'user.jpg', -6.88555000, 107.61380000, 22.00, 75, 'tepat_waktu', 'Absen pulang reguler', '2026-09-07 17:12:39'),
(157, 4, 1, '2026-09-07', 'masuk', '08:05:21', 'user.jpg', -6.20875000, 106.82265000, 30.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-09-07 08:05:21'),
(158, 4, 1, '2026-09-07', 'pulang', '17:13:39', 'user.jpg', -6.20875000, 106.82265000, 25.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-09-07 17:13:39'),
(159, 1, 1, '2026-09-08', 'masuk', '08:02:14', 'user.jpg', -6.20880000, 106.82270000, 14.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-09-08 08:02:14'),
(160, 1, 1, '2026-09-08', 'pulang', '17:10:26', 'user.jpg', -6.20880000, 106.82270000, 14.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-09-08 17:10:26'),
(161, 2, 1, '2026-09-08', 'masuk', '08:03:14', 'user.jpg', -6.20880000, 106.82275000, 19.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-09-08 08:03:14'),
(162, 2, 1, '2026-09-08', 'pulang', '17:11:26', 'user.jpg', -6.20880000, 106.82275000, 17.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-09-08 17:11:26'),
(163, 3, 1, '2026-09-08', 'masuk', '08:04:14', 'user.jpg', -6.88560000, 107.61400000, 24.00, 75, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-09-08 08:04:14'),
(164, 3, 1, '2026-09-08', 'pulang', '17:12:26', 'user.jpg', -6.88560000, 107.61400000, 20.00, 75, 'tepat_waktu', 'Absen pulang reguler', '2026-09-08 17:12:26'),
(165, 4, 1, '2026-09-08', 'masuk', '08:05:14', 'user.jpg', -6.20880000, 106.82260000, 29.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-09-08 08:05:14'),
(166, 4, 1, '2026-09-08', 'pulang', '17:13:26', 'user.jpg', -6.20880000, 106.82260000, 23.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-09-08 17:13:26'),
(167, 1, 1, '2026-09-09', 'masuk', '08:02:07', 'user.jpg', -6.20885000, 106.82265000, 13.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-09-09 08:02:07'),
(168, 1, 1, '2026-09-09', 'pulang', '17:10:13', 'user.jpg', -6.20885000, 106.82265000, 12.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-09-09 17:10:13'),
(169, 2, 1, '2026-09-09', 'masuk', '08:03:07', 'user.jpg', -6.20885000, 106.82270000, 18.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-09-09 08:03:07'),
(170, 2, 1, '2026-09-09', 'pulang', '17:11:13', 'user.jpg', -6.20885000, 106.82270000, 15.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-09-09 17:11:13'),
(171, 3, 1, '2026-09-09', 'masuk', '08:04:07', 'user.jpg', -6.88565000, 107.61395000, 23.00, 75, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-09-09 08:04:07'),
(172, 3, 1, '2026-09-09', 'pulang', '18:31:09', 'user.jpg', -6.88565000, 107.61395000, 18.00, 75, 'lembur', 'Lembur menyelesaikan rekapitulasi data pekerjaan', '2026-09-09 18:31:09'),
(173, 4, 1, '2026-09-09', 'masuk', '08:05:07', 'user.jpg', -6.20885000, 106.82280000, 28.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-09-09 08:05:07'),
(174, 4, 1, '2026-09-09', 'pulang', '17:13:13', 'user.jpg', -6.20885000, 106.82280000, 21.00, 100, 'tepat_waktu', 'Absen pulang reguler', '2026-09-09 17:13:13'),
(175, 1, 1, '2026-09-10', 'masuk', '08:02:00', 'user.jpg', -6.20890000, 106.82260000, 12.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-09-10 08:02:00'),
(176, 1, 1, '2026-09-10', 'pulang', '18:30:00', 'user.jpg', -6.20890000, 106.82260000, 10.00, 100, 'lembur', 'Lembur menyelesaikan rekapitulasi data pekerjaan', '2026-09-10 18:30:00'),
(179, 3, 1, '2026-09-10', 'masuk', '08:04:00', 'user.jpg', -6.88570000, 107.61390000, 22.00, 75, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-09-10 08:04:00'),
(180, 4, 1, '2026-09-10', 'masuk', '08:05:00', 'user.jpg', -6.20890000, 106.82275000, 27.00, 100, 'tepat_waktu', 'Hadir tepat waktu di lokasi kantor', '2026-09-10 08:05:00'),
(181, 2, 6, '2026-11-09', 'masuk', '20:38:48', 'selfie_2_20261109_203848_6af1bed850e0f.jpg', -8.64911089, 115.22347934, 44.00, 100, 'terlambat', '', '2026-11-09 12:38:48'),
(182, 2, 6, '2026-11-09', 'pulang', '20:44:06', 'selfie_2_20261109_204406_6af1c016a8ffe.jpg', -8.64911504, 115.22351922, 48.40, 100, 'tepat_waktu', '', '2026-11-09 12:44:06');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `kode_role` varchar(50) NOT NULL,
  `nama_role` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `warna_badge` varchar(30) DEFAULT 'indigo' COMMENT 'purple, blue, amber, emerald, rose, indigo, cyan',
  `izin_akses` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Array JSON hak akses fitur yang diizinkan' CHECK (json_valid(`izin_akses`)),
  `is_system` tinyint(1) DEFAULT 0 COMMENT '1 = Bawaan sistem, 0 = Custom role',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `kode_role`, `nama_role`, `deskripsi`, `warna_badge`, `izin_akses`, `is_system`, `created_at`) VALUES
(1, 'ROLE_SUPER_ADMIN', 'Super Administrator', 'Memiliki akses penuh tanpa batas ke semua modul konfigurasi, master data, RBAC, cuti, dan audit presensi.', 'purple', '[\"presensi_masuk_pulang\",\"riwayat_presensi_pribadi\",\"pengajuan_cuti_karyawan\",\"admin_persetujuan_cuti\",\"admin_titik_kantor\",\"admin_data_karyawan\",\"admin_jadwal_libur\",\"admin_manajemen_role\",\"admin_log_presensi\",\"admin_rekap_ekspor\"]', 1, '2026-09-10 02:38:10'),
(2, 'ROLE_HR_MANAGER', 'HR & People Manager', 'Akses mengelola data karyawan, persetujuan cuti & kuota tahunan, jadwal libur bersama, memantau absensi realtime, dan rekapitulasi.', 'blue', '[\"presensi_masuk_pulang\",\"riwayat_presensi_pribadi\",\"pengajuan_cuti_karyawan\",\"admin_persetujuan_cuti\",\"admin_data_karyawan\",\"admin_jadwal_libur\",\"admin_log_presensi\",\"admin_rekap_ekspor\"]', 0, '2026-09-10 02:38:10'),
(3, 'ROLE_SUPERVISOR', 'Supervisor Operasional', 'Mengawasi log kehadiran tim lapangan, meninjau permohonan cuti tim, dan memantau kedisiplinan kerja.', 'amber', '[\"presensi_masuk_pulang\",\"riwayat_presensi_pribadi\",\"pengajuan_cuti_karyawan\",\"admin_persetujuan_cuti\",\"admin_log_presensi\",\"admin_rekap_ekspor\"]', 0, '2026-09-10 02:38:10'),
(4, 'ROLE_STAFF', 'Staff Karyawan', 'Melakukan presensi masuk/pulang menggunakan selfie GPS, melihat riwayat mandiri, serta mengajukan cuti tahunan dan izin khusus.', 'emerald', '[\"presensi_masuk_pulang\",\"riwayat_presensi_pribadi\",\"pengajuan_cuti_karyawan\"]', 1, '2026-09-10 02:38:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cuti_karyawan`
--
ALTER TABLE `cuti_karyawan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cuti_karyawan` (`karyawan_id`),
  ADD KEY `idx_cuti_approver` (`target_approver_id`),
  ADD KEY `idx_cuti_status` (`status`),
  ADD KEY `idx_cuti_periode` (`tanggal_mulai`,`tanggal_selesai`);

--
-- Indexes for table `jadwal_libur`
--
ALTER TABLE `jadwal_libur`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tanggal_libur` (`tanggal_mulai`,`tanggal_selesai`);

--
-- Indexes for table `jadwal_shift_karyawan`
--
ALTER TABLE `jadwal_shift_karyawan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_karyawan_tanggal` (`karyawan_id`,`tanggal`),
  ADD KEY `idx_jsk_tanggal` (`tanggal`),
  ADD KEY `idx_jsk_karyawan` (`karyawan_id`),
  ADD KEY `fk_jsk_shift` (`shift_id`);

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nip` (`nip`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_karyawan_role` (`role_id`);

--
-- Indexes for table `lokasi_kantor`
--
ALTER TABLE `lokasi_kantor`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_lokasi` (`kode_lokasi`);

--
-- Indexes for table `master_shift`
--
ALTER TABLE `master_shift`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_shift` (`kode_shift`);

--
-- Indexes for table `pengajuan_lainnya`
--
ALTER TABLE `pengajuan_lainnya`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pl_karyawan` (`karyawan_id`),
  ADD KEY `idx_pl_approver` (`target_approver_id`),
  ADD KEY `idx_pl_status` (`status`),
  ADD KEY `idx_pl_tanggal` (`tanggal`);

--
-- Indexes for table `presensi`
--
ALTER TABLE `presensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tanggal` (`tanggal`),
  ADD KEY `idx_karyawan` (`karyawan_id`),
  ADD KEY `fk_presensi_lokasi` (`lokasi_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_role` (`kode_role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cuti_karyawan`
--
ALTER TABLE `cuti_karyawan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jadwal_libur`
--
ALTER TABLE `jadwal_libur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `jadwal_shift_karyawan`
--
ALTER TABLE `jadwal_shift_karyawan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT for table `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lokasi_kantor`
--
ALTER TABLE `lokasi_kantor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `master_shift`
--
ALTER TABLE `master_shift`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pengajuan_lainnya`
--
ALTER TABLE `pengajuan_lainnya`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `presensi`
--
ALTER TABLE `presensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=183;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cuti_karyawan`
--
ALTER TABLE `cuti_karyawan`
  ADD CONSTRAINT `fk_cuti_karyawan` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jadwal_shift_karyawan`
--
ALTER TABLE `jadwal_shift_karyawan`
  ADD CONSTRAINT `fk_jsk_karyawan` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_jsk_shift` FOREIGN KEY (`shift_id`) REFERENCES `master_shift` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD CONSTRAINT `fk_karyawan_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `pengajuan_lainnya`
--
ALTER TABLE `pengajuan_lainnya`
  ADD CONSTRAINT `fk_pl_karyawan` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `presensi`
--
ALTER TABLE `presensi`
  ADD CONSTRAINT `fk_presensi_karyawan` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_presensi_lokasi` FOREIGN KEY (`lokasi_id`) REFERENCES `lokasi_kantor` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
