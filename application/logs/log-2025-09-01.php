<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2025-09-01 11:17:27 --> Severity: Notice --> Undefined index: minid D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\profil.php 12
ERROR - 2025-09-01 11:17:27 --> Severity: Notice --> Undefined index: nama D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\profil.php 17
ERROR - 2025-09-01 06:17:27 --> 404 Page Not Found: Dashboard/assets
ERROR - 2025-09-01 18:49:30 --> Query error: Unknown column 'produksi_telur' in 'field list' - Invalid query: SELECT SUM(`produksi_telur`) AS `produksi_telur`
FROM `kos_produksi`
WHERE DATE(tanggal_input) = '2025-09-01'
ERROR - 2025-09-01 18:49:34 --> Query error: Unknown column 'produksi_telur' in 'field list' - Invalid query: SELECT SUM(`produksi_telur`) AS `produksi_telur`
FROM `kos_produksi`
WHERE DATE(tanggal_input) = '2025-09-01'
ERROR - 2025-09-01 14:05:39 --> 404 Page Not Found: Assets/plugins
ERROR - 2025-09-01 15:04:28 --> 404 Page Not Found: Assets/plugins
ERROR - 2025-09-01 15:51:42 --> 404 Page Not Found: Assets/plugins
ERROR - 2025-09-01 21:06:25 --> Severity: error --> Exception: Cannot use object of type mysqli as array D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\controllers\Dashboard.php 242
ERROR - 2025-09-01 16:09:27 --> Severity: Compile Error --> Cannot redeclare Dashboard::get_default_charts() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\controllers\Dashboard.php 551
ERROR - 2025-09-01 16:09:58 --> Severity: Compile Error --> Cannot redeclare Dashboard::get_default_charts() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\controllers\Dashboard.php 551
ERROR - 2025-09-01 16:10:27 --> Severity: Compile Error --> Cannot redeclare Dashboard::get_default_charts() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\controllers\Dashboard.php 551
ERROR - 2025-09-01 16:10:29 --> Severity: Compile Error --> Cannot redeclare Dashboard::get_default_charts() D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\controllers\Dashboard.php 551
ERROR - 2025-09-01 23:15:32 --> Severity: error --> Exception: Call to a member function fetch_assoc() on bool D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\dashboard.php 130
ERROR - 2025-09-01 23:15:39 --> Severity: error --> Exception: Call to a member function fetch_assoc() on bool D:\CODE\XAMPP\XAMPP-7.4.14\htdocs\vigazafarm_clean\application\views\mimin\dashboard.php 130
ERROR - 2025-09-01 23:22:55 --> Query error: Unknown column 'tanggal_masuk' in 'field list' - Invalid query: 
				SELECT 
					CONCAT('PEN-', batch) as name,
					tanggal_mulai as start_date,
					DATE_ADD(tanggal_mulai, INTERVAL 21 DAY) as end_date,
					status,
					'penetasan' as category
				FROM kos_penetasan 
				WHERE tanggal_mulai >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)
				UNION ALL
				SELECT 
					CONCAT('PEM-', id_pembesaran) as name,
					tanggal_masuk as start_date,
					IFNULL(tanggal_keluar, DATE_ADD(tanggal_masuk, INTERVAL 120 DAY)) as end_date,
					'aktif' as status,
					'pembesaran' as category
				FROM kos_pembesaran 
				WHERE tanggal_masuk >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)
				ORDER BY start_date DESC
			
ERROR - 2025-09-01 23:22:57 --> Query error: Unknown column 'tanggal_masuk' in 'field list' - Invalid query: 
				SELECT 
					CONCAT('PEN-', batch) as name,
					tanggal_mulai as start_date,
					DATE_ADD(tanggal_mulai, INTERVAL 21 DAY) as end_date,
					status,
					'penetasan' as category
				FROM kos_penetasan 
				WHERE tanggal_mulai >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)
				UNION ALL
				SELECT 
					CONCAT('PEM-', id_pembesaran) as name,
					tanggal_masuk as start_date,
					IFNULL(tanggal_keluar, DATE_ADD(tanggal_masuk, INTERVAL 120 DAY)) as end_date,
					'aktif' as status,
					'pembesaran' as category
				FROM kos_pembesaran 
				WHERE tanggal_masuk >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)
				ORDER BY start_date DESC
			
ERROR - 2025-09-01 23:23:06 --> Query error: Unknown column 'tanggal_masuk' in 'field list' - Invalid query: 
				SELECT 
					CONCAT('PEN-', batch) as name,
					tanggal_mulai as start_date,
					DATE_ADD(tanggal_mulai, INTERVAL 21 DAY) as end_date,
					status,
					'penetasan' as category
				FROM kos_penetasan 
				WHERE tanggal_mulai >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)
				UNION ALL
				SELECT 
					CONCAT('PEM-', id_pembesaran) as name,
					tanggal_masuk as start_date,
					IFNULL(tanggal_keluar, DATE_ADD(tanggal_masuk, INTERVAL 120 DAY)) as end_date,
					'aktif' as status,
					'pembesaran' as category
				FROM kos_pembesaran 
				WHERE tanggal_masuk >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)
				ORDER BY start_date DESC
			
ERROR - 2025-09-01 19:01:10 --> 404 Page Not Found: Assets/images
ERROR - 2025-09-01 19:01:10 --> 404 Page Not Found: Assets/images
ERROR - 2025-09-01 19:01:10 --> 404 Page Not Found: Assets/images
ERROR - 2025-09-01 19:01:10 --> 404 Page Not Found: Assets/images
ERROR - 2025-09-01 21:05:51 --> 404 Page Not Found: Assets/plugins
ERROR - 2025-09-01 21:06:23 --> 404 Page Not Found: Assets/plugins
ERROR - 2025-09-01 21:11:47 --> 404 Page Not Found: Assets/plugins
ERROR - 2025-09-01 21:21:15 --> 404 Page Not Found: Assets/plugins
ERROR - 2025-09-01 21:28:58 --> 404 Page Not Found: Assets/plugins
ERROR - 2025-09-01 21:36:11 --> 404 Page Not Found: Assets/plugins
ERROR - 2025-09-01 21:39:51 --> 404 Page Not Found: Assets/plugins
ERROR - 2025-09-01 21:40:24 --> 404 Page Not Found: Assets/plugins
ERROR - 2025-09-01 22:16:54 --> 404 Page Not Found: Assets/plugins
ERROR - 2025-09-01 23:10:00 --> 404 Page Not Found: Mimin/dashboard
ERROR - 2025-09-01 23:14:15 --> 404 Page Not Found: Mimin/dashboard
ERROR - 2025-09-01 23:15:29 --> 404 Page Not Found: Mimin/dashboard
ERROR - 2025-09-01 23:23:11 --> 404 Page Not Found: Mimin/dashboard
