-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 11 Sep 2025 pada 04.15
-- Versi server: 10.4.17-MariaDB
-- Versi PHP: 7.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vigazafarm_clean`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `kos_jabatan`
--

CREATE TABLE `kos_jabatan` (
  `id_jabatan` int(11) NOT NULL,
  `nama_jabatan` varchar(255) NOT NULL,
  `gaji_pokok` decimal(15,2) DEFAULT 0.00,
  `tunjangan` decimal(15,2) DEFAULT 0.00,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `kos_jabatan`
--

INSERT INTO `kos_jabatan` (`id_jabatan`, `nama_jabatan`, `gaji_pokok`, `tunjangan`, `keterangan`, `created_at`) VALUES
(1, 'Supervisor', '5000000.00', '1000000.00', 'Mengawasi operasional harian', '2025-08-30 14:34:44'),
(2, 'Operator Kandang', '3000000.00', '500000.00', 'Merawat kandang dan ayam', '2025-08-30 14:34:44'),
(3, 'Operator Penetasan', '3500000.00', '700000.00', 'Menangani proses penetasan', '2025-08-30 14:34:44'),
(4, 'Cleaning Service', '2500000.00', '300000.00', 'Membersihkan area peternakan', '2025-08-30 14:34:44'),
(5, 'Manager Farm', '7000000.00', '1500000.00', 'Mengelola seluruh operasional farm', '2025-09-01 04:18:47'),
(6, 'Operator Pakan', '3200000.00', '600000.00', 'Mengelola pemberian pakan dan nutrisi', '2025-09-01 04:18:47'),
(7, 'Quality Control', '4000000.00', '800000.00', 'Mengontrol kualitas produk dan proses', '2025-09-01 04:18:47'),
(8, 'Maintenance', '3800000.00', '700000.00', 'Perawatan mesin dan equipment', '2025-09-01 04:18:47'),
(9, 'Security', '2800000.00', '400000.00', 'Keamanan area peternakan', '2025-09-01 04:18:47'),
(10, 'Administrasi', '3500000.00', '500000.00', 'Mengelola data dan dokumentasi', '2025-09-01 04:18:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kos_kandang`
--

