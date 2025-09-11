<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'mimin';
$route['404_override'] = 'notf';
$route['translate_uri_dashes'] = FALSE;

// $route['home'] = 'home?bank=pilih';

// ======================== BACKOFFICE ROUTES ========================
// Redirect backoffice ke dashboard
$route['backoffice'] = 'dashboard';
$route['backoffice/(.*)'] = 'backoffice/$1'; // Maintain compatibility with old backoffice

// Dashboard routes
$route['dashboard'] = 'dashboard/index';
$route['dashboard/profil'] = 'dashboard/profil';
$route['dashboard/update_profil'] = 'dashboard/update_profil';
$route['dashboard/log'] = 'dashboard/log';

// Kandang routes
$route['kandang'] = 'kandang/index';
$route['kandang/tambah'] = 'kandang/tambah';
$route['kandang/edit/(:num)'] = 'kandang/edit/$1';
$route['kandang/update'] = 'kandang/update';
$route['kandang/status/(:num)'] = 'kandang/status/$1';

// Karyawan routes
$route['karyawan'] = 'karyawan/index';
$route['karyawan/tambah'] = 'karyawan/tambah';
$route['karyawan/simpan'] = 'karyawan/simpan';
$route['karyawan/edit/(:num)'] = 'karyawan/edit/$1';
$route['karyawan/update'] = 'karyawan/update';
$route['karyawan/status/(:num)'] = 'karyawan/status/$1';

// Pembesaran routes
$route['pembesaran'] = 'pembesaran/index';
$route['pembesaran/tambah'] = 'pembesaran/tambah';
$route['pembesaran/simpan'] = 'pembesaran/simpan';
$route['pembesaran/edit/(:num)'] = 'pembesaran/edit/$1';
$route['pembesaran/update'] = 'pembesaran/update';
$route['pembesaran/status/(:num)'] = 'pembesaran/status/$1';
$route['pembesaran/detail/(:num)'] = 'pembesaran/detail/$1';
$route['pembesaran/generate'] = 'pembesaran/generate';

// Settings routes
$route['settings'] = 'settings/index';
$route['settings/pengaturan'] = 'settings/index';
$route['settings/update_template'] = 'settings/update_template';
$route['settings/admin'] = 'settings/admin';
$route['settings/tambah_admin'] = 'settings/tambah_admin';
$route['settings/simpan_admin'] = 'settings/simpan_admin';
$route['settings/edit_admin/(:num)'] = 'settings/edit_admin/$1';
$route['settings/update_admin'] = 'settings/update_admin';

// Penetasan routes
$route['penetasan'] = 'penetasan/index';
$route['penetasan/tambah'] = 'penetasan/tambah';
$route['penetasan/proses_tambah'] = 'penetasan/proses_tambah';
$route['penetasan/edit/(:num)'] = 'penetasan/edit/$1';
$route['penetasan/proses_edit'] = 'penetasan/proses_edit';
$route['penetasan/detail/(:num)'] = 'penetasan/detail/$1';
$route['penetasan/progress/(:any)'] = 'penetasan/progress/$1';
$route['penetasan/update_status'] = 'penetasan/update_status';
$route['penetasan/update_hasil'] = 'penetasan/update_hasil';
$route['penetasan/laporan'] = 'penetasan/laporan';
$route['penetasan/export_laporan'] = 'penetasan/export_laporan';
$route['penetasan/hapus/(:num)'] = 'penetasan/hapus/$1';

// Produksi routes
$route['produksi'] = 'produksi/index';
$route['produksi/tambah'] = 'produksi/tambah';
$route['produksi/proses_tambah'] = 'produksi/proses_tambah';
$route['produksi/edit/(:num)'] = 'produksi/edit/$1';
$route['produksi/proses_edit'] = 'produksi/proses_edit';
$route['produksi/detail/(:num)'] = 'produksi/detail/$1';
$route['produksi/laporan_harian'] = 'produksi/laporan_harian';
$route['produksi/laporan_bulanan'] = 'produksi/laporan_bulanan';
$route['produksi/trend'] = 'produksi/trend';
$route['produksi/api_trend'] = 'produksi/api_trend';
$route['produksi/ranking'] = 'produksi/ranking';
$route['produksi/export_laporan'] = 'produksi/export_laporan';
$route['produksi/update_kualitas'] = 'produksi/update_kualitas';
$route['produksi/hapus/(:num)'] = 'produksi/hapus/$1';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
