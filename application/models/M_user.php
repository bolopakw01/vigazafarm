<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_user extends CI_Model
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
		if ($this->session->userdata('isHp') == "") {
			redirect(base_url());
		}
		if ($this->session->userdata('isPass') == "") {
			redirect(base_url());
		}
		if ($this->session->userdata('isLevel') !== 'ortu') {
			redirect(base_url());
		}
	}

	/* ========================================= READ ========================================= */

	public function profil($data)
	{
		$this->db->where('no_hp', $data);
		return $this->db->get('mnl_ortu');
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

	public function bb_regist()
	{
		$this->db->select('id_mnl_siswa');
		$this->db->from('mnl_siswa');
		$this->db->where('status', 'belum');
		$this->db->where('no_hp', $this->session->userdata('isHp'));
		$query =  $this->db->get();
		return $hasil = $query->num_rows();
	}

	public function bb_spp()
	{
		$this->db->select('mnl_pembayaran.id_mnl_siswa');
		$this->db->from('mnl_pembayaran');
		$this->db->join('mnl_siswa', 'mnl_siswa.id_mnl_siswa=mnl_pembayaran.id_mnl_siswa');
		$this->db->where('mnl_pembayaran.status', 'belum');
		$this->db->where('mnl_siswa.no_hp', $this->session->userdata('isHp'));
		$query =  $this->db->get();
		return $hasil = $query->num_rows();
	}

	public function jml_bb_regist()
	{
		$this->db->select_sum('mnl_regist.nominal');
		$this->db->from('mnl_regist');
		$this->db->join('mnl_siswa', 'mnl_siswa.id_mnl_regist=mnl_regist.id_mnl_regist');
		$this->db->join('mnl_pembayaran', 'mnl_pembayaran.id_mnl_siswa=mnl_siswa.id_mnl_siswa');
		$this->db->where('mnl_siswa.status', 'belum');
		$this->db->where('mnl_siswa.no_hp', $this->session->userdata('isHp'));
		$result = $this->db->get()->row();

		if ($result->nominal == null) {
			return 0;
		} else {
			return $result->nominal;
		}
	}

	public function jml_bb_spp()
	{
		$this->db->select_sum('mnl_spp.nominal');
		$this->db->from('mnl_spp');
		$this->db->join('mnl_siswa', 'mnl_siswa.id_mnl_spp=mnl_spp.id_mnl_spp');
		$this->db->join('mnl_pembayaran', 'mnl_pembayaran.id_mnl_siswa=mnl_siswa.id_mnl_siswa');
		$this->db->where('mnl_pembayaran.status', 'belum');
		$this->db->where('mnl_siswa.no_hp', $this->session->userdata('isHp'));
		$result = $this->db->get()->row();

		if ($result->nominal == null) {
			return 0;
		} else {
			return $result->nominal;
		}
	}

	public function rd_siswa($no_hp)
	{
		$this->db->select('*');
		$this->db->from('mnl_siswa');
		$this->db->join('mnl_kelas', 'mnl_kelas.id_mnl_kelas=mnl_siswa.id_mnl_kelas');
		$this->db->where('no_hp', $no_hp);
		return $this->db->get();
	}

	public function rd_invoice($id)
	{
		$this->db->select('mnl_pembayaran.*, mnl_siswa.id_mnl_siswa, mnl_siswa.id_mnl_kelas, mnl_siswa.id_mnl_lokasi, mnl_siswa.nama, mnl_spp.id_mnl_spp, mnl_spp.nominal');
		$this->db->from('mnl_pembayaran');
		$this->db->join('mnl_siswa', 'mnl_siswa.id_mnl_siswa=mnl_pembayaran.id_mnl_siswa');
		$this->db->join('mnl_spp', 'mnl_spp.id_mnl_spp=mnl_pembayaran.id_mnl_spp');
		$this->db->where('mnl_pembayaran.id_mnl_siswa', $id);
		return $this->db->get();
	}

	public function rd_history($no_hp)
	{
		$this->db->select('mnl_pembayaran.*, mnl_kelas.*, mnl_lokasi.*, mnl_siswa.id_mnl_siswa, mnl_siswa.id_mnl_kelas, mnl_siswa.id_mnl_lokasi, mnl_siswa.nama');
		$this->db->join('mnl_siswa', 'mnl_siswa.id_mnl_siswa=mnl_pembayaran.id_mnl_siswa');
		$this->db->join('mnl_kelas', 'mnl_kelas.id_mnl_kelas=mnl_siswa.id_mnl_kelas');
		$this->db->join('mnl_lokasi', 'mnl_lokasi.id_mnl_lokasi=mnl_siswa.id_mnl_lokasi');
		$this->db->where('mnl_siswa.no_hp', $no_hp);
		$this->db->where('mnl_pembayaran.status', 'sudah');
		$this->db->order_by('mnl_pembayaran.id_mnl_pembayaran', 'DESC');
		return $this->db->get('mnl_pembayaran')->result();
	}

	public function rd_pembayaran($id)
	{
		$this->db->select('*');
		$this->db->from('mnl_pembayaran');
		$this->db->where('id_mnl_siswa', $id);
		return $this->db->get();
	}

	function getid_siswa($id_mnl_siswa = '')
	{
		$this->db->join('mnl_regist', 'mnl_regist.id_mnl_regist=mnl_siswa.id_mnl_regist');
		return $this->db->get_where('mnl_siswa', array('id_mnl_siswa' => $id_mnl_siswa))->row();
	}

	function get_pmb_regist($id_mnl_siswa = '')
	{
		return $this->db->get_where('mnl_pmb_regist', array('id_mnl_siswa' => $id_mnl_siswa))->row();
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
		$this->db->update('mnl_ortu');
		return TRUE;
	}

	public function update($table, $data, $where)
	{
		$this->db->set($data);
		$this->db->where($where);
		$this->db->update($table);
		return TRUE;
	}

	/* ========================================= DELETE ========================================= */

	function hapus($table, $where)
	{
		$this->db->where($where);
		$this->db->delete($table);
	}
}
