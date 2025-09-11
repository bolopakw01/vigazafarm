<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Kandang Controller
 * Menangani semua fungsi terkait manajemen kandang
 */
class Kandang extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library(['session', 'template']);
		$this->load->helper(['url']);
		$this->load->model(['M_kandang', 'm_min']);
		
		// Authentication check (hanya super admin)
		if ($this->session->userdata('isLog') != TRUE) {
			redirect('mimin');
		}
		if ($this->session->userdata('isLevel') != 'super_admin') {
			redirect('dashboard');
		}
		
		date_default_timezone_set("Asia/Jakarta");
	}

	/**
	 * Load template method
	 */
	private function load_template($view, $data = array())
	{
		// Set common data
		$data['thisPage'] = 'master';
		$data['thisPg'] = 'kandang';
		
		// Load views
		$this->load->view('admin/template/header', $data);
		$this->load->view('admin/template/top', $data);  
		$this->load->view('admin/template/sidebar', $data);
		$this->load->view('admin/' . $view, $data);
		$this->load->view('admin/template/footer', $data);
	}

	/**
	 * Halaman daftar kandang
	 */
	public function index()
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'kandang';

		$data['data']		= $this->m_min->rd_kandang();
		$data['jml_siswa']	= $this->m_min->jml_penghuni();
		$data['jml_bb']		= $this->m_min->jml_bb();
		$data['jml_br']		= $this->m_min->jml_br();

		$this->load_template('page/kandang_index', $data);
	}

	/**
	 * Tambah kandang baru
	 */
	public function tambah()
	{
		$nama = $this->input->post('nama');
		$kapasitas = $this->input->post('kapasitas');
		$tipe = $this->input->post('tipe');
		$lokasi = $this->input->post('lokasi');

		if ($this->M_kandang->cek_kandang($nama)->num_rows() == 1) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-warning\" id=\"alert\"><center>Data Kandang Sudah Ada</center></div></div>");
			redirect('kandang');
		} else {
			$data = array(
				'nama' => $nama,
				'kapasitas' => $kapasitas ? $kapasitas : 0,
				'kapasitas_terisi' => 0,
				'tipe' => $tipe ? $tipe : 'penetasan',
				'lokasi' => $lokasi,
				'status' => 'aktif',
				'tanggal' => date('Y-m-d'),
				'biaya' => 0,
				'created_at' => date('Y-m-d H:i:s')
			);

			$insert = $this->m_min->insert('kos_kandang', $data);

			if ($insert) {
				// Log aktivitas
				$tb_log = 'log';
				$log = array(
					'id_user' 	=> $this->session->userdata('isId'),
					'aksi'    	=> 'Menambah data kandang - ' . $nama,
				);
				$this->m_min->insert($tb_log, $log);

				$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Data kandang berhasil ditambahkan</center></div></div>");
			} else {
				$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal menambah data kandang</center></div></div>");
			}

			redirect('kandang');
		}
	}

	/**
	 * Edit kandang
	 */
	public function edit($id)
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'kandang';
		$data['kandang']	= $this->m_min->get_kandang_by_id($id)->row_array();

		$this->load_template($data, 'admin/page/edit_kandang');
	}

	/**
	 * Update kandang
	 */
	public function update()
	{
		$id = $this->input->post('id');
		$nama = $this->input->post('nama_kandang');

		$data = array(
			'nama_kandang' => $nama,
			'updated_at' => date('Y-m-d H:i:s')
		);

		$where = array('id' => $id);
		$update = $this->m_min->update('v_kandang', $data, $where);

		if ($update) {
			// Log aktivitas
			$tb_log = 'log';
			$log = array(
				'id_user' 	=> $this->session->userdata('isId'),
				'aksi'    	=> 'Mengupdate data kandang - ' . $nama,
			);
			$this->m_min->insert($tb_log, $log);

			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Data kandang berhasil diupdate</center></div></div>");
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal mengupdate data kandang</center></div></div>");
		}

		redirect('kandang');
	}

	/**
	 * Ubah status kandang (aktif/nonaktif)
	 */
	public function status($id)
	{
		$kandang = $this->m_min->get_kandang_by_id($id)->row_array();
		
		if ($kandang) {
			$new_status = ($kandang['status'] == 'aktif') ? 'nonaktif' : 'aktif';
			
			$data = array(
				'status' => $new_status,
				'updated_at' => date('Y-m-d H:i:s')
			);

			$where = array('id' => $id);
			$update = $this->m_min->update('v_kandang', $data, $where);

			if ($update) {
				// Log aktivitas
				$tb_log = 'log';
				$log = array(
					'id_user' 	=> $this->session->userdata('isId'),
					'aksi'    	=> 'Mengubah status kandang - ' . $kandang['nama_kandang'] . ' menjadi ' . $new_status,
				);
				$this->m_min->insert($tb_log, $log);

				$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Status kandang berhasil diubah</center></div></div>");
			} else {
				$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal mengubah status kandang</center></div></div>");
			}
		}

		redirect('kandang');
	}
}
