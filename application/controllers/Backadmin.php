<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Backadmin extends CI_Controller
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
		if ($this->session->userdata('isLevel') !== 'operator') {
			redirect(base_url());
		}

		$this->load->model('m_admin');

		date_default_timezone_set("Asia/Jakarta");
	}

	public function profil()
	{
		$data['profil'] 	= $this->m_admin->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'profil';
		$data['thisPg']		= 'profil';

		$datat['record']	= $this->m_admin->log_today();

		$this->template->kepala('tbase/kepala', 'admin/template/header', $datat);
		$this->template->samping('tbase/samping', 'admin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'admin/template/top');
		$this->template->isi('tbase/isi', 'admin/profil');
		$this->template->kaki('tbase/kaki', 'admin/template/footer');
	}

	function update_profil()
	{

		$pass_db = $this->input->post('pass_db');
		$pass =	$this->input->post('pass');

		if ($pass === $pass_db) {
			$data = array(
				'nama' 		=> $this->input->post('nm'),
				'username' 	=> $this->input->post('uname')
			);
		} else {
			$data = array(
				'nama' 		=> $this->input->post('nm'),
				'username' 	=> $this->input->post('uname'),
				'password' 	=> get_hash($pass)
			);
		}

		$where = array('minid' => $this->input->post('minid')); //array where query sebagai identitas pada saat query dijalankan
		$uprof = $this->m_admin->update_profil($data, $where); //akses model untuk menyimpan ke database

		if ($uprof) {
			$sessionArray = array(
				'isLog' 	=> TRUE,
				'isId' 		=> $this->input->post('minid'),
				'isUname' 	=> $this->input->post('uname'),
				'isPass' 	=> get_hash($this->input->post('pass')),
				'isLevel' 	=> 'operator'
			);

			$this->session->set_userdata($sessionArray);

			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Update data berhasil</center></div></div>");
			redirect('backadmin/profil'); //jika berhasil maka akan ditampilkan view vupload
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Update Data Gagal !!</center></div></div>");
			redirect('backadmin/profil'); //jika gagal maka akan ditampilkan form upload
		}
	}

	public function log()
	{
		$data['profil'] 	= $this->m_admin->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'settings';
		$data['thisPg']		= 'log';

		$datat['record']	= $this->m_admin->log_today();

		$data['log']		= $this->m_admin->log()->result();

		$this->template->kepala('tbase/kepala', 'admin/template/header', $datat);
		$this->template->samping('tbase/samping', 'admin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'admin/template/top');
		$this->template->isi('tbase/isi', 'admin/log');
		$this->template->kaki('tbase/kaki', 'admin/template/footer');
	}

	/* ========================================= PAGE ========================================= */

	public function index()
	{
		$data['profil'] 	= $this->m_admin->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'dashboard';
		$data['thisPg']		= 'dashboard';

		$datat['record']	= $this->m_admin->log_today();

		$data['data']		= $this->m_admin->rd_pembayaran_ba();
		$data['jml_siswa']	= $this->m_admin->jml_siswa();
		$data['jml_bb']		= $this->m_admin->jml_bb();
		$data['jml_br']		= $this->m_admin->jml_br();

		$this->template->kepala('tbase/kepala', 'admin/template/header', $datat);
		$this->template->samping('tbase/samping', 'admin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'admin/template/top');
		$this->template->isi('tbase/isi', 'admin/dashboard');
		$this->template->kaki('tbase/kaki', 'admin/template/footer');
	}

	public function pengaturan()
	{
		$data['profil'] 	= $this->m_admin->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'pengaturan';

		$datat['record']	= $this->m_admin->log_today();
		// $datat['data']		= $this->m_admin->rd_base()->row_array();
		$datat['generate']	= $this->m_admin->cek_pembayaran();

		$this->template->kepala('tbase/kepala', 'admin/template/header', $datat);
		$this->template->samping('tbase/samping', 'admin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'admin/template/top');
		$this->template->isi('tbase/isi', 'admin/transaksi/pengaturan');
		$this->template->kaki('tbase/kaki', 'admin/template/footer');
	}

	// public function up_nominal()
	// {
	// 	$table 		= 'mnl_base';

	// 	$where 		= array(
	// 		'id_mnl_base'	=> 1
	// 	);

	// 	$data = array(
	// 		'nominal'		=> str_replace(".", "", $this->input->post('nominal'))
	// 	);

	// 	$send = $this->m_admin->update($table, $data, $where);

	// 	$tb_log = 'log';
	// 	$log   	= array(
	// 		'id_user' 	=> $this->session->userdata('isId'),
	// 		'aksi'    	=> 'Mengubah data nominal pada ID - ' . 1 . '',
	// 	);
	// 	$this->m_admin->insert($tb_log, $log);

	// 	if ($send) {
	// 		$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
	// 		redirect('backadmin/pengaturan');
	// 	} else {
	// 		$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Gambar</center></div></div>");
	// 		redirect('backadmin/pengaturan');
	// 	}
	// }

	public function generate()
	{

		$this->m_admin->copy_data_siswa();

		$table 		= 'mnl_tmp_pembayaran';
		$tables		= 'mnl_pembayaran';

		$data = array(
			'bulan'		=> date('m'),
			'tahun'		=> date('Y'),
			'status'	=> 'belum'
		);

		$send = $this->m_admin->generate($table, $data);

		$this->m_admin->copy_data_pembayaran();
		$this->m_admin->truncate_tmp_pembayaran();

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Generate data bulan - ' . date('m') . '',
		);
		$this->m_admin->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Generate Data</center></div></div>");
			redirect('backadmin/pengaturan');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Generate Data</center></div></div>");
			redirect('backadmin/pengaturan');
		}
	}

	// ------------------------------------------------------------------------------------------

	// ------------------------------- MASTER - ADMIN -------------------------------

	public function admin()
	{
		$data['profil'] 	= $this->m_admin->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'admin';

		$datat['record']	= $this->m_admin->log_today();

		$data['data']		= $this->m_admin->rd_admin();

		$this->template->kepala('tbase/kepala', 'admin/template/header', $datat);
		$this->template->samping('tbase/samping', 'admin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'admin/template/top');
		$this->template->isi('tbase/isi', 'admin/transaksi/admin');
		$this->template->kaki('tbase/kaki', 'admin/template/footer');
	}

	public function tambah_admin()
	{
		$data['profil'] 	= $this->m_admin->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'admin';

		$datat['record']	= $this->m_admin->log_today();

		$this->template->kepala('tbase/kepala', 'admin/template/header', $datat);
		$this->template->samping('tbase/samping', 'admin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'admin/template/top');
		$this->template->isi('tbase/isi', 'admin/transaksi/tambah_admin');
		$this->template->kaki('tbase/kaki', 'admin/template/footer');
	}

	public function in_admin()
	{
		$table 		= 'simimin';

		if ($this->m_admin->cek_admin($this->input->post('uname'))->num_rows() == 1) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-warning\" id=\"alert\"><center>Username sudah ada</center></div></div>");
			redirect('backadmin/admin');
		} else {
			$data = array(
				'nama'			=> $this->input->post('nama'),
				'username'		=> $this->input->post('uname'),
				'password'  	=> get_hash($this->input->post('pass')),
				'hp'			=> $this->input->post('no_hp'),
				'level'   		=> 'operator',
				'jabatan' 		=> 'Admin'
			);

			$send = $this->m_admin->insert($table, $data);
		}

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Menambah data admin - ' . $this->input->post('nama') . '',
		);
		$this->m_admin->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backadmin/admin');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Gambar</center></div></div>");
			redirect('backadmin/admin');
		}
	}

	public function edit_admin($id)
	{
		$data['profil'] 	= $this->m_admin->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'admin';

		$datat['record']	= $this->m_admin->log_today();

		$data['data'] 		= $this->m_admin->edit_admin($id)->row_array();

		$this->template->kepala('tbase/kepala', 'admin/template/header', $datat);
		$this->template->samping('tbase/samping', 'admin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'admin/template/top');
		$this->template->isi('tbase/isi', 'admin/transaksi/edit_admin');
		$this->template->kaki('tbase/kaki', 'admin/template/footer');
	}

	public function up_admin()
	{
		$table 		= 'simimin';

		$where 		= array(
			'minid'	=> $this->input->post('id')
		);

		$pass_db 	= $this->input->post('pass_db');
		$pass 		= $this->input->post('password');

		if ($pass === $pass_db) {
			$data = array(
				'nama'   			=> $this->input->post('nama'),
				'username'   		=> $this->input->post('uname'),
				'hp'   				=> $this->input->post('no_hp')
			);
		} else {
			$data = array(
				'nama'   			=> $this->input->post('nama'),
				'username'   		=> $this->input->post('uname'),
				'hp'   				=> $this->input->post('no_hp'),
				'password' 			=> get_hash($pass)
			);
		}

		$send = $this->m_admin->update($table, $data, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Mengubah data admin dengan ID - ' . $this->input->post('id') . '',
		);
		$this->m_admin->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backadmin/admin');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Gambar</center></div></div>");
			redirect('backadmin/admin');
		}
	}

	function hapus_admin($id)
	{
		$table 		= 'simimin';
		$where 		= array('minid' => $id);

		$data = array(
			'hapus'   			=> 1
		);

		$this->m_admin->update($table, $data, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Menghapus data admin dengan ID - ' . $id . '',
		);
		$this->m_admin->insert($tb_log, $log);

		$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil hapus data</center></div></div>");
		redirect('backadmin/admin');
	}

	// ------------------------------------------------------------------------------------------

	// ------------------------------- MASTER - KELAS -------------------------------

	public function kelas()
	{
		$data['profil'] 	= $this->m_admin->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'kelas';

		$datat['record']	= $this->m_admin->log_today();

		$data['kelas']		= $this->m_admin->rd_kelas();

		$this->template->kepala('tbase/kepala', 'admin/template/header', $datat);
		$this->template->samping('tbase/samping', 'admin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'admin/template/top');
		$this->template->isi('tbase/isi', 'admin/transaksi/kelas');
		$this->template->kaki('tbase/kaki', 'admin/template/footer');
	}

	public function tambah_kelas()
	{
		$data['profil'] 	= $this->m_admin->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'kelas';

		$datat['record']	= $this->m_admin->log_today();

		$this->template->kepala('tbase/kepala', 'admin/template/header', $datat);
		$this->template->samping('tbase/samping', 'admin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'admin/template/top');
		$this->template->isi('tbase/isi', 'admin/transaksi/tambah_kelas');
		$this->template->kaki('tbase/kaki', 'admin/template/footer');
	}

	public function in_kelas()
	{
		$table 	= 'mnl_kelas';

		$data = array(
			'kelas'   => $this->input->post('nama')
		);

		$send = $this->m_admin->insert($table, $data);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Menambah data kelas - ' . $this->input->post('nama') . '',
		);
		$this->m_admin->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backadmin/kelas');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Gambar</center></div></div>");
			redirect('backadmin/kelas');
		}
	}

	public function edit_kelas($id)
	{
		$data['profil'] 	= $this->m_admin->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'settings';
		$data['thisPg']		= 'kelas';

		$datat['record']	= $this->m_admin->log_today();

		$data['data'] 		= $this->m_admin->edit_kelas($id)->row_array();

		$this->template->kepala('tbase/kepala', 'admin/template/header', $datat);
		$this->template->samping('tbase/samping', 'admin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'admin/template/top');
		$this->template->isi('tbase/isi', 'admin/transaksi/edit_kelas');
		$this->template->kaki('tbase/kaki', 'admin/template/footer');
	}

	public function up_kelas()
	{
		$table 		= 'mnl_kelas';

		$where 		= array(
			'id_mnl_kelas'	=> $this->input->post('id')
		);

		$data = array(
			'kelas'		=> $this->input->post('nama')
		);

		$send = $this->m_admin->update($table, $data, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Mengubah data kelas dengan ID - ' . $this->input->post('id') . '',
		);
		$this->m_admin->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backadmin/kelas');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Gambar</center></div></div>");
			redirect('backadmin/kelas');
		}
	}

	function hapus_kelas($id)
	{
		$table 		= 'mnl_kelas';
		$where 		= array('id_mnl_kelas' => $id);

		$this->m_admin->hapus($table, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Menghapus data kelas dengan ID - ' . $id . '',
		);
		$this->m_admin->insert($tb_log, $log);

		$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil hapus data</center></div></div>");
		redirect('backadmin/kelas');
	}

	// ------------------------------------------------------------------------------------------

	// ------------------------------- MASTER - LOKASI -------------------------------

	public function lokasi()
	{
		$data['profil'] 	= $this->m_admin->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'lokasi';

		$datat['record']	= $this->m_admin->log_today();

		$data['lokasi']		= $this->m_admin->rd_lokasi();

		$this->template->kepala('tbase/kepala', 'admin/template/header', $datat);
		$this->template->samping('tbase/samping', 'admin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'admin/template/top');
		$this->template->isi('tbase/isi', 'admin/transaksi/lokasi');
		$this->template->kaki('tbase/kaki', 'admin/template/footer');
	}

	public function tambah_lokasi()
	{
		$data['profil'] 	= $this->m_admin->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'lokasi';

		$datat['record']	= $this->m_admin->log_today();

		$this->template->kepala('tbase/kepala', 'admin/template/header', $datat);
		$this->template->samping('tbase/samping', 'admin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'admin/template/top');
		$this->template->isi('tbase/isi', 'admin/transaksi/tambah_lokasi');
		$this->template->kaki('tbase/kaki', 'admin/template/footer');
	}

	public function in_lokasi()
	{
		$table 	= 'mnl_lokasi';

		$data = array(
			'lokasi'   => $this->input->post('nama')
		);

		$send = $this->m_admin->insert($table, $data);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Menambah data lokasi - ' . $this->input->post('nama') . '',
		);
		$this->m_admin->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backadmin/lokasi');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Gambar</center></div></div>");
			redirect('backadmin/lokasi');
		}
	}

	public function edit_lokasi($id)
	{
		$data['profil'] 	= $this->m_admin->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'settings';
		$data['thisPg']		= 'lokasi';

		$datat['record']	= $this->m_admin->log_today();

		$data['data'] 		= $this->m_admin->edit_lokasi($id)->row_array();

		$this->template->kepala('tbase/kepala', 'admin/template/header', $datat);
		$this->template->samping('tbase/samping', 'admin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'admin/template/top');
		$this->template->isi('tbase/isi', 'admin/transaksi/edit_lokasi');
		$this->template->kaki('tbase/kaki', 'admin/template/footer');
	}

	public function up_lokasi()
	{
		$table 		= 'mnl_lokasi';

		$where 		= array(
			'id_mnl_lokasi'	=> $this->input->post('id')
		);

		$data = array(
			'lokasi'		=> $this->input->post('nama')
		);

		$send = $this->m_admin->update($table, $data, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Mengubah data lokasi dengan ID - ' . $this->input->post('id') . '',
		);
		$this->m_admin->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backadmin/lokasi');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Gambar</center></div></div>");
			redirect('backadmin/lokasi');
		}
	}

	function hapus_lokasi($id)
	{
		$table 		= 'mnl_lokasi';
		$where 		= array('id_mnl_lokasi' => $id);

		$this->m_admin->hapus($table, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Menghapus data lokasi dengan ID - ' . $id . '',
		);
		$this->m_admin->insert($tb_log, $log);

		$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil hapus data</center></div></div>");
		redirect('backadmin/lokasi');
	}

	// ------------------------------------------------------------------------------------------

	// ------------------------------- MASTER - CLUB -------------------------------

	public function club()
	{
		$data['profil'] 	= $this->m_admin->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'club';

		$datat['record']	= $this->m_admin->log_today();

		$data['club']		= $this->m_admin->rd_club();

		$this->template->kepala('tbase/kepala', 'admin/template/header', $datat);
		$this->template->samping('tbase/samping', 'admin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'admin/template/top');
		$this->template->isi('tbase/isi', 'admin/transaksi/club');
		$this->template->kaki('tbase/kaki', 'admin/template/footer');
	}

	public function tambah_club()
	{
		$data['profil'] 	= $this->m_admin->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'club';

		$datat['record']	= $this->m_admin->log_today();

		$this->template->kepala('tbase/kepala', 'admin/template/header', $datat);
		$this->template->samping('tbase/samping', 'admin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'admin/template/top');
		$this->template->isi('tbase/isi', 'admin/transaksi/tambah_club');
		$this->template->kaki('tbase/kaki', 'admin/template/footer');
	}

	public function in_club()
	{
		$table 	= 'mnl_club';

		$data = array(
			'club'   => $this->input->post('nama')
		);

		$send = $this->m_admin->insert($table, $data);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Menambah data club - ' . $this->input->post('nama') . '',
		);
		$this->m_admin->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backadmin/club');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Gambar</center></div></div>");
			redirect('backadmin/club');
		}
	}

	public function edit_club($id)
	{
		$data['profil'] 	= $this->m_admin->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'settings';
		$data['thisPg']		= 'club';

		$datat['record']	= $this->m_admin->log_today();

		$data['data'] 		= $this->m_admin->edit_club($id)->row_array();

		$this->template->kepala('tbase/kepala', 'admin/template/header', $datat);
		$this->template->samping('tbase/samping', 'admin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'admin/template/top');
		$this->template->isi('tbase/isi', 'admin/transaksi/edit_club');
		$this->template->kaki('tbase/kaki', 'admin/template/footer');
	}

	public function up_club()
	{
		$table 		= 'mnl_club';

		$where 		= array(
			'id_mnl_club'	=> $this->input->post('id')
		);

		$data = array(
			'club'		=> $this->input->post('nama')
		);

		$send = $this->m_admin->update($table, $data, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Mengubah data club dengan ID - ' . $this->input->post('id') . '',
		);
		$this->m_admin->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backadmin/club');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Gambar</center></div></div>");
			redirect('backadmin/club');
		}
	}

	function hapus_club($id)
	{
		$table 		= 'mnl_club';
		$where 		= array('id_mnl_club' => $id);

		$this->m_admin->hapus($table, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Menghapus data club dengan ID - ' . $id . '',
		);
		$this->m_admin->insert($tb_log, $log);

		$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil hapus data</center></div></div>");
		redirect('backadmin/club');
	}

	// ------------------------------------------------------------------------------------------

	// ------------------------------- MASTER - SPP -------------------------------

	public function spp()
	{
		$data['profil'] 	= $this->m_admin->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'spp';

		$datat['record']	= $this->m_admin->log_today();

		$data['spp']		= $this->m_admin->rd_spp();

		$this->template->kepala('tbase/kepala', 'admin/template/header', $datat);
		$this->template->samping('tbase/samping', 'admin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'admin/template/top');
		$this->template->isi('tbase/isi', 'admin/transaksi/spp');
		$this->template->kaki('tbase/kaki', 'admin/template/footer');
	}

	public function tambah_spp()
	{
		$data['profil'] 	= $this->m_admin->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'spp';

		$datat['record']	= $this->m_admin->log_today();

		$data['kelas']		= $this->m_admin->rd_kelas();
		$data['lokasi']		= $this->m_admin->rd_lokasi();
		$data['club']		= $this->m_admin->rd_club();

		$this->template->kepala('tbase/kepala', 'admin/template/header', $datat);
		$this->template->samping('tbase/samping', 'admin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'admin/template/top');
		$this->template->isi('tbase/isi', 'admin/transaksi/tambah_spp');
		$this->template->kaki('tbase/kaki', 'admin/template/footer');
	}

	public function in_spp()
	{
		$table 	= 'mnl_spp';

		$data = array(
			'nama'   			=> $this->input->post('nama'),
			'nominal'			=> str_replace(".", "", $this->input->post('nominal')),
			'id_mnl_club'   	=> $this->input->post('club'),
			'id_mnl_kelas'   	=> $this->input->post('kelas'),
			'id_mnl_lokasi'   	=> $this->input->post('lokasi')
		);

		$send = $this->m_admin->insert($table, $data);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Menambah data SPP - ' . $this->input->post('nama') . '',
		);
		$this->m_admin->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backadmin/spp');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Gambar</center></div></div>");
			redirect('backadmin/spp');
		}
	}

	public function edit_spp($id)
	{
		$data['profil'] 	= $this->m_admin->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'settings';
		$data['thisPg']		= 'spp';

		$datat['record']	= $this->m_admin->log_today();

		$data['data'] 		= $this->m_admin->edit_spp($id)->row_array();

		$data['kelas']		= $this->m_admin->rd_kelas();
		$data['lokasi']		= $this->m_admin->rd_lokasi();
		$data['club']		= $this->m_admin->rd_club();

		$this->template->kepala('tbase/kepala', 'admin/template/header', $datat);
		$this->template->samping('tbase/samping', 'admin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'admin/template/top');
		$this->template->isi('tbase/isi', 'admin/transaksi/edit_spp');
		$this->template->kaki('tbase/kaki', 'admin/template/footer');
	}

	public function up_spp()
	{
		$table 		= 'mnl_spp';

		$where 		= array(
			'id_mnl_spp'	=> $this->input->post('id')
		);

		$data = array(
			'id_mnl_kelas'		=> $this->input->post('kelas'),
			'id_mnl_lokasi'		=> $this->input->post('lokasi'),
			'id_mnl_club'		=> $this->input->post('club'),
			'nama'				=> $this->input->post('nama'),
			'nominal'			=> str_replace(".", "", $this->input->post('nominal'))
		);

		$send = $this->m_admin->update($table, $data, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Mengubah data SPP dengan ID - ' . $this->input->post('id') . '',
		);
		$this->m_admin->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backadmin/spp');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Gambar</center></div></div>");
			redirect('backadmin/spp');
		}
	}

	function hapus_spp($id)
	{
		$table 		= 'mnl_spp';
		$where 		= array('id_mnl_spp' => $id);

		$this->m_admin->hapus($table, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Menghapus data SPP dengan ID - ' . $id . '',
		);
		$this->m_admin->insert($tb_log, $log);

		$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil hapus data</center></div></div>");
		redirect('backadmin/spp');
	}

	// ------------------------------------------------------------------------------------------

	// ------------------------------- MASTER - SISWA -------------------------------

	public function siswa()
	{
		$data['profil'] 	= $this->m_admin->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'siswa';

		$datat['record']	= $this->m_admin->log_today();

		$data['data']		= $this->m_admin->rd_siswa();

		$this->template->kepala('tbase/kepala', 'admin/template/header', $datat);
		$this->template->samping('tbase/samping', 'admin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'admin/template/top');
		$this->template->isi('tbase/isi', 'admin/transaksi/siswa');
		$this->template->kaki('tbase/kaki', 'admin/template/footer');
	}

	public function tambah_siswa()
	{
		$data['profil'] 	= $this->m_admin->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'siswa';

		$datat['record']	= $this->m_admin->log_today();

		$data['kelas']		= $this->m_admin->rd_kelas();
		$data['lokasi']		= $this->m_admin->rd_lokasi();
		$data['club']		= $this->m_admin->rd_club();

		$data['regist']		= $this->m_admin->rd_regist();
		$data['spp']		= $this->m_admin->rd_spp();

		$this->template->kepala('tbase/kepala', 'admin/template/header', $datat);
		$this->template->samping('tbase/samping', 'admin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'admin/template/top');
		$this->template->isi('tbase/isi', 'admin/transaksi/tambah_siswa');
		$this->template->kaki('tbase/kaki', 'admin/template/footer');
	}

	public function in_siswa()
	{
		$table 		= 'mnl_siswa';
		$tables		= 'mnl_ortu';
		$tgl_lahir 	= $this->input->post('tgl_lahir');
		// $password 	= preg_replace("/[^0-9]/", "", $tgl_lahir);
		$no_hp		= $this->input->post('no_hp');
		$password 	= substr($no_hp, -4);

		$row        	= $this->m_admin->get_last_id($table)->row_array();
		$id_mnl_siswa   = $row['id_mnl_siswa'] + 1;

		if ($this->m_admin->cek_ortu($no_hp)->num_rows() == 1) {

			$data = array(
				'id_mnl_kelas'		=> $this->input->post('kelas'),
				'id_mnl_lokasi'		=> $this->input->post('lokasi'),
				'id_mnl_club'		=> $this->input->post('club'),
				'id_mnl_regist'		=> $this->input->post('regist'),
				'id_mnl_spp'		=> $this->input->post('spp'),
				'nama'   			=> $this->input->post('nama'),
				'ortu'   			=> $this->input->post('ortu'),
				'email'   			=> $this->input->post('email'),
				'no_hp'   			=> $no_hp,
				'password'  		=> get_hash($password),
				'tgl_lahir'			=> $tgl_lahir,
				'alamat'   			=> $this->input->post('alamat'),
				'level'   			=> 'siswa',
				'status'   			=> 'belum',
				'jabatan' 			=> 'Siswa'
			);

			$send = $this->m_admin->insert($table, $data);
		} else {

			$data = array(
				'id_mnl_kelas'		=> $this->input->post('kelas'),
				'id_mnl_lokasi'		=> $this->input->post('lokasi'),
				'id_mnl_club'		=> $this->input->post('club'),
				'id_mnl_regist'		=> $this->input->post('regist'),
				'id_mnl_spp'		=> $this->input->post('spp'),
				'nama'   			=> $this->input->post('nama'),
				'ortu'   			=> $this->input->post('ortu'),
				'email'   			=> $this->input->post('email'),
				'no_hp'   			=> $no_hp,
				'password'  		=> get_hash($password),
				'tgl_lahir'			=> $tgl_lahir,
				'alamat'   			=> $this->input->post('alamat'),
				'level'   			=> 'siswa',
				'status'   			=> 'belum',
				'jabatan' 			=> 'Siswa'
			);

			$datas = array(
				'nama'   			=> $this->input->post('ortu'),
				'email'   			=> $this->input->post('email'),
				'no_hp'   			=> $no_hp,
				'password'  		=> get_hash($password),
				'level'   			=> 'ortu',
				'status'   			=> 'belum',
				'jabatan' 			=> 'Parent'
			);

			$this->m_admin->insert($table, $data);
			$send = $this->m_admin->insert($tables, $datas);
		}

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Menambah data siswa - ' . $this->input->post('nama') . '',
		);
		$this->m_admin->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backadmin/siswa');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Gambar</center></div></div>");
			redirect('backadmin/siswa');
		}
	}

	public function edit_siswa($id)
	{
		$data['profil'] 	= $this->m_admin->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'siswa';

		$datat['record']	= $this->m_admin->log_today();

		$data['data'] 		= $this->m_admin->edit_siswa($id)->row_array();
		$data['kelas']		= $this->m_admin->rd_kelas();
		$data['lokasi']		= $this->m_admin->rd_lokasi();
		$data['club']		= $this->m_admin->rd_club();

		$data['regist']		= $this->m_admin->rd_regist();
		$data['spp']		= $this->m_admin->rd_spp();


		$this->template->kepala('tbase/kepala', 'admin/template/header', $datat);
		$this->template->samping('tbase/samping', 'admin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'admin/template/top');
		$this->template->isi('tbase/isi', 'admin/transaksi/edit_siswa');
		$this->template->kaki('tbase/kaki', 'admin/template/footer');
	}

	public function up_siswa()
	{
		$table 		= 'mnl_siswa';

		$where 		= array(
			'id_mnl_siswa'	=> $this->input->post('id')
		);

		$pass_db 	= $this->input->post('pass_db');
		$pass 		= $this->input->post('password');

		if ($pass === $pass_db) {
			$data = array(
				'nama'   			=> $this->input->post('nama'),
				'email'   			=> $this->input->post('email'),
				'no_hp'   			=> $this->input->post('no_hp'),
				'alamat'   			=> $this->input->post('alamat'),
				'id_mnl_kelas'		=> $this->input->post('kelas'),
				'id_mnl_lokasi'		=> $this->input->post('lokasi'),
				'id_mnl_club'		=> $this->input->post('club'),
				'id_mnl_regist'		=> $this->input->post('regist'),
				'id_mnl_spp'		=> $this->input->post('spp'),
				'ortu'   			=> $this->input->post('ortu'),
				'tgl_lahir'			=> $this->input->post('tgl_lahir')
			);
		} else {
			$data = array(
				'nama'   			=> $this->input->post('nama'),
				'email'   			=> $this->input->post('email'),
				'no_hp'   			=> $this->input->post('no_hp'),
				'password' 			=> get_hash($pass),
				'alamat'   			=> $this->input->post('alamat'),
				'id_mnl_kelas'		=> $this->input->post('kelas'),
				'id_mnl_lokasi'		=> $this->input->post('lokasi'),
				'id_mnl_club'		=> $this->input->post('club'),
				'id_mnl_regist'		=> $this->input->post('regist'),
				'id_mnl_spp'		=> $this->input->post('spp'),
				'ortu'   			=> $this->input->post('ortu'),
				'tgl_lahir'			=> $this->input->post('tgl_lahir')
			);
		}

		$send = $this->m_admin->update($table, $data, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Mengubah data siswa dengan ID - ' . $this->input->post('id') . '',
		);
		$this->m_admin->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backadmin/siswa');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Gambar</center></div></div>");
			redirect('backadmin/siswa');
		}
	}

	function hapus_siswa($id)
	{
		$table 		= 'mnl_siswa';
		$where 		= array('id_mnl_siswa' => $id);

		$this->m_admin->hapus($table, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Menghapus data siswa dengan ID - ' . $id . '',
		);
		$this->m_admin->insert($tb_log, $log);

		$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil hapus data</center></div></div>");
		redirect('backadmin/siswa');
	}

	public function status_siswa($id)
	{
		$table 		= 'mnl_siswa';

		$status 	= $this->uri->segment('4');

		$where 		= array(
			'id_mnl_siswa'	=> $id
		);

		if ($status === 'aktif') {
			$data = array(
				'status'		=> 'tidak aktif',
			);
		} else {
			$data = array(
				'status'		=> 'aktif'
			);
		}

		$send = $this->m_admin->update($table, $data, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Mengubah data status siswa dengan ID - ' . $this->input->post('id') . '',
		);
		$this->m_admin->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backadmin/siswa');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Gambar</center></div></div>");
			redirect('backadmin/siswa');
		}
	}

	// ------------------------------------------------------------------------------------------

	// ------------------------------- MASTER - BIAYA REGISTRASI -------------------------------

	public function regist()
	{
		$data['profil'] 	= $this->m_admin->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'biaya registrasi';

		$datat['record']	= $this->m_admin->log_today();

		$data['data']		= $this->m_admin->rd_regist();

		$this->template->kepala('tbase/kepala', 'admin/template/header', $datat);
		$this->template->samping('tbase/samping', 'admin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'admin/template/top');
		$this->template->isi('tbase/isi', 'admin/transaksi/regist');
		$this->template->kaki('tbase/kaki', 'admin/template/footer');
	}

	public function tambah_regist()
	{
		$data['profil'] 	= $this->m_admin->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'biaya registrasi';

		$datat['record']	= $this->m_admin->log_today();

		$data['kelas']		= $this->m_admin->rd_kelas();
		$data['lokasi']		= $this->m_admin->rd_lokasi();
		$data['club']		= $this->m_admin->rd_club();

		$this->template->kepala('tbase/kepala', 'admin/template/header', $datat);
		$this->template->samping('tbase/samping', 'admin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'admin/template/top');
		$this->template->isi('tbase/isi', 'admin/transaksi/tambah_regist');
		$this->template->kaki('tbase/kaki', 'admin/template/footer');
	}

	public function in_regist()
	{
		$table 		= 'mnl_regist';

		$data = array(
			'id_mnl_kelas'		=> $this->input->post('kelas'),
			'id_mnl_lokasi'		=> $this->input->post('lokasi'),
			'id_mnl_club'		=> $this->input->post('club'),
			'nama'				=> $this->input->post('nama'),
			'nominal'			=> str_replace(".", "", $this->input->post('nominal'))
		);

		$send = $this->m_admin->insert($table, $data);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Menambah data biaya registrasi - ' . $this->input->post('nama') . '',
		);
		$this->m_admin->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backadmin/regist');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Gambar</center></div></div>");
			redirect('backadmin/regist');
		}
	}

	public function edit_regist($id)
	{
		$data['profil'] 	= $this->m_admin->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'biaya registrasi';

		$datat['record']	= $this->m_admin->log_today();

		$data['data'] 		= $this->m_admin->edit_regist($id)->row_array();
		$data['kelas']		= $this->m_admin->rd_kelas();
		$data['lokasi']		= $this->m_admin->rd_lokasi();
		$data['club']		= $this->m_admin->rd_club();

		$this->template->kepala('tbase/kepala', 'admin/template/header', $datat);
		$this->template->samping('tbase/samping', 'admin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'admin/template/top');
		$this->template->isi('tbase/isi', 'admin/transaksi/edit_regist');
		$this->template->kaki('tbase/kaki', 'admin/template/footer');
	}

	public function up_regist()
	{
		$table 		= 'mnl_regist';

		$where 		= array(
			'id_mnl_regist'	=> $this->input->post('id')
		);

		$data = array(
			'id_mnl_kelas'		=> $this->input->post('kelas'),
			'id_mnl_lokasi'		=> $this->input->post('lokasi'),
			'id_mnl_club'		=> $this->input->post('club'),
			'nama'				=> $this->input->post('nama'),
			'nominal'			=> str_replace(".", "", $this->input->post('nominal'))
		);

		$send = $this->m_admin->update($table, $data, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Mengubah data biaya registrasi dengan ID - ' . $this->input->post('id') . '',
		);
		$this->m_admin->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backadmin/regist');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Gambar</center></div></div>");
			redirect('backadmin/regist');
		}
	}

	function hapus_regist($id)
	{
		$table 		= 'mnl_regist';
		$where 		= array('id_mnl_regist' => $id);

		$this->m_admin->hapus($table, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Menghapus data biaya registrasi dengan ID - ' . $id . '',
		);
		$this->m_admin->insert($tb_log, $log);

		$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil hapus data</center></div></div>");
		redirect('backadmin/regist');
	}

	public function status_regist($id)
	{
		$table 		= 'mnl_siswa';

		$status 	= $this->uri->segment('4');

		$where 		= array(
			'id_mnl_siswa'	=> $id
		);

		if ($status == 'belum') {
			$data = array(
				'status'		=> 'aktif',
			);
		} else {
			$data = array(
				'status'		=> 'belum'
			);
		}

		$send = $this->m_admin->update($table, $data, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Mengubah data registrasi siswa dengan ID - ' . $this->input->post('id') . '',
		);
		$this->m_admin->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backadmin/siswa');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Gambar</center></div></div>");
			redirect('backadmin/siswa');
		}
	}


	// ------------------------------------------------------------------------------------------

	// ------------------------------- REGISTRASI - SEMUA -------------------------------

	public function pembayaran_regist($id)
	{
		$data['profil'] 	= $this->m_admin->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'registrasi';

		$datat['record']	= $this->m_admin->log_today();


		if ($id == 'all') {
			$data['thisPg']		= 'semua';
			$data['data']		= $this->m_admin->rd_pembayaran_regist_all();
		} else if ($id == 'ba') {
			$data['thisPg']		= 'butuh approval';
			$data['data']		= $this->m_admin->rd_pembayaran_regist_ba();
		} else if ($id == 'bb') {
			$data['thisPg']		= 'belum bayar';
			$data['data']		= $this->m_admin->rd_pembayaran_regist_bb();
		} else if ($id == 'sb') {
			$data['thisPg']		= 'sudah bayar';
			$data['data']		= $this->m_admin->rd_pembayaran_regist_sb();
		}

		$this->template->kepala('tbase/kepala', 'admin/template/header', $datat);
		$this->template->samping('tbase/samping', 'admin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'admin/template/top');
		$this->template->isi('tbase/isi', 'admin/transaksi/pembayaran_regist');
		$this->template->kaki('tbase/kaki', 'admin/template/footer');
	}

	public function approve_regist()
	{
		$table		= 'mnl_siswa';

		$data = array(
			'status'	=> 'aktif'
		);

		$where = array(
			'id_mnl_siswa' => $this->input->post('id')
		);

		$send = $this->m_admin->update($table, $data, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Mengganti status pembayaran pada siswa dengan ID - ' . $this->input->post('id') . '',
		);
		$this->m_admin->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Ganti Status</center></div></div>");
			redirect('backadmin/pembayaran_regist/ba');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Ganti Status</center></div></div>");
			redirect('backadmin/pembayaran_regist/ba');
		}
	}

	public function up_bukti_regist()
	{
		$table 		= 'mnl_pmb_regist';
		$tables 	= 'mnl_siswa';

		$id   		= $this->input->post('id');

		$path = './assets/back/images/regist/';

		$where = array('id_mnl_siswa' => $id);

		// get foto
		$config['upload_path'] = $path;
		$config['allowed_types'] = 'jpg|png|jpeg|svg|ico';
		$config['max_size'] = '2048';  //2MB max
		$config['max_width'] = '4480'; // pixel
		$config['max_height'] = '4480'; // pixel
		$config['file_name'] = $_FILES['fotopost']['name'];

		$this->upload->initialize($config);

		if (!empty($_FILES['fotopost']['name'])) {
			if ($this->upload->do_upload('fotopost')) {
				$foto = $this->upload->data();
				$data = array(
					'bukti'      		=> $foto['file_name'],
					'id_mnl_siswa'   	=> $this->input->post('id'),
					'id_mnl_regist'   	=> $this->input->post('id_regist'),
					'ket'   			=> $this->input->post('ket'),
					'tanggal'   		=> date('Y-m-d'),
					'waktu' 			=> date('H:i:s')
				);

				// hapus foto pada direktori
				// @unlink($path . $this->input->post('filelama'));
				$datas = array(
					'status'      		=> 'aktif'
				);

				$this->m_admin->insert($table, $data);
				$this->m_admin->update($tables, $datas, $where);

				$tb_log = 'log';
				$log   	= array(
					'id_user' 	=> $this->session->userdata('isId'),
					'aksi'    	=> 'Mengupload bukti registrasi siswa dengan ID - ' . $id . '',
				);
				$this->m_admin->insert($tb_log, $log);

				$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Upload Bukti Transfer</center></div></div>");
				redirect('backadmin/pembayaran_regist/bb');
			} else {
				$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Bukti Transfer</center></div></div>");
				redirect('backadmin/pembayaran_regist/bb');
			}
		}
	}

	// ------------------------------------------------------------------------------------------

	// ------------------------------- PEMBAYARAN - SEMUA -------------------------------

	public function pembayaran($id)
	{
		$data['profil'] 	= $this->m_admin->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'pembayaran';

		$datat['record']	= $this->m_admin->log_today();


		if ($id == 'all') {
			$data['thisPg']		= 'semua';
			$data['data']		= $this->m_admin->rd_pembayaran_all();
		} else if ($id == 'ba') {
			$data['thisPg']		= 'butuh approval';
			$data['data']		= $this->m_admin->rd_pembayaran_ba();
		} else if ($id == 'bb') {
			$data['thisPg']		= 'belum bayar';
			$data['data']		= $this->m_admin->rd_pembayaran_bb();
		} else if ($id == 'sb') {
			$data['thisPg']		= 'sudah bayar';
			$data['data']		= $this->m_admin->rd_pembayaran_sb();
		}

		$this->template->kepala('tbase/kepala', 'admin/template/header', $datat);
		$this->template->samping('tbase/samping', 'admin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'admin/template/top');
		$this->template->isi('tbase/isi', 'admin/transaksi/pembayaran');
		$this->template->kaki('tbase/kaki', 'admin/template/footer');
	}

	public function up_bukti_tf()
	{
		$table 		= 'mnl_pembayaran';
		$id   		= $this->input->post('id');

		$path = './assets/back/images/bukti/';

		$where = array('id_mnl_siswa' => $id);

		// get foto
		$config['upload_path'] = $path;
		$config['allowed_types'] = 'jpg|png|jpeg|svg|ico';
		$config['max_size'] = '2048';  //2MB max
		$config['max_width'] = '4480'; // pixel
		$config['max_height'] = '4480'; // pixel
		$config['file_name'] = $_FILES['fotopost']['name'];

		$this->upload->initialize($config);

		if (!empty($_FILES['fotopost']['name'])) {
			if ($this->upload->do_upload('fotopost')) {
				$foto = $this->upload->data();
				$data = array(
					'bukti'      		=> $foto['file_name'],
					'ket'   			=> $this->input->post('ket'),
					'tanggal'   		=> date('Y-m-d'),
					'waktu' 			=> date('H:i:s'),
					'status'   			=> 'sudah'
				);

				// hapus foto pada direktori
				@unlink($path . $this->input->post('filelama'));

				$this->m_admin->update($table, $data, $where);

				$tb_log = 'log';
				$log   	= array(
					'id_user' 	=> $this->session->userdata('isId'),
					'aksi'    	=> 'Mengupload bukti transfer siswa dengan ID - ' . $id . '',
				);
				$this->m_admin->insert($tb_log, $log);

				$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Upload Bukti Transfer</center></div></div>");
				redirect('backadmin/pembayaran/bb');
			} else {
				$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Bukti Transfer</center></div></div>");
				redirect('backadmin/pembayaran/bb');
			}
		}
	}

	public function approve()
	{
		$table		= 'mnl_pembayaran';

		$data = array(
			'status'	=> 'sudah'
		);

		$where = array(
			'id_mnl_siswa' => $this->input->post('id')
		);

		$send = $this->m_admin->update($table, $data, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Mengganti status pembayaran pada siswa dengan ID - ' . $this->input->post('id') . '',
		);
		$this->m_admin->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Ganti Status</center></div></div>");
			redirect('backadmin/pembayaran/ba');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Ganti Status</center></div></div>");
			redirect('backadmin/pembayaran/ba');
		}
	}

	// ------------------------------------------------------------------------------------------

}
