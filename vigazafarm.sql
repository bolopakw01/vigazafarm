-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 27 Agu 2025 pada 07.52
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
-- Database: `vigazafarm`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembesaran`
--

CREATE TABLE `pembesaran` (
  `id_pembesaran` int(11) NOT NULL,
  `id_penetasan` int(11) NOT NULL,
  `tanggal_masuk` date NOT NULL,
  `jumlah_bibit` int(11) NOT NULL,
  `jenis_kelamin` enum('jantan','betina','campuran') DEFAULT 'campuran'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `penetasan`
--

CREATE TABLE `penetasan` (
  `id_penetasan` int(11) NOT NULL,
  `tanggal_simpan_telur` date NOT NULL,
  `jumlah_telur` int(11) NOT NULL,
  `tanggal_menetas` date NOT NULL,
  `jumlah_menetas` int(11) NOT NULL,
  `jumlah_doc` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `produksi`
--

CREATE TABLE `produksi` (
  `id_produksi` int(11) NOT NULL,
  `tanggal_mulai_produksi` date NOT NULL,
  `jumlah_indukan` int(11) NOT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `produksi_kematian`
--

CREATE TABLE `produksi_kematian` (
  `id_kematian` int(11) NOT NULL,
  `id_produksi` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jumlah_mati` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `produksi_pakan`
--

CREATE TABLE `produksi_pakan` (
  `id_pakan` int(11) NOT NULL,
  `id_produksi` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jumlah_pakan` float NOT NULL COMMENT 'dalam Kg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `produksi_telur`
--

CREATE TABLE `produksi_telur` (
  `id_telur` int(11) NOT NULL,
  `id_produksi` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jumlah_telur` int(11) NOT NULL COMMENT 'dalam butir'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `simimin`
--

CREATE TABLE `simimin` (
  `minid` int(11) NOT NULL,
  `minuser` varchar(50) NOT NULL,
  `minpass` varchar(255) NOT NULL,
  `minnama` varchar(50) NOT NULL,
  `minlevel` varchar(20) NOT NULL,
  `foto` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `simimin`
--

INSERT INTO `simimin` (`minid`, `minuser`, `minpass`, `minnama`, `minlevel`, `foto`) VALUES
(1, 'admin', '$2y$10$38/s8g9Q1D5fA.5.7.p6tuxrH5nKOPSVq.44cYNb4a3iG53v4Q/f.', 'Administrator', 'mimin', 'user.png');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `pembesaran`
--
ALTER TABLE `pembesaran`
  ADD PRIMARY KEY (`id_pembesaran`);

--
-- Indeks untuk tabel `penetasan`
--
ALTER TABLE `penetasan`
  ADD PRIMARY KEY (`id_penetasan`);

--
-- Indeks untuk tabel `produksi`
--
ALTER TABLE `produksi`
  ADD PRIMARY KEY (`id_produksi`);

--
-- Indeks untuk tabel `produksi_kematian`
--
ALTER TABLE `produksi_kematian`
  ADD PRIMARY KEY (`id_kematian`);

--
-- Indeks untuk tabel `produksi_pakan`
--
ALTER TABLE `produksi_pakan`
  ADD PRIMARY KEY (`id_pakan`);

--
-- Indeks untuk tabel `produksi_telur`
--
ALTER TABLE `produksi_telur`
  ADD PRIMARY KEY (`id_telur`);

--
-- Indeks untuk tabel `simimin`
--
ALTER TABLE `simimin`
  ADD PRIMARY KEY (`minid`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `pembesaran`
--
ALTER TABLE `pembesaran`
  MODIFY `id_pembesaran` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `penetasan`
--
ALTER TABLE `penetasan`
  MODIFY `id_penetasan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `produksi`
--
ALTER TABLE `produksi`
  MODIFY `id_produksi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `produksi_kematian`
--
ALTER TABLE `produksi_kematian`
  MODIFY `id_kematian` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `produksi_pakan`
--
ALTER TABLE `produksi_pakan`
  MODIFY `id_pakan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `produksi_telur`
--
ALTER TABLE `produksi_telur`
  MODIFY `id_telur` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `simimin`
--
ALTER TABLE `simimin`
  MODIFY `minid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
