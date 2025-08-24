-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 06, 2025 at 12:36 PM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 7.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vigazafarm`
--

-- --------------------------------------------------------

--
-- Table structure for table `bank`
--

CREATE TABLE `bank` (
  `id_bank` int(11) NOT NULL,
  `nama_bank` varchar(200) NOT NULL,
  `atas_nama` varchar(200) NOT NULL,
  `no_rek` int(11) NOT NULL,
  `metode` varchar(50) NOT NULL,
  `logo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `bank`
--

INSERT INTO `bank` (`id_bank`, `nama_bank`, `atas_nama`, `no_rek`, `metode`, `logo`) VALUES
(2, 'bni', 'yayasan kebaikan kita bersama', 2332424, 'transfer bank', 'bni.png'),
(4, 'mandiri', 'yayasan kebaikan kita bersama', 234234, 'transfer bank', 'mandiri.png'),
(5, 'bca', 'yayasan kebaikan kita bersama', 2334, 'transfer bank', 'bca.png'),
(6, 'bsi', 'yayasan kebaikan kita bersama', 23234, 'transfer bank', 'bsi.png'),
(7, 'gopay', 'yayasan kebaikan kita bersama', 12312323, 'E-Wallet', 'gopay.png'),
(8, 'spay', 'yayasan kebaikan kita bersama', 3434, 'E-Wallet', 'shopeepayqris.png'),
(9, 'bri', 'yayasan kebaikan kita bersama', 2147483647, 'transfer bank', 'bri.png'),
(10, 'dana', 'yayasan kebaikan kita bersama', 2147483647, 'E-Wallet', 'dana.png');

-- --------------------------------------------------------

--
-- Table structure for table `base`
--

