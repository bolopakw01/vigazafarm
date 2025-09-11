<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2025-08-31 01:06:22 --> Severity: error --> Exception: Call to undefined method M_penetasan::get_detail_penetasan() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\controllers\Penetasan.php 197
ERROR - 2025-08-31 01:06:26 --> Severity: error --> Exception: Call to undefined method M_penetasan::get_detail_penetasan() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\controllers\Penetasan.php 197
ERROR - 2025-08-31 01:07:18 --> Query error: Unknown column 'v_pembesaran.hapus' in 'where clause' - Invalid query: SELECT `v_kandang`.`id_kandang`, `v_kandang`.`nama` as `kandang`, `v_pembesaran`.*
FROM `v_pembesaran`
JOIN `v_kandang` ON `v_kandang`.`id_kandang`=`v_pembesaran`.`id_kandang`
WHERE `v_pembesaran`.`hapus` =0
ORDER BY `v_pembesaran`.`tgl_masuk`
ERROR - 2025-08-31 01:07:41 --> Severity: Notice --> Undefined property: stdClass::$suhu_optimal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\penetasan\tambah.php 135
ERROR - 2025-08-31 01:07:41 --> Severity: Notice --> Undefined property: stdClass::$kelembaban_optimal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\penetasan\tambah.php 136
ERROR - 2025-08-31 01:07:41 --> Severity: Notice --> Undefined property: stdClass::$nama_mesin D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\penetasan\tambah.php 138
ERROR - 2025-08-31 01:07:41 --> Severity: Notice --> Undefined property: stdClass::$suhu_optimal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\penetasan\tambah.php 135
ERROR - 2025-08-31 01:07:41 --> Severity: Notice --> Undefined property: stdClass::$kelembaban_optimal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\penetasan\tambah.php 136
ERROR - 2025-08-31 01:07:41 --> Severity: Notice --> Undefined property: stdClass::$nama_mesin D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\penetasan\tambah.php 138
ERROR - 2025-08-31 01:08:08 --> Query error: Unknown column 'v_pembesaran.hapus' in 'where clause' - Invalid query: SELECT `v_kandang`.`id_kandang`, `v_kandang`.`nama` as `kandang`, `v_pembesaran`.*
FROM `v_pembesaran`
JOIN `v_kandang` ON `v_kandang`.`id_kandang`=`v_pembesaran`.`id_kandang`
WHERE `v_pembesaran`.`hapus` =0
ORDER BY `v_pembesaran`.`tgl_masuk`
ERROR - 2025-08-31 01:09:52 --> Query error: Unknown column 'v_pembesaran.hapus' in 'where clause' - Invalid query: SELECT `v_kandang`.`id_kandang`, `v_kandang`.`nama` as `kandang`, `v_pembesaran`.*
FROM `v_pembesaran`
JOIN `v_kandang` ON `v_kandang`.`id_kandang`=`v_pembesaran`.`id_kandang`
WHERE `v_pembesaran`.`hapus` =0
ORDER BY `v_pembesaran`.`tgl_masuk`
ERROR - 2025-08-31 01:12:29 --> Query error: Unknown column 'v_pembesaran.hapus' in 'where clause' - Invalid query: SELECT `v_kandang`.`id_kandang`, `v_kandang`.`nama` as `kandang`, `v_pembesaran`.*
FROM `v_pembesaran`
JOIN `v_kandang` ON `v_kandang`.`id_kandang`=`v_pembesaran`.`id_kandang`
WHERE `v_pembesaran`.`hapus` =0
ORDER BY `v_pembesaran`.`tgl_masuk`
ERROR - 2025-08-31 01:23:38 --> Query error: Unknown column 'jml_saat_ini' in 'field list' - Invalid query: SELECT SUM(jml_saat_ini) as total_populasi
FROM `kos_pembesaran`
WHERE `status` = 'aktif'
ERROR - 2025-08-31 01:23:41 --> Severity: Notice --> Undefined property: stdClass::$waktu D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 164
ERROR - 2025-08-31 01:23:41 --> Severity: Notice --> Undefined property: stdClass::$satuan D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 176
ERROR - 2025-08-31 01:23:45 --> Query error: Unknown column 'jml_saat_ini' in 'field list' - Invalid query: SELECT SUM(jml_saat_ini) as total_populasi
FROM `kos_pembesaran`
WHERE `status` = 'aktif'
ERROR - 2025-08-31 01:30:14 --> Query error: Unknown column 'k.nama_kandang' in 'field list' - Invalid query: SELECT `p`.*, `k`.`nama_kandang`, `p`.`tanggal_mulai`, `p`.`jumlah_bibit` as `jumlah_awal`, `p`.`jumlah_hidup` as `jumlah_saat_ini`, `p`.`tanggal_selesai` as `target_panen`
FROM `kos_pembesaran` `p`
LEFT JOIN `kos_kandang` `k` ON `p`.`id_kandang` = `k`.`id_kandang`
WHERE `p`.`status` = 'aktif'
ORDER BY `p`.`tanggal_mulai` DESC
ERROR - 2025-08-31 01:30:15 --> Query error: Unknown column 'k.nama_kandang' in 'field list' - Invalid query: SELECT `p`.*, `k`.`nama_kandang`, `p`.`tanggal_mulai`, `p`.`jumlah_bibit` as `jumlah_awal`, `p`.`jumlah_hidup` as `jumlah_saat_ini`, `p`.`tanggal_selesai` as `target_panen`
FROM `kos_pembesaran` `p`
LEFT JOIN `kos_kandang` `k` ON `p`.`id_kandang` = `k`.`id_kandang`
WHERE `p`.`status` = 'aktif'
ORDER BY `p`.`tanggal_mulai` DESC
ERROR - 2025-08-31 01:30:16 --> Severity: Notice --> Undefined property: stdClass::$waktu D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 164
ERROR - 2025-08-31 01:30:16 --> Severity: Notice --> Undefined property: stdClass::$satuan D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 176
ERROR - 2025-08-31 01:30:21 --> Query error: Unknown column 'k.nama_kandang' in 'field list' - Invalid query: SELECT `p`.*, `k`.`nama_kandang`, `p`.`tanggal_mulai`, `p`.`jumlah_bibit` as `jumlah_awal`, `p`.`jumlah_hidup` as `jumlah_saat_ini`, `p`.`tanggal_selesai` as `target_panen`
FROM `kos_pembesaran` `p`
LEFT JOIN `kos_kandang` `k` ON `p`.`id_kandang` = `k`.`id_kandang`
WHERE `p`.`status` = 'aktif'
ORDER BY `p`.`tanggal_mulai` DESC
ERROR - 2025-08-31 01:44:34 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 179
ERROR - 2025-08-31 01:44:34 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 180
ERROR - 2025-08-31 01:44:34 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 01:44:34 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 01:44:48 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 179
ERROR - 2025-08-31 01:44:48 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 180
ERROR - 2025-08-31 01:44:48 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 01:44:48 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 01:45:10 --> Severity: Notice --> Undefined property: stdClass::$waktu D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 164
ERROR - 2025-08-31 01:45:10 --> Severity: Notice --> Undefined property: stdClass::$satuan D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 176
ERROR - 2025-08-31 01:46:10 --> Query error: Unknown column 'hapus' in 'where clause' - Invalid query: SELECT *
FROM `v_kandang`
WHERE `hapus` =0
ORDER BY `nama`
ERROR - 2025-08-31 01:46:11 --> Severity: error --> Exception: Call to undefined method M_min::rd_karyawan() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\controllers\Karyawan.php 54
ERROR - 2025-08-31 01:46:13 --> Query error: Unknown column 'hapus' in 'where clause' - Invalid query: SELECT *
FROM `v_kandang`
WHERE `hapus` =0
ORDER BY `nama`
ERROR - 2025-08-31 01:46:14 --> Severity: error --> Exception: Call to undefined method M_min::rd_karyawan() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\controllers\Karyawan.php 54
ERROR - 2025-08-31 01:46:17 --> Severity: Notice --> Undefined property: stdClass::$waktu D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 164
ERROR - 2025-08-31 01:46:17 --> Severity: Notice --> Undefined property: stdClass::$satuan D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 176
ERROR - 2025-08-31 01:46:18 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 179
ERROR - 2025-08-31 01:46:18 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 180
ERROR - 2025-08-31 01:46:18 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 01:46:18 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 01:46:19 --> Severity: Notice --> Undefined property: stdClass::$waktu D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 164
ERROR - 2025-08-31 01:46:19 --> Severity: Notice --> Undefined property: stdClass::$satuan D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 176
ERROR - 2025-08-31 01:46:33 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 179
ERROR - 2025-08-31 01:46:33 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 180
ERROR - 2025-08-31 01:46:33 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 01:46:33 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 01:56:31 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 179
ERROR - 2025-08-31 01:56:31 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 180
ERROR - 2025-08-31 01:56:31 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 01:56:31 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 01:56:44 --> Query error: Unknown column 'hapus' in 'where clause' - Invalid query: SELECT *
FROM `v_kandang`
WHERE `hapus` =0
ORDER BY `nama`
ERROR - 2025-08-31 02:11:29 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 179
ERROR - 2025-08-31 02:11:29 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 180
ERROR - 2025-08-31 02:11:29 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 02:11:29 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 02:11:34 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 179
ERROR - 2025-08-31 02:11:34 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 180
ERROR - 2025-08-31 02:11:34 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 02:11:34 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 02:11:37 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 179
ERROR - 2025-08-31 02:11:37 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 180
ERROR - 2025-08-31 02:11:37 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 02:11:37 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 02:13:03 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 179
ERROR - 2025-08-31 02:13:03 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 180
ERROR - 2025-08-31 02:13:03 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 02:13:03 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 02:27:26 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 179
ERROR - 2025-08-31 02:27:26 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 180
ERROR - 2025-08-31 02:27:26 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 02:27:26 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 02:27:39 --> Query error: Unknown column 'hapus' in 'where clause' - Invalid query: SELECT *
FROM `v_kandang`
WHERE `hapus` =0
ORDER BY `nama`
ERROR - 2025-08-31 08:11:44 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 179
ERROR - 2025-08-31 08:11:44 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 180
ERROR - 2025-08-31 08:11:44 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 08:11:44 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 03:12:50 --> Severity: Notice --> Undefined variable: thisPage D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 743
ERROR - 2025-08-31 03:12:50 --> Severity: Notice --> Undefined variable: thisPage D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 753
ERROR - 2025-08-31 03:12:50 --> Severity: Notice --> Undefined variable: thisPg D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 757
ERROR - 2025-08-31 03:12:50 --> Severity: Notice --> Undefined variable: thisPg D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 758
ERROR - 2025-08-31 03:12:50 --> Severity: Notice --> Undefined variable: thisPg D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 759
ERROR - 2025-08-31 03:12:50 --> Severity: Notice --> Undefined variable: thisPg D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 760
ERROR - 2025-08-31 03:12:50 --> Severity: Notice --> Undefined variable: statistik D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 624
ERROR - 2025-08-31 03:12:50 --> Severity: Notice --> Trying to get property 'total_ayam_hidup' of non-object D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 624
ERROR - 2025-08-31 03:12:50 --> Severity: Notice --> Undefined variable: statistik D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 635
ERROR - 2025-08-31 03:12:50 --> Severity: Notice --> Trying to get property 'rata_telur_harian' of non-object D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 635
ERROR - 2025-08-31 03:12:50 --> Severity: Notice --> Undefined property: stdClass::$waktu D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 730
ERROR - 2025-08-31 03:12:50 --> Severity: Notice --> Undefined property: stdClass::$satuan D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 742
ERROR - 2025-08-31 03:12:54 --> Severity: Notice --> Undefined variable: thisPage D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 743
ERROR - 2025-08-31 03:12:54 --> Severity: Notice --> Undefined variable: thisPage D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 753
ERROR - 2025-08-31 03:12:54 --> Severity: Notice --> Undefined variable: thisPg D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 757
ERROR - 2025-08-31 03:12:54 --> Severity: Notice --> Undefined variable: thisPg D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 758
ERROR - 2025-08-31 03:12:54 --> Severity: Notice --> Undefined variable: thisPg D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 759
ERROR - 2025-08-31 03:12:54 --> Severity: Notice --> Undefined variable: thisPg D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 760
ERROR - 2025-08-31 03:12:54 --> Severity: Notice --> Undefined variable: statistik D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 624
ERROR - 2025-08-31 03:12:54 --> Severity: Notice --> Trying to get property 'total_ayam_hidup' of non-object D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 624
ERROR - 2025-08-31 03:12:54 --> Severity: Notice --> Undefined variable: statistik D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 635
ERROR - 2025-08-31 03:12:54 --> Severity: Notice --> Trying to get property 'rata_telur_harian' of non-object D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 635
ERROR - 2025-08-31 03:12:54 --> Severity: Notice --> Undefined property: stdClass::$waktu D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 730
ERROR - 2025-08-31 03:12:54 --> Severity: Notice --> Undefined property: stdClass::$satuan D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 742
ERROR - 2025-08-31 03:12:58 --> Severity: Notice --> Undefined variable: thisPage D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 743
ERROR - 2025-08-31 03:12:58 --> Severity: Notice --> Undefined variable: thisPage D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 753
ERROR - 2025-08-31 03:12:58 --> Severity: Notice --> Undefined variable: thisPg D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 757
ERROR - 2025-08-31 03:12:58 --> Severity: Notice --> Undefined variable: thisPg D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 758
ERROR - 2025-08-31 03:12:58 --> Severity: Notice --> Undefined variable: thisPg D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 759
ERROR - 2025-08-31 03:12:58 --> Severity: Notice --> Undefined variable: thisPg D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 760
ERROR - 2025-08-31 03:12:58 --> 404 Page Not Found: Produksi/assets
ERROR - 2025-08-31 08:13:41 --> Severity: Notice --> Undefined variable: thisPage D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 743
ERROR - 2025-08-31 08:13:41 --> Severity: Notice --> Undefined variable: thisPage D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 753
ERROR - 2025-08-31 08:13:41 --> Severity: Notice --> Undefined variable: thisPg D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 757
ERROR - 2025-08-31 08:13:41 --> Severity: Notice --> Undefined variable: thisPg D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 758
ERROR - 2025-08-31 08:13:41 --> Severity: Notice --> Undefined variable: thisPg D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 759
ERROR - 2025-08-31 08:13:41 --> Severity: Notice --> Undefined variable: thisPg D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 760
ERROR - 2025-08-31 08:13:41 --> Severity: Notice --> Undefined variable: statistik D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 624
ERROR - 2025-08-31 08:13:41 --> Severity: Notice --> Trying to get property 'total_ayam_hidup' of non-object D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 624
ERROR - 2025-08-31 08:13:41 --> Severity: Notice --> Undefined variable: statistik D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 635
ERROR - 2025-08-31 08:13:41 --> Severity: Notice --> Trying to get property 'rata_telur_harian' of non-object D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 635
ERROR - 2025-08-31 08:13:41 --> Severity: Notice --> Undefined property: stdClass::$waktu D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 730
ERROR - 2025-08-31 08:13:41 --> Severity: Notice --> Undefined property: stdClass::$satuan D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 742
ERROR - 2025-08-31 08:14:15 --> Severity: Notice --> Undefined variable: thisPage D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 743
ERROR - 2025-08-31 08:14:15 --> Severity: Notice --> Undefined variable: thisPage D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 746
ERROR - 2025-08-31 08:14:15 --> Severity: Notice --> Undefined variable: thisPage D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 748
ERROR - 2025-08-31 08:14:15 --> Severity: Notice --> Undefined variable: thisPage D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 753
ERROR - 2025-08-31 08:14:15 --> Severity: Notice --> Undefined variable: thisPg D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 757
ERROR - 2025-08-31 08:14:15 --> Severity: Notice --> Undefined variable: thisPg D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 758
ERROR - 2025-08-31 08:14:15 --> Severity: Notice --> Undefined variable: thisPg D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 759
ERROR - 2025-08-31 08:14:15 --> Severity: Notice --> Undefined variable: thisPg D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 760
ERROR - 2025-08-31 08:14:15 --> Severity: Notice --> Undefined variable: thisPage D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 768
ERROR - 2025-08-31 08:14:15 --> Severity: Notice --> Undefined variable: thisPg D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 772
ERROR - 2025-08-31 08:14:15 --> Severity: Notice --> Undefined variable: thisPg D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 773
ERROR - 2025-08-31 08:14:15 --> Severity: Notice --> Undefined variable: thisPage D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\template\sidebar.php 779
ERROR - 2025-08-31 08:14:15 --> Severity: Notice --> Undefined variable: statistik D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 624
ERROR - 2025-08-31 08:14:15 --> Severity: Notice --> Trying to get property 'total_ayam_hidup' of non-object D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 624
ERROR - 2025-08-31 08:14:15 --> Severity: Notice --> Undefined variable: statistik D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 635
ERROR - 2025-08-31 08:14:15 --> Severity: Notice --> Trying to get property 'rata_telur_harian' of non-object D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 635
ERROR - 2025-08-31 08:14:15 --> Severity: Notice --> Undefined property: stdClass::$waktu D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 730
ERROR - 2025-08-31 08:14:15 --> Severity: Notice --> Undefined property: stdClass::$satuan D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\produksi\index.php 742
ERROR - 2025-08-31 08:14:37 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 179
ERROR - 2025-08-31 08:14:37 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 180
ERROR - 2025-08-31 08:14:37 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 08:14:37 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 08:21:46 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 179
ERROR - 2025-08-31 08:21:46 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 180
ERROR - 2025-08-31 08:21:46 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 08:21:46 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 08:22:08 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 179
ERROR - 2025-08-31 08:22:08 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 180
ERROR - 2025-08-31 08:22:08 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 08:22:08 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 08:22:39 --> Severity: error --> Exception: Call to undefined method M_penetasan::get_detail_penetasan() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\controllers\Penetasan.php 197
ERROR - 2025-08-31 08:28:32 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 179
ERROR - 2025-08-31 08:28:32 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 180
ERROR - 2025-08-31 08:28:32 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 08:28:32 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 03:35:53 --> 404 Page Not Found: Assets/images
ERROR - 2025-08-31 03:35:53 --> 404 Page Not Found: Assets/images
ERROR - 2025-08-31 03:35:53 --> 404 Page Not Found: Assets/images
ERROR - 2025-08-31 03:35:53 --> 404 Page Not Found: Assets/images
ERROR - 2025-08-31 08:35:58 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 179
ERROR - 2025-08-31 08:35:58 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 180
ERROR - 2025-08-31 08:35:58 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 08:35:58 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 03:36:06 --> 404 Page Not Found: Assets/images
ERROR - 2025-08-31 03:36:06 --> 404 Page Not Found: Assets/images
ERROR - 2025-08-31 03:36:06 --> 404 Page Not Found: Assets/images
ERROR - 2025-08-31 03:36:06 --> 404 Page Not Found: Assets/images
ERROR - 2025-08-31 03:36:08 --> 404 Page Not Found: Produksi/assets
ERROR - 2025-08-31 03:36:19 --> 404 Page Not Found: Produksi/assets
ERROR - 2025-08-31 08:36:22 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Shared\String.php 529
ERROR - 2025-08-31 08:36:22 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Shared\String.php 530
ERROR - 2025-08-31 08:36:22 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Shared\String.php 536
ERROR - 2025-08-31 08:36:22 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Shared\String.php 536
ERROR - 2025-08-31 08:36:22 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Shared\String.php 537
ERROR - 2025-08-31 08:36:22 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Shared\String.php 537
ERROR - 2025-08-31 08:36:22 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Calculation.php 2186
ERROR - 2025-08-31 08:36:22 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Calculation.php 2186
ERROR - 2025-08-31 08:36:22 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Calculation.php 2294
ERROR - 2025-08-31 08:36:22 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Calculation.php 2294
ERROR - 2025-08-31 08:36:22 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Calculation.php 2296
ERROR - 2025-08-31 08:36:22 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Calculation.php 2372
ERROR - 2025-08-31 08:36:22 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Calculation.php 2374
ERROR - 2025-08-31 08:36:22 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Calculation.php 2383
ERROR - 2025-08-31 08:36:22 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Calculation.php 2632
ERROR - 2025-08-31 08:36:22 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Calculation.php 2761
ERROR - 2025-08-31 08:36:22 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Calculation.php 2763
ERROR - 2025-08-31 08:36:22 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Calculation.php 2764
ERROR - 2025-08-31 08:36:22 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Calculation.php 3039
ERROR - 2025-08-31 08:36:22 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Calculation.php 3039
ERROR - 2025-08-31 08:36:22 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Calculation.php 3042
ERROR - 2025-08-31 08:36:22 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Calculation.php 3043
ERROR - 2025-08-31 08:36:22 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Calculation.php 3459
ERROR - 2025-08-31 08:36:22 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Calculation.php 3459
ERROR - 2025-08-31 08:36:22 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Calculation.php 3501
ERROR - 2025-08-31 08:36:22 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Calculation.php 3505
ERROR - 2025-08-31 08:36:22 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Calculation.php 3558
ERROR - 2025-08-31 08:36:22 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Calculation.php 3559
ERROR - 2025-08-31 08:36:22 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Worksheet\AutoFilter.php 729
ERROR - 2025-08-31 08:36:22 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Worksheet\AutoFilter.php 729
ERROR - 2025-08-31 08:36:23 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Cell.php 772
ERROR - 2025-08-31 08:36:23 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Cell.php 773
ERROR - 2025-08-31 08:36:23 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Cell.php 776
ERROR - 2025-08-31 08:36:23 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Cell.php 777
ERROR - 2025-08-31 08:36:23 --> Severity: 8192 --> Array and string offset access syntax with curly braces is deprecated D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\libraries\PHPExcel\Cell.php 777
ERROR - 2025-08-31 08:36:26 --> Query error: Unknown column 'hapus' in 'where clause' - Invalid query: SELECT *
FROM `v_kandang`
WHERE `hapus` =0
ORDER BY `nama`
ERROR - 2025-08-31 08:36:28 --> Severity: error --> Exception: Call to undefined method M_min::rd_karyawan() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\controllers\Karyawan.php 54
ERROR - 2025-08-31 08:36:43 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 179
ERROR - 2025-08-31 08:36:43 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 180
ERROR - 2025-08-31 08:36:43 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 08:36:43 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 03:36:44 --> 404 Page Not Found: Assets/images
ERROR - 2025-08-31 03:36:44 --> 404 Page Not Found: Assets/images
ERROR - 2025-08-31 03:36:44 --> 404 Page Not Found: Assets/images
ERROR - 2025-08-31 03:36:44 --> 404 Page Not Found: Assets/images
ERROR - 2025-08-31 08:36:45 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 179
ERROR - 2025-08-31 08:36:45 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 180
ERROR - 2025-08-31 08:36:45 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 08:36:45 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 08:40:13 --> Query error: Unknown column 'hapus' in 'where clause' - Invalid query: SELECT *
FROM `v_kandang`
WHERE `hapus` =0
ORDER BY `nama`
ERROR - 2025-08-31 08:40:27 --> Severity: error --> Exception: Call to undefined method M_min::rd_karyawan() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\controllers\Karyawan.php 54
ERROR - 2025-08-31 08:46:50 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 179
ERROR - 2025-08-31 08:46:50 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 180
ERROR - 2025-08-31 08:46:50 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 08:46:50 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 08:46:51 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 179
ERROR - 2025-08-31 08:46:51 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 180
ERROR - 2025-08-31 08:46:51 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 08:46:51 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 03:46:52 --> 404 Page Not Found: Assets/images
ERROR - 2025-08-31 03:46:52 --> 404 Page Not Found: Assets/images
ERROR - 2025-08-31 03:46:52 --> 404 Page Not Found: Assets/images
ERROR - 2025-08-31 03:46:52 --> 404 Page Not Found: Assets/images
ERROR - 2025-08-31 08:46:57 --> Query error: Table 'vigazafarm_clean.mnl_siswa' doesn't exist - Invalid query: SELECT `id_mnl_siswa`
FROM `mnl_siswa`
ERROR - 2025-08-31 08:46:58 --> Query error: Table 'vigazafarm_clean.mnl_siswa' doesn't exist - Invalid query: SELECT `id_mnl_siswa`
FROM `mnl_siswa`
ERROR - 2025-08-31 08:47:01 --> Query error: Table 'vigazafarm_clean.mnl_siswa' doesn't exist - Invalid query: SELECT `id_mnl_siswa`
FROM `mnl_siswa`
ERROR - 2025-08-31 08:47:13 --> Query error: Table 'vigazafarm_clean.mnl_siswa' doesn't exist - Invalid query: SELECT `id_mnl_siswa`
FROM `mnl_siswa`
ERROR - 2025-08-31 08:50:47 --> Severity: Notice --> Undefined property: stdClass::$alamat D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 57
ERROR - 2025-08-31 08:50:47 --> Severity: Notice --> Undefined property: stdClass::$tgl_berdiri D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 58
ERROR - 2025-08-31 08:50:47 --> Severity: Notice --> Undefined property: stdClass::$alamat D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 57
ERROR - 2025-08-31 08:50:47 --> Severity: Notice --> Undefined property: stdClass::$tgl_berdiri D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 58
ERROR - 2025-08-31 08:50:47 --> Severity: Notice --> Undefined property: stdClass::$alamat D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 57
ERROR - 2025-08-31 08:50:47 --> Severity: Notice --> Undefined property: stdClass::$tgl_berdiri D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 58
ERROR - 2025-08-31 08:50:47 --> Severity: Notice --> Undefined property: stdClass::$alamat D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 57
ERROR - 2025-08-31 08:50:47 --> Severity: Notice --> Undefined property: stdClass::$tgl_berdiri D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 58
ERROR - 2025-08-31 08:50:47 --> Severity: Notice --> Undefined property: stdClass::$alamat D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 57
ERROR - 2025-08-31 08:50:47 --> Severity: Notice --> Undefined property: stdClass::$tgl_berdiri D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 58
ERROR - 2025-08-31 08:50:47 --> Severity: Notice --> Undefined variable: penghunilama D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 234
ERROR - 2025-08-31 08:50:47 --> Severity: Warning --> Invalid argument supplied for foreach() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 234
ERROR - 2025-08-31 08:50:47 --> Severity: Notice --> Undefined variable: kamar D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 297
ERROR - 2025-08-31 08:50:47 --> Severity: Warning --> Invalid argument supplied for foreach() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 297
ERROR - 2025-08-31 08:50:54 --> Severity: Notice --> Undefined variable: penghunilama D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\karyawan.php 352
ERROR - 2025-08-31 08:50:54 --> Severity: Warning --> Invalid argument supplied for foreach() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\karyawan.php 352
ERROR - 2025-08-31 08:50:54 --> Severity: Notice --> Undefined variable: kamar D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\karyawan.php 411
ERROR - 2025-08-31 08:50:54 --> Severity: Warning --> Invalid argument supplied for foreach() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\karyawan.php 411
ERROR - 2025-08-31 08:51:04 --> Severity: Notice --> Undefined property: stdClass::$alamat D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 57
ERROR - 2025-08-31 08:51:04 --> Severity: Notice --> Undefined property: stdClass::$tgl_berdiri D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 58
ERROR - 2025-08-31 08:51:04 --> Severity: Notice --> Undefined property: stdClass::$alamat D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 57
ERROR - 2025-08-31 08:51:04 --> Severity: Notice --> Undefined property: stdClass::$tgl_berdiri D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 58
ERROR - 2025-08-31 08:51:04 --> Severity: Notice --> Undefined property: stdClass::$alamat D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 57
ERROR - 2025-08-31 08:51:04 --> Severity: Notice --> Undefined property: stdClass::$tgl_berdiri D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 58
ERROR - 2025-08-31 08:51:04 --> Severity: Notice --> Undefined property: stdClass::$alamat D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 57
ERROR - 2025-08-31 08:51:04 --> Severity: Notice --> Undefined property: stdClass::$tgl_berdiri D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 58
ERROR - 2025-08-31 08:51:04 --> Severity: Notice --> Undefined property: stdClass::$alamat D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 57
ERROR - 2025-08-31 08:51:04 --> Severity: Notice --> Undefined property: stdClass::$tgl_berdiri D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 58
ERROR - 2025-08-31 08:51:04 --> Severity: Notice --> Undefined variable: penghunilama D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 234
ERROR - 2025-08-31 08:51:04 --> Severity: Warning --> Invalid argument supplied for foreach() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 234
ERROR - 2025-08-31 08:51:04 --> Severity: Notice --> Undefined variable: kamar D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 297
ERROR - 2025-08-31 08:51:04 --> Severity: Warning --> Invalid argument supplied for foreach() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 297
ERROR - 2025-08-31 08:55:51 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 179
ERROR - 2025-08-31 08:55:51 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 180
ERROR - 2025-08-31 08:55:51 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 08:55:51 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 03:55:52 --> 404 Page Not Found: Assets/images
ERROR - 2025-08-31 03:55:52 --> 404 Page Not Found: Assets/images
ERROR - 2025-08-31 03:55:52 --> 404 Page Not Found: Assets/images
ERROR - 2025-08-31 03:55:52 --> 404 Page Not Found: Assets/images
ERROR - 2025-08-31 08:56:00 --> Severity: Notice --> Undefined variable: penghunilama D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\karyawan.php 352
ERROR - 2025-08-31 08:56:00 --> Severity: Warning --> Invalid argument supplied for foreach() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\karyawan.php 352
ERROR - 2025-08-31 08:56:00 --> Severity: Notice --> Undefined variable: kamar D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\karyawan.php 411
ERROR - 2025-08-31 08:56:00 --> Severity: Warning --> Invalid argument supplied for foreach() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\karyawan.php 411
ERROR - 2025-08-31 08:56:01 --> Severity: Notice --> Undefined property: stdClass::$alamat D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 57
ERROR - 2025-08-31 08:56:01 --> Severity: Notice --> Undefined property: stdClass::$tgl_berdiri D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 58
ERROR - 2025-08-31 08:56:01 --> Severity: Notice --> Undefined property: stdClass::$alamat D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 57
ERROR - 2025-08-31 08:56:01 --> Severity: Notice --> Undefined property: stdClass::$tgl_berdiri D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 58
ERROR - 2025-08-31 08:56:01 --> Severity: Notice --> Undefined property: stdClass::$alamat D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 57
ERROR - 2025-08-31 08:56:01 --> Severity: Notice --> Undefined property: stdClass::$tgl_berdiri D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 58
ERROR - 2025-08-31 08:56:01 --> Severity: Notice --> Undefined property: stdClass::$alamat D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 57
ERROR - 2025-08-31 08:56:01 --> Severity: Notice --> Undefined property: stdClass::$tgl_berdiri D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 58
ERROR - 2025-08-31 08:56:01 --> Severity: Notice --> Undefined property: stdClass::$alamat D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 57
ERROR - 2025-08-31 08:56:01 --> Severity: Notice --> Undefined property: stdClass::$tgl_berdiri D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 58
ERROR - 2025-08-31 08:56:01 --> Severity: Notice --> Undefined variable: penghunilama D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 234
ERROR - 2025-08-31 08:56:01 --> Severity: Warning --> Invalid argument supplied for foreach() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 234
ERROR - 2025-08-31 08:56:01 --> Severity: Notice --> Undefined variable: kamar D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 297
ERROR - 2025-08-31 08:56:01 --> Severity: Warning --> Invalid argument supplied for foreach() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 297
ERROR - 2025-08-31 08:56:23 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 179
ERROR - 2025-08-31 08:56:23 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 180
ERROR - 2025-08-31 08:56:23 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 08:56:23 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 03:56:25 --> 404 Page Not Found: Assets/images
ERROR - 2025-08-31 03:56:25 --> 404 Page Not Found: Assets/images
ERROR - 2025-08-31 03:56:25 --> 404 Page Not Found: Assets/images
ERROR - 2025-08-31 03:56:25 --> 404 Page Not Found: Assets/images
ERROR - 2025-08-31 08:56:30 --> Severity: Notice --> Undefined variable: penghunilama D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\karyawan.php 352
ERROR - 2025-08-31 08:56:30 --> Severity: Warning --> Invalid argument supplied for foreach() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\karyawan.php 352
ERROR - 2025-08-31 08:56:30 --> Severity: Notice --> Undefined variable: kamar D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\karyawan.php 411
ERROR - 2025-08-31 08:56:30 --> Severity: Warning --> Invalid argument supplied for foreach() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\karyawan.php 411
ERROR - 2025-08-31 09:13:08 --> Severity: Notice --> Undefined property: stdClass::$alamat D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 57
ERROR - 2025-08-31 09:13:08 --> Severity: Notice --> Undefined property: stdClass::$tgl_berdiri D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 58
ERROR - 2025-08-31 09:13:08 --> Severity: Notice --> Undefined property: stdClass::$alamat D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 57
ERROR - 2025-08-31 09:13:08 --> Severity: Notice --> Undefined property: stdClass::$tgl_berdiri D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 58
ERROR - 2025-08-31 09:13:08 --> Severity: Notice --> Undefined property: stdClass::$alamat D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 57
ERROR - 2025-08-31 09:13:08 --> Severity: Notice --> Undefined property: stdClass::$tgl_berdiri D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 58
ERROR - 2025-08-31 09:13:08 --> Severity: Notice --> Undefined property: stdClass::$alamat D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 57
ERROR - 2025-08-31 09:13:08 --> Severity: Notice --> Undefined property: stdClass::$tgl_berdiri D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 58
ERROR - 2025-08-31 09:13:08 --> Severity: Notice --> Undefined property: stdClass::$alamat D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 57
ERROR - 2025-08-31 09:13:08 --> Severity: Notice --> Undefined property: stdClass::$tgl_berdiri D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 58
ERROR - 2025-08-31 09:13:08 --> Severity: Notice --> Undefined variable: penghunilama D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 234
ERROR - 2025-08-31 09:13:08 --> Severity: Warning --> Invalid argument supplied for foreach() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 234
ERROR - 2025-08-31 09:13:08 --> Severity: Notice --> Undefined variable: kamar D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 297
ERROR - 2025-08-31 09:13:08 --> Severity: Warning --> Invalid argument supplied for foreach() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 297
ERROR - 2025-08-31 09:13:10 --> Severity: Notice --> Undefined variable: penghunilama D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\karyawan.php 352
ERROR - 2025-08-31 09:13:10 --> Severity: Warning --> Invalid argument supplied for foreach() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\karyawan.php 352
ERROR - 2025-08-31 09:13:10 --> Severity: Notice --> Undefined variable: kamar D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\karyawan.php 411
ERROR - 2025-08-31 09:13:10 --> Severity: Warning --> Invalid argument supplied for foreach() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\karyawan.php 411
ERROR - 2025-08-31 12:42:41 --> 404 Page Not Found: Assets/images
ERROR - 2025-08-31 12:42:41 --> 404 Page Not Found: Assets/images
ERROR - 2025-08-31 12:42:41 --> 404 Page Not Found: Assets/images
ERROR - 2025-08-31 12:42:41 --> 404 Page Not Found: Assets/images
ERROR - 2025-08-31 17:42:42 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 179
ERROR - 2025-08-31 17:42:42 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 180
ERROR - 2025-08-31 17:42:42 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 17:42:42 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 17:42:49 --> Severity: Notice --> Undefined property: stdClass::$alamat D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 57
ERROR - 2025-08-31 17:42:49 --> Severity: Notice --> Undefined property: stdClass::$tgl_berdiri D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 58
ERROR - 2025-08-31 17:42:49 --> Severity: Notice --> Undefined property: stdClass::$alamat D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 57
ERROR - 2025-08-31 17:42:49 --> Severity: Notice --> Undefined property: stdClass::$tgl_berdiri D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 58
ERROR - 2025-08-31 17:42:49 --> Severity: Notice --> Undefined property: stdClass::$alamat D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 57
ERROR - 2025-08-31 17:42:49 --> Severity: Notice --> Undefined property: stdClass::$tgl_berdiri D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 58
ERROR - 2025-08-31 17:42:49 --> Severity: Notice --> Undefined property: stdClass::$alamat D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 57
ERROR - 2025-08-31 17:42:49 --> Severity: Notice --> Undefined property: stdClass::$tgl_berdiri D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 58
ERROR - 2025-08-31 17:42:49 --> Severity: Notice --> Undefined property: stdClass::$alamat D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 57
ERROR - 2025-08-31 17:42:49 --> Severity: Notice --> Undefined property: stdClass::$tgl_berdiri D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 58
ERROR - 2025-08-31 17:42:49 --> Severity: Notice --> Undefined variable: penghunilama D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 234
ERROR - 2025-08-31 17:42:49 --> Severity: Warning --> Invalid argument supplied for foreach() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 234
ERROR - 2025-08-31 17:42:49 --> Severity: Notice --> Undefined variable: kamar D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 297
ERROR - 2025-08-31 17:42:49 --> Severity: Warning --> Invalid argument supplied for foreach() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 297
ERROR - 2025-08-31 17:42:50 --> Severity: Notice --> Undefined variable: penghunilama D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\karyawan.php 352
ERROR - 2025-08-31 17:42:50 --> Severity: Warning --> Invalid argument supplied for foreach() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\karyawan.php 352
ERROR - 2025-08-31 17:42:50 --> Severity: Notice --> Undefined variable: kamar D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\karyawan.php 411
ERROR - 2025-08-31 17:42:50 --> Severity: Warning --> Invalid argument supplied for foreach() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\karyawan.php 411
ERROR - 2025-08-31 17:43:23 --> Severity: Notice --> Undefined property: stdClass::$alamat D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 57
ERROR - 2025-08-31 17:43:23 --> Severity: Notice --> Undefined property: stdClass::$tgl_berdiri D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 58
ERROR - 2025-08-31 17:43:23 --> Severity: Notice --> Undefined property: stdClass::$alamat D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 57
ERROR - 2025-08-31 17:43:23 --> Severity: Notice --> Undefined property: stdClass::$tgl_berdiri D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 58
ERROR - 2025-08-31 17:43:23 --> Severity: Notice --> Undefined property: stdClass::$alamat D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 57
ERROR - 2025-08-31 17:43:23 --> Severity: Notice --> Undefined property: stdClass::$tgl_berdiri D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 58
ERROR - 2025-08-31 17:43:23 --> Severity: Notice --> Undefined property: stdClass::$alamat D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 57
ERROR - 2025-08-31 17:43:23 --> Severity: Notice --> Undefined property: stdClass::$tgl_berdiri D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 58
ERROR - 2025-08-31 17:43:23 --> Severity: Notice --> Undefined property: stdClass::$alamat D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 57
ERROR - 2025-08-31 17:43:23 --> Severity: Notice --> Undefined property: stdClass::$tgl_berdiri D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 58
ERROR - 2025-08-31 17:43:23 --> Severity: Notice --> Undefined variable: penghunilama D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 234
ERROR - 2025-08-31 17:43:23 --> Severity: Warning --> Invalid argument supplied for foreach() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 234
ERROR - 2025-08-31 17:43:23 --> Severity: Notice --> Undefined variable: kamar D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 297
ERROR - 2025-08-31 17:43:23 --> Severity: Warning --> Invalid argument supplied for foreach() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\page\kandang.php 297
ERROR - 2025-08-31 18:07:23 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 179
ERROR - 2025-08-31 18:07:23 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 180
ERROR - 2025-08-31 18:07:23 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 18:07:23 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 13:07:32 --> 404 Page Not Found: Assets/images
ERROR - 2025-08-31 13:07:32 --> 404 Page Not Found: Assets/images
ERROR - 2025-08-31 13:07:32 --> 404 Page Not Found: Assets/images
ERROR - 2025-08-31 13:07:32 --> 404 Page Not Found: Assets/images
ERROR - 2025-08-31 18:13:26 --> Severity: error --> Exception: Call to undefined method M_produksi::get_telur_hari_ini() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\controllers\Produksi.php 36
ERROR - 2025-08-31 18:13:27 --> Severity: error --> Exception: Call to undefined method M_produksi::get_telur_hari_ini() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\controllers\Produksi.php 36
ERROR - 2025-08-31 18:13:28 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 179
ERROR - 2025-08-31 18:13:28 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 180
ERROR - 2025-08-31 18:13:28 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 18:13:28 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 18:13:31 --> Severity: error --> Exception: Call to undefined method M_produksi::get_telur_hari_ini() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\controllers\Produksi.php 36
ERROR - 2025-08-31 18:15:45 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 179
ERROR - 2025-08-31 18:15:45 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 180
ERROR - 2025-08-31 18:15:45 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 18:15:45 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 18:17:50 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 179
ERROR - 2025-08-31 18:17:50 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 180
ERROR - 2025-08-31 18:17:50 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 18:17:50 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 13:18:03 --> 404 Page Not Found: Registerhtml/index
ERROR - 2025-08-31 18:18:40 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 179
ERROR - 2025-08-31 18:18:40 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 180
ERROR - 2025-08-31 18:18:40 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 18:18:40 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 18:18:47 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 179
ERROR - 2025-08-31 18:18:47 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 180
ERROR - 2025-08-31 18:18:47 --> Severity: Notice --> Undefined property: stdClass::$jumlah_saat_ini D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 18:18:47 --> Severity: Notice --> Undefined property: stdClass::$jumlah_awal D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\pembesaran\index.php 182
ERROR - 2025-08-31 18:24:34 --> Query error: Unknown column 'biaya_total' in 'field list' - Invalid query: SELECT SUM(biaya_total) as total
FROM `kos_pembesaran`
WHERE `status` = 'aktif'
ERROR - 2025-08-31 18:24:40 --> Query error: Unknown column 'biaya_total' in 'field list' - Invalid query: SELECT SUM(biaya_total) as total
FROM `kos_pembesaran`
WHERE `status` = 'aktif'
ERROR - 2025-08-31 18:40:31 --> Severity: error --> Exception: Call to undefined method M_min::get_template_messages() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\controllers\Settings.php 25
ERROR - 2025-08-31 13:40:32 --> 404 Page Not Found: User-management/index
ERROR - 2025-08-31 13:40:34 --> 404 Page Not Found: Analytics/index
ERROR - 2025-08-31 13:40:35 --> 404 Page Not Found: Backup/index
ERROR - 2025-08-31 13:40:36 --> 404 Page Not Found: Logs/index
