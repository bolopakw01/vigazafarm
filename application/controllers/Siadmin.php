<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Siadmin extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_loadmin');
	}

	public function index()
	{
		$this->load->view('admin/login');
	}

	public function auth()
	{
		if ($this->m_loadmin->cek_mimin()->num_rows() == 1) {
			// hash verifikasi
			$secure = $this->m_loadmin->cek_mimin()->row();
			if (hash_verified($this->input->post('password'), $secure->password)) {
				$sessionArray = array(
					'isLog' => TRUE,
					'isId' => $secure->minid,
					'isUname' => $secure->username,
					'isPass' => $secure->password,
					'isLevel' => $secure->level
				);

				$this->session->set_userdata($sessionArray);
				redirect('backadmin');
			} else {
				$this->session->set_flashdata('pesan', '<div class=\'alert alert-danger\' id=\'gone\'><center>Password Salah</center></div>');
				redirect('siadmin');
			}
		} else {
			$this->session->set_flashdata('pesan', '<div class=\'alert alert-danger\' id=\'gone\'><center>Username Tidak Terdaftar</center></div>');
			redirect('siadmin');
		}
	}

	public function logout()
	{
		$this->session->unset_userdata('isLog');
		$this->session->unset_userdata('isId');
		$this->session->unset_userdata('isUname');
		$this->session->unset_userdata('isPass');
		$this->session->unset_userdata('isLevel');
		session_destroy();
		redirect('siadmin');
	}
}
