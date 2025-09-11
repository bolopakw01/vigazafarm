<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . 'controllers/Base_Controller.php');

/**
 * Settings Controller
 * Menangani pengaturan sistem dan manajemen admin
 */
class Settings extends Base_Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Halaman pengaturan sistem
	 */
	public function index()
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'settings';
		$data['thisPg']		= 'pengaturan';
		$data['template']	= $this->m_min->get_template_messages();

		$this->load_template($data, 'admin/page/pengaturan_admin');
	}

	/**
	 * Update template pesan
	 */
	public function update_template()
	{
		$msg_hmin14 = $this->input->post('msg_hmin14');
		$msg_hmin7 = $this->input->post('msg_hmin7');
		$msg_hmin3 = $this->input->post('msg_hmin3');
		$msg_hplus3 = $this->input->post('msg_hplus3');
		$msg_hplus7 = $this->input->post('msg_hplus7');

		$templates = array(
			array('key' => 'hmin14', 'message' => $msg_hmin14),
			array('key' => 'hmin7', 'message' => $msg_hmin7),
			array('key' => 'hmin3', 'message' => $msg_hmin3),
			array('key' => 'hplus3', 'message' => $msg_hplus3),
			array('key' => 'hplus7', 'message' => $msg_hplus7)
		);

		$success = true;
		foreach ($templates as $template) {
			$data = array('message' => $template['message']);
			$where = array('key' => $template['key']);
			
			if (!$this->m_min->update('template_messages', $data, $where)) {
				$success = false;
				break;
			}
		}

		if ($success) {
			// Log aktivitas
			$tb_log = 'log';
			$log = array(
				'id_user' 	=> $this->session->userdata('isId'),
				'aksi'    	=> 'Mengupdate template pesan WhatsApp',
			);
			$this->m_min->insert($tb_log, $log);

			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Template pesan berhasil diupdate</center></div></div>");
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal mengupdate template pesan</center></div></div>");
		}

		redirect('settings');
	}

	/**
	 * Halaman manajemen admin
	 */
	public function admin()
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'settings';
		$data['thisPg']		= 'admin';
		$data['admin']		= $this->m_min->rd_admin();

		$this->load_template($data, 'admin/page/admin_management');
	}

	/**
	 * Form tambah admin
	 */
	public function tambah_admin()
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'settings';
		$data['thisPg']		= 'admin';

		$this->load_template($data, 'admin/page/tambah_admin');
	}

	/**
	 * Simpan admin baru
	 */
	public function simpan_admin()
	{
		$nama = $this->input->post('nama');
		$username = $this->input->post('username');
		$password = $this->input->post('password');

		// Cek apakah username sudah ada
		if ($this->m_min->cek_username($username)->num_rows() > 0) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-warning\" id=\"alert\"><center>Username sudah ada</center></div></div>");
			redirect('settings/tambah_admin');
			return;
		}

		$data = array(
			'nama' => $nama,
			'username' => $username,
			'password' => get_hash($password),
			'level' => 'mimin',
			'status' => 'aktif',
			'created_at' => date('Y-m-d H:i:s')
		);

		$insert = $this->m_min->insert('mnl_admin', $data);

		if ($insert) {
			// Log aktivitas
			$tb_log = 'log';
			$log = array(
				'id_user' 	=> $this->session->userdata('isId'),
				'aksi'    	=> 'Menambah admin baru - ' . $nama,
			);
			$this->m_min->insert($tb_log, $log);

			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Admin berhasil ditambahkan</center></div></div>");
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal menambah admin</center></div></div>");
		}

		redirect('settings/admin');
	}

	/**
	 * Edit admin
	 */
	public function edit_admin($id)
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'settings';
		$data['thisPg']		= 'admin';
		$data['admin']		= $this->m_min->get_admin_by_id($id)->row_array();

		$this->load_template($data, 'admin/page/edit_admin');
	}

	/**
	 * Update admin
	 */
	public function update_admin()
	{
		$id = $this->input->post('id');
		$nama = $this->input->post('nama');
		$username = $this->input->post('username');
		$password_baru = $this->input->post('password');

		$data = array(
			'nama' => $nama,
			'username' => $username,
			'updated_at' => date('Y-m-d H:i:s')
		);

		// Jika ada password baru
		if (!empty($password_baru)) {
			$data['password'] = get_hash($password_baru);
		}

		$where = array('minid' => $id);
		$update = $this->m_min->update('mnl_admin', $data, $where);

		if ($update) {
			// Log aktivitas
			$tb_log = 'log';
			$log = array(
				'id_user' 	=> $this->session->userdata('isId'),
				'aksi'    	=> 'Mengupdate data admin - ' . $nama,
			);
			$this->m_min->insert($tb_log, $log);

			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Data admin berhasil diupdate</center></div></div>");
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal mengupdate data admin</center></div></div>");
		}

		redirect('settings/admin');
	}
}
