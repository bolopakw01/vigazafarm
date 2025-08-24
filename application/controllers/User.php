<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_louser');
	}

	public function index()
	{
		$this->load->view('user/login');
	}

	public function auth()
	{
		if ($this->m_louser->cek()->num_rows() == 1) {
			// hash verifikasi
			$secure = $this->m_louser->cek()->row();
			if (hash_verified($this->input->post('password'), $secure->password)) {
				$sessionArray = array(
					'isLog' => TRUE,
					'isId' => $secure->id_mnl_ortu,
					'isHp' => $secure->no_hp,
					'isPass' => $secure->password,
					'isLevel' => $secure->level
				);

				$this->session->set_userdata($sessionArray);
				redirect('backuser');
			} else {
				$this->session->set_flashdata('pesan', '<div class=\'alert alert-danger\' id=\'gone\'><center>Password Salah</center></div>');
				redirect('user');
			}
		} else {
			$this->session->set_flashdata('pesan', '<div class=\'alert alert-danger\' id=\'gone\'><center>Username Tidak Terdaftar</center></div>');
			redirect('user');
		}
	}

	public function logout()
	{
		$this->session->unset_userdata('isLog');
		$this->session->unset_userdata('isId');
		$this->session->unset_userdata('isHp');
		$this->session->unset_userdata('isPass');
		$this->session->unset_userdata('isLevel');
		session_destroy();
		redirect('user');
	}
}
