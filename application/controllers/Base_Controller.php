<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Enhanced Base Controller dengan Role-Based Access Control
 * Mendukung 3 level akses: admin, manager, operator
 */
class Base_Controller extends CI_Controller
{
	protected $user_data;
	protected $user_role;
	protected $allowed_roles = [];

	public function __construct()
	{
		parent::__construct();
		
		// Load libraries
		$this->load->library(['session', 'template']);
		$this->load->helper(['url', 'security']);
		$this->load->model(['m_min']);
		
		// Check authentication
		$this->check_authentication();
		
		// Check authorization
		$this->check_authorization();
		
		// Load common data
		$this->load_common_data();
		
		date_default_timezone_set("Asia/Jakarta");
	}

	/**
	 * Check if user is authenticated
	 */
	private function check_authentication()
	{
		// Support old session format
		if ($this->session->userdata('isLog') == FALSE) {
			redirect('mimin');
		}
		if ($this->session->userdata('isId') == "") {
			redirect('mimin');
		}
		if ($this->session->userdata('isUname') == "") {
			redirect('mimin');
		}
		if ($this->session->userdata('isPass') == "") {
			redirect('mimin');
		}

		// Get user data safely
		$username = $this->session->userdata('isUname');
		
		try {
			$result = $this->m_min->profil($username);
			
			if ($result && $result->num_rows() > 0) {
				$this->user_data = $result->row();
			} else {
				// Fallback user data from session
				$this->user_data = (object) [
					'id' => $this->session->userdata('isId'),
					'username' => $this->session->userdata('isUname'),
					'level' => $this->session->userdata('isLevel'),
					'nama_lengkap' => $this->session->userdata('isUname'),
					'role' => $this->session->userdata('isLevel') == 'super_admin' ? 'super_admin' : 'admin'
				];
			}
		} catch (Exception $e) {
			// Fallback user data from session if database error
			$this->user_data = (object) [
				'id' => $this->session->userdata('isId'),
				'username' => $this->session->userdata('isUname'),
				'level' => $this->session->userdata('isLevel'),
				'nama_lengkap' => $this->session->userdata('isUname'),
				'role' => $this->session->userdata('isLevel') == 'super_admin' ? 'super_admin' : 'admin'
			];
		}

		if (!$this->user_data) {
			$this->session->sess_destroy();
			redirect(base_url());
			exit;
		}

		// Set role from database or default to admin for backward compatibility
		$this->user_role = $this->user_data->role ?? 'admin';
	}

	/**
	 * Check if user has permission to access current controller/method
	 */
	private function check_authorization()
	{
		// If no specific roles defined, allow all authenticated users
		if (empty($this->allowed_roles)) {
			return;
		}

		// Check if user role is in allowed roles
		if (!in_array($this->user_role, $this->allowed_roles)) {
			show_error('Access Denied: Anda tidak memiliki permission untuk mengakses halaman ini.', 403);
		}
	}

	/**
	 * Load common data for all views
	 */
	private function load_common_data()
	{
		// Set common view data
		$this->common_data = [
			'user' => $this->user_data,
			'user_role' => $this->user_role,
			'page_title' => 'Vigaza Farm Management System',
			'current_time' => date('Y-m-d H:i:s')
		];
	}

	/**
	 * Set required roles for controller
	 */
	protected function require_roles($roles)
	{
		$this->allowed_roles = is_array($roles) ? $roles : [$roles];
		$this->check_authorization();
	}

	/**
	 * Check if user has specific role
	 */
	protected function has_role($role)
	{
		return $this->user_role === $role;
	}

	/**
	 * Check if user has any of the specified roles
	 */
	protected function has_any_role($roles)
	{
		$roles = is_array($roles) ? $roles : [$roles];
		return in_array($this->user_role, $roles);
	}

	/**
	 * Get user permissions based on role
	 */
	protected function get_permissions()
	{
		$permissions = [
			'admin' => [
				'dashboard' => ['read'],
				'penetasan' => ['create', 'read', 'update', 'delete', 'export'],
				'pembesaran' => ['create', 'read', 'update', 'delete', 'export'],
				'produksi' => ['create', 'read', 'update', 'delete', 'export'],
				'kandang' => ['create', 'read', 'update', 'delete'],
				'karyawan' => ['create', 'read', 'update', 'delete'],
				'users' => ['create', 'read', 'update', 'delete'],
				'laporan' => ['read', 'export'],
				'settings' => ['read', 'update']
			],
			'manager' => [
				'dashboard' => ['read'],
				'penetasan' => ['read', 'export'],
				'pembesaran' => ['read', 'export'],
				'produksi' => ['read', 'export'],
				'kandang' => ['read'],
				'karyawan' => ['read'],
				'laporan' => ['read', 'export']
			],
			'operator' => [
				'dashboard' => ['read'],
				'penetasan' => ['create', 'read', 'update'],
				'pembesaran' => ['create', 'read', 'update'],
				'produksi' => ['create', 'read', 'update'],
				'kandang' => ['read']
			]
		];

		return $permissions[$this->user_role] ?? [];
	}

