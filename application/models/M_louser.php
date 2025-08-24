<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_louser extends CI_Model
{

	public function cek()
	{
		$this->db->select('*');
		$this->db->from('mnl_ortu');
		$this->db->where('no_hp', $this->input->post('uname'));
		return $this->db->get();
	}
}
