<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_login extends CI_Model
{

	public function cek_mimin()
	{
		$this->db->select('*');
		$this->db->from('simimin');
		$this->db->where('username', $this->input->post('uname'));
		return $this->db->get();
	}
}