CREATE TABLE `kos_kandang` (
  `id_kandang` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `kapasitas` int(11) NOT NULL DEFAULT 0,
  `kapasitas_terisi` int(11) DEFAULT 0,
  `tipe` enum('penetasan','pembesaran','produksi','karantina') NOT NULL DEFAULT 'produksi',
  `lokasi` varchar(255) DEFAULT NULL,
  `status` enum('aktif','maintenance','kosong') NOT NULL DEFAULT 'aktif',
  `keterangan` text DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `biaya` decimal(15,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `kos_kandang`
--

INSERT INTO `kos_kandang` (`id_kandang`, `nama`, `kapasitas`, `kapasitas_terisi`, `tipe`, `lokasi`, `status`, `keterangan`, `tanggal`, `biaya`, `created_at`, `updated_at`) VALUES
(1, 'H-001', 1000, 850, 'penetasan', 'Zona A', 'aktif', 'Incubator Building A', NULL, '0.00', '2025-09-01 16:30:54', '2025-09-01 16:30:54'),
(2, 'H-002', 1000, 920, 'penetasan', 'Zona A', 'aktif', 'Incubator Building B', NULL, '0.00', '2025-09-01 16:30:54', '2025-09-01 16:30:54'),
(3, 'H-003', 1200, 1050, 'penetasan', 'Zona B', 'aktif', 'Incubator Building C', NULL, '0.00', '2025-09-01 16:30:54', '2025-09-01 16:30:54'),
(4, 'PB-001', 800, 720, 'pembesaran', 'Zona B', 'aktif', 'Brooding House 1', NULL, '0.00', '2025-09-01 16:30:54', '2025-09-01 16:30:54'),
(5, 'PB-002', 800, 680, 'pembesaran', 'Zona B', 'aktif', 'Brooding House 2', NULL, '0.00', '2025-09-01 16:30:54', '2025-09-01 16:30:54'),
(6, 'PB-003', 1000, 850, 'pembesaran', 'Zona C', 'aktif', 'Brooding House 3', NULL, '0.00', '2025-09-01 16:30:54', '2025-09-01 16:30:54'),
(7, 'PB-004', 1000, 780, 'pembesaran', 'Zona C', 'aktif', 'Brooding House 4', NULL, '0.00', '2025-09-01 16:30:54', '2025-09-01 16:30:54'),
(8, 'PR-001', 1500, 1425, 'produksi', 'Zona D', 'aktif', 'Layer House A1', NULL, '0.00', '2025-09-01 16:30:54', '2025-09-01 16:30:54'),
(9, 'PR-002', 1500, 1380, 'produksi', 'Zona D', 'aktif', 'Layer House A2', NULL, '0.00', '2025-09-01 16:30:54', '2025-09-01 16:30:54'),
(10, 'PR-003', 1800, 1650, 'produksi', 'Zona E', 'aktif', 'Layer House B1', NULL, '0.00', '2025-09-01 16:30:54', '2025-09-01 16:30:54'),
(11, 'PR-004', 1800, 1710, 'produksi', 'Zona E', 'aktif', 'Layer House B2', NULL, '0.00', '2025-09-01 16:30:54', '2025-09-01 16:30:54'),
(12, 'PR-005', 2000, 1800, 'produksi', 'Zona F', 'aktif', 'Layer House C1', NULL, '0.00', '2025-09-01 16:30:54', '2025-09-01 16:30:54'),
(13, 'PR-006', 2000, 1850, 'produksi', 'Zona F', 'aktif', 'Layer House C2', NULL, '0.00', '2025-09-01 16:30:54', '2025-09-01 16:30:54'),
(14, 'PR-007', 1200, 980, 'produksi', 'Zona G', 'aktif', 'Layer House D1', NULL, '0.00', '2025-09-01 16:30:54', '2025-09-01 16:30:54'),
(15, 'PR-008', 1200, 1100, 'produksi', 'Zona G', 'aktif', 'Layer House D2', NULL, '0.00', '2025-09-01 16:30:54', '2025-09-01 16:30:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kos_karyawan`
--

CREATE TABLE `kos_karyawan` (
  `id_karyawan` int(11) NOT NULL,
  `nip` varchar(50) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `id_jabatan` int(11) DEFAULT NULL,
  `jabatan` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('L','P') DEFAULT NULL,
  `tanggal_masuk` date DEFAULT NULL,
  `tanggal_keluar` date DEFAULT NULL,
  `gaji_pokok` decimal(15,2) DEFAULT 0.00,
  `tunjangan` decimal(15,2) DEFAULT 0.00,
  `total_gaji` decimal(15,2) DEFAULT 0.00,
  `status` enum('aktif','non_aktif','resign') NOT NULL DEFAULT 'aktif',
  `foto` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kos_mesin`
--

CREATE TABLE `kos_mesin` (
  `id_mesin` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `tipe` varchar(100) DEFAULT NULL,
  `kapasitas` int(11) NOT NULL DEFAULT 0,
  `status` enum('aktif','maintenance','rusak') NOT NULL DEFAULT 'aktif',
  `keterangan` text DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `kos_mesin`
--

INSERT INTO `kos_mesin` (`id_mesin`, `nama`, `tipe`, `kapasitas`, `status`, `keterangan`, `tanggal`, `created_at`) VALUES
(1, 'Mesin Penetasan A1', 'Automatic', 1000, 'aktif', NULL, '2024-01-01', '2025-08-30 14:34:43'),
(2, 'Mesin Penetasan B1', 'Semi-Automatic', 500, 'aktif', NULL, '2024-01-01', '2025-08-30 14:34:43'),
(3, 'Mesin Penetasan C1', 'Manual', 300, 'maintenance', NULL, '2024-01-01', '2025-08-30 14:34:43'),
(4, 'Mesin Penetasan A2', 'Automatic', 1000, 'aktif', 'Mesin penetasan backup', '2024-01-01', '2025-09-01 04:18:47'),
(5, 'Mesin Penetasan D1', 'Semi-Automatic', 800, 'aktif', 'Mesin penetasan untuk batch kecil', '2024-01-01', '2025-09-01 04:18:47'),
(6, 'Mesin Pakan Otomatis F1', 'Feeding System', 2000, 'aktif', 'Sistem pemberian pakan otomatis', '2024-01-01', '2025-09-01 04:18:47'),
(7, 'Generator Backup G1', 'Power Generator', 500, 'aktif', 'Generator cadangan untuk emergency', '2024-01-01', '2025-09-01 04:18:47'),
(8, 'Mesin Cleaning H1', 'Cleaning System', 100, 'maintenance', 'Sistem pembersihan otomatis', '2024-01-01', '2025-09-01 04:18:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kos_notifikasi`
--

CREATE TABLE `kos_notifikasi` (
  `id_notifikasi` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','warning','success','error') DEFAULT 'info',
  `target_user` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `kos_notifikasi`
--

INSERT INTO `kos_notifikasi` (`id_notifikasi`, `title`, `message`, `type`, `target_user`, `is_read`, `created_at`) VALUES
(1, 'Penetasan Selesai', 'Batch PEN202501001 telah selesai dengan hasil 750 DOC', 'success', 1, 0, '2025-08-30 14:34:44'),
(2, 'Monitoring Harian', 'Reminder: Input data monitoring harian pembesaran', 'warning', 1, 0, '2025-08-30 14:34:44');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kos_payment`
--

CREATE TABLE `kos_payment` (
  `id_payment` int(11) NOT NULL,
  `reference_id` varchar(100) DEFAULT NULL,
  `reference_type` enum('gaji','pembelian','penjualan','maintenance') DEFAULT 'gaji',
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `payment_date` date NOT NULL,
  `payment_method` enum('cash','transfer','check') DEFAULT 'cash',
  `status` enum('pending','paid','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kos_pembesaran`
--

CREATE TABLE `kos_pembesaran` (
  `id_pembesaran` int(11) NOT NULL,
  `periode` varchar(50) DEFAULT NULL,
  `batch_penetasan` varchar(50) DEFAULT NULL,
  `id_kandang` int(11) DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `target_hari` int(11) DEFAULT 35,
  `jumlah_bibit` int(11) DEFAULT 0,
  `jumlah_hidup` int(11) DEFAULT 0,
  `jumlah_mati` int(11) DEFAULT 0,
  `berat_rata` decimal(6,2) DEFAULT 0.00,
  `konsumsi_pakan` decimal(8,2) DEFAULT 0.00,
  `biaya_pakan` decimal(15,2) DEFAULT 0.00,
  `biaya_obat` decimal(15,2) DEFAULT 0.00,
  `biaya_lain` decimal(15,2) DEFAULT 0.00,
  `total_biaya` decimal(15,2) DEFAULT 0.00,
  `status` enum('persiapan','aktif','selesai','gagal') NOT NULL DEFAULT 'persiapan',
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `kos_pembesaran`
--

INSERT INTO `kos_pembesaran` (`id_pembesaran`, `periode`, `batch_penetasan`, `id_kandang`, `tanggal_mulai`, `tanggal_selesai`, `tanggal`, `target_hari`, `jumlah_bibit`, `jumlah_hidup`, `jumlah_mati`, `berat_rata`, `konsumsi_pakan`, `biaya_pakan`, `biaya_obat`, `biaya_lain`, `total_biaya`, `status`, `catatan`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'Juli-2025-P1', 'BATCH-2025-07-001', 1, '2025-07-22', '2025-09-01', '2025-09-01', 35, 3000, 2850, 150, '1.80', '7500.00', '37500000.00', '250000.00', '150000.00', '37900000.00', 'selesai', 'Kandang A1 - batch Juli 1', '2025-09-01 16:33:35', '2025-09-01 16:33:35', NULL, NULL),
(2, 'Juli-2025-P1', 'BATCH-2025-07-001', 2, '2025-07-22', '2025-09-01', '2025-09-01', 35, 3100, 2945, 155, '1.85', '7750.00', '38750000.00', '260000.00', '160000.00', '39170000.00', 'selesai', 'Kandang A2 - batch Juli 1', '2025-09-01 16:33:35', '2025-09-01 16:33:35', NULL, NULL),
(3, 'Juli-2025-P1', 'BATCH-2025-07-001', 3, '2025-07-22', '2025-09-01', '2025-09-01', 35, 3100, 2975, 125, '1.90', '7650.00', '38250000.00', '240000.00', '140000.00', '38630000.00', 'selesai', 'Kandang A3 - batch Juli 1', '2025-09-01 16:33:35', '2025-09-01 16:33:35', NULL, NULL),
(4, 'Juli-2025-P2', 'BATCH-2025-07-002', 4, '2025-07-29', '2025-09-08', '2025-09-08', 35, 3600, 3420, 180, '1.88', '9000.00', '45000000.00', '300000.00', '200000.00', '45500000.00', 'selesai', 'Kandang B1 - batch Juli 2', '2025-09-01 16:33:35', '2025-09-01 16:33:35', NULL, NULL),
(5, 'Juli-2025-P2', 'BATCH-2025-07-002', 5, '2025-07-29', '2025-09-08', '2025-09-08', 35, 3600, 3456, 144, '1.92', '8950.00', '44750000.00', '290000.00', '190000.00', '45230000.00', 'selesai', 'Kandang B2 - batch Juli 2', '2025-09-01 16:33:35', '2025-09-01 16:33:35', NULL, NULL),
(6, 'Juli-2025-P2', 'BATCH-2025-07-002', 6, '2025-07-29', '2025-09-08', '2025-09-08', 35, 3600, 3420, 180, '1.85', '9100.00', '45500000.00', '310000.00', '210000.00', '46020000.00', 'selesai', 'Kandang B3 - batch Juli 2', '2025-09-01 16:33:35', '2025-09-01 16:33:35', NULL, NULL),
(7, 'Agustus-2025-P1', 'BATCH-2025-08-001', 7, '2025-08-22', NULL, '2025-09-05', 35, 3800, 3648, 152, '1.25', '4560.00', '22800000.00', '120000.00', '80000.00', '23000000.00', 'aktif', 'Kandang C1 - batch Agustus 1 - minggu ke-2', '2025-09-01 16:33:35', '2025-09-01 16:33:35', NULL, NULL),
(8, 'Agustus-2025-P1', 'BATCH-2025-08-001', 8, '2025-08-22', NULL, '2025-09-05', 35, 3850, 3696, 154, '1.28', '4620.00', '23100000.00', '125000.00', '85000.00', '23310000.00', 'aktif', 'Kandang C2 - batch Agustus 1 - minggu ke-2', '2025-09-01 16:33:35', '2025-09-01 16:33:35', NULL, NULL),
(9, 'Agustus-2025-P1', 'BATCH-2025-08-001', 9, '2025-08-22', NULL, '2025-09-05', 35, 3850, 3735, 115, '1.30', '4750.00', '23750000.00', '118000.00', '82000.00', '23950000.00', 'aktif', 'Kandang C3 - batch Agustus 1 - minggu ke-2', '2025-09-01 16:33:35', '2025-09-01 16:33:35', NULL, NULL),
(10, 'Agustus-2025-P2', 'BATCH-2025-08-002', 10, '2025-08-29', NULL, '2025-09-05', 35, 3540, 3363, 177, '1.05', '3540.00', '17700000.00', '95000.00', '65000.00', '17860000.00', 'aktif', 'Kandang D1 - batch Agustus 2 - minggu ke-1', '2025-09-01 16:33:35', '2025-09-01 16:33:35', NULL, NULL),
(11, 'Agustus-2025-P2', 'BATCH-2025-08-002', 11, '2025-08-29', NULL, '2025-09-05', 35, 3540, 3416, 124, '1.08', '3650.00', '18250000.00', '88000.00', '60000.00', '18398000.00', 'aktif', 'Kandang D2 - batch Agustus 2 - minggu ke-1', '2025-09-01 16:33:35', '2025-09-01 16:33:35', NULL, NULL),
(12, 'Agustus-2025-P2', 'BATCH-2025-08-002', 12, '2025-08-29', NULL, '2025-09-05', 35, 3540, 3398, 142, '1.02', '3590.00', '17950000.00', '92000.00', '62000.00', '18104000.00', 'aktif', 'Kandang D3 - batch Agustus 2 - minggu ke-1', '2025-09-01 16:33:35', '2025-09-01 16:33:35', NULL, NULL),
(13, 'Agustus-2025-P3', 'BATCH-2025-08-003', 13, '2025-09-05', NULL, '2025-09-05', 35, 3900, 3861, 39, '0.85', '1950.00', '9750000.00', '45000.00', '30000.00', '9825000.00', 'aktif', 'Kandang E1 - batch Agustus 3 - hari ke-1', '2025-09-01 16:33:35', '2025-09-01 16:33:35', NULL, NULL),
(14, 'Agustus-2025-P3', 'BATCH-2025-08-003', 14, '2025-09-05', NULL, '2025-09-05', 35, 3900, 3873, 27, '0.88', '2000.00', '10000000.00', '42000.00', '28000.00', '10070000.00', 'aktif', 'Kandang E2 - batch Agustus 3 - hari ke-1', '2025-09-01 16:33:35', '2025-09-01 16:33:35', NULL, NULL),
(15, 'Agustus-2025-P3', 'BATCH-2025-08-003', 15, '2025-09-05', NULL, '2025-09-05', 35, 3900, 3885, 15, '0.90', '1980.00', '9900000.00', '38000.00', '25000.00', '9963000.00', 'aktif', 'Kandang E3 - batch Agustus 3 - hari ke-1', '2025-09-01 16:33:35', '2025-09-01 16:33:35', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kos_penetasan`
--

CREATE TABLE `kos_penetasan` (
  `id_penetasan` int(11) NOT NULL,
  `batch` varchar(50) NOT NULL,
  `id_mesin` int(11) DEFAULT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `lama_penetasan` int(11) DEFAULT 21,
  `jumlah_telur` int(11) NOT NULL,
  `hasil_menetas` int(11) DEFAULT 0,
  `hasil_gagal` int(11) DEFAULT 0,
  `persentase_menetas` decimal(5,2) DEFAULT 0.00,
  `suhu_rata` decimal(4,1) DEFAULT 37.5,
  `kelembaban_rata` decimal(4,1) DEFAULT 60.0,
  `status` enum('persiapan','proses','selesai','gagal') NOT NULL DEFAULT 'persiapan',
  `catatan` text DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `kos_penetasan`
--

INSERT INTO `kos_penetasan` (`id_penetasan`, `batch`, `id_mesin`, `tanggal_mulai`, `tanggal_selesai`, `lama_penetasan`, `jumlah_telur`, `hasil_menetas`, `hasil_gagal`, `persentase_menetas`, `suhu_rata`, `kelembaban_rata`, `status`, `catatan`, `tanggal`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'BATCH-2025-07-001', 1, '2025-07-01', '2025-07-22', 21, 10000, 9200, 800, '92.00', '37.5', '60.0', 'selesai', 'Batch pertama Juli - hasil bagus', NULL, '2025-09-01 16:31:41', '2025-09-02 07:12:07', NULL, NULL),
(2, 'BATCH-2025-07-002', NULL, '2025-07-08', '2025-07-29', 21, 12000, 10800, 1200, '90.00', '37.4', '61.0', 'selesai', 'Batch kedua Juli', NULL, '2025-09-01 16:31:41', '2025-09-01 16:31:41', NULL, NULL),
(3, 'BATCH-2025-07-003', NULL, '2025-07-15', '2025-08-05', 21, 11000, 9900, 1100, '90.00', '37.6', '59.5', 'selesai', 'Batch ketiga Juli', NULL, '2025-09-01 16:31:41', '2025-09-01 16:31:41', NULL, NULL),
(4, 'BATCH-2025-07-004', NULL, '2025-07-22', '2025-08-12', 21, 10500, 9450, 1050, '90.00', '37.5', '60.2', 'selesai', 'Batch keempat Juli', NULL, '2025-09-01 16:31:41', '2025-09-01 16:31:41', NULL, NULL),
(5, 'BATCH-2025-08-001', NULL, '2025-08-01', '2025-08-22', 21, 12500, 11500, 1000, '92.00', '37.3', '60.5', 'selesai', 'Batch pertama Agustus - excellent', NULL, '2025-09-01 16:31:41', '2025-09-01 16:31:41', NULL, NULL),
(6, 'BATCH-2025-08-002', NULL, '2025-08-08', '2025-08-29', 21, 11800, 10620, 1180, '90.00', '37.7', '59.8', 'selesai', 'Batch kedua Agustus', NULL, '2025-09-01 16:31:41', '2025-09-01 16:31:41', NULL, NULL),
(7, 'BATCH-2025-08-003', NULL, '2025-08-15', '2025-09-05', 21, 13000, 11700, 1300, '90.00', '37.4', '60.3', 'selesai', 'Batch ketiga Agustus', NULL, '2025-09-01 16:31:41', '2025-09-01 16:31:41', NULL, NULL),
(8, 'BATCH-2025-08-004', NULL, '2025-08-22', '2025-09-12', 21, 12200, 11000, 1200, '90.16', '37.6', '60.1', 'selesai', 'Batch keempat Agustus', NULL, '2025-09-01 16:31:41', '2025-09-01 16:31:41', NULL, NULL),
(9, 'BATCH-2025-09-001', NULL, '2025-09-01', NULL, 21, 13500, 0, 0, '0.00', '37.5', '60.0', 'proses', 'Batch pertama September - dalam proses hari ke-1', NULL, '2025-09-01 16:31:41', '2025-09-01 16:31:41', NULL, NULL),
(10, 'BATCH-2025-09-002', NULL, '2025-09-08', NULL, 21, 12800, 0, 0, '0.00', '37.5', '60.0', 'persiapan', 'Batch kedua September - persiapan', NULL, '2025-09-01 16:31:41', '2025-09-01 16:31:41', NULL, NULL),
(11, 'BATCH-2025-09-02-001', 2, '2025-09-02', '2025-09-27', 25, 50, 0, 0, '0.00', '37.5', '60.0', 'proses', '', '2025-09-02', '2025-09-02 08:14:23', '2025-09-02 08:14:39', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kos_penghuni`
--

CREATE TABLE `kos_penghuni` (
  `id_penghuni` int(11) NOT NULL,
  `id_kandang` int(11) NOT NULL,
  `nama_penghuni` varchar(255) DEFAULT NULL,
  `jenis_penghuni` enum('telur','doc','ayam_muda','ayam_dewasa') DEFAULT 'ayam_dewasa',
  `jumlah` int(11) DEFAULT 1,
  `tanggal_masuk` date NOT NULL,
  `tanggal_keluar` date DEFAULT NULL,
  `status` enum('aktif','keluar') DEFAULT 'aktif',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kos_produksi`
--

CREATE TABLE `kos_produksi` (
  `id_produksi` int(11) NOT NULL,
  `batch_penetasan` varchar(50) DEFAULT NULL,
  `batch_pembesaran` varchar(50) DEFAULT NULL,
  `id_kandang` int(11) DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `tanggal_mulai_produksi` date DEFAULT NULL,
  `jenis_produksi` enum('telur','daging','ayam_hidup') DEFAULT 'telur',
  `jumlah_ayam_awal` int(11) DEFAULT 0,
  `jumlah_ayam_saat_ini` int(11) DEFAULT 0,
  `jumlah` int(11) DEFAULT 0,
  `berat` decimal(6,2) DEFAULT 0.00,
  `harga_satuan` decimal(15,2) DEFAULT 0.00,
  `total_nilai` decimal(15,2) DEFAULT 0.00,
  `total_telur_produksi` int(11) DEFAULT 0,
  `total_pakan_konsumsi` decimal(10,2) DEFAULT 0.00,
  `total_kematian` int(11) DEFAULT 0,
  `fase_produksi` enum('awal','puncak','akhir') DEFAULT 'awal',
  `kualitas` enum('A','B','C','Reject') DEFAULT 'A',
  `status` enum('persiapan','aktif','selesai','gagal') NOT NULL DEFAULT 'persiapan',
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `kos_produksi`
--

INSERT INTO `kos_produksi` (`id_produksi`, `batch_penetasan`, `batch_pembesaran`, `id_kandang`, `tanggal_mulai`, `tanggal_selesai`, `tanggal`, `tanggal_mulai_produksi`, `jenis_produksi`, `jumlah_ayam_awal`, `jumlah_ayam_saat_ini`, `jumlah`, `berat`, `harga_satuan`, `total_nilai`, `total_telur_produksi`, `total_pakan_konsumsi`, `total_kematian`, `fase_produksi`, `kualitas`, `status`, `catatan`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'BATCH-2025-07-001', 'Juli-2025-P1', 1, '2025-07-22', '2025-12-15', '2025-12-15', '2025-09-15', 'telur', 2850, 2620, 45000, '0.00', '2500.00', '112500000.00', 45000, '12000.00', 230, 'akhir', 'A', 'selesai', 'Produksi telur A1 - 4 bulan excellent', '2025-09-01 16:34:24', '2025-09-01 16:34:24', NULL, NULL),
(2, 'BATCH-2025-07-001', 'Juli-2025-P1', 2, '2025-07-22', '2025-12-15', '2025-12-15', '2025-09-15', 'telur', 2945, 2715, 47500, '0.00', '2500.00', '118750000.00', 47500, '12300.00', 230, 'akhir', 'A', 'selesai', 'Produksi telur A2 - 4 bulan excellent', '2025-09-01 16:34:24', '2025-09-01 16:34:24', NULL, NULL),
(3, 'BATCH-2025-07-001', 'Juli-2025-P1', 3, '2025-07-22', '2025-12-15', '2025-12-15', '2025-09-15', 'telur', 2975, 2750, 48200, '0.00', '2500.00', '120500000.00', 48200, '12150.00', 225, 'akhir', 'A', 'selesai', 'Produksi telur A3 - 4 bulan excellent', '2025-09-01 16:34:24', '2025-09-01 16:34:24', NULL, NULL),
(4, 'BATCH-2025-07-002', 'Juli-2025-P2', 4, '2025-07-29', '2025-11-15', '2025-11-15', '2025-10-08', 'ayam_hidup', 3420, 0, 3200, '2.20', '35000.00', '112000000.00', 0, '15500.00', 220, 'akhir', 'A', 'selesai', 'Ayam hidup B1 - 2.2kg rata-rata', '2025-09-01 16:34:24', '2025-09-01 16:34:24', NULL, NULL),
(5, 'BATCH-2025-07-002', 'Juli-2025-P2', 5, '2025-07-29', '2025-11-15', '2025-11-15', '2025-10-08', 'ayam_hidup', 3456, 0, 3240, '2.25', '35000.00', '113400000.00', 0, '15650.00', 216, 'akhir', 'A', 'selesai', 'Ayam hidup B2 - 2.25kg rata-rata', '2025-09-01 16:34:24', '2025-09-01 16:34:24', NULL, NULL),
(6, 'BATCH-2025-07-002', 'Juli-2025-P2', 6, '2025-07-29', '2025-11-15', '2025-11-15', '2025-10-08', 'ayam_hidup', 3420, 0, 3190, '2.18', '34000.00', '108460000.00', 0, '15800.00', 230, 'akhir', 'A', 'selesai', 'Ayam hidup B3 - 2.18kg rata-rata', '2025-09-01 16:34:24', '2025-09-01 16:34:24', NULL, NULL),
(7, 'BATCH-2025-08-001', 'Agustus-2025-P1', 7, '2025-08-22', NULL, '2025-09-05', '2025-10-01', 'telur', 3648, 3580, 8500, '0.00', '2500.00', '21250000.00', 8500, '3200.00', 68, 'awal', 'A', 'aktif', 'Produksi telur C1 - baru mulai fase awal', '2025-09-01 16:34:24', '2025-09-01 16:34:24', NULL, NULL),
(8, 'BATCH-2025-08-001', 'Agustus-2025-P1', 8, '2025-08-22', NULL, '2025-09-05', '2025-10-01', 'telur', 3696, 3620, 8800, '0.00', '2500.00', '22000000.00', 8800, '3250.00', 76, 'awal', 'A', 'aktif', 'Produksi telur C2 - baru mulai fase awal', '2025-09-01 16:34:24', '2025-09-01 16:34:24', NULL, NULL),
(9, 'BATCH-2025-08-001', 'Agustus-2025-P1', 9, '2025-08-22', NULL, '2025-09-05', '2025-10-01', 'telur', 3735, 3650, 9100, '0.00', '2500.00', '22750000.00', 9100, '3180.00', 85, 'awal', 'A', 'aktif', 'Produksi telur C3 - baru mulai fase awal', '2025-09-01 16:34:24', '2025-09-01 16:34:24', NULL, NULL),
(10, 'BATCH-2025-08-002', 'Agustus-2025-P2', 10, '2025-08-29', NULL, '2025-09-05', NULL, 'ayam_hidup', 3363, 3310, 0, '1.05', '0.00', '0.00', 0, '1800.00', 53, 'awal', 'A', 'persiapan', 'Masih pembesaran - target 2.5kg', '2025-09-01 16:34:24', '2025-09-01 16:34:24', NULL, NULL),
(11, 'BATCH-2025-08-002', 'Agustus-2025-P2', 11, '2025-08-29', NULL, '2025-09-05', NULL, 'ayam_hidup', 3416, 3370, 0, '1.08', '0.00', '0.00', 0, '1850.00', 46, 'awal', 'A', 'persiapan', 'Masih pembesaran - target 2.5kg', '2025-09-01 16:34:24', '2025-09-01 16:34:24', NULL, NULL),
(12, 'BATCH-2025-08-002', 'Agustus-2025-P2', 12, '2025-08-29', NULL, '2025-09-05', NULL, 'ayam_hidup', 3398, 3360, 0, '1.02', '0.00', '0.00', 0, '1780.00', 38, 'awal', 'A', 'persiapan', 'Masih pembesaran - target 2.5kg', '2025-09-01 16:34:24', '2025-09-01 16:34:24', NULL, NULL),
(13, 'BATCH-2025-08-003', 'Agustus-2025-P3', 13, '2025-09-05', NULL, '2025-09-05', NULL, 'telur', 3861, 3850, 0, '0.85', '0.00', '0.00', 0, '980.00', 11, 'awal', 'A', 'persiapan', 'Baru DOC - target produksi telur', '2025-09-01 16:34:24', '2025-09-01 16:34:24', NULL, NULL),
(14, 'BATCH-2025-08-003', 'Agustus-2025-P3', 14, '2025-09-05', NULL, '2025-09-05', NULL, 'telur', 3873, 3865, 0, '0.88', '0.00', '0.00', 0, '1000.00', 8, 'awal', 'A', 'persiapan', 'Baru DOC - target produksi telur', '2025-09-01 16:34:24', '2025-09-01 16:34:24', NULL, NULL),
(15, 'BATCH-2025-08-003', 'Agustus-2025-P3', 15, '2025-09-05', NULL, '2025-09-05', NULL, 'telur', 3885, 3880, 0, '0.90', '0.00', '0.00', 0, '990.00', 5, 'awal', 'A', 'persiapan', 'Baru DOC - target produksi telur', '2025-09-01 16:34:24', '2025-09-01 16:34:24', NULL, NULL),
(16, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, '0.00', '0.00', '0.00', 0, '0.00', 0, 'awal', 'A', 'persiapan', NULL, '2025-09-02 06:25:06', '2025-09-02 06:25:06', NULL, NULL),
(17, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, '0.00', '0.00', '0.00', 0, '0.00', 0, 'awal', 'A', 'persiapan', NULL, '2025-09-02 06:25:10', '2025-09-02 06:25:10', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembesaran_monitoring`
--

CREATE TABLE `pembesaran_monitoring` (
  `id_monitoring` int(11) NOT NULL,
  `id_pembesaran` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `umur_hari` int(11) NOT NULL,
  `jumlah_ayam` int(11) NOT NULL,
  `berat_rata` decimal(6,2) DEFAULT 0.00,
  `konsumsi_pakan` decimal(8,2) DEFAULT 0.00,
  `jumlah_mati` int(11) DEFAULT 0,
  `penyebab_mati` varchar(255) DEFAULT NULL,
  `suhu_kandang` decimal(4,1) DEFAULT NULL,
  `kelembaban` decimal(4,1) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `pembesaran_monitoring`
--

INSERT INTO `pembesaran_monitoring` (`id_monitoring`, `id_pembesaran`, `tanggal`, `umur_hari`, `jumlah_ayam`, `berat_rata`, `konsumsi_pakan`, `jumlah_mati`, `penyebab_mati`, `suhu_kandang`, `kelembaban`, `catatan`, `created_at`, `created_by`) VALUES
(1, 1, '2025-01-23', 1, 750, '0.05', '2.50', 5, NULL, NULL, NULL, NULL, '2025-08-30 14:34:44', 1),
(2, 1, '2025-01-24', 2, 745, '0.08', '3.00', 3, NULL, NULL, NULL, NULL, '2025-08-30 14:34:44', 1),
(3, 1, '2025-01-25', 3, 742, '0.12', '3.80', 2, NULL, NULL, NULL, NULL, '2025-08-30 14:34:44', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `produksi_harian`
--

CREATE TABLE `produksi_harian` (
  `id_produksi_harian` int(11) NOT NULL,
  `id_produksi` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jumlah_telur` int(11) DEFAULT 0,
  `berat_telur` decimal(6,2) DEFAULT 0.00,
  `konsumsi_pakan` decimal(8,2) DEFAULT 0.00,
  `jumlah_mati` int(11) DEFAULT 0,
  `penyebab_mati` varchar(255) DEFAULT NULL,
  `cuaca` varchar(100) DEFAULT NULL,
  `suhu_kandang` decimal(4,1) DEFAULT NULL,
  `kelembaban` decimal(4,1) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `produksi_harian`
--

INSERT INTO `produksi_harian` (`id_produksi_harian`, `id_produksi`, `tanggal`, `jumlah_telur`, `berat_telur`, `konsumsi_pakan`, `jumlah_mati`, `penyebab_mati`, `cuaca`, `suhu_kandang`, `kelembaban`, `catatan`, `created_at`, `created_by`) VALUES
(1, 1, '2025-03-01', 450, '0.00', '25.50', 1, NULL, NULL, NULL, NULL, NULL, '2025-08-30 14:34:44', 1),
(2, 1, '2025-03-02', 475, '0.00', '26.00', 0, NULL, NULL, NULL, NULL, NULL, '2025-08-30 14:34:44', 1),
(3, 1, '2025-03-03', 480, '0.00', '25.80', 2, NULL, NULL, NULL, NULL, NULL, '2025-08-30 14:34:44', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `simimin`
--

CREATE TABLE `simimin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `level` varchar(50) DEFAULT 'mimin',
  `role` varchar(50) DEFAULT 'admin',
  `nama_lengkap` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `status` enum('aktif','non_aktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `simimin`
--

INSERT INTO `simimin` (`id`, `username`, `password`, `level`, `role`, `nama_lengkap`, `email`, `phone`, `status`, `created_at`) VALUES
(1, 'admin', '$2y$10$1n6BRy1aREngchNUDA7PNOO5HkbMqVnH0DChHYuUo/MyVUxvTLup2', 'mimin', 'admin', 'Administrator', NULL, NULL, 'aktif', '2025-08-30 14:34:43'),
(2, 'vigaza', '$2y$10$lwgrPu/Wwq2G121FIuWHXuP.REfWKebMF1eRoZM0BVCrlyiHuUpAq', 'super_admin', 'super_admin', 'Super Administrator Vigaza', NULL, NULL, 'aktif', '2025-08-30 14:34:43');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('admin','manager','operator') NOT NULL DEFAULT 'operator',
  `status` enum('aktif','non_aktif') NOT NULL DEFAULT 'aktif',
  `avatar` varchar(255) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `phone`, `role`, `status`, `avatar`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', NULL, NULL, 'admin', 'aktif', NULL, NULL, '2025-08-30 14:34:43', '2025-08-30 14:34:43'),
(2, 'manager', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Manager Farm', NULL, NULL, 'manager', 'aktif', NULL, NULL, '2025-08-30 14:34:43', '2025-08-30 14:34:43'),
(3, 'operator', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Operator', NULL, NULL, 'operator', 'aktif', NULL, NULL, '2025-08-30 14:34:43', '2025-08-30 14:34:43');

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `v_kandang`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_kandang` (
`id_kandang` int(11)
,`nama` varchar(255)
,`kapasitas` int(11)
,`kapasitas_terisi` int(11)
,`tipe` enum('penetasan','pembesaran','produksi','karantina')
,`lokasi` varchar(255)
,`status` enum('aktif','maintenance','kosong')
,`keterangan` text
,`tanggal` date
,`biaya` decimal(15,2)
,`created_at` timestamp
,`updated_at` timestamp
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `v_karyawan`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_karyawan` (
`id_karyawan` int(11)
,`nip` varchar(50)
,`nama` varchar(255)
,`nama_lengkap` varchar(255)
,`id_jabatan` int(11)
,`jabatan` varchar(255)
,`email` varchar(255)
,`phone` varchar(20)
,`alamat` text
,`tanggal_lahir` date
,`jenis_kelamin` enum('L','P')
,`tanggal_masuk` date
,`tanggal_keluar` date
,`gaji_pokok` decimal(15,2)
,`tunjangan` decimal(15,2)
,`total_gaji` decimal(15,2)
,`status` enum('aktif','non_aktif','resign')
,`foto` varchar(255)
,`keterangan` text
,`created_at` timestamp
,`updated_at` timestamp
,`nama_jabatan` varchar(255)
,`gaji_pokok_jabatan` decimal(15,2)
,`tunjangan_jabatan` decimal(15,2)
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `v_mesin`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_mesin` (
`id_mesin` int(11)
,`nama` varchar(255)
,`tipe` varchar(100)
,`kapasitas` int(11)
,`status` enum('aktif','maintenance','rusak')
,`keterangan` text
,`tanggal` date
,`created_at` timestamp
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `v_pembesaran`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_pembesaran` (
`id_pembesaran` int(11)
,`periode` varchar(50)
,`batch_penetasan` varchar(50)
,`id_kandang` int(11)
,`tanggal_mulai` date
,`tanggal_selesai` date
,`tanggal` date
,`target_hari` int(11)
,`jumlah_bibit` int(11)
,`jumlah_hidup` int(11)
,`jumlah_mati` int(11)
,`berat_rata` decimal(6,2)
,`konsumsi_pakan` decimal(8,2)
,`biaya_pakan` decimal(15,2)
,`biaya_obat` decimal(15,2)
,`biaya_lain` decimal(15,2)
,`total_biaya` decimal(15,2)
,`status` enum('persiapan','aktif','selesai','gagal')
,`catatan` text
,`created_at` timestamp
,`updated_at` timestamp
,`created_by` int(11)
,`updated_by` int(11)
,`nama_kandang` varchar(255)
,`kapasitas` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `v_penetasan`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_penetasan` (
`id_penetasan` int(11)
,`batch` varchar(50)
,`id_mesin` int(11)
,`tanggal_mulai` date
,`tanggal_selesai` date
,`lama_penetasan` int(11)
,`jumlah_telur` int(11)
,`hasil_menetas` int(11)
,`hasil_gagal` int(11)
,`persentase_menetas` decimal(5,2)
,`suhu_rata` decimal(4,1)
,`kelembaban_rata` decimal(4,1)
,`status` enum('persiapan','proses','selesai','gagal')
,`catatan` text
,`tanggal` date
,`created_at` timestamp
,`updated_at` timestamp
,`created_by` int(11)
,`updated_by` int(11)
,`nama_mesin` varchar(255)
,`kapasitas_mesin` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `v_produksi`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_produksi` (
`id_produksi` int(11)
,`batch_penetasan` varchar(50)
,`batch_pembesaran` varchar(50)
,`id_kandang` int(11)
,`tanggal_mulai` date
,`tanggal_selesai` date
,`tanggal` date
,`tanggal_mulai_produksi` date
,`jenis_produksi` enum('telur','daging','ayam_hidup')
,`jumlah_ayam_awal` int(11)
,`jumlah_ayam_saat_ini` int(11)
,`jumlah` int(11)
,`berat` decimal(6,2)
,`harga_satuan` decimal(15,2)
,`total_nilai` decimal(15,2)
,`total_telur_produksi` int(11)
,`total_pakan_konsumsi` decimal(10,2)
,`total_kematian` int(11)
,`fase_produksi` enum('awal','puncak','akhir')
,`kualitas` enum('A','B','C','Reject')
,`status` enum('persiapan','aktif','selesai','gagal')
,`catatan` text
,`created_at` timestamp
,`updated_at` timestamp
,`created_by` int(11)
,`updated_by` int(11)
,`nama_kandang` varchar(255)
,`kapasitas` int(11)
);

-- --------------------------------------------------------

--
-- Struktur dari tabel `workflow_logs`
--

CREATE TABLE `workflow_logs` (
  `id` int(11) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `stage` enum('penetasan','pembesaran','produksi') NOT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `workflow_logs`
--

INSERT INTO `workflow_logs` (`id`, `entity_id`, `stage`, `action`, `description`, `user_id`, `created_at`) VALUES
(1, 1, 'penetasan', 'mulai', 'Penetasan batch PEN202501001 dimulai', 1, '2025-08-30 14:34:44'),
(2, 1, 'penetasan', 'selesai', 'Penetasan selesai menghasilkan 750 DOC', 1, '2025-08-30 14:34:44'),
(3, 1, 'pembesaran', 'persiapan', 'Persiapan pembesaran dari penetasan batch PEN202501001', 1, '2025-08-30 14:34:44'),
(4, 1, 'pembesaran', 'mulai', 'Pembesaran dimulai', 1, '2025-08-30 14:34:44'),
(5, 1, 'produksi', 'persiapan', 'Persiapan produksi dari pembesaran batch PEN202501001', 1, '2025-08-30 14:34:44');

-- --------------------------------------------------------

--
-- Struktur untuk view `v_kandang`
--
DROP TABLE IF EXISTS `v_kandang`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_kandang`  AS SELECT `kos_kandang`.`id_kandang` AS `id_kandang`, `kos_kandang`.`nama` AS `nama`, `kos_kandang`.`kapasitas` AS `kapasitas`, `kos_kandang`.`kapasitas_terisi` AS `kapasitas_terisi`, `kos_kandang`.`tipe` AS `tipe`, `kos_kandang`.`lokasi` AS `lokasi`, `kos_kandang`.`status` AS `status`, `kos_kandang`.`keterangan` AS `keterangan`, `kos_kandang`.`tanggal` AS `tanggal`, `kos_kandang`.`biaya` AS `biaya`, `kos_kandang`.`created_at` AS `created_at`, `kos_kandang`.`updated_at` AS `updated_at` FROM `kos_kandang` ;

-- --------------------------------------------------------

--
-- Struktur untuk view `v_karyawan`
--
DROP TABLE IF EXISTS `v_karyawan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_karyawan`  AS SELECT `k`.`id_karyawan` AS `id_karyawan`, `k`.`nip` AS `nip`, `k`.`nama` AS `nama`, `k`.`nama_lengkap` AS `nama_lengkap`, `k`.`id_jabatan` AS `id_jabatan`, `k`.`jabatan` AS `jabatan`, `k`.`email` AS `email`, `k`.`phone` AS `phone`, `k`.`alamat` AS `alamat`, `k`.`tanggal_lahir` AS `tanggal_lahir`, `k`.`jenis_kelamin` AS `jenis_kelamin`, `k`.`tanggal_masuk` AS `tanggal_masuk`, `k`.`tanggal_keluar` AS `tanggal_keluar`, `k`.`gaji_pokok` AS `gaji_pokok`, `k`.`tunjangan` AS `tunjangan`, `k`.`total_gaji` AS `total_gaji`, `k`.`status` AS `status`, `k`.`foto` AS `foto`, `k`.`keterangan` AS `keterangan`, `k`.`created_at` AS `created_at`, `k`.`updated_at` AS `updated_at`, `j`.`nama_jabatan` AS `nama_jabatan`, `j`.`gaji_pokok` AS `gaji_pokok_jabatan`, `j`.`tunjangan` AS `tunjangan_jabatan` FROM (`kos_karyawan` `k` left join `kos_jabatan` `j` on(`k`.`id_jabatan` = `j`.`id_jabatan`)) ;

-- --------------------------------------------------------

--
-- Struktur untuk view `v_mesin`
--
DROP TABLE IF EXISTS `v_mesin`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_mesin`  AS SELECT `kos_mesin`.`id_mesin` AS `id_mesin`, `kos_mesin`.`nama` AS `nama`, `kos_mesin`.`tipe` AS `tipe`, `kos_mesin`.`kapasitas` AS `kapasitas`, `kos_mesin`.`status` AS `status`, `kos_mesin`.`keterangan` AS `keterangan`, `kos_mesin`.`tanggal` AS `tanggal`, `kos_mesin`.`created_at` AS `created_at` FROM `kos_mesin` ;

-- --------------------------------------------------------

--
-- Struktur untuk view `v_pembesaran`
--
DROP TABLE IF EXISTS `v_pembesaran`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_pembesaran`  AS SELECT `p`.`id_pembesaran` AS `id_pembesaran`, `p`.`periode` AS `periode`, `p`.`batch_penetasan` AS `batch_penetasan`, `p`.`id_kandang` AS `id_kandang`, `p`.`tanggal_mulai` AS `tanggal_mulai`, `p`.`tanggal_selesai` AS `tanggal_selesai`, `p`.`tanggal` AS `tanggal`, `p`.`target_hari` AS `target_hari`, `p`.`jumlah_bibit` AS `jumlah_bibit`, `p`.`jumlah_hidup` AS `jumlah_hidup`, `p`.`jumlah_mati` AS `jumlah_mati`, `p`.`berat_rata` AS `berat_rata`, `p`.`konsumsi_pakan` AS `konsumsi_pakan`, `p`.`biaya_pakan` AS `biaya_pakan`, `p`.`biaya_obat` AS `biaya_obat`, `p`.`biaya_lain` AS `biaya_lain`, `p`.`total_biaya` AS `total_biaya`, `p`.`status` AS `status`, `p`.`catatan` AS `catatan`, `p`.`created_at` AS `created_at`, `p`.`updated_at` AS `updated_at`, `p`.`created_by` AS `created_by`, `p`.`updated_by` AS `updated_by`, `k`.`nama` AS `nama_kandang`, `k`.`kapasitas` AS `kapasitas` FROM (`kos_pembesaran` `p` left join `kos_kandang` `k` on(`p`.`id_kandang` = `k`.`id_kandang`)) ;

-- --------------------------------------------------------

--
-- Struktur untuk view `v_penetasan`
--
DROP TABLE IF EXISTS `v_penetasan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_penetasan`  AS SELECT `p`.`id_penetasan` AS `id_penetasan`, `p`.`batch` AS `batch`, `p`.`id_mesin` AS `id_mesin`, `p`.`tanggal_mulai` AS `tanggal_mulai`, `p`.`tanggal_selesai` AS `tanggal_selesai`, `p`.`lama_penetasan` AS `lama_penetasan`, `p`.`jumlah_telur` AS `jumlah_telur`, `p`.`hasil_menetas` AS `hasil_menetas`, `p`.`hasil_gagal` AS `hasil_gagal`, `p`.`persentase_menetas` AS `persentase_menetas`, `p`.`suhu_rata` AS `suhu_rata`, `p`.`kelembaban_rata` AS `kelembaban_rata`, `p`.`status` AS `status`, `p`.`catatan` AS `catatan`, `p`.`tanggal` AS `tanggal`, `p`.`created_at` AS `created_at`, `p`.`updated_at` AS `updated_at`, `p`.`created_by` AS `created_by`, `p`.`updated_by` AS `updated_by`, `m`.`nama` AS `nama_mesin`, `m`.`kapasitas` AS `kapasitas_mesin` FROM (`kos_penetasan` `p` left join `kos_mesin` `m` on(`p`.`id_mesin` = `m`.`id_mesin`)) ;

-- --------------------------------------------------------

--
-- Struktur untuk view `v_produksi`
--
DROP TABLE IF EXISTS `v_produksi`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_produksi`  AS SELECT `p`.`id_produksi` AS `id_produksi`, `p`.`batch_penetasan` AS `batch_penetasan`, `p`.`batch_pembesaran` AS `batch_pembesaran`, `p`.`id_kandang` AS `id_kandang`, `p`.`tanggal_mulai` AS `tanggal_mulai`, `p`.`tanggal_selesai` AS `tanggal_selesai`, `p`.`tanggal` AS `tanggal`, `p`.`tanggal_mulai_produksi` AS `tanggal_mulai_produksi`, `p`.`jenis_produksi` AS `jenis_produksi`, `p`.`jumlah_ayam_awal` AS `jumlah_ayam_awal`, `p`.`jumlah_ayam_saat_ini` AS `jumlah_ayam_saat_ini`, `p`.`jumlah` AS `jumlah`, `p`.`berat` AS `berat`, `p`.`harga_satuan` AS `harga_satuan`, `p`.`total_nilai` AS `total_nilai`, `p`.`total_telur_produksi` AS `total_telur_produksi`, `p`.`total_pakan_konsumsi` AS `total_pakan_konsumsi`, `p`.`total_kematian` AS `total_kematian`, `p`.`fase_produksi` AS `fase_produksi`, `p`.`kualitas` AS `kualitas`, `p`.`status` AS `status`, `p`.`catatan` AS `catatan`, `p`.`created_at` AS `created_at`, `p`.`updated_at` AS `updated_at`, `p`.`created_by` AS `created_by`, `p`.`updated_by` AS `updated_by`, `k`.`nama` AS `nama_kandang`, `k`.`kapasitas` AS `kapasitas` FROM (`kos_produksi` `p` left join `kos_kandang` `k` on(`p`.`id_kandang` = `k`.`id_kandang`)) ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `kos_jabatan`
--
ALTER TABLE `kos_jabatan`
  ADD PRIMARY KEY (`id_jabatan`);

--
-- Indeks untuk tabel `kos_kandang`
--
ALTER TABLE `kos_kandang`
  ADD PRIMARY KEY (`id_kandang`),
  ADD KEY `idx_kandang_status` (`status`),
  ADD KEY `idx_kandang_tipe` (`tipe`);

--
-- Indeks untuk tabel `kos_karyawan`
--
ALTER TABLE `kos_karyawan`
  ADD PRIMARY KEY (`id_karyawan`),
  ADD UNIQUE KEY `nip` (`nip`),
  ADD KEY `id_jabatan` (`id_jabatan`),
  ADD KEY `idx_karyawan_status` (`status`),
  ADD KEY `idx_karyawan_jabatan` (`id_jabatan`);

--
-- Indeks untuk tabel `kos_mesin`
--
ALTER TABLE `kos_mesin`
  ADD PRIMARY KEY (`id_mesin`);

--
-- Indeks untuk tabel `kos_notifikasi`
--
ALTER TABLE `kos_notifikasi`
  ADD PRIMARY KEY (`id_notifikasi`),
  ADD KEY `target_user` (`target_user`);

--
-- Indeks untuk tabel `kos_payment`
--
ALTER TABLE `kos_payment`
  ADD PRIMARY KEY (`id_payment`);

--
-- Indeks untuk tabel `kos_pembesaran`
--
ALTER TABLE `kos_pembesaran`
  ADD PRIMARY KEY (`id_pembesaran`),
  ADD KEY `batch_penetasan` (`batch_penetasan`),
  ADD KEY `id_kandang` (`id_kandang`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_pembesaran_batch` (`batch_penetasan`),
  ADD KEY `idx_pembesaran_tanggal` (`tanggal`);

--
-- Indeks untuk tabel `kos_penetasan`
--
ALTER TABLE `kos_penetasan`
  ADD PRIMARY KEY (`id_penetasan`),
  ADD UNIQUE KEY `batch` (`batch`),
  ADD KEY `id_mesin` (`id_mesin`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_penetasan_batch` (`batch`),
  ADD KEY `idx_penetasan_tanggal` (`tanggal`);

--
-- Indeks untuk tabel `kos_penghuni`
--
ALTER TABLE `kos_penghuni`
  ADD PRIMARY KEY (`id_penghuni`),
  ADD KEY `id_kandang` (`id_kandang`);

--
-- Indeks untuk tabel `kos_produksi`
--
ALTER TABLE `kos_produksi`
  ADD PRIMARY KEY (`id_produksi`),
  ADD KEY `batch_penetasan` (`batch_penetasan`),
  ADD KEY `id_kandang` (`id_kandang`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_produksi_batch` (`batch_penetasan`),
  ADD KEY `idx_produksi_tanggal` (`tanggal`);

--
-- Indeks untuk tabel `pembesaran_monitoring`
--
ALTER TABLE `pembesaran_monitoring`
  ADD PRIMARY KEY (`id_monitoring`),
  ADD UNIQUE KEY `unique_daily` (`id_pembesaran`,`tanggal`),
  ADD KEY `id_pembesaran` (`id_pembesaran`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_pembesaran_monitoring_date` (`tanggal`);

--
-- Indeks untuk tabel `produksi_harian`
--
ALTER TABLE `produksi_harian`
  ADD PRIMARY KEY (`id_produksi_harian`),
  ADD UNIQUE KEY `unique_daily_prod` (`id_produksi`,`tanggal`),
  ADD KEY `id_produksi` (`id_produksi`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_produksi_harian_date` (`tanggal`);

--
-- Indeks untuk tabel `simimin`
--
ALTER TABLE `simimin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `workflow_logs`
--
ALTER TABLE `workflow_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `entity_id` (`entity_id`),
  ADD KEY `stage` (`stage`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_workflow_stage_date` (`stage`,`created_at`),
  ADD KEY `idx_workflow_entity_stage` (`entity_id`,`stage`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `kos_jabatan`
--
ALTER TABLE `kos_jabatan`
  MODIFY `id_jabatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `kos_kandang`
--
ALTER TABLE `kos_kandang`
  MODIFY `id_kandang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `kos_karyawan`
--
ALTER TABLE `kos_karyawan`
  MODIFY `id_karyawan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kos_mesin`
--
ALTER TABLE `kos_mesin`
  MODIFY `id_mesin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `kos_notifikasi`
--
ALTER TABLE `kos_notifikasi`
  MODIFY `id_notifikasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `kos_payment`
--
ALTER TABLE `kos_payment`
  MODIFY `id_payment` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kos_pembesaran`
--
ALTER TABLE `kos_pembesaran`
  MODIFY `id_pembesaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `kos_penetasan`
--
ALTER TABLE `kos_penetasan`
  MODIFY `id_penetasan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `kos_penghuni`
--
ALTER TABLE `kos_penghuni`
  MODIFY `id_penghuni` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kos_produksi`
--
ALTER TABLE `kos_produksi`
  MODIFY `id_produksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `pembesaran_monitoring`
--
ALTER TABLE `pembesaran_monitoring`
  MODIFY `id_monitoring` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `produksi_harian`
--
ALTER TABLE `produksi_harian`
  MODIFY `id_produksi_harian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `simimin`
--
ALTER TABLE `simimin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `workflow_logs`
--
ALTER TABLE `workflow_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `kos_karyawan`
--
ALTER TABLE `kos_karyawan`
  ADD CONSTRAINT `kos_karyawan_ibfk_1` FOREIGN KEY (`id_jabatan`) REFERENCES `kos_jabatan` (`id_jabatan`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `kos_pembesaran`
--
ALTER TABLE `kos_pembesaran`
  ADD CONSTRAINT `kos_pembesaran_ibfk_1` FOREIGN KEY (`id_kandang`) REFERENCES `kos_kandang` (`id_kandang`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `kos_penetasan`
--
ALTER TABLE `kos_penetasan`
  ADD CONSTRAINT `kos_penetasan_ibfk_1` FOREIGN KEY (`id_mesin`) REFERENCES `kos_mesin` (`id_mesin`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `kos_penghuni`
--
ALTER TABLE `kos_penghuni`
  ADD CONSTRAINT `kos_penghuni_ibfk_1` FOREIGN KEY (`id_kandang`) REFERENCES `kos_kandang` (`id_kandang`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kos_produksi`
--
ALTER TABLE `kos_produksi`
  ADD CONSTRAINT `kos_produksi_ibfk_1` FOREIGN KEY (`id_kandang`) REFERENCES `kos_kandang` (`id_kandang`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `pembesaran_monitoring`
--
ALTER TABLE `pembesaran_monitoring`
  ADD CONSTRAINT `pembesaran_monitoring_ibfk_1` FOREIGN KEY (`id_pembesaran`) REFERENCES `kos_pembesaran` (`id_pembesaran`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `produksi_harian`
--
ALTER TABLE `produksi_harian`
  ADD CONSTRAINT `produksi_harian_ibfk_1` FOREIGN KEY (`id_produksi`) REFERENCES `kos_produksi` (`id_produksi`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
