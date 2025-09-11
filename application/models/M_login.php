<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_login extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Check login credentials (legacy support)
	 */
	public function cek_mimin()
	{
		$this->db->select('*');
		$this->db->from('simimin');
		$this->db->where('username', $this->input->post('uname'));
		return $this->db->get();
	}

	/**
	 * Get user by username from users table (new enhanced system)
	 */
	public function get_user_by_username($username)
	{
		if ($this->db->table_exists('users')) {
			$this->db->select('*');
			$this->db->from('users');
			$this->db->where('username', $username);
			$this->db->where('status', 'aktif');
			return $this->db->get()->row();
		}
		return null;
	}

	/**
	 * Authenticate user with password verification
	 */
	public function authenticate($username, $password)
	{
		$user = $this->get_user_by_username($username);
		
		if ($user && password_verify($password, $user->password)) {
			return $user;
		}
		
		return false;
	}

	/**
	 * Update last login timestamp
	 */
	public function update_last_login($user_id)
	{
		if ($this->db->table_exists('users')) {
			$this->db->where('id', $user_id);
			$this->db->update('users', [
				'last_login' => date('Y-m-d H:i:s')
			]);
		}
	}

	/**
	 * Get all users with role filtering
	 */
	public function get_users($role = null)
	{
		if (!$this->db->table_exists('users')) {
			return [];
		}

		$this->db->select('id, username, nama_lengkap, email, phone, role, status, last_login, created_at');
		$this->db->from('users');
		
		if ($role) {
			$this->db->where('role', $role);
		}
		
		$this->db->order_by('created_at', 'DESC');
		return $this->db->get()->result();
	}

	/**
	 * Create new user
	 */
	public function create_user($data)
	{
		if (!$this->db->table_exists('users')) {
			return false;
		}

		// Hash password
		if (isset($data['password'])) {
			$data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
		}

		$data['created_at'] = date('Y-m-d H:i:s');
		return $this->db->insert('users', $data);
	}

	/**
	 * Update user
	 */
	public function update_user($id, $data)
	{
		if (!$this->db->table_exists('users')) {
			return false;
		}

		// Hash password if provided
		if (isset($data['password']) && !empty($data['password'])) {
			$data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
		} else {
			unset($data['password']);
		}

		$data['updated_at'] = date('Y-m-d H:i:s');
		$this->db->where('id', $id);
		return $this->db->update('users', $data);
	}

	/**
	 * Delete user
	 */
	public function delete_user($id)
	{
		if (!$this->db->table_exists('users')) {
			return false;
		}

		$this->db->where('id', $id);
		return $this->db->delete('users');
	}

	/**
	 * Check if username exists
	 */
	public function username_exists($username, $exclude_id = null)
	{
		if (!$this->db->table_exists('users')) {
			return false;
		}

		$this->db->where('username', $username);
		
		if ($exclude_id) {
			$this->db->where('id !=', $exclude_id);
		}
		
		return $this->db->count_all_results('users') > 0;
	}

	/**
	 * Check if email exists
	 */
	public function email_exists($email, $exclude_id = null)
	{
		if (!$this->db->table_exists('users')) {
			return false;
		}

		$this->db->where('email', $email);
		
		if ($exclude_id) {
			$this->db->where('id !=', $exclude_id);
		}
		
		return $this->db->count_all_results('users') > 0;
	}

	/**
	 * Get user by ID
	 */
	public function get_user_by_id($id)
	{
		if (!$this->db->table_exists('users')) {
			return null;
		}

		$this->db->select('*');
		$this->db->from('users');
		$this->db->where('id', $id);
		return $this->db->get()->row();
	}

	/**
	 * Change user status
	 */
	public function change_user_status($id, $status)
	{
		if (!$this->db->table_exists('users')) {
			return false;
		}

		$this->db->where('id', $id);
		return $this->db->update('users', [
			'status' => $status,
			'updated_at' => date('Y-m-d H:i:s')
		]);
	}

	/**
	 * Get user statistics
	 */
	public function get_user_stats()
	{
		if (!$this->db->table_exists('users')) {
			return [
				'total_users' => 0,
				'active_users' => 0,
				'admin_count' => 0,
				'manager_count' => 0,
				'operator_count' => 0
			];
		}

		$this->db->select('
			COUNT(*) as total_users,
			COUNT(CASE WHEN status = "aktif" THEN 1 END) as active_users,
			COUNT(CASE WHEN role = "admin" THEN 1 END) as admin_count,
			COUNT(CASE WHEN role = "manager" THEN 1 END) as manager_count,
			COUNT(CASE WHEN role = "operator" THEN 1 END) as operator_count
		');
		$this->db->from('users');
		return $this->db->get()->row_array();
	}
}
