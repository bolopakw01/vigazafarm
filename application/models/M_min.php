<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_min extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		
		// Comment out authentication check for login process
		// $this->check_authentication();
	}

	/**
	 * Enhanced authentication check with role-based support
	 */
	private function check_authentication()
	{
		// Check if user is logged in (legacy or new system)
		$is_logged_legacy = $this->session->userdata('isLog') == TRUE;
		$is_logged_new = $this->session->userdata('user_logged_in') == TRUE;
		
		if (!$is_logged_legacy && !$is_logged_new) {
			redirect(base_url());
		}
		
		// Legacy system compatibility
		if ($is_logged_legacy) {
			$required_fields = ['isId', 'isUname', 'isPass', 'isLevel'];
			foreach ($required_fields as $field) {
				if (empty($this->session->userdata($field))) {
					redirect(base_url());
				}
			}
			
			// Check if user has admin privileges in legacy system
			if ($this->session->userdata('isLevel') !== 'mimin') {
				redirect(base_url());
			}
		}
		
		// New system compatibility
		if ($is_logged_new) {
			$user_id = $this->session->userdata('user_id');
			$user_role = $this->session->userdata('user_role');
			
			if (empty($user_id) || empty($user_role)) {
				redirect(base_url());
			}
			
			// Check if user has required privileges (admin or manager)
			if (!in_array($user_role, ['admin', 'manager'])) {
				redirect(base_url());
			}
		}
	}

	/**
	 * Check if user has specific permission
	 */
	public function has_permission($permission)
	{
		$user_role = $this->session->userdata('user_role');
		
		// If legacy system, assume admin permissions
		if ($this->session->userdata('isLevel') === 'mimin') {
			return true;
		}
		
		// Permission matrix for new system
		$permissions = [
			'admin' => [
				'kandang_create', 'kandang_read', 'kandang_update', 'kandang_delete',
				'penetasan_create', 'penetasan_read', 'penetasan_update', 'penetasan_delete',
				'pembesaran_create', 'pembesaran_read', 'pembesaran_update', 'pembesaran_delete',
				'produksi_create', 'produksi_read', 'produksi_update', 'produksi_delete',
				'karyawan_create', 'karyawan_read', 'karyawan_update', 'karyawan_delete',
				'laporan_read', 'laporan_export', 'settings_update', 'user_management'
			],
			'manager' => [
				'kandang_read', 'kandang_update',
				'penetasan_create', 'penetasan_read', 'penetasan_update',
				'pembesaran_create', 'pembesaran_read', 'pembesaran_update',
				'produksi_create', 'produksi_read', 'produksi_update',
				'karyawan_read', 'laporan_read', 'laporan_export'
			],
			'operator' => [
				'kandang_read',
				'penetasan_read', 'penetasan_update',
				'pembesaran_read', 'pembesaran_update',
				'produksi_read', 'produksi_update'
			]
		];
		
		return in_array($permission, $permissions[$user_role] ?? []);
	}

	/* ========================================= CEK ========================================= */

	public function cek_kandang($nama)
	{
		$this->db->select('*');
		$this->db->from('v_kandang');
		$this->db->where('nama', $nama);
		return $this->db->get();
	}

	public function cek_pembesaran($periode)
	{
		$this->db->select('*');
		$this->db->from('v_pembesaran');
		$this->db->where('periode', $periode);
		return $this->db->get();
	}

	public function cek_admin($uname)
	{
		$this->db->select('*');
		$this->db->from('simimin');
		$this->db->where('username', $uname);
		return $this->db->get();
	}

	/* ========================================= READ ========================================= */

	public function profil($data)
	{
		$this->db->where('username', $data);
		return $this->db->get('simimin');
	}

	function log_today()
	{
		// Sementara return empty result karena tabel log belum ada
		// Bisa diganti dengan workflow_logs nanti
		return $this->db->query("SELECT 0 as count WHERE 1=0");
	}

	public function log()
	{
		// Sementara return empty result karena tabel log belum ada
		// Bisa diganti dengan workflow_logs nanti
		return $this->db->query("SELECT 0 as count WHERE 1=0");
	}

	public function jml_penghuni()
	{
		// Count total ayam yang sedang dipelihara (dari kos_produksi yang aktif)
		$this->db->select_sum('jumlah_ayam_saat_ini');
		$this->db->from('kos_produksi');
		$this->db->where('status', 'aktif');
		$query = $this->db->get();
		$result = $query->row();
		return $result->jumlah_ayam_saat_ini ? $result->jumlah_ayam_saat_ini : 0;
	}

	public function jml_bb()
	{
		// Count kandang yang maintenance (butuh perhatian)
		$this->db->select('id_kandang');
		$this->db->from('kos_kandang');
		$this->db->where('status', 'maintenance');
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function jml_br()
	{
		// Count proses penetasan yang masih berjalan
		$this->db->select('id_penetasan');
		$this->db->from('kos_penetasan');
		$this->db->where('status', 'proses');
		$query = $this->db->get();
		return $query->num_rows();
	}

	// ------------------------------- MASTER - KANDANG -------------------------------

	public function rd_kandang()
	{
		$this->db->order_by('nama');
		$this->db->where('status !=', 'deleted');
		return $this->db->get('kos_kandang')->result();
	}

	// ------------------------------- MASTER - KARYAWAN -------------------------------

	public function rd_karyawan()
	{
		$this->db->order_by('nama');
		$this->db->where('status !=', 'resign');
		return $this->db->get('kos_karyawan')->result();
	}

	public function cek_karyawan($nip)
	{
		$this->db->select('*');
		$this->db->from('kos_karyawan');
		$this->db->where('nip', $nip);
		return $this->db->get();
	}

	public function cek_email_karyawan($email)
	{
		$this->db->select('*');
		$this->db->from('kos_karyawan');
		$this->db->where('email', $email);
		return $this->db->get();
	}

	// ------------------------------- OPERASIONAL - PEMBESARAN -------------------------------

	public function rd_pembesaran()
	{
		$this->db->select('v_kandang.id_kandang, v_kandang.nama as kandang, v_pembesaran.*');
		$this->db->join('v_kandang', 'v_kandang.id_kandang=v_pembesaran.id_kandang');
		$this->db->where('v_pembesaran.hapus', 0);
		$this->db->order_by('v_pembesaran.tgl_masuk');
		return $this->db->get('v_pembesaran')->result();
	}

	// public function rd_pembesaran($id_kandang)
	// {
	// 	$this->db->order_by('tgl_masuk');
	// 	$this->db->where('id_kandang', $id_kandang);
	// 	$this->db->where('hapus', 0);
	// 	return $this->db->get('v_pembesaran')->result();
	// }

	function edit_kelas($id)
	{
		return $this->db->get_where('mnl_kelas', array('id_mnl_kelas' => $id));
	}

	// ------------------------------- SAMPAI SINI  -------------------------------















































































	// ------------------------------- MASTER - KAMAR -------------------------------

	public function rd_kamar()
	{
		$this->db->order_by('nomor');
		$this->db->join('kos_jenis_kamar', 'kos_jenis_kamar.id_jenis_kamar=kos_kamar.id_jenis_kamar');
		return $this->db->get('kos_kamar')->result();
	}

	public function rd_kamar_status()
	{
		$this->db->order_by('nomor');
		$this->db->join('kos_jenis_kamar', 'kos_jenis_kamar.id_jenis_kamar=kos_kamar.id_jenis_kamar');
		$this->db->where('status', 'belum');
		return $this->db->get('kos_kamar')->result();
	}

	function edit_kamar($id)
	{
		$this->db->join('kos_jenis_kamar', 'kos_jenis_kamar.id_jenis_kamar=kos_kamar.id_jenis_kamar');
		return $this->db->get_where('kos_kamar', array('id_kamar' => $id));
	}

	// ------------------------------- MASTER - CLUB -------------------------------

	public function rd_jkamar()
	{
		$this->db->order_by('jenis');
		return $this->db->get('kos_jenis_kamar')->result();
	}

	function edit_jkamar($id)
	{
		return $this->db->get_where('kos_jenis_kamar', array('id_jenis_kamar' => $id));
	}

	// ------------------------------- MASTER - SPP -------------------------------

	public function rd_spp()
	{
		$this->db->join('mnl_club', 'mnl_club.id_mnl_club=mnl_spp.id_mnl_club');
		$this->db->join('mnl_kelas', 'mnl_kelas.id_mnl_kelas=mnl_spp.id_mnl_kelas');
		$this->db->join('mnl_lokasi', 'mnl_lokasi.id_mnl_lokasi=mnl_spp.id_mnl_lokasi');
		return $this->db->get('mnl_spp')->result();
	}

	function edit_spp($id)
	{
		$this->db->join('mnl_club', 'mnl_club.id_mnl_club=mnl_spp.id_mnl_club');
		$this->db->join('mnl_kelas', 'mnl_kelas.id_mnl_kelas=mnl_spp.id_mnl_kelas');
		$this->db->join('mnl_lokasi', 'mnl_lokasi.id_mnl_lokasi=mnl_spp.id_mnl_lokasi');
		return $this->db->get_where('mnl_spp', array('id_mnl_spp' => $id));
	}

	// ------------------------------- MASTER - ADMIN -------------------------------

	public function rd_admin()
	{
		$this->db->where('level', 'operator');
		$this->db->where('hapus', 0);
		return $this->db->get('simimin')->result();
	}

	function edit_admin($id)
	{
		$this->db->select('*');
		$this->db->where('id', $id); // Update dari 'minid' ke 'id'
		return $this->db->get('simimin');
	}

	// ------------------------------- MASTER - PENGHUNI -------------------------------

	public function rd_penghuni()
	{
		$this->db->select('kos_penghuni.*, kos_penghuni.status as statusnya, kos_kamar.*, kos_jenis_kamar.*');
		$this->db->join('kos_kamar', 'kos_kamar.id_kamar=kos_penghuni.id_kamar');
		$this->db->join('kos_jenis_kamar', 'kos_jenis_kamar.id_jenis_kamar=kos_kamar.id_jenis_kamar');
		return $this->db->get('kos_penghuni')->result();
	}

	// function edit_penghuni($id)
	// {
	// 	$this->db->select('mnl_siswa.*, mnl_club.*, mnl_kelas.*, mnl_lokasi.*, mnl_regist.nama as regist, mnl_regist.id_mnl_regist, mnl_spp.nama as spp, mnl_spp.id_mnl_spp');
	// 	$this->db->join('mnl_club', 'mnl_club.id_mnl_club=mnl_siswa.id_mnl_club');
	// 	$this->db->join('mnl_kelas', 'mnl_kelas.id_mnl_kelas=mnl_siswa.id_mnl_kelas');
	// 	$this->db->join('mnl_lokasi', 'mnl_lokasi.id_mnl_lokasi=mnl_siswa.id_mnl_lokasi');
	// 	$this->db->join('mnl_regist', 'mnl_regist.id_mnl_regist=mnl_siswa.id_mnl_regist');
	// 	$this->db->join('mnl_spp', 'mnl_spp.id_mnl_spp=mnl_siswa.id_mnl_spp');
	// 	$this->db->where('id_mnl_siswa', $id);
	// 	return $this->db->get('mnl_siswa');
	// }

	function edit_penghuni($id)
	{
		$this->db->select('kos_penghuni.*, kos_kamar.id_kamar, kos_kamar.id_jenis_kamar, kos_kamar.nomor, kos_jenis_kamar.id_jenis_kamar, kos_jenis_kamar.jenis');
		$this->db->join('kos_kamar', 'kos_kamar.id_kamar=kos_penghuni.id_kamar');
		$this->db->join('kos_jenis_kamar', 'kos_jenis_kamar.id_jenis_kamar=kos_kamar.id_jenis_kamar');
		$this->db->where('id_penghuni', $id);
		return $this->db->get('kos_penghuni');
	}

	function get_last_id($table)
	{
		// Get last ID for any table dynamically
		$this->db->select('*');
		$this->db->order_by('created_at', 'DESC');
		$this->db->limit(1);
		return $this->db->get($table);
	}

	// ------------------------------- MASTER - BIAYA REGISTRASI -------------------------------

	public function rd_regist()
	{
		$this->db->join('mnl_club', 'mnl_club.id_mnl_club=mnl_regist.id_mnl_club');
		$this->db->join('mnl_kelas', 'mnl_kelas.id_mnl_kelas=mnl_regist.id_mnl_kelas');
		$this->db->join('mnl_lokasi', 'mnl_lokasi.id_mnl_lokasi=mnl_regist.id_mnl_lokasi');
		return $this->db->get('mnl_regist')->result();
	}

	function edit_regist($id)
	{
		$this->db->join('mnl_club', 'mnl_club.id_mnl_club=mnl_regist.id_mnl_club');
		$this->db->join('mnl_kelas', 'mnl_kelas.id_mnl_kelas=mnl_regist.id_mnl_kelas');
		$this->db->join('mnl_lokasi', 'mnl_lokasi.id_mnl_lokasi=mnl_regist.id_mnl_lokasi');
		return $this->db->get_where('mnl_regist', array('id_mnl_regist' => $id));
	}

	// ------------------------------- PEMBAYARAN - PEMBAYARAN -------------------------------

	// Method ini tidak digunakan untuk sistem peternakan
	// public function copy_data_penghuni()
	// {
	// 	$query = $this->db->query("INSERT INTO mnl_tmp_pembayaran (id_mnl_siswa, id_mnl_spp) SELECT id_mnl_siswa, id_mnl_spp FROM mnl_siswa");
	// 	return $query;
	// }

	public function copy_data_penghuni()
	{
		// Method tidak digunakan untuk sistem peternakan - dikosongkan
		return true;
	}

	// public function data_tambah()
	// {
	// 	return $this->db->affected_rows('mml_pembayaran') > 0;
	// }

	public function truncate_tmp_pembayaran()
	{
		// return $this->db->truncate('mnl_tmp_pembayaran');
		return true;
	}

	public function copy_data_pembayaran()
	{
		// Method tidak digunakan untuk sistem peternakan
		return true;
	}

	public function cek_pembayaran()
	{
		// Method tidak digunakan untuk sistem peternakan
		return null;
	}

	public function bukti_tf($id)
	{
		// Method tidak digunakan untuk sistem peternakan
		return null;
	}

	public function rd_pembayaran_all()
	{
		// Method tidak digunakan untuk sistem peternakan
		return array();
	}

	public function rd_pembayaran_ba()
	{
		// Method tidak digunakan untuk sistem peternakan
		return array();
	}

	public function rd_pembayaran_bb()
	{
		// Method tidak digunakan untuk sistem peternakan  
		return array();
	}

	public function rd_pembayaran_sb()
	{
		// Method tidak digunakan untuk sistem peternakan
		return array();
	}

	public function rd_pembayaran_regist_all()
	{
		$this->db->select('mnl_pmb_regist.*, mnl_siswa.id_mnl_siswa, mnl_siswa.id_mnl_kelas, mnl_siswa.id_mnl_lokasi, mnl_siswa.nama, mnl_siswa.status, mnl_regist.id_mnl_regist, mnl_regist.nominal');
		$this->db->join('mnl_siswa', 'mnl_siswa.id_mnl_siswa=mnl_pmb_regist.id_mnl_siswa');
		$this->db->join('mnl_regist', 'mnl_regist.id_mnl_regist=mnl_pmb_regist.id_mnl_regist');
		return $this->db->get('mnl_pmb_regist')->result();
	}

	public function rd_pembayaran_regist_ba()
	{
		$this->db->select('mnl_pmb_regist.*, mnl_siswa.id_mnl_siswa, mnl_siswa.id_mnl_kelas, mnl_siswa.id_mnl_lokasi, mnl_siswa.nama, mnl_siswa.status, mnl_regist.id_mnl_regist, mnl_regist.nominal');
		$this->db->join('mnl_siswa', 'mnl_siswa.id_mnl_siswa=mnl_pmb_regist.id_mnl_siswa');
		$this->db->join('mnl_regist', 'mnl_regist.id_mnl_regist=mnl_pmb_regist.id_mnl_regist');
		$this->db->where('mnl_siswa.status', 'proses');
		return $this->db->get('mnl_pmb_regist')->result();
	}

	// public function rd_pembayaran_regist_bb()
	// {
	// 	$this->db->select('mnl_pmb_regist.*, mnl_siswa.id_mnl_siswa, mnl_siswa.id_mnl_kelas, mnl_siswa.id_mnl_lokasi, mnl_siswa.nama, mnl_siswa.status, mnl_regist.id_mnl_regist, mnl_regist.nominal');
	// 	$this->db->join('mnl_siswa', 'mnl_siswa.id_mnl_siswa=mnl_pmb_regist.id_mnl_siswa');
	// 	$this->db->join('mnl_regist', 'mnl_regist.id_mnl_regist=mnl_pmb_regist.id_mnl_regist');
	// 	$this->db->where('mnl_siswa.status', 'belum');
	// 	return $this->db->get('mnl_pmb_regist')->result();
	// }

	public function rd_pembayaran_regist_bb()
	{
		$this->db->select('mnl_siswa.*, mnl_regist.id_mnl_regist, mnl_regist.nominal');
		$this->db->join('mnl_regist', 'mnl_siswa.id_mnl_regist=mnl_regist.id_mnl_regist');
		$this->db->where('mnl_siswa.status', 'belum');
		return $this->db->get('mnl_siswa')->result();
	}

	public function rd_pembayaran_regist_sb()
	{
		$this->db->select('mnl_pmb_regist.*, mnl_siswa.id_mnl_siswa, mnl_siswa.id_mnl_kelas, mnl_siswa.id_mnl_lokasi, mnl_siswa.nama, mnl_siswa.status, mnl_regist.id_mnl_regist, mnl_regist.nominal');
		$this->db->join('mnl_siswa', 'mnl_siswa.id_mnl_siswa=mnl_pmb_regist.id_mnl_siswa');
		$this->db->join('mnl_regist', 'mnl_regist.id_mnl_regist=mnl_pmb_regist.id_mnl_regist');
		$this->db->where('mnl_siswa.status', 'aktif');
		return $this->db->get('mnl_pmb_regist')->result();
	}

	/* ========================================= INSERT ========================================= */

	public function insert($table, $data)
	{
		$this->db->insert($table, $data);
		return TRUE;
	}

	/* ========================================= UPDATE ========================================= */

	public function update_profil($data, $where)
	{
		$this->db->set($data);
		$this->db->where($where);
		$this->db->update('simimin');
		return TRUE;
	}

	public function update($table, $data, $where)
	{
		$this->db->set($data);
		$this->db->where($where);
		$this->db->update($table);
		return TRUE;
	}

	public function generate($table, $data)
	{
		$this->db->set($data);
		$this->db->update($table);
		return TRUE;
	}

	function msg_hmin14()
	{
		$this->db->select('msg');
		$this->db->where('template', 'hmin14');
		return $this->db->get('kos_msg');
	}

	function msg_hmin7()
	{
		$this->db->select('msg');
		$this->db->where('template', 'hmin7');
		return $this->db->get('kos_msg');
	}

	function msg_hmin3()
	{
		$this->db->select('msg');
		$this->db->where('template', 'hmin3');
		return $this->db->get('kos_msg');
	}

	function msg_hplus3()
	{
		$this->db->select('msg');
		$this->db->where('template', 'hplus3');
		return $this->db->get('kos_msg');
	}

	function msg_hplus7()
	{
		$this->db->select('msg');
		$this->db->where('template', 'hplus7');
		return $this->db->get('kos_msg');
	}

	/* ========================================= DELETE ========================================= */

	function hapus($table, $where)
	{
		$this->db->where($where);
		$this->db->delete($table);
	}
}
