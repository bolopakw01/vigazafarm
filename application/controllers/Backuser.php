<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Backuser extends CI_Controller
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

		$this->load->model('m_user');

		date_default_timezone_set("Asia/Jakarta");
	}

	public function profil()
	{
		$data['profil'] 	= $this->m_user->profil($this->session->userdata('isHp'))->row_array();
		$data['thisPage']	= 'profil';
		$data['thisPg']		= 'profil';

		$datat['record']	= $this->m_user->log_today();
		$datat['stat_user']	= $this->m_user->cek($this->session->userdata('isHp'))->num_rows();

		$this->template->kepala('tbase/kepala', 'user/template/header', $datat);
		$this->template->samping('tbase/samping', 'user/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'user/template/top');
		$this->template->isi('tbase/isi', 'user/profil');
		$this->template->kaki('tbase/kaki', 'user/template/footer');
	}

	function update_profil()
	{

		$pass_db = $this->input->post('pass_db');
		$pass =	$this->input->post('pass');

		if ($pass === $pass_db) {
			$data = array(
				'nama' 		=> $this->input->post('nm'),
				'no_hp' 	=> $this->input->post('uname')
			);
		} else {
			$data = array(
				'nama' 		=> $this->input->post('nm'),
				'no_hp' 	=> $this->input->post('uname'),
				'password' 	=> get_hash($pass)
			);
		}

		$where = array('id_mnl_ortu' => $this->input->post('minid')); //array where query sebagai identitas pada saat query dijalankan
		$uprof = $this->m_user->update_profil($data, $where); //akses model untuk menyimpan ke database

		if ($uprof) {
			$sessionArray = array(
				'isLog' 	=> TRUE,
				'isId' 		=> $this->input->post('minid'),
				'isHp' 		=> $this->input->post('uname'),
				'isPass' 	=> get_hash($this->input->post('pass')),
				'isLevel' 	=> 'ortu'
			);

			$this->session->set_userdata($sessionArray);

			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Update data berhasil</center></div></div>");
			redirect('backuser/profil'); //jika berhasil maka akan ditampilkan view vupload
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Update Data Gagal !!</center></div></div>");
			redirect('backuser/profil'); //jika gagal maka akan ditampilkan form upload
		}
	}

	public function log()
	{
		$data['profil'] 	= $this->m_user->profil($this->session->userdata('isHp'))->row_array();
		$data['thisPage']	= 'settings';
		$data['thisPg']		= 'log';

		$datat['record']	= $this->m_user->log_today();
		$datat['stat_user']	= $this->m_user->cek($this->session->userdata('isHp'))->num_rows();

		$data['log']		= $this->m_user->log()->result();

		$this->template->kepala('tbase/kepala', 'user/template/header', $datat);
		$this->template->samping('tbase/samping', 'user/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'user/template/top');
		$this->template->isi('tbase/isi', 'user/log');
		$this->template->kaki('tbase/kaki', 'user/template/footer');
	}

	public function log_as_fund()
	{
		// hash verifikasi
		$secure = $this->m_user->cek($this->session->userdata('isHp'))->row();
		$sessionArray = array(
			'isLog' => TRUE,
			'isId' => $secure->id_fundraiser,
			'isHp' => $this->session->userdata('isHp'),
			'isPass' => $this->session->userdata('isPass'),
			'isLevel' => $secure->level
		);

		$this->session->set_userdata($sessionArray);
		redirect('backfund');
	}

	/* ========================================= PAGE ========================================= */

	public function index()
	{
		$data['profil'] 	= $this->m_user->profil($this->session->userdata('isHp'))->row_array();
		$data['thisPage']	= 'dashboard';
		$data['thisPg']		= 'dashboard';

		$datat['record']	= $this->m_user->log_today();

		$data['bb_regist']		= $this->m_user->bb_regist();
		$data['bb_spp']			= $this->m_user->bb_spp();
		$data['jml_bb_regist']	= $this->m_user->jml_bb_regist();
		$data['jml_bb_spp']		= $this->m_user->jml_bb_spp();

		$this->template->kepala('tbase/kepala', 'user/template/header', $datat);
		$this->template->samping('tbase/samping', 'user/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'user/template/top');
		$this->template->isi('tbase/isi', 'user/dashboard');
		$this->template->kaki('tbase/kaki', 'user/template/footer');
	}

	public function invoice()
	{
		$data['profil'] 	= $this->m_user->profil($this->session->userdata('isHp'))->row_array();
		$data['thisPage']	= 'invoice';
		$data['thisPg']		= 'invoice';

		$datat['record']	= $this->m_user->log_today();

		$data['data']		= $this->m_user->rd_siswa($this->session->userdata('isHp'))->result();
		// $data['images']		= $this->m_user->rd_pembayaran($id)->row_array();

		$this->template->kepala('tbase/kepala', 'user/template/header', $datat);
		$this->template->samping('tbase/samping', 'user/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'user/template/top');
		$this->template->isi('tbase/isi', 'user/transaksi/siswa');
		$this->template->kaki('tbase/kaki', 'user/template/footer');
	}

	public function detail($id)
	{
		$data['profil'] 	= $this->m_user->profil($this->session->userdata('isHp'))->row_array();
		$data['thisPage']	= 'invoice';
		$data['thisPg']		= 'invoice';

		$datat['record']	= $this->m_user->log_today();

		$data['data']		= $this->m_user->rd_invoice($id)->result();
		$data['images']		= $this->m_user->rd_pembayaran($id)->row_array();

		$this->template->kepala('tbase/kepala', 'user/template/header', $datat);
		$this->template->samping('tbase/samping', 'user/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'user/template/top');
		$this->template->isi('tbase/isi', 'user/transaksi/detail');
		$this->template->kaki('tbase/kaki', 'user/template/footer');
	}

	public function history()
	{
		$data['profil'] 	= $this->m_user->profil($this->session->userdata('isHp'))->row_array();
		$data['thisPage']	= 'history';
		$data['thisPg']		= 'history';

		$datat['record']	= $this->m_user->log_today();

		$data['data']		= $this->m_user->rd_history($this->session->userdata('isHp'));

		$this->template->kepala('tbase/kepala', 'user/template/header', $datat);
		$this->template->samping('tbase/samping', 'user/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'user/template/top');
		$this->template->isi('tbase/isi', 'user/transaksi/history');
		$this->template->kaki('tbase/kaki', 'user/template/footer');
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
					'status'   			=> 'proses'
				);

				// hapus foto pada direktori
				@unlink($path . $this->input->post('filelama'));

				$this->m_user->update($table, $data, $where);

				$tb_log = 'log';
				$log   	= array(
					'id_user' 	=> $this->session->userdata('isId'),
					'aksi'    	=> 'Mengupload bukti transfer siswa dengan ID - ' . $id . '',
				);
				$this->m_user->insert($tb_log, $log);

				$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Upload Bukti Transfer</center></div></div>");
				redirect('backuser/detail/' . $id . '');
			} else {
				$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Bukti Transfer</center></div></div>");
				redirect('backuser/detail/' . $id . '');
			}
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
					'status'      		=> 'proses'
				);

				$this->m_user->insert($table, $data);
				$this->m_user->update($tables, $datas, $where);

				$tb_log = 'log';
				$log   	= array(
					'id_user' 	=> $this->session->userdata('isId'),
					'aksi'    	=> 'Mengupload bukti registrasi siswa dengan ID - ' . $id . '',
				);
				$this->m_user->insert($tb_log, $log);

				$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Upload Bukti Transfer</center></div></div>");
				redirect('backuser/invoice');
			} else {
				$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Bukti Transfer</center></div></div>");
				redirect('backuser/invoice');
			}
		}
	}

	function edit()
	{
		$id_mnl_siswa = $this->input->post('id_mnl_siswa');
		$data['hasil'] = $this->m_user->getid_siswa($id_mnl_siswa);
		$data['regist'] = $this->m_user->get_pmb_regist($id_mnl_siswa);
		$this->load->view('user/transaksi/edit', $data);
	}

	function lihat()
	{
		$id_mnl_siswa = $this->input->post('id_mnl_siswa');
		$data['hasil'] = $this->m_user->get_pmb_regist($id_mnl_siswa);
		$this->load->view('user/transaksi/lihat', $data);
	}
}