CREATE TABLE `base` (
  `id_base` int(11) NOT NULL,
  `favicon` varchar(100) NOT NULL,
  `header` text NOT NULL,
  `logo_header` varchar(100) NOT NULL,
  `logo_footer` varchar(100) NOT NULL,
  `telp` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `alamat` text NOT NULL,
  `fb` varchar(50) NOT NULL,
  `ig` varchar(50) NOT NULL,
  `about` text NOT NULL,
  `video` varchar(100) NOT NULL,
  `image` varchar(100) NOT NULL,
  `motto` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `base`
--

INSERT INTO `base` (`id_base`, `favicon`, `header`, `logo_header`, `logo_footer`, `telp`, `email`, `alamat`, `fb`, `ig`, `about`, `video`, `image`, `motto`) VALUES
(1, 'fav.png', 'Kebaikan Kita Bersama', 'logo-1.png', 'footer-logo.png', '+626668880000', 'needhelp@company.com', '80 broklyn golden street', 'https://www.facebook.com/hjcorporate', 'https://www.instagram.com/hj.corp/', 'Lorem ipsum dolor sit ame consect etur pisicing elit sed do eiusmod tempor incididunt ut labore.', 'https://www.youtube.com/watch?v=i9E_Blai8vk', 'help-them-bg.jpg', 'Help them whenever they are in need');

-- --------------------------------------------------------

--
-- Table structure for table `campaign`
--

CREATE TABLE `campaign` (
  `id_campaign` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `id_fundraiser` int(11) NOT NULL,
  `image` varchar(200) NOT NULL,
  `thumbnail` varchar(200) NOT NULL,
  `judul` text NOT NULL,
  `permalink` text NOT NULL,
  `konten` text NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` time NOT NULL,
  `mulai` date NOT NULL,
  `selesai` date NOT NULL,
  `target` int(11) NOT NULL,
  `status` varchar(10) NOT NULL,
  `stat_img` varchar(10) NOT NULL,
  `stat_thumb` varchar(10) NOT NULL,
  `publish` varchar(10) NOT NULL,
  `tgl_publish` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `campaign`
--

INSERT INTO `campaign` (`id_campaign`, `id_kategori`, `id_fundraiser`, `image`, `thumbnail`, `judul`, `permalink`, `konten`, `tanggal`, `waktu`, `mulai`, `selesai`, `target`, `status`, `stat_img`, `stat_thumb`, `publish`, `tgl_publish`) VALUES
(1, 2, 1, 'causes-one-img-1.jpg', '', 'Bangun Masjid Indonesia Pertama di London-UK', '', 'There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don\'t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn\'t anything embarrassing hidden in the middle of text.<br/><br/>\r\n\r\nLorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.', '2021-10-14', '09:00:00', '2021-10-14', '2021-10-31', 50000000, 'biasa', 'belum', 'belum', 'no', '0000-00-00'),
(2, 2, 1, 'causes-one-img-1.jpg', '', 'Raises Fund for Clean & Healthy Water', '', 'There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don\'t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn\'t anything embarrassing hidden in the middle of text.<br/><br/>\r\n\r\nLorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.', '2021-10-14', '00:00:00', '2021-10-14', '2021-10-31', 10000000, 'mendesak', 'belum', 'belum', 'yes', '2022-02-01'),
(3, 2, 1, 'causes-one-img-1.jpg', '', 'Raises Fund for Clean & Healthy Water', '', 'There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don\'t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn\'t anything embarrassing hidden in the middle of text.<br/><br/>\r\n\r\nLorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.', '2021-10-14', '00:00:00', '2021-10-14', '2021-10-31', 50000000, 'biasa', 'belum', 'belum', 'no', '0000-00-00'),
(4, 2, 1, 'causes-one-img-1.jpg', '', 'Raises Fund for Clean & Healthy Water', '', 'There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don\'t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn\'t anything embarrassing hidden in the middle of text.<br/><br/>\r\n\r\nLorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.', '2021-10-14', '00:00:00', '2021-10-14', '2021-10-31', 50000000, 'biasa', 'belum', 'belum', 'no', '0000-00-00'),
(5, 1, 1, 'causes-one-img-1.jpg', '', 'Raises Fund for Clean & Healthy Water', '', 'There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don\'t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn\'t anything embarrassing hidden in the middle of text.<br/><br/>\r\n\r\nLorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.', '2021-10-14', '00:00:00', '2021-10-14', '0000-00-00', 50000000, 'biasa', 'belum', 'belum', 'no', '0000-00-00'),
(11, 1, 1, '7__Ducati_Panigale_R1.jpg', '', 'tesset', '', 'tesset', '2022-01-09', '09:51:23', '2022-01-09', '2022-01-22', 12345, 'biasa', 'belum', 'belum', 'no', '0000-00-00'),
(12, 1, 1, '6__Ford_Ranger_Wildtrak.jpg', '', 'tes', '', 'tes', '2022-01-09', '10:59:19', '2022-01-09', '2022-01-15', 123, 'biasa', 'belum', 'belum', 'no', '0000-00-00'),
(13, 1, 1, '4__Umroh_Haji_Keluarga_Besar.jpg', 'aaykpn.png', 'aaa', '', 'aaa', '2022-01-14', '17:03:39', '2022-01-14', '2022-01-29', 6000000, 'biasa', 'sudah', 'sudah', 'no', '2022-02-03'),
(14, 4, 1, '6__Ford_Ranger_Wildtrak1.jpg', '1__Brio_RS.jpg', 'bbb', '', 'bb', '2022-01-11', '14:24:46', '2022-01-11', '2022-01-22', 10000000, 'review', 'sudah', 'sudah', 'yes', '2022-02-03'),
(15, 1, 1, 'image.jpg', 'thumbnail.jpg', 'aa', '', 'aa', '2022-02-04', '14:46:49', '2022-02-04', '2022-03-12', 10000000, 'biasa', 'belum', 'belum', 'no', NULL),
(16, 4, 4, '6__Ford_Ranger_Wildtrak2.jpg', '7__Ducati_Panigale_R3.jpg', 'cc', '', 'bb', '2022-02-04', '14:48:30', '2022-02-04', '2022-03-12', 10000000, 'biasa', 'sudah', 'sudah', 'yes', '2022-02-10'),
(17, 1, 4, '6__Ford_Ranger_Wildtrak3.jpg', '1__Brio_RS1.jpg', 'cc', '', 'cc', '2022-02-04', '15:08:56', '2022-02-04', '2022-02-24', 5000000, 'biasa', 'sudah', 'sudah', 'no', NULL),
(18, 2, 4, '1__Brio_RS31.jpg', '1__Brio_RS3.jpg', 'rr', '', 'rr', '2022-02-04', '15:09:12', '2022-02-04', '2022-03-01', 6000000, 'review', 'sudah', 'sudah', 'no', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `doa`
--

CREATE TABLE `doa` (
  `id_doa` int(11) NOT NULL,
  `id_campaign` int(11) NOT NULL,
  `id_donor` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` time NOT NULL,
  `isi` text NOT NULL,
  `asdonatur` varchar(20) NOT NULL,
  `amin` int(20) NOT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `doa`
--

INSERT INTO `doa` (`id_doa`, `id_campaign`, `id_donor`, `tanggal`, `waktu`, `isi`, `asdonatur`, `amin`, `status`) VALUES
(1, 14, 5, '2022-02-10', '04:22:44', 'tes', 'tidak', 5, 'publish'),
(2, 14, 4, '2022-02-12', '11:14:28', 'tes', 'yes', 9, 'publish'),
(3, 0, 0, '0000-00-00', '00:00:00', '', '', 1, ''),
(4, 0, 0, '0000-00-00', '00:00:00', '', '', 6, ''),
(5, 0, 0, '0000-00-00', '00:00:00', '', '', 6, ''),
(6, 0, 0, '0000-00-00', '00:00:00', '', '', 1, ''),
(7, 0, 0, '0000-00-00', '00:00:00', '', '', 1, ''),
(8, 14, 10, '2022-02-14', '04:52:13', 'tes', 'ya', 0, 'pending'),
(9, 14, 10, '2022-02-14', '04:54:46', 'set', 'tidak', 2, 'pending'),
(10, 14, 10, '2022-02-14', '22:22:52', 'res', 'tidak', 3, 'publish'),
(11, 16, 11, '2022-02-15', '09:34:51', 'tes', 'ya', 0, 'publish'),
(12, 0, 5, '2022-02-20', '14:54:24', 'hhh', 'ya', 0, 'pending'),
(13, 2, 5, '2022-02-20', '15:00:46', 'bb', 'ya', 0, 'pending'),
(14, 0, 5, '2022-02-20', '15:15:46', 'ts', 'tidak', 0, 'pending'),
(15, 0, 5, '2022-02-20', '15:16:10', 'ts', 'tidak', 0, 'pending'),
(16, 2, 5, '2022-02-20', '15:23:52', 'tes', 'tidak', 0, 'pending'),
(17, 2, 5, '2022-02-20', '18:30:37', 'tes', 'ya', 0, 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `donasi`
--

CREATE TABLE `donasi` (
  `id_donasi` int(11) NOT NULL,
  `id_campaign` int(11) NOT NULL,
  `id_donor` int(11) NOT NULL,
  `id_bank` int(11) NOT NULL,
  `nominal` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` time NOT NULL,
  `status` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `donasi`
--

INSERT INTO `donasi` (`id_donasi`, `id_campaign`, `id_donor`, `id_bank`, `nominal`, `tanggal`, `waktu`, `status`) VALUES
(1, 14, 5, 2, 1400000, '2022-02-10', '04:22:44', 'berhasil'),
(2, 14, 2, 4, 1500000, '2022-02-10', '11:14:28', 'berhasil'),
(3, 2, 2, 2, 1000000, '2022-02-10', '11:14:28', 'berhasil'),
(4, 14, 10, 2, 123, '2022-02-14', '04:52:13', 'pending'),
(5, 14, 10, 2, 234324, '2022-02-14', '04:54:46', 'berhasil'),
(6, 14, 10, 6, 6576557, '2022-02-14', '22:22:52', 'berhasil'),
(7, 16, 10, 2, 12345, '2022-02-15', '09:34:51', 'berhasil'),
(8, 0, 5, 8, 10000, '2022-02-20', '14:54:24', 'pending'),
(9, 2, 5, 8, 6000, '2022-02-20', '15:01:08', 'proses'),
(10, 0, 5, 9, 12345, '2022-02-20', '15:15:46', 'pending'),
(11, 0, 5, 9, 12345, '2022-02-20', '15:16:10', 'pending'),
(12, 2, 5, 8, 54656, '2022-02-20', '15:24:00', 'berhasil'),
(13, 2, 5, 8, 5000, '2022-02-20', '18:30:53', 'proses');

-- --------------------------------------------------------

--
-- Table structure for table `donor`
--

CREATE TABLE `donor` (
  `id_donor` int(11) NOT NULL,
  `nama` varchar(200) NOT NULL,
  `alamat` text NOT NULL,
  `no_hp` int(11) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `status` varchar(50) NOT NULL,
  `level` varchar(50) NOT NULL,
  `jabatan` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `donor`
--

INSERT INTO `donor` (`id_donor`, `nama`, `alamat`, `no_hp`, `email`, `password`, `status`, `level`, `jabatan`) VALUES
(2, 'Donatur', 'anonim', 12345, 'anonim@anonim.anonim', '$2y$10$rcypixINnA5tBvoaZ5.taOBS3Iwx5AxgRVQpr6LhddF/XILJn96mq', '', 'donor', 'Donatur'),
(4, 'set', 'set', 12213, 'set@set.set', 'set', '', 'donor', 'Donor'),
(5, 'aaa', 'aaa', 12313, 'aa@aa.aa', '$2y$10$rcypixINnA5tBvoaZ5.taOBS3Iwx5AxgRVQpr6LhddF/XILJn96mq', '', 'donor', 'Donor'),
(6, 'Donatur', 'anonim', 12345, 'anonim@anonim.anonim', 'anonim', '', 'donor', 'Donor'),
(7, 'tes tis', '', 0, 'nn@jdf.er', '$2y$10$Is91HJ0SiXFrbF.ALihis.VCpUd9bFh3Ppa95tHq1QlJ6VPOMkJvW', '', 'donor', 'Donor'),
(8, 'tes tis', '', 0, 'wer@wer.wer', '', '', 'donor', 'Donor'),
(9, 'tt', '', 0, 'tt@tt.tt', '$2y$10$0v2tAak4vxYSPXwSIXIBSepXWBZrup459JeeZBtB4qpFZqbbEyDoK', '', 'donor', 'Donor'),
(10, 'ss', '', 0, 'ss@ss.ss', '$2y$10$JtTMzok6.ttVqHsNbgiF4ezFLx6bhuQ2oSLa/AiSh6Fh3FW/934MO', '', 'donor', 'Donor');

-- --------------------------------------------------------

--
-- Table structure for table `fundraiser`
--

CREATE TABLE `fundraiser` (
  `id_fundraiser` int(11) NOT NULL,
  `nama` varchar(200) NOT NULL,
  `alamat` text NOT NULL,
  `no_hp` int(11) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `status` varchar(50) NOT NULL,
  `level` varchar(50) NOT NULL,
  `jabatan` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `fundraiser`
--

INSERT INTO `fundraiser` (`id_fundraiser`, `nama`, `alamat`, `no_hp`, `email`, `password`, `status`, `level`, `jabatan`) VALUES
(1, 'Admin Utama', 'tes', 123, 'super@kebaikankitabersama.id', '$2y$10$rcypixINnA5tBvoaZ5.taOBS3Iwx5AxgRVQpr6LhddF/XILJn96mq', '', 'fundraiser', 'Fundraiser'),
(4, 'aa', 'tes', 123, 'aa@aa.aa', '$2y$10$zlhl6ZWaf0Bx2szlJv5Li.UWPG8eP4FRwEg12/p6xvuoLQGwxFnyu', '', 'fundraiser', 'Fundraiser'),
(5, 'set', 'set', 12345, 'set@set.set', 'set', '', 'fundraiser', 'Fundraiser'),
(6, 'tes', 'tesa', 42353452, 'tes@tes.tess', 'tes', '', 'fundraiser', 'Fundraiser'),
(7, 'asdf', 'asdf', 2134, 'asdf@adsf.asdf', 'asdf', '', 'fundraiser', 'Fundraiser');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `kategori`) VALUES
(2, 'Medical'),
(4, 'Social');

-- --------------------------------------------------------

--
-- Table structure for table `keunggulan`
--

CREATE TABLE `keunggulan` (
  `id_keunggulan` int(11) NOT NULL,
  `judul` varchar(100) NOT NULL,
  `konten` text NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `keunggulan`
--

INSERT INTO `keunggulan` (`id_keunggulan`, `judul`, `konten`, `image`) VALUES
(1, 'Become a Volunteer', 'There are many variations of but the majority have simply free text suffered.', 'tes_-_Copy.png'),
(2, ' Quick Fundraising', 'There are many variations of but the majority have simply free text suffered.', 'tes1.png'),
(3, 'Start Donating', 'There are many variations of but the majority have simply free text suffered.', 'tes11.png');

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE `log` (
  `id_log` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `aksi` varchar(100) NOT NULL,
  `tanggal` date NOT NULL DEFAULT current_timestamp(),
  `waktu` time NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `log`
--

INSERT INTO `log` (`id_log`, `id_user`, `aksi`, `tanggal`, `waktu`) VALUES
(1, 1, 'Menambah data donatur - set', '2022-01-09', '17:21:32'),
(2, 1, 'Menambah data donatur - aaa', '2022-01-09', '17:21:54'),
(3, 1, 'Menambah data kampanye donasi - aaa', '2022-01-11', '14:12:32'),
(4, 1, 'Menambah data kampanye donasi - bbb', '2022-01-11', '14:24:46'),
(5, 1, 'Mengubah data kampanye donasi dengan ID - 13', '2022-01-14', '17:02:40'),
(6, 1, 'Mengubah data kampanye donasi dengan ID - 13', '2022-01-14', '17:03:39'),
(7, 1, 'Mengubah data gambar - thumbnail', '2022-01-14', '17:07:09'),
(8, 1, 'Mengubah data gambar - image', '2022-01-14', '17:09:15'),
(9, 1, 'Mengubah data publish dengan ID - ', '2022-01-14', '17:18:16'),
(10, 1, 'Mengubah data publish dengan ID - ', '2022-01-14', '17:22:00'),
(11, 1, 'Mengubah data publish dengan ID - ', '2022-01-14', '17:22:06'),
(12, 1, 'Mengubah data publish dengan ID - ', '2022-01-14', '17:22:21'),
(13, 1, 'Mengubah data publish dengan ID - ', '2022-01-14', '17:23:25'),
(14, 1, 'Mengubah data publish dengan ID - ', '2022-01-14', '17:25:25'),
(15, 1, 'Mengubah data publish dengan ID - ', '2022-01-14', '17:26:08'),
(16, 1, 'Mengubah data publish dengan ID - ', '2022-01-14', '17:26:15'),
(17, 1, 'Mengubah data gambar - thumbnail', '2022-01-14', '17:46:32'),
(18, 1, 'Mengubah data gambar - image', '2022-01-14', '17:46:53'),
(19, 4, 'Mengubah data publish dengan ID - ', '2022-02-01', '17:46:02'),
(20, 4, 'Mengubah data publish dengan ID - ', '2022-02-01', '17:54:29'),
(21, 4, 'Mengubah data publish dengan ID - ', '2022-02-01', '17:55:32'),
(22, 1, 'Mengubah data publish dengan ID - ', '2022-02-03', '12:58:21'),
(23, 1, 'Mengubah data publish dengan ID - ', '2022-02-03', '13:06:11'),
(24, 1, 'Mengubah data publish dengan ID - ', '2022-02-03', '13:07:48'),
(25, 1, 'Mengubah data publish dengan ID - ', '2022-02-03', '13:10:54'),
(26, 1, 'Mengubah data publish dengan ID - ', '2022-02-03', '13:11:00'),
(27, 1, 'Mengubah data publish dengan ID - ', '2022-02-03', '14:04:22'),
(28, 1, 'Mengubah data publish dengan ID - ', '2022-02-03', '14:04:27'),
(29, 1, 'Mengubah data publish dengan ID - ', '2022-02-03', '14:04:44'),
(30, 4, 'Menambah data kampanye donasi - aa', '2022-02-04', '14:46:49'),
(31, 4, 'Menambah data kampanye donasi - bb', '2022-02-04', '14:48:30'),
(32, 4, 'Mengubah data gambar - thumbnail', '2022-02-04', '15:08:14'),
(33, 4, 'Mengubah data gambar - image', '2022-02-04', '15:08:27'),
(34, 4, 'Menambah data kampanye donasi - cc', '2022-02-04', '15:08:56'),
(35, 4, 'Menambah data kampanye donasi - rr', '2022-02-04', '15:09:12'),
(36, 4, 'Mengubah data gambar - thumbnail', '2022-02-04', '15:09:22'),
(37, 4, 'Mengubah data gambar - image', '2022-02-04', '15:09:32'),
(38, 4, 'Mengubah data gambar - thumbnail', '2022-02-04', '15:09:44'),
(39, 4, 'Mengubah data gambar - image', '2022-02-04', '15:09:53'),
(40, 4, 'Mengubah data publish dengan ID - ', '2022-02-04', '15:10:01'),
(41, 4, 'Mengubah data publish dengan ID - ', '2022-02-04', '15:10:18'),
(42, 4, 'Mengubah data publish dengan ID - ', '2022-02-04', '15:10:26'),
(43, 4, 'Mengubah data publish dengan ID - ', '2022-02-04', '15:49:39'),
(44, 4, 'Mengubah data publish dengan ID - ', '2022-02-04', '15:49:53'),
(45, 4, 'Mengubah data publish dengan ID - ', '2022-02-04', '15:49:58'),
(46, 4, 'Mengubah data publish dengan ID - ', '2022-02-04', '15:50:19'),
(47, 1, 'Menambah data bank - mandiri', '2022-02-08', '07:22:44'),
(48, 1, 'Mengubah data bank dengan ID - 1', '2022-02-08', '07:23:52'),
(49, 1, 'Menambah data bank - bni', '2022-02-08', '07:24:05'),
(50, 1, 'Menghapus data bank dengan ID - 1', '2022-02-08', '07:24:10'),
(51, 1, 'Menambah data bank - jago', '2022-02-08', '11:15:01'),
(52, 1, 'Mengubah data bank dengan ID - 3', '2022-02-08', '11:17:07'),
(53, 1, 'Mengubah data bank dengan ID - 3', '2022-02-08', '11:17:17'),
(54, 1, 'Menghapus data bank dengan ID - 3', '2022-02-08', '11:17:34'),
(55, 1, 'Mengubah data bank dengan ID - 2', '2022-02-08', '11:17:48'),
(56, 1, 'Mengubah data bank dengan ID - 2', '2022-02-08', '11:59:45'),
(57, 1, 'Menambah data bank - mandiri', '2022-02-08', '12:00:11'),
(58, 1, 'Menambah data bank - bca', '2022-02-08', '12:00:29'),
(59, 1, 'Menambah data bank - bsi', '2022-02-08', '12:00:50'),
(60, 1, 'Menambah data bank - gopay', '2022-02-08', '12:01:40'),
(61, 1, 'Menambah data bank - spay', '2022-02-08', '12:02:11'),
(62, 1, 'Mengubah data bank dengan ID - 7', '2022-02-08', '12:02:25'),
(63, 1, 'Mengubah data bank dengan ID - 6', '2022-02-08', '12:02:34'),
(64, 1, 'Mengubah data bank dengan ID - 8', '2022-02-08', '12:02:45'),
(65, 1, 'Menambah data bank - bri', '2022-02-08', '12:03:35'),
(66, 1, 'Menambah data bank - dana', '2022-02-08', '12:03:57'),
(67, 5, 'Menambah data kategori - ', '2022-02-10', '04:19:36'),
(68, 5, 'Menambah data kategori - ', '2022-02-10', '04:20:38'),
(69, 5, 'Menambah data kategori - ', '2022-02-10', '04:22:44'),
(70, 1, 'Mengubah data publish dengan ID - ', '2022-02-10', '15:15:41'),
(71, 10, 'Menambah data kategori - ', '2022-02-14', '04:52:13'),
(72, 10, 'Menambah data kategori - ', '2022-02-14', '04:54:46'),
(73, 10, 'Menambah data kategori - ', '2022-02-14', '22:22:52'),
(74, 10, 'Menambah data kategori - ', '2022-02-15', '09:34:51'),
(75, 1, 'Mengubah status transaksi dengan ID Donasi - 6', '2022-02-15', '13:26:56'),
(76, 1, 'Mengubah status transaksi dengan ID Donasi - 6', '2022-02-15', '13:27:40'),
(77, 1, 'Mengubah status transaksi dengan ID Donasi - 5', '2022-02-15', '13:27:55'),
(78, 1, 'Mengubah status transaksi dengan ID Donasi - 5', '2022-02-15', '13:34:54'),
(79, 1, 'Mengubah status transaksi dengan ID Donasi - 5', '2022-02-15', '13:49:22'),
(80, 1, 'Mengubah status transaksi dengan ID Donasi - 5', '2022-02-15', '13:50:13'),
(81, 1, 'Mengubah status transaksi dengan ID Donasi - 5', '2022-02-15', '14:02:58'),
(82, 1, 'Mengubah status transaksi dengan ID Donasi - 5', '2022-02-15', '14:08:39'),
(83, 5, 'Menambah data kategori - ', '2022-02-20', '14:54:24'),
(84, 5, 'Menambah data kategori - ', '2022-02-20', '15:00:46'),
(85, 5, 'Menambah data kategori - ', '2022-02-20', '15:15:46'),
(86, 5, 'Menambah data kategori - ', '2022-02-20', '15:16:10'),
(87, 5, 'Menambah data kategori - ', '2022-02-20', '15:23:52'),
(88, 5, 'Menambah data kategori - ', '2022-02-20', '18:30:37'),
(89, 1, 'Menghapus data newsletter dengan ID- 1', '2022-02-22', '00:57:04'),
(90, 1, 'Menambah data kelas - Mini Mix', '2022-06-02', '09:54:41'),
(91, 1, 'Mengubah data kelas dengan ID - 1', '2022-06-02', '09:55:00'),
(92, 1, 'Menghapus data kelas dengan ID - 1', '2022-06-02', '09:55:05'),
(93, 1, 'Menambah data lokasi - Auriga', '2022-06-02', '09:58:44'),
(94, 1, 'Mengubah data lokasi dengan ID - 1', '2022-06-02', '09:58:51'),
(95, 1, 'Menghapus data lokasi dengan ID - 1', '2022-06-02', '09:58:56'),
(96, 1, 'Menambah data kelas - Mini Mix', '2022-06-02', '12:57:08'),
(97, 1, 'Menambah data lokasi - Auriga', '2022-06-02', '12:57:24'),
(98, 1, 'Menambah data siswa - Ghazali Alvaro Ramdhani', '2022-06-02', '14:20:27'),
(99, 1, 'Menambah data siswa - Ghazali Alvaro Ramdhani', '2022-06-02', '14:23:08'),
(100, 1, 'Menambah data siswa - Ghazali Alvaro Ramdhani', '2022-06-02', '14:25:56'),
(101, 1, 'Mengubah data siswa dengan ID - 2', '2022-06-02', '18:06:39'),
(102, 1, 'Mengubah data siswa dengan ID - 2', '2022-06-02', '20:01:38'),
(103, 1, 'Menghapus data siswa dengan ID - 1', '2022-06-02', '20:01:56'),
(104, 1, 'Mengubah data siswa dengan ID - 3', '2022-06-03', '13:58:39'),
(105, 1, 'Mengubah data siswa dengan ID - 2', '2022-06-03', '13:59:54'),
(106, 1, 'Menambah data siswa - tes', '2022-06-03', '14:00:46'),
(107, 1, 'Menambah data siswa - te', '2022-06-03', '14:25:15'),
(108, 1, 'Menghapus data siswa dengan ID - 5', '2022-06-03', '14:25:23'),
(109, 1, 'Mengubah data kelas dengan ID - ', '2022-06-03', '21:40:48'),
(110, 1, 'Mengubah data kelas dengan ID - ', '2022-06-03', '21:42:30'),
(111, 1, 'Mengubah data kelas dengan ID - 1', '2022-06-03', '21:44:52'),
(112, 1, 'Mengubah data kelas dengan ID - 1', '2022-06-03', '21:48:25'),
(113, 1, 'Generate data bulan - 06', '2022-06-03', '22:53:11'),
(114, 1, 'Generate data bulan - 06', '2022-06-03', '23:01:55'),
(115, 1, 'Generate data bulan - 06', '2022-06-03', '23:04:58'),
(116, 1, 'Generate data bulan - 06', '2022-06-03', '23:17:34'),
(117, 1, 'Generate data bulan - 06', '2022-06-03', '23:19:09'),
(118, 1, 'Generate data bulan - 06', '2022-06-05', '07:07:06'),
(119, 1, 'Generate data bulan - 06', '2022-06-05', '07:10:31'),
(120, 1, 'Generate data bulan - 06', '2022-06-05', '07:12:38'),
(121, 1, 'Generate data bulan - 06', '2022-06-05', '07:17:47'),
(122, 1, 'Generate data bulan - 06', '2022-06-05', '07:20:08'),
(123, 1, 'Generate data bulan - 06', '2022-06-05', '07:22:25'),
(124, 1, 'Generate data bulan - 06', '2022-06-05', '07:23:20'),
(125, 1, 'Generate data bulan - 06', '2022-06-05', '07:47:13'),
(126, 1, 'Generate data bulan - 06', '2022-06-05', '07:47:25'),
(127, 1, 'Generate data bulan - 06', '2022-06-05', '12:39:50'),
(128, 1, 'Generate data bulan - 06', '2022-06-05', '12:41:42'),
(129, 1, 'Generate data bulan - 06', '2022-06-05', '12:41:48'),
(130, 1, 'Generate data bulan - 06', '2022-06-05', '12:42:19'),
(131, 1, 'Generate data bulan - 06', '2022-06-05', '12:45:28'),
(132, 1, 'Generate data bulan - 06', '2022-06-05', '12:52:04'),
(133, 1, 'Generate data bulan - 06', '2022-06-05', '12:54:37'),
(134, 1, 'Generate data bulan - 06', '2022-06-05', '12:55:06'),
(135, 1, 'Generate data bulan - 06', '2022-06-05', '12:55:12'),
(136, 1, 'Generate data bulan - 06', '2022-06-05', '12:55:17'),
(137, 1, 'Generate data bulan - 06', '2022-06-05', '12:55:22'),
(138, 1, 'Generate data bulan - 06', '2022-06-05', '13:08:48'),
(139, 1, 'Generate data bulan - 06', '2022-06-05', '13:08:54'),
(140, 1, 'Generate data bulan - 06', '2022-06-05', '13:17:06'),
(141, 1, 'Generate data bulan - 06', '2022-06-05', '13:17:16'),
(142, 1, 'Generate data bulan - 06', '2022-06-05', '13:17:38'),
(143, 1, 'Generate data bulan - 06', '2022-06-05', '13:17:59'),
(144, 1, 'Generate data bulan - 06', '2022-06-05', '13:18:37'),
(145, 1, 'Menambah data siswa - set', '2022-06-06', '04:36:00'),
(146, 1, 'Menambah data siswa - set', '2022-06-06', '04:37:33'),
(147, 1, 'Menambah data siswa - Ghazali Alvaro Ramdhani', '2022-06-06', '04:41:58'),
(148, 1, 'Menambah data siswa - Ibrahim Azka Ramdhani', '2022-06-06', '04:42:49'),
(149, 1, 'Menambah data siswa - tes', '2022-06-06', '04:43:18'),
(150, 1, 'Menambah data siswa - set', '2022-06-06', '04:43:56'),
(151, 1, 'Menambah data siswa - Ghazali Alvaro Ramdhani', '2022-06-06', '05:10:21'),
(152, 1, 'Menambah data siswa - Ibrahim Azka Ramdhani', '2022-06-06', '05:11:00'),
(153, 1, 'Menambah data siswa - tes', '2022-06-06', '05:11:32'),
(154, 1, 'Menambah data siswa - set', '2022-06-06', '05:11:57'),
(155, 1, 'Menambah data club - Mataram', '2022-06-07', '04:35:08'),
(156, 1, 'Menambah data club - Spartan', '2022-06-07', '04:35:23'),
(157, 1, 'Menambah data club - tes', '2022-06-07', '04:35:31'),
(158, 1, 'Mengubah data club dengan ID - 3', '2022-06-07', '04:36:49'),
(159, 1, 'Menghapus data club dengan ID - 3', '2022-06-07', '04:36:53'),
(160, 1, 'Menambah data club - tes', '2022-06-07', '04:37:46'),
(161, 1, 'Menghapus data club dengan ID - 4', '2022-06-07', '04:37:53'),
(162, 1, 'Menambah data siswa - aa', '2022-06-07', '04:48:40'),
(163, 1, 'Menambah data SPP - MTR-AURIGA-KIDDOS-500', '2022-06-07', '05:25:51'),
(164, 1, 'Menghapus data SPP dengan ID - 1', '2022-06-07', '05:31:41'),
(165, 1, 'Menambah data SPP - MTR-AURIGA-KIDDOS-500', '2022-06-07', '05:31:56'),
(166, 1, 'Menambah data SPP - MTR-AURIGA-KIDDOS-500', '2022-06-07', '05:33:04'),
(167, 1, 'Menambah data club - tes', '2022-06-22', '14:11:28'),
(168, 1, 'Menghapus data club dengan ID - 5', '2022-06-22', '14:11:34'),
(169, 1, 'Menambah data kelas - tes', '2022-06-22', '15:05:06'),
(170, 1, 'Mengubah data kelas dengan ID - 3', '2022-06-22', '15:05:13'),
(171, 1, 'Menghapus data kelas dengan ID - 3', '2022-06-22', '15:05:19'),
(172, 1, 'Menambah data biaya registrasi - ', '2022-06-24', '10:46:25'),
(173, 1, 'Mengubah data biaya registrasi dengan ID - 2', '2022-06-24', '10:48:46'),
(174, 1, 'Menghapus data biaya registrasi dengan ID - 2', '2022-06-24', '10:48:54'),
(175, 1, 'Menambah data biaya registrasi - MTR-AURIGA-MINIMIX-500', '2022-06-24', '13:24:51'),
(176, 1, 'Mengubah data biaya registrasi dengan ID - 3', '2022-06-24', '13:26:01'),
(177, 1, 'Mengubah data biaya registrasi dengan ID - 3', '2022-06-24', '13:26:25'),
(178, 1, 'Menghapus data biaya registrasi dengan ID - 3', '2022-06-24', '13:26:31'),
(179, 1, 'Menambah data biaya registrasi - SPT-AURIGA-MINIMIX-350', '2022-06-24', '13:26:55'),
(180, 1, 'Menambah data SPP - SPT-AURIGA-MINIMIX-350', '2022-06-26', '15:01:42'),
(181, 1, 'Mengubah data SPP dengan ID - 4', '2022-06-26', '15:04:30'),
(182, 1, 'Mengubah data SPP dengan ID - 4', '2022-06-26', '15:04:42'),
(183, 1, 'Mengubah data status siswa dengan ID - ', '2022-06-26', '15:18:32'),
(184, 1, 'Mengubah data status siswa dengan ID - ', '2022-06-26', '15:18:37'),
(185, 1, 'Mengubah data registrasi siswa dengan ID - ', '2022-06-26', '15:20:15'),
(186, 1, 'Mengubah data registrasi siswa dengan ID - ', '2022-06-26', '15:20:51'),
(187, 1, 'Mengubah data registrasi siswa dengan ID - ', '2022-06-26', '15:21:09'),
(188, 1, 'Mengubah data registrasi siswa dengan ID - ', '2022-06-26', '15:21:14'),
(189, 1, 'Mengubah data registrasi siswa dengan ID - ', '2022-06-26', '15:21:19'),
(190, 1, 'Menambah data siswa - vvvv', '2022-06-26', '15:40:12'),
(191, 1, 'Mengubah data siswa dengan ID - 6', '2022-06-26', '15:55:40'),
(192, 1, 'Generate data bulan - 06', '2022-06-26', '15:59:29'),
(193, 1, 'Generate data bulan - 06', '2022-06-26', '16:15:06'),
(194, 1, 'Menambah data kandang - ', '2024-07-06', '15:13:35'),
(195, 1, 'Menambah data kandang - ', '2024-07-09', '02:11:27'),
(196, 1, 'Menambah data pembesaran - ', '2024-07-12', '08:10:21');

-- --------------------------------------------------------

--
-- Table structure for table `mnl_base`
--

CREATE TABLE `mnl_base` (
  `id_mnl_base` int(11) NOT NULL,
  `nominal` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `mnl_base`
--

INSERT INTO `mnl_base` (`id_mnl_base`, `nominal`) VALUES
(1, '500000');

-- --------------------------------------------------------

--
-- Table structure for table `mnl_club`
--

CREATE TABLE `mnl_club` (
  `id_mnl_club` int(11) NOT NULL,
  `club` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `mnl_club`
--

INSERT INTO `mnl_club` (`id_mnl_club`, `club`) VALUES
(1, 'Mataram'),
(2, 'Spartan');

-- --------------------------------------------------------

--
-- Table structure for table `mnl_kelas`
--

CREATE TABLE `mnl_kelas` (
  `id_mnl_kelas` int(11) NOT NULL,
  `kelas` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `mnl_kelas`
--

INSERT INTO `mnl_kelas` (`id_mnl_kelas`, `kelas`) VALUES
(2, 'Mini Mix');

-- --------------------------------------------------------

--
-- Table structure for table `mnl_lokasi`
--

CREATE TABLE `mnl_lokasi` (
  `id_mnl_lokasi` int(11) NOT NULL,
  `lokasi` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `mnl_lokasi`
--

INSERT INTO `mnl_lokasi` (`id_mnl_lokasi`, `lokasi`) VALUES
(2, 'Auriga');

-- --------------------------------------------------------

--
-- Table structure for table `mnl_ortu`
--

CREATE TABLE `mnl_ortu` (
  `id_mnl_ortu` int(11) NOT NULL,
  `nama` varchar(200) NOT NULL,
  `no_hp` varchar(50) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `status` varchar(50) NOT NULL,
  `level` varchar(50) NOT NULL,
  `jabatan` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `mnl_ortu`
--

INSERT INTO `mnl_ortu` (`id_mnl_ortu`, `nama`, `no_hp`, `email`, `password`, `status`, `level`, `jabatan`) VALUES
(1, 'Hardi Setia Ramdhani', '081916664548', 'ramdhanihardi@gmail.com', '$2y$10$/rVmum9kt3bKM/SI/c63sei1g1JVUdn8B3/x0Ps/hFRFx7pvwp7uu', 'aktif', 'ortu', 'Parent'),
(2, 'tes', '123', 'tes@tes.tes', '$2y$10$dnlodQHnOmVxn49yuV274Oa5LohYbL4tL7VS13k8MWd5hY5ioaW/K', 'aktif', 'ortu', 'Parent'),
(3, 'set', '1234', 'set@set.set', '$2y$10$BlavZiFofJiDEgtWmJ9Xl.3WV3WVV.OksE2j/pIMTaqeuYrKOidTK', 'aktif', 'ortu', 'Parent'),
(4, 'aa', '123456', 'aa@aa.aa', '$2y$10$2H0/3Q91.K0i.Gn2M4QIH.U73ax2k8OFl07Wo8N7BhmW94W952OCW', 'aktif', 'ortu', 'Parent'),
(5, 'gggg', '082127981234', 'bawahtanah1945@gmail.com', '$2y$10$uMgwEeAG64sXBaO0hPrWfeoGT.i2ybVNhe1102XeVZReEV/QUpEFK', 'aktif', 'ortu', 'Parent');

-- --------------------------------------------------------

--
-- Table structure for table `mnl_pembayaran`
--

CREATE TABLE `mnl_pembayaran` (
  `id_mnl_pembayaran` int(11) NOT NULL,
  `id_mnl_siswa` int(11) NOT NULL,
  `id_mnl_spp` int(11) NOT NULL,
  `bulan` varchar(20) NOT NULL,
  `tahun` varchar(10) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` time NOT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `mnl_pembayaran`
--

INSERT INTO `mnl_pembayaran` (`id_mnl_pembayaran`, `id_mnl_siswa`, `id_mnl_spp`, `bulan`, `tahun`, `tanggal`, `waktu`, `status`) VALUES
(1, 1, 3, '06', '2022', '0000-00-00', '00:00:00', 'belum'),
(2, 2, 3, '06', '2022', '0000-00-00', '00:00:00', 'belum'),
(3, 3, 3, '06', '2022', '0000-00-00', '00:00:00', 'belum'),
(4, 4, 3, '06', '2022', '0000-00-00', '00:00:00', 'belum'),
(5, 5, 3, '06', '2022', '0000-00-00', '00:00:00', 'belum'),
(6, 6, 4, '06', '2022', '0000-00-00', '00:00:00', 'belum');

-- --------------------------------------------------------

--
-- Table structure for table `mnl_pmb_regist`
--

CREATE TABLE `mnl_pmb_regist` (
  `id_mnl_pmb_regist` int(11) NOT NULL,
  `id_mnl_siswa` int(11) NOT NULL,
  `id_mnl_regist` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `mnl_regist`
--

CREATE TABLE `mnl_regist` (
  `id_mnl_regist` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nominal` varchar(50) NOT NULL,
  `id_mnl_club` int(11) NOT NULL,
  `id_mnl_kelas` int(11) NOT NULL,
  `id_mnl_lokasi` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `mnl_regist`
--

INSERT INTO `mnl_regist` (`id_mnl_regist`, `nama`, `nominal`, `id_mnl_club`, `id_mnl_kelas`, `id_mnl_lokasi`) VALUES
(1, 'MTR-AURIGA-KIDDOS-500', '500000', 1, 2, 2),
(4, 'SPT-AURIGA-MINIMIX-350', '350000', 2, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `mnl_siswa`
--

CREATE TABLE `mnl_siswa` (
  `id_mnl_siswa` int(11) NOT NULL,
  `id_mnl_kelas` int(11) NOT NULL,
  `id_mnl_lokasi` int(11) NOT NULL,
  `id_mnl_club` int(11) NOT NULL,
  `id_mnl_regist` int(11) NOT NULL,
  `id_mnl_spp` int(11) NOT NULL,
  `nama` varchar(200) NOT NULL,
  `ortu` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `no_hp` varchar(50) NOT NULL,
  `email` varchar(200) NOT NULL,
  `tgl_lahir` date NOT NULL,
  `password` varchar(200) NOT NULL,
  `regist` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `level` varchar(50) NOT NULL,
  `jabatan` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `mnl_siswa`
--

INSERT INTO `mnl_siswa` (`id_mnl_siswa`, `id_mnl_kelas`, `id_mnl_lokasi`, `id_mnl_club`, `id_mnl_regist`, `id_mnl_spp`, `nama`, `ortu`, `alamat`, `no_hp`, `email`, `tgl_lahir`, `password`, `regist`, `status`, `level`, `jabatan`) VALUES
(1, 2, 2, 1, 1, 3, 'Ghazali Alvaro Ramdhani', 'Hardi Setia Ramdhani', 'Palgading 03/18, Sinduharjo, Ngaglik, Sleman, D.I. Yogyakarta', '081916664548', 'ramdhanihardi@gmail.com', '2015-04-10', '$2y$10$kK1f8r/bnCP5FsS6sWwcaOiO4HeuMwmNRW18ozGpef53dcKQkA2Se', 0, 'aktif', 'siswa', 'Siswa'),
(2, 2, 2, 1, 1, 3, 'Ibrahim Azka Ramdhani', 'Hardi Setia Ramdhani', 'Palgading 03/18, Sinduharjo, Ngaglik, Sleman, D.I. Yogyakarta', '081916664548', 'ramdhanihardi@gmail.com', '2018-02-12', '$2y$10$Zx136zWGtoMTprSF/R4wWu1nVnG3rSGID91e0.cS5mN3io5TNcIiC', 0, 'aktif', 'siswa', 'Siswa'),
(3, 2, 2, 1, 1, 3, 'tes', 'tes', 'tes', '123', 'tes@tes.tes', '2010-05-17', '$2y$10$DB7bpbtVbjShYoWbMHi6BuigbddNyNfZnchfSCQQCOkHB75gSsY3G', 0, 'aktif', 'siswa', 'Siswa'),
(4, 2, 2, 1, 1, 3, 'set', 'set', 'set', '1234', 'set@set.set', '2013-01-24', '$2y$10$y1IOQCRwIcGYC92xGka/DOaemEmdOZGcMFreHr7GVJqOsuyDGTFaO', 0, 'aktif', 'siswa', 'Siswa'),
(5, 2, 2, 2, 2, 3, 'aa', 'aa', 'aa', '123456', 'aa@aa.aa', '2013-02-05', '$2y$10$PHLYZPnEFmLXwf1pf2Cpr./iES50Wu5lBRopS4vtacG0870wroP8C', 0, 'aktif', 'siswa', 'Siswa'),
(6, 2, 2, 2, 4, 4, 'ccc', 'bbb', 'Sidorejo, Kel. Caturharjos', '082127981234', 'kk@kk.kk', '2007-07-27', '$2y$10$czKqP7DGVbpTOXKhzlH8VukcAsAUmMjhn1thpoZWi6H1/7VULO/oK', 0, 'aktif', 'siswa', 'Siswa');

-- --------------------------------------------------------

--
-- Table structure for table `mnl_spp`
--

CREATE TABLE `mnl_spp` (
  `id_mnl_spp` int(11) NOT NULL,
  `nama` varchar(200) NOT NULL,
  `nominal` varchar(50) NOT NULL,
  `id_mnl_club` int(11) NOT NULL,
  `id_mnl_kelas` int(11) NOT NULL,
  `id_mnl_lokasi` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `mnl_spp`
--

INSERT INTO `mnl_spp` (`id_mnl_spp`, `nama`, `nominal`, `id_mnl_club`, `id_mnl_kelas`, `id_mnl_lokasi`) VALUES
(3, 'MTR-AURIGA-KIDDOS-500', '500000', 1, 2, 2),
(4, 'SPT-AURIGA-MINIMIX-300', '300000', 2, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `mnl_tmp_pembayaran`
--

CREATE TABLE `mnl_tmp_pembayaran` (
  `id_mnl_tmp_pembayaran` int(11) NOT NULL,
  `id_mnl_siswa` int(11) NOT NULL,
  `id_mnl_spp` int(11) NOT NULL,
  `bulan` varchar(20) NOT NULL,
  `tahun` varchar(10) NOT NULL,
  `nominal` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `mnl_transaksi`
--

CREATE TABLE `mnl_transaksi` (
  `id_mnl_transaksi` int(11) NOT NULL,
  `id_mnl_siswa` int(11) NOT NULL,
  `jumlah` varchar(50) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` int(11) NOT NULL,
  `ket` text NOT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `newsletter`
--

CREATE TABLE `newsletter` (
  `id_newsletter` int(11) NOT NULL,
  `email` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `partner`
--

CREATE TABLE `partner` (
  `id_partner` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `logo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `partner`
--

INSERT INTO `partner` (`id_partner`, `nama`, `logo`) VALUES
(9, 'PT. Satu', 'brand-1-5.png'),
(10, 'PT. Dua', 'brand-1-51.png'),
(11, 'PT. Tiga', 'brand-1-52.png'),
(12, 'PT. Empat', 'brand-1-53.png'),
(13, 'PT. Lima', 'brand-1-54.png');

-- --------------------------------------------------------

--
-- Table structure for table `simimin`
--

CREATE TABLE `simimin` (
  `minid` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `hp` varchar(20) NOT NULL,
  `level` varchar(20) NOT NULL,
  `jabatan` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `simimin`
--

INSERT INTO `simimin` (`minid`, `nama`, `username`, `password`, `hp`, `level`, `jabatan`) VALUES
(1, 'Dhila', 'tes', '$2y$10$rcypixINnA5tBvoaZ5.taOBS3Iwx5AxgRVQpr6LhddF/XILJn96mq', '', 'mimin', 'Admin Utama');

-- --------------------------------------------------------

--
-- Table structure for table `v_kandang`
--

CREATE TABLE `v_kandang` (
  `id_kandang` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `pic` varchar(100) DEFAULT NULL,
  `hp` varchar(50) DEFAULT NULL,
  `alamat` text NOT NULL,
  `keterangan` text NOT NULL,
  `tgl_berdiri` date NOT NULL,
  `status` varchar(50) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` time NOT NULL,
  `hapus` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `v_kandang`
--

INSERT INTO `v_kandang` (`id_kandang`, `nama`, `pic`, `hp`, `alamat`, `keterangan`, `tgl_berdiri`, `status`, `tanggal`, `waktu`, `hapus`) VALUES
(1, 'Vigaza 2', NULL, NULL, 'Dsn. Sruni 01/17, Wuirsari, Cangkringan, Sleman', 'Kandang Ke-2', '2024-07-06', 'aktif', '2024-07-06', '15:13:35', 0),
(2, 'Vigaza', NULL, NULL, 'Palgading 03/18, Sinduharjo, Ngaglik, Sleman', 'tes', '2024-07-09', 'aktif', '2024-07-09', '02:11:27', 0);

-- --------------------------------------------------------

--
-- Table structure for table `v_pb_mati`
--

CREATE TABLE `v_pb_mati` (
  `id_pb_mati` int(11) NOT NULL,
  `id_pembesaran` int(11) NOT NULL,
  `tgl_input` date NOT NULL,
  `jumlah` varchar(20) NOT NULL,
  `keterangan` text NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` time NOT NULL,
  `hapus` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `v_pb_pakan`
--

CREATE TABLE `v_pb_pakan` (
  `id_pb_pakan` int(11) NOT NULL,
  `id_pembesaran` int(11) NOT NULL,
  `tgl_input` date NOT NULL,
  `jumlah` varchar(20) NOT NULL,
  `keterangan` text NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` time NOT NULL,
  `hapus` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `v_pembesaran`
--

CREATE TABLE `v_pembesaran` (
  `id_pembesaran` int(11) NOT NULL,
  `id_kandang` int(11) NOT NULL,
  `periode` varchar(200) NOT NULL,
  `tgl_masuk` date NOT NULL,
  `harga` varchar(100) NOT NULL,
  `populasi` varchar(100) NOT NULL,
  `keterangan` text NOT NULL,
  `status` varchar(50) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` time NOT NULL,
  `hapus` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `v_pembesaran`
--

INSERT INTO `v_pembesaran` (`id_pembesaran`, `id_kandang`, `periode`, `tgl_masuk`, `harga`, `populasi`, `keterangan`, `status`, `tanggal`, `waktu`, `hapus`) VALUES
(1, 2, 'Bulan Juni', '2024-06-17', '2700', '3680', 'tes', 'aktif', '2024-07-12', '08:10:21', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bank`
--
ALTER TABLE `bank`
  ADD PRIMARY KEY (`id_bank`);

--
-- Indexes for table `base`
--
ALTER TABLE `base`
  ADD PRIMARY KEY (`id_base`);

--
-- Indexes for table `campaign`
--
ALTER TABLE `campaign`
  ADD PRIMARY KEY (`id_campaign`);

--
-- Indexes for table `doa`
--
ALTER TABLE `doa`
  ADD PRIMARY KEY (`id_doa`);

--
-- Indexes for table `donasi`
--
ALTER TABLE `donasi`
  ADD PRIMARY KEY (`id_donasi`);

--
-- Indexes for table `donor`
--
ALTER TABLE `donor`
  ADD PRIMARY KEY (`id_donor`);

--
-- Indexes for table `fundraiser`
--
ALTER TABLE `fundraiser`
  ADD PRIMARY KEY (`id_fundraiser`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `keunggulan`
--
ALTER TABLE `keunggulan`
  ADD PRIMARY KEY (`id_keunggulan`);

--
-- Indexes for table `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`id_log`);

--
-- Indexes for table `mnl_base`
--
ALTER TABLE `mnl_base`
  ADD PRIMARY KEY (`id_mnl_base`);

--
-- Indexes for table `mnl_club`
--
ALTER TABLE `mnl_club`
  ADD PRIMARY KEY (`id_mnl_club`);

--
-- Indexes for table `mnl_kelas`
--
ALTER TABLE `mnl_kelas`
  ADD PRIMARY KEY (`id_mnl_kelas`);

--
-- Indexes for table `mnl_lokasi`
--
ALTER TABLE `mnl_lokasi`
  ADD PRIMARY KEY (`id_mnl_lokasi`);

--
-- Indexes for table `mnl_ortu`
--
ALTER TABLE `mnl_ortu`
  ADD PRIMARY KEY (`id_mnl_ortu`);

--
-- Indexes for table `mnl_pembayaran`
--
ALTER TABLE `mnl_pembayaran`
  ADD PRIMARY KEY (`id_mnl_pembayaran`);

--
-- Indexes for table `mnl_pmb_regist`
--
ALTER TABLE `mnl_pmb_regist`
  ADD PRIMARY KEY (`id_mnl_pmb_regist`);

--
-- Indexes for table `mnl_regist`
--
ALTER TABLE `mnl_regist`
  ADD PRIMARY KEY (`id_mnl_regist`);

--
-- Indexes for table `mnl_siswa`
--
ALTER TABLE `mnl_siswa`
  ADD PRIMARY KEY (`id_mnl_siswa`);

--
-- Indexes for table `mnl_spp`
--
ALTER TABLE `mnl_spp`
  ADD PRIMARY KEY (`id_mnl_spp`);

--
-- Indexes for table `mnl_tmp_pembayaran`
--
ALTER TABLE `mnl_tmp_pembayaran`
  ADD PRIMARY KEY (`id_mnl_tmp_pembayaran`);

--
-- Indexes for table `mnl_transaksi`
--
ALTER TABLE `mnl_transaksi`
  ADD PRIMARY KEY (`id_mnl_transaksi`);

--
-- Indexes for table `newsletter`
--
ALTER TABLE `newsletter`
  ADD PRIMARY KEY (`id_newsletter`);

--
-- Indexes for table `partner`
--
ALTER TABLE `partner`
  ADD PRIMARY KEY (`id_partner`);

--
-- Indexes for table `simimin`
--
ALTER TABLE `simimin`
  ADD PRIMARY KEY (`minid`);

--
-- Indexes for table `v_kandang`
--
ALTER TABLE `v_kandang`
  ADD PRIMARY KEY (`id_kandang`);

--
-- Indexes for table `v_pb_mati`
--
ALTER TABLE `v_pb_mati`
  ADD PRIMARY KEY (`id_pb_mati`);

--
-- Indexes for table `v_pb_pakan`
--
ALTER TABLE `v_pb_pakan`
  ADD PRIMARY KEY (`id_pb_pakan`);

--
-- Indexes for table `v_pembesaran`
--
ALTER TABLE `v_pembesaran`
  ADD PRIMARY KEY (`id_pembesaran`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bank`
--
ALTER TABLE `bank`
  MODIFY `id_bank` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `base`
--
ALTER TABLE `base`
  MODIFY `id_base` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `campaign`
--
ALTER TABLE `campaign`
  MODIFY `id_campaign` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `doa`
--
ALTER TABLE `doa`
  MODIFY `id_doa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `donasi`
--
ALTER TABLE `donasi`
  MODIFY `id_donasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `donor`
--
ALTER TABLE `donor`
  MODIFY `id_donor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `fundraiser`
--
ALTER TABLE `fundraiser`
  MODIFY `id_fundraiser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `keunggulan`
--
ALTER TABLE `keunggulan`
  MODIFY `id_keunggulan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `log`
--
ALTER TABLE `log`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=197;

--
-- AUTO_INCREMENT for table `mnl_base`
--
ALTER TABLE `mnl_base`
  MODIFY `id_mnl_base` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mnl_club`
--
ALTER TABLE `mnl_club`
  MODIFY `id_mnl_club` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `mnl_kelas`
--
ALTER TABLE `mnl_kelas`
  MODIFY `id_mnl_kelas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `mnl_lokasi`
--
ALTER TABLE `mnl_lokasi`
  MODIFY `id_mnl_lokasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `mnl_ortu`
--
ALTER TABLE `mnl_ortu`
  MODIFY `id_mnl_ortu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `mnl_pembayaran`
--
ALTER TABLE `mnl_pembayaran`
  MODIFY `id_mnl_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `mnl_pmb_regist`
--
ALTER TABLE `mnl_pmb_regist`
  MODIFY `id_mnl_pmb_regist` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mnl_regist`
--
ALTER TABLE `mnl_regist`
  MODIFY `id_mnl_regist` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `mnl_siswa`
--
ALTER TABLE `mnl_siswa`
  MODIFY `id_mnl_siswa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `mnl_spp`
--
ALTER TABLE `mnl_spp`
  MODIFY `id_mnl_spp` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `mnl_tmp_pembayaran`
--
ALTER TABLE `mnl_tmp_pembayaran`
  MODIFY `id_mnl_tmp_pembayaran` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mnl_transaksi`
--
ALTER TABLE `mnl_transaksi`
  MODIFY `id_mnl_transaksi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `newsletter`
--
ALTER TABLE `newsletter`
  MODIFY `id_newsletter` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `partner`
--
ALTER TABLE `partner`
  MODIFY `id_partner` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `simimin`
--
ALTER TABLE `simimin`
  MODIFY `minid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `v_kandang`
--
ALTER TABLE `v_kandang`
  MODIFY `id_kandang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `v_pb_mati`
--
ALTER TABLE `v_pb_mati`
  MODIFY `id_pb_mati` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `v_pb_pakan`
--
ALTER TABLE `v_pb_pakan`
  MODIFY `id_pb_pakan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `v_pembesaran`
--
ALTER TABLE `v_pembesaran`
  MODIFY `id_pembesaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
