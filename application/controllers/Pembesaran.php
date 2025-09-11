<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Pembesaran Controller
 * Menangani semua fungsi terkait manajemen pembesaran
 */
class Pembesaran extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library(['session', 'template']);
		$this->load->helper(['url']);
		$this->load->model(['M_pembesaran', 'M_penetasan', 'm_min']);
		
		// Simple authentication check
		if ($this->session->userdata('isLog') != TRUE) {
			redirect('mimin');
		}
		if ($this->session->userdata('isUname') == "") {
			redirect('mimin');
		}
		
		date_default_timezone_set("Asia/Jakarta");
	}

	/**
	 * Load template method
	 */
	private function load_template($view, $data = array())
	{
		// Set common data
		$data['thisPage'] = 'opr';
		$data['thisPg'] = 'pembesaran';
		
		// Load views
		$this->load->view('admin/template/header', $data);
		$this->load->view('admin/template/top', $data);  
		$this->load->view('admin/template/sidebar', $data);
		$this->load->view('admin/' . $view, $data);
		$this->load->view('admin/template/footer', $data);
	}

	/**
	 * Dashboard pembesaran dengan statistik dan data aktif
	 */
	public function index()
	{
		try {
			$data['profil'] = $this->m_min->profil($this->session->userdata('isUname'))->row_array();
			
			// Get statistik pembesaran
			$data['periode_aktif'] = $this->M_pembesaran->get_periode_aktif();
			$data['total_hidup'] = $this->M_pembesaran->get_total_hidup();
			$data['rata_berat'] = $this->M_pembesaran->get_rata_berat();
			$data['total_biaya'] = $this->M_pembesaran->get_total_biaya();
			
			// Get data pembesaran untuk tabel
			$data['data'] = $this->M_pembesaran->get_all_pembesaran();
			
		} catch (Exception $e) {
			// Fallback data jika error
			$data['profil'] = array('username' => $this->session->userdata('isUname'));
			$data['periode_aktif'] = 0;
			$data['total_hidup'] = 0;
			$data['rata_berat'] = 0;
			$data['total_biaya'] = 0;
			$data['data'] = array();
		}

		$this->load_template('pembesaran/index_pembesaran', $data);
	}

	/**
	 * Halaman daftar semua pembesaran
	 */
	public function daftar()
	{
		try {
			$data['profil'] = $this->m_min->profil($this->session->userdata('isUname'))->row_array();
			$data['data'] = $this->M_pembesaran->get_all_pembesaran();
			$data['kandang'] = $this->m_min->rd_kandang_aktif();
		} catch (Exception $e) {
			$data['profil'] = array('username' => $this->session->userdata('isUname'));
			$data['data'] = array();
			$data['kandang'] = array();
		}

		$this->load_template('pembesaran/daftar_pembesaran', $data);
	}

	/**
	 * Form tambah pembesaran
	 */
	public function tambah()
	{
		$data['profil'] = $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['kandang'] = $this->m_min->rd_kandang_aktif();

		$this->load_template('pembesaran/tambah_pembesaran', $data);
	}

	/**
	 * Halaman detail pembesaran
	 */
	public function detail($id = null)
	{
		if (!$id) {
			$this->session->set_flashdata('error', 'ID pembesaran tidak valid');
			redirect('pembesaran');
		}

		try {
			$data['profil'] = $this->m_min->profil($this->session->userdata('isUname'))->row_array();
			$data['pembesaran'] = $this->M_pembesaran->get_pembesaran_by_id($id);
			
			if (!$data['pembesaran']) {
				$this->session->set_flashdata('error', 'Data pembesaran tidak ditemukan');
				redirect('pembesaran');
			}
		} catch (Exception $e) {
			$this->session->set_flashdata('error', 'Terjadi kesalahan saat mengambil data');
			redirect('pembesaran');
		}

		$this->load_template('pembesaran/detail_pembesaran', $data);
	}

	/**
	 * Simpan pembesaran baru
	 */
	public function simpan()
	{
		$periode = $this->input->post('periode');
		$id_kandang = $this->input->post('id_kandang');
		$tgl_masuk = $this->input->post('tgl_masuk');
		$jml_awal = $this->input->post('jml_awal');
		$berat_awal = $this->input->post('berat_awal');
		$durasi_hari = $this->input->post('durasi_hari');
		$target_panen = $this->input->post('target_panen');
		$target_berat = $this->input->post('target_berat');
		$catatan = $this->input->post('catatan');

		$data = array(
			'periode' => $periode,
			'id_kandang' => $id_kandang,
			'tanggal_mulai' => $tgl_masuk,
			'tanggal_selesai' => $target_panen,
			'target_hari' => $durasi_hari,
			'jumlah_bibit' => $jml_awal,
			'jumlah_hidup' => $jml_awal,
			'jumlah_mati' => 0,
			'berat_rata' => $berat_awal,
			'status' => 'aktif',
			'catatan' => $catatan,
			'created_at' => date('Y-m-d H:i:s')
		);

		try {
			$insert = $this->M_pembesaran->insert_pembesaran($data);

			if ($insert) {
				// Log aktivitas
				$log = array(
					'id_user' => $this->session->userdata('isId'),
					'aksi' => 'Menambah periode pembesaran baru - ' . $periode,
				);
				$this->m_min->insert('log', $log);

				$this->session->set_flashdata('success', 'Periode pembesaran berhasil ditambahkan');
			} else {
				$this->session->set_flashdata('error', 'Gagal menambahkan periode pembesaran');
			}
		} catch (Exception $e) {
			$this->session->set_flashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
		}

		redirect('pembesaran');
	}

	/**
	 * Form edit pembesaran
	 */
	public function edit($id = null)
	{
		if (!$id) {
			$this->session->set_flashdata('error', 'ID pembesaran tidak valid');
			redirect('pembesaran');
		}

		$data['profil'] = $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['pembesaran'] = $this->M_pembesaran->get_pembesaran_by_id($id);
		$data['kandang'] = $this->m_min->rd_kandang_aktif();

		if (!$data['pembesaran']) {
			$this->session->set_flashdata('error', 'Data pembesaran tidak ditemukan');
			redirect('pembesaran');
		}

		$this->load_template('pembesaran/edit_pembesaran', $data);
	}

	/**
	 * Update data pembesaran
	 */
	public function update()
	{
		$id = $this->input->post('id_pembesaran');
		$periode = $this->input->post('periode');
		$id_kandang = $this->input->post('id_kandang');
		$jml_saat_ini = $this->input->post('jml_saat_ini');
		$target_panen = $this->input->post('target_panen');
		$target_berat = $this->input->post('target_berat');
		$catatan = $this->input->post('catatan');

		// Get data existing untuk hitung jumlah mati
		$existing = $this->M_pembesaran->get_pembesaran_by_id($id);
		$jumlah_mati = $existing->jumlah_bibit - $jml_saat_ini;

		$data = array(
			'periode' => $periode,
			'id_kandang' => $id_kandang,
			'jumlah_hidup' => $jml_saat_ini,
			'jumlah_mati' => $jumlah_mati,
			'tanggal_selesai' => $target_panen,
			'berat_rata' => $target_berat,
			'catatan' => $catatan,
			'updated_at' => date('Y-m-d H:i:s')
		);

		try {
			$update = $this->M_pembesaran->update_pembesaran($id, $data);

			if ($update) {
				// Log aktivitas
				$log = array(
					'id_user' => $this->session->userdata('isId'),
					'aksi' => 'Mengupdate data pembesaran - ' . $periode,
				);
				$this->m_min->insert('log', $log);

				$this->session->set_flashdata('success', 'Data pembesaran berhasil diupdate');
			} else {
				$this->session->set_flashdata('error', 'Gagal mengupdate data pembesaran');
			}
		} catch (Exception $e) {
			$this->session->set_flashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
		}

		redirect('pembesaran/detail/' . $id);
	}

	/**
	 * Selesaikan pembesaran (panen)
	 */
	public function panen($id = null)
	{
		if (!$id) {
			$this->session->set_flashdata('error', 'ID pembesaran tidak valid');
			redirect('pembesaran');
		}

		try {
			$pembesaran = $this->M_pembesaran->get_pembesaran_by_id($id);
			
			if (!$pembesaran) {
				$this->session->set_flashdata('error', 'Data pembesaran tidak ditemukan');
				redirect('pembesaran');
			}

			if ($pembesaran->status != 'aktif') {
				$this->session->set_flashdata('error', 'Pembesaran sudah tidak aktif');
				redirect('pembesaran/detail/' . $id);
			}

			$data = array(
				'status' => 'selesai',
				'tanggal' => date('Y-m-d'),
				'updated_at' => date('Y-m-d H:i:s')
			);

			$update = $this->M_pembesaran->update_pembesaran($id, $data);

			if ($update) {
				// Log aktivitas
				$log = array(
					'id_user' => $this->session->userdata('isId'),
					'aksi' => 'Menyelesaikan pembesaran (panen) - ' . $pembesaran->periode,
				);
				$this->m_min->insert('log', $log);

				$this->session->set_flashdata('success', 'Pembesaran berhasil diselesaikan (panen)');
			} else {
				$this->session->set_flashdata('error', 'Gagal menyelesaikan pembesaran');
			}
		} catch (Exception $e) {
			$this->session->set_flashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
		}

		redirect('pembesaran');
	}

	/**
	 * Hapus data pembesaran
	 */
	public function hapus($id = null)
	{
		if (!$id) {
			$this->session->set_flashdata('error', 'ID pembesaran tidak valid');
			redirect('pembesaran');
		}

		try {
			$pembesaran = $this->M_pembesaran->get_pembesaran_by_id($id);
			
			if (!$pembesaran) {
				$this->session->set_flashdata('error', 'Data pembesaran tidak ditemukan');
				redirect('pembesaran');
			}

			$delete = $this->M_pembesaran->delete_pembesaran($id);

			if ($delete) {
				// Log aktivitas
				$log = array(
					'id_user' => $this->session->userdata('isId'),
					'aksi' => 'Menghapus data pembesaran - ' . $pembesaran->periode,
				);
				$this->m_min->insert('log', $log);

				$this->session->set_flashdata('success', 'Data pembesaran berhasil dihapus');
			} else {
				$this->session->set_flashdata('error', 'Gagal menghapus data pembesaran');
			}
		} catch (Exception $e) {
			$this->session->set_flashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
		}

		redirect('pembesaran');
	}

	/**
	 * Generate laporan pembesaran
	 */
	public function laporan()
	{
		$bulan = $this->input->post('bulan') ?? date('m');
		$tahun = $this->input->post('tahun') ?? date('Y');

		try {
			$data['profil'] = $this->m_min->profil($this->session->userdata('isUname'))->row_array();
			$data['laporan'] = $this->M_pembesaran->get_laporan_pembesaran($bulan, $tahun);
			$data['bulan'] = $bulan;
			$data['tahun'] = $tahun;
		} catch (Exception $e) {
			$data['profil'] = array('username' => $this->session->userdata('isUname'));
			$data['laporan'] = array();
			$data['bulan'] = $bulan;
			$data['tahun'] = $tahun;
		}

		$this->load_template('pembesaran/laporan', $data);
	}

	/**
	 * AJAX: Detail pembesaran
	 */
	public function detail_ajax()
	{
		$id = $this->input->post('id');
		
		if (!$id) {
			echo json_encode(['status' => false, 'message' => 'ID tidak valid']);
			return;
		}

		try {
			$pembesaran = $this->M_pembesaran->get_pembesaran_by_id($id);
			
			if ($pembesaran) {
				echo json_encode(['status' => true, 'data' => $pembesaran]);
			} else {
				echo json_encode(['status' => false, 'message' => 'Data tidak ditemukan']);
			}
		} catch (Exception $e) {
			echo json_encode(['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
		}
	}

	/**
	 * AJAX: Form edit pembesaran
	 */
	public function form_edit()
	{
		$id = $this->input->post('id');
		
		if (!$id) {
			echo json_encode(['status' => false, 'message' => 'ID tidak valid']);
			return;
		}

		try {
			$pembesaran = $this->M_pembesaran->get_pembesaran_by_id($id);
			$kandang = $this->m_min->rd_kandang_aktif();
			
			if ($pembesaran) {
				echo json_encode([
					'status' => true, 
					'data' => $pembesaran,
					'kandang' => $kandang
				]);
			} else {
				echo json_encode(['status' => false, 'message' => 'Data tidak ditemukan']);
			}
		} catch (Exception $e) {
			echo json_encode(['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
		}
	}

	/**
	 * AJAX: Simpan update pembesaran
	 */
	public function update_ajax()
	{
		$id = $this->input->post('id');
		$periode = $this->input->post('periode');
		$id_kandang = $this->input->post('id_kandang');
		$tanggal_mulai = $this->input->post('tanggal_mulai');
		$tanggal_selesai = $this->input->post('tanggal_selesai');
		$jumlah_bibit = $this->input->post('jumlah_bibit');
		$jumlah_hidup = $this->input->post('jumlah_hidup');
		$jumlah_mati = $this->input->post('jumlah_mati');
		$berat_rata = $this->input->post('berat_rata');
		$catatan = $this->input->post('catatan');

		$data = array(
			'periode' => $periode,
			'id_kandang' => $id_kandang,
			'tanggal_mulai' => $tanggal_mulai,
			'tanggal_selesai' => $tanggal_selesai,
			'jumlah_bibit' => $jumlah_bibit,
			'jumlah_hidup' => $jumlah_hidup,
			'jumlah_mati' => $jumlah_mati,
			'berat_rata' => $berat_rata,
			'catatan' => $catatan,
			'updated_at' => date('Y-m-d H:i:s')
		);

		try {
			$update = $this->M_pembesaran->update_pembesaran($id, $data);

			if ($update) {
				$log = array(
					'id_user' => $this->session->userdata('isId'),
					'aksi' => 'Mengupdate periode pembesaran - ' . $periode,
				);
				$this->m_min->insert('log', $log);

				echo json_encode(['status' => true, 'message' => 'Data berhasil diupdate']);
			} else {
				echo json_encode(['status' => false, 'message' => 'Gagal mengupdate data']);
			}
		} catch (Exception $e) {
			echo json_encode(['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
		}
	}

	/**
	 * AJAX: Hapus pembesaran
	 */
	public function hapus_ajax()
	{
		$id = $this->input->post('id');
		
		if (!$id) {
			echo json_encode(['status' => false, 'message' => 'ID tidak valid']);
			return;
		}

		try {
			$pembesaran = $this->M_pembesaran->get_pembesaran_by_id($id);
			
			if (!$pembesaran) {
				echo json_encode(['status' => false, 'message' => 'Data tidak ditemukan']);
				return;
			}

			$delete = $this->M_pembesaran->delete_pembesaran($id);

			if ($delete) {
				$log = array(
					'id_user' => $this->session->userdata('isId'),
					'aksi' => 'Menghapus periode pembesaran - ' . $pembesaran->periode,
				);
				$this->m_min->insert('log', $log);

				echo json_encode(['status' => true, 'message' => 'Data berhasil dihapus']);
			} else {
				echo json_encode(['status' => false, 'message' => 'Gagal menghapus data']);
			}
		} catch (Exception $e) {
			echo json_encode(['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
		}
	}

	/**
	 * AJAX: Tambah pembesaran baru
	 */
	public function tambah_ajax()
	{
		$periode = $this->input->post('periode');
		$id_kandang = $this->input->post('id_kandang');
		$tanggal_mulai = $this->input->post('tanggal_mulai');
		$tanggal_selesai = $this->input->post('tanggal_selesai');
		$jumlah_bibit = $this->input->post('jumlah_bibit');
		$berat_rata = $this->input->post('berat_rata');
		$catatan = $this->input->post('catatan');

		$data = array(
			'periode' => $periode,
			'id_kandang' => $id_kandang,
			'tanggal_mulai' => $tanggal_mulai,
			'tanggal_selesai' => $tanggal_selesai,
			'jumlah_bibit' => $jumlah_bibit,
			'jumlah_hidup' => $jumlah_bibit,
			'jumlah_mati' => 0,
			'berat_rata' => $berat_rata,
			'status' => 'aktif',
			'catatan' => $catatan,
			'created_at' => date('Y-m-d H:i:s')
		);

		try {
			$insert = $this->M_pembesaran->insert_pembesaran($data);

			if ($insert) {
				$log = array(
					'id_user' => $this->session->userdata('isId'),
					'aksi' => 'Menambah periode pembesaran baru - ' . $periode,
				);
				$this->m_min->insert('log', $log);

				echo json_encode(['status' => true, 'message' => 'Data berhasil ditambahkan']);
			} else {
				echo json_encode(['status' => false, 'message' => 'Gagal menambahkan data']);
			}
		} catch (Exception $e) {
			echo json_encode(['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
		}
	}
}
