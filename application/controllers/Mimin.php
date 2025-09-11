<?php
defined('BASEPATH') or exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Mimin extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_login');
		$this->load->helper('login'); // Load login helper
	}

	public function index()
	{
		// Debug: Check if there's any alert flashdata
		$alert = $this->session->flashdata('alert');
		if ($alert) {
			log_message('debug', 'Alert flashdata found in index: ' . json_encode($alert));
			error_log('Alert flashdata found in index: ' . json_encode($alert));
		} else {
			log_message('debug', 'No alert flashdata found in index');
			error_log('No alert flashdata found in index');
		}
		
		$this->load->view('admin/login_admin');
	}

	public function login()
	{
		// Redirect to index (login page)
		$this->index();
	}

	public function auth()
	{
		// Cek POST data
		$username = trim($this->input->post('uname'));
		$password = $this->input->post('password');
		
		// Debug: Log attempt
		log_message('debug', 'Login attempt for username: ' . $username);
		error_log('Login attempt - Username: ' . $username . ', Password length: ' . strlen($password));
		
		// Validasi input
		if (empty($username) || empty($password)) {
			$this->session->set_flashdata('alert', array(
				'type' => 'error',
				'title' => 'Error!',
				'message' => 'Username dan Password harus diisi'
			));
			redirect('mimin');
			return;
		}
		
		// Query database - pastikan menggunakan parameter yang benar
		$this->db->select('*');
		$this->db->from('simimin');
		$this->db->where('username', $username);
		$this->db->where('status', 'aktif');
		$result = $this->db->get();
		
		if ($result->num_rows() == 1) {
			$user = $result->row();
			
			// Debug: Log user found
			log_message('debug', 'User found - Level: ' . $user->level . ', Role: ' . $user->role);
			error_log('User found - Password verify test: ' . (password_verify($password, $user->password) ? 'SUCCESS' : 'FAILED'));
			
			// Verifikasi password
			if (password_verify($password, $user->password)) {
				// Set session data dengan explicit casting
				$sessionData = array(
					'isLog' => TRUE,
					'isId' => (int)$user->id,
					'minid' => (int)$user->id,
					'isUname' => $user->username,
					'isPass' => $user->password,
					'isLevel' => $user->level,
					'isRole' => $user->role,
					'nama_lengkap' => $user->nama_lengkap
				);

				// Set session data (CodeIgniter 3 way)
				$this->session->set_userdata($sessionData);
				
				// Set success alert
				$this->session->set_flashdata('alert', array(
					'type' => 'success',
					'title' => 'Berhasil!',
					'message' => 'Login berhasil. Selamat datang ' . $user->nama_lengkap . '!'
				));
				
				// Debug: Log successful login before redirect
				log_message('debug', 'Login successful, setting flashdata and redirecting');
				error_log('Login successful for user: ' . $user->username . ', redirecting to dashboard');
				
				// Redirect berdasarkan level
				redirect('mimin'); // Redirect to mimin first to show alert, then JavaScript will redirect to dashboard
			} else {
				// Debug: Log password verification failure
				log_message('debug', 'Password verification failed for user: ' . $user->username);
				error_log('Password verification failed for user: ' . $user->username);
				
				$this->session->set_flashdata('alert', array(
					'type' => 'error',
					'title' => 'Error!',
					'message' => 'Password yang Anda masukkan salah'
				));
				redirect('mimin');
			}
		} else {
			// Debug: Log user not found
			log_message('debug', 'User not found: ' . $username);
			error_log('User not found: ' . $username);
			
			$this->session->set_flashdata('alert', array(
				'type' => 'error',
				'title' => 'Error!',
				'message' => 'Username tidak terdaftar atau tidak aktif'
			));
			redirect('mimin');
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
		redirect('mimin');
	}
}
