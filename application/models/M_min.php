<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_min extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		if ($this->session->userdata('isLog') == FALSE) {
			redirect(base_url());
		}
		if ($this->session->userdata('isId') == "") {
			redirect(base_url());
		}
		if ($this->session->userdata('isUname') == "") {
			redirect(base_url());
		}
		if ($this->session->userdata('isPass') == "") {
			redirect(base_url());
		}
		if ($this->session->userdata('isLevel') !== 'mimin') {
			redirect(base_url());
		}
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
		$today = date('Y-m-d');
		$query = "select * from log join simimin on simimin.minid=log.id_user where log.tanggal='$today' order by log.id_log desc";
		return $this->db->query($query);
	}

	public function log()
	{
		$this->db->select('*');
		$this->db->from('log');
		$this->db->join('simimin', 'simimin.minid=log.id_user');
		$this->db->order_by('log.id_log', 'DESC');
		return $this->db->get();
	}

	public function jml_penghuni()
	{
		$this->db->select('id_mnl_siswa');
		$this->db->from('mnl_siswa');
		$query =  $this->db->get();
		return $hasil = $query->num_rows();
	}

	public function jml_bb()
	{
		$this->db->select('id_mnl_siswa');
		$this->db->from('mnl_pembayaran');
		$this->db->where('status', 'belum');
		$query =  $this->db->get();
		return $hasil = $query->num_rows();
	}

	public function jml_br()
	{
		$this->db->select('id_mnl_siswa');
		$this->db->from('mnl_siswa');
		$this->db->where('status', 'belum');
		$query =  $this->db->get();
		return $hasil = $query->num_rows();
	}

	// ------------------------------- MASTER - KANDANG -------------------------------

	public function rd_kandang()
	{
		$this->db->order_by('nama');
		// $this->db->join('kos_jenis_kamar', 'kos_jenis_kamar.id_jenis_kamar=kos_kamar.id_jenis_kamar');
		$this->db->where('hapus', 0);
		return $this->db->get('v_kandang')->result();
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
		$this->db->where('minid', $id);
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
		$this->db->select_max('id_mnl_siswa');
		$this->db->order_by('id_mnl_siswa', 'DESC');
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

	// public function copy_data_penghuni()
	// {
	// 	$query = $this->db->query("INSERT INTO mnl_tmp_pembayaran (id_mnl_siswa, id_mnl_spp) SELECT id_mnl_siswa, id_mnl_spp FROM mnl_siswa");
	// 	return $query;
	// }

	public function copy_data_penghuni()
	{
		$query = $this->db->query("INSERT INTO mnl_tmp_pembayaran (id_mnl_siswa, id_mnl_spp) SELECT id_mnl_siswa, id_mnl_spp
		FROM mnl_siswa
		WHERE NOT EXISTS 
		(SELECT id_mnl_siswa FROM mnl_pembayaran WHERE mnl_pembayaran.id_mnl_siswa = mnl_siswa.id_mnl_siswa AND mnl_pembayaran.bulan = '" . date('m') . "')");
		return $query;
	}

	// public function data_tambah()
	// {
	// 	return $this->db->affected_rows('mml_pembayaran') > 0;
	// }

	public function truncate_tmp_pembayaran()
	{
		return $this->db->truncate('mnl_tmp_pembayaran');
	}

	public function copy_data_pembayaran()
	{
		$query = $this->db->query("INSERT INTO `mnl_pembayaran` (`id_mnl_siswa`,`id_mnl_spp`,`bulan`,`tahun`,`status`) SELECT `id_mnl_siswa`,`id_mnl_spp`,`bulan`,`tahun`,`status` FROM `mnl_tmp_pembayaran`");
		return $query;
	}

	public function cek_pembayaran()
	{
		$this->db->select('*');
		$this->db->from('mnl_pembayaran');
		$this->db->where('bulan', date('m'));
		$this->db->where('tahun', date('Y'));
		$query =  $this->db->get();
		$hasil = $query->row();
		return $hasil;
	}

	public function bukti_tf($id)
	{
		$this->db->select('*');
		$this->db->from('mnl_pembayaran');
		$this->db->where('id_mnl_siswa', $id);
		return  $this->db->get();
	}

	public function rd_pembayaran_all()
	{
		$this->db->select('mnl_pembayaran.*, mnl_kelas.*, mnl_lokasi.*, mnl_siswa.id_mnl_siswa, mnl_siswa.id_mnl_kelas, mnl_siswa.id_mnl_lokasi, mnl_siswa.nama, mnl_spp.id_mnl_spp, mnl_spp.nominal');
		$this->db->join('mnl_siswa', 'mnl_siswa.id_mnl_siswa=mnl_pembayaran.id_mnl_siswa');
		$this->db->join('mnl_kelas', 'mnl_kelas.id_mnl_kelas=mnl_siswa.id_mnl_kelas');
		$this->db->join('mnl_lokasi', 'mnl_lokasi.id_mnl_lokasi=mnl_siswa.id_mnl_lokasi');
		$this->db->join('mnl_spp', 'mnl_spp.id_mnl_spp=mnl_pembayaran.id_mnl_spp');
		$this->db->order_by('mnl_pembayaran.bulan');
		return $this->db->get('mnl_pembayaran')->result();
	}

	public function rd_pembayaran_ba()
	{
		$this->db->select('mnl_pembayaran.*, mnl_kelas.*, mnl_lokasi.*, mnl_siswa.id_mnl_siswa, mnl_siswa.id_mnl_kelas, mnl_siswa.id_mnl_lokasi, mnl_siswa.nama, mnl_spp.id_mnl_spp, mnl_spp.nominal');
		$this->db->join('mnl_siswa', 'mnl_siswa.id_mnl_siswa=mnl_pembayaran.id_mnl_siswa');
		$this->db->join('mnl_kelas', 'mnl_kelas.id_mnl_kelas=mnl_siswa.id_mnl_kelas');
		$this->db->join('mnl_lokasi', 'mnl_lokasi.id_mnl_lokasi=mnl_siswa.id_mnl_lokasi');
		$this->db->join('mnl_spp', 'mnl_spp.id_mnl_spp=mnl_pembayaran.id_mnl_spp');
		$this->db->where('mnl_pembayaran.status', 'proses');
		$this->db->order_by('mnl_pembayaran.bulan');
		return $this->db->get('mnl_pembayaran')->result();
	}

	public function rd_pembayaran_bb()
	{
		$this->db->select('mnl_pembayaran.*, mnl_kelas.*, mnl_lokasi.*, mnl_siswa.id_mnl_siswa, mnl_siswa.id_mnl_kelas, mnl_siswa.id_mnl_lokasi, mnl_siswa.nama, mnl_spp.id_mnl_spp, mnl_spp.nominal');
		$this->db->join('mnl_siswa', 'mnl_siswa.id_mnl_siswa=mnl_pembayaran.id_mnl_siswa');
		$this->db->join('mnl_kelas', 'mnl_kelas.id_mnl_kelas=mnl_siswa.id_mnl_kelas');
		$this->db->join('mnl_lokasi', 'mnl_lokasi.id_mnl_lokasi=mnl_siswa.id_mnl_lokasi');
		$this->db->join('mnl_spp', 'mnl_spp.id_mnl_spp=mnl_pembayaran.id_mnl_spp');
		$this->db->where('mnl_pembayaran.status', 'belum');
		$this->db->order_by('mnl_pembayaran.bulan');
		return $this->db->get('mnl_pembayaran')->result();
	}

	public function rd_pembayaran_sb()
	{
		$this->db->select('mnl_pembayaran.*, mnl_kelas.*, mnl_lokasi.*, mnl_siswa.id_mnl_siswa, mnl_siswa.id_mnl_kelas, mnl_siswa.id_mnl_lokasi, mnl_siswa.nama, mnl_spp.id_mnl_spp, mnl_spp.nominal');
		$this->db->join('mnl_siswa', 'mnl_siswa.id_mnl_siswa=mnl_pembayaran.id_mnl_siswa');
		$this->db->join('mnl_kelas', 'mnl_kelas.id_mnl_kelas=mnl_siswa.id_mnl_kelas');
		$this->db->join('mnl_lokasi', 'mnl_lokasi.id_mnl_lokasi=mnl_siswa.id_mnl_lokasi');
		$this->db->join('mnl_spp', 'mnl_spp.id_mnl_spp=mnl_pembayaran.id_mnl_spp');
		$this->db->where('mnl_pembayaran.status', 'sudah');
		$this->db->order_by('mnl_pembayaran.bulan');
		return $this->db->get('mnl_pembayaran')->result();
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