	/**
	 * Check if user can perform action on module
	 */
	protected function can($module, $action)
	{
		$permissions = $this->get_permissions();
		return isset($permissions[$module]) && in_array($action, $permissions[$module]);
	}

	/**
	 * Require specific permission or show error
	 */
	protected function require_permission($module, $action)
	{
		if (!$this->can($module, $action)) {
			show_error("Access Denied: Anda tidak memiliki permission '$action' pada modul '$module'.", 403);
		}
	}

	/**
	 * JSON response helper
	 */
	protected function json_response($data, $status_code = 200)
	{
		$this->output
			->set_status_header($status_code)
			->set_content_type('application/json')
			->set_output(json_encode($data));
	}

	/**
	 * Success response helper
	 */
	protected function success_response($message, $data = null)
	{
		$response = [
			'status' => 'success',
			'message' => $message
		];
		
		if ($data !== null) {
			$response['data'] = $data;
		}
		
		$this->json_response($response);
	}

	/**
	 * Error response helper
	 */
	protected function error_response($message, $status_code = 400)
	{
		$this->json_response([
			'status' => 'error',
			'message' => $message
		], $status_code);
	}

	/**
	 * Log user activity
	 */
	protected function log_activity($action, $module, $description = '')
	{
		if (!isset($this->user_data->id)) return;
		
		$log_data = [
			'user_id' => $this->user_data->id,
			'action' => $action,
			'module' => $module,
			'description' => $description,
			'ip_address' => $this->input->ip_address(),
			'user_agent' => substr($this->input->user_agent(), 0, 255),
			'created_at' => date('Y-m-d H:i:s')
		];

		// Only log if table exists
		if ($this->db->table_exists('activity_logs')) {
			$this->db->insert('activity_logs', $log_data);
		}
	}

	/**
	 * Enhanced load template - backward compatible
	 */
	protected function load_template($data, $view = null, $datat = null)
	{
		// Support new format: load_template('view', $data)
		if (is_string($data) && is_array($view)) {
			$temp = $data;
			$data = $view;
			$view = $temp;
		}
		
		// Merge with common data if available
		if (isset($this->common_data)) {
			$data = array_merge($this->common_data, $data);
		}

		// Use legacy template system
		if ($datat === null) {
			$datat['record'] = $this->m_min->log_today();
		}

		$this->template->kepala('tbase/kepala', 'admin/template/header', $datat);
		$this->template->samping('tbase/samping', 'admin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'admin/template/top');
		$this->template->isi('tbase/isi', $view);
		$this->template->kaki('tbase/kaki', 'admin/template/footer');
	}

	/**
	 * Fungsi helper untuk WhatsApp API blast (legacy support)
	 */
	protected function api_blast($name, $receiver, $msg, $sch)
	{
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, 'https://mresidence.siaranwa.com/posts');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, [
			'user_id' => '1',
			'device_id' => '1',
			'phonebook_id' => '1',
			'delay' => '10',
			'name' => $name,
			'type' => 'text',
			'status' => 'waiting',
			'message' => '{"text":"' . $msg . '"}',
			'schedule' => $sch,
			'sender' => '6283843357816',
			'receiver' => $receiver,
			'statusmu' => 'pending',
		]);

		curl_exec($curl);
		curl_close($curl);
	}

	/**
	 * Fungsi helper untuk scheduling 5 tahun (legacy support)
	 */
	protected function lima_tahun($name, $receiver, $tgl, $msg_hmin14, $msg_hmin7, $msg_hmin3, $msg_hplus3, $msg_hplus7)
	{
		$date = date_create($tgl);

		for ($x = 1; $x <= 5; $x++) {
			date_add($date, date_interval_create_from_date_string("30 days"));
			$tanggal = date_format($date, "Y-m-d H:i:s");

			$hmin14 	= date("Y-m-d H:i:s", strtotime("$tanggal -14 day"));
			$hmin7  	= date("Y-m-d H:i:s", strtotime("$tanggal -7 day"));
			$hmin3  	= date("Y-m-d H:i:s", strtotime("$tanggal -3 day"));
			$hplus3  	= date("Y-m-d H:i:s", strtotime("$tanggal +3 day"));
			$hplus7  	= date("Y-m-d H:i:s", strtotime("$tanggal +7 day"));

			$bulan = date('m', strtotime($tanggal));
			$tahun = date('Y', strtotime($tanggal));

			$this->api_blast('H-14_' . $name . '_' . $bulan . '_' . $tahun, $receiver, $msg_hmin14, $hmin14);
			$this->api_blast('H-7_' . $name . '_' . $bulan . '_' . $tahun, $receiver, $msg_hmin7, $hmin7);
			$this->api_blast('H-3_' . $name . '_' . $bulan . '_' . $tahun, $receiver, $msg_hmin3, $hmin3);
			$this->api_blast('H+3_' . $name . '_' . $bulan . '_' . $tahun, $receiver, $msg_hplus3, $hplus3);
			$this->api_blast('H+7_' . $name . '_' . $bulan . '_' . $tahun, $receiver, $msg_hplus7, $hplus7);
		}
	}
}
