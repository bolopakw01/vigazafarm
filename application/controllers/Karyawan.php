<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Karyawan Controller
 * Menangani semua fungsi terkait manajemen karyawan
 */
class Karyawan extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library(['session', 'template']);
		$this->load->helper(['url']);
		$this->load->model(['M_karyawan', 'm_min']);
		
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
		$data['thisPg'] = 'karyawan';
		
		// Load views
		$this->load->view('admin/template/header', $data);
		$this->load->view('admin/template/top', $data);  
		$this->load->view('admin/template/sidebar', $data);
		$this->load->view('admin/' . $view, $data);
		$this->load->view('admin/template/footer', $data);
	}

	/**
	 * Halaman daftar karyawan
	 */
	public function index()
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'karyawan';

		$data['karyawan']	= $this->m_min->rd_karyawan();
		$data['jml_siswa']	= $this->m_min->jml_penghuni();
		$data['jml_bb']		= $this->m_min->jml_bb();
		$data['jml_br']		= $this->m_min->jml_br();

		$this->load_template('page/karyawan_index', $data);
	}

	/**
	 * Tambah karyawan baru (from modal)
	 */
	public function tambah()
	{
		$nip = $this->input->post('nip');
		$nama = $this->input->post('nama');
		$nama_lengkap = $this->input->post('nama_lengkap') ?: $nama;
		$jabatan = $this->input->post('jabatan');
		$email = $this->input->post('email');
		$phone = $this->input->post('phone');
		$tanggal_lahir = $this->input->post('tanggal_lahir');
		$jenis_kelamin = $this->input->post('jenis_kelamin') ?: 'L';
		$tanggal_masuk = $this->input->post('tanggal_masuk') ?: date('Y-m-d');
		$gaji_pokok = $this->input->post('gaji_pokok') ?: 0;
		$tunjangan = $this->input->post('tunjangan') ?: 0;
		$status = $this->input->post('status') ?: 'aktif';
		$alamat = $this->input->post('alamat');
		$keterangan = $this->input->post('keterangan');

		$total_gaji = $gaji_pokok + $tunjangan;

		// Cek apakah NIP sudah ada
		$cek_nip = $this->db->get_where('kos_karyawan', ['nip' => $nip]);
		if ($cek_nip->num_rows() > 0) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-warning\" id=\"alert\"><center>NIP sudah terdaftar</center></div></div>");
			redirect('karyawan');
			return;
		}

		// Cek apakah email sudah ada (jika diisi)
		if (!empty($email)) {
			$cek_email = $this->db->get_where('kos_karyawan', ['email' => $email]);
			if ($cek_email->num_rows() > 0) {
				$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-warning\" id=\"alert\"><center>Email sudah terdaftar</center></div></div>");
				redirect('karyawan');
				return;
			}
		}

		$data = array(
			'nip' => $nip,
			'nama' => $nama,
			'nama_lengkap' => $nama_lengkap,
			'jabatan' => $jabatan,
			'email' => $email,
			'phone' => $phone,
			'tanggal_lahir' => $tanggal_lahir,
			'jenis_kelamin' => $jenis_kelamin,
			'tanggal_masuk' => $tanggal_masuk,
			'gaji_pokok' => $gaji_pokok,
			'tunjangan' => $tunjangan,
			'total_gaji' => $total_gaji,
			'status' => $status,
			'alamat' => $alamat,
			'keterangan' => $keterangan,
			'created_at' => date('Y-m-d H:i:s')
		);

		$insert = $this->m_min->insert('kos_karyawan', $data);

		if ($insert) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Karyawan berhasil ditambahkan</center></div></div>");
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal menambahkan karyawan</center></div></div>");
		}

		redirect('karyawan');
	}

	/**
	 * Simpan karyawan baru
	 */
	public function simpan()
	{
		$nip = $this->input->post('nip');
		$nama = $this->input->post('nama');
		$email = $this->input->post('email');
		$phone = $this->input->post('phone');
		$alamat = $this->input->post('alamat');
		$jabatan = $this->input->post('jabatan');
		$gaji_pokok = $this->input->post('gaji_pokok');

		// Cek apakah NIP sudah ada
		if ($this->m_min->cek_karyawan($nip)->num_rows() > 0) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-warning\" id=\"alert\"><center>NIP sudah terdaftar</center></div></div>");
			redirect('karyawan/tambah');
			return;
		}

		// Cek apakah email sudah ada
		if ($this->m_min->cek_email_karyawan($email)->num_rows() > 0) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-warning\" id=\"alert\"><center>Email sudah terdaftar</center></div></div>");
			redirect('karyawan/tambah');
			return;
		}

		$data = array(
			'nip' => $nip,
			'nama' => $nama,
			'nama_lengkap' => $nama,
			'email' => $email,
			'phone' => $phone,
			'alamat' => $alamat,
			'jabatan' => $jabatan,
			'gaji_pokok' => $gaji_pokok ? $gaji_pokok : 0,
			'tunjangan' => 0,
			'total_gaji' => $gaji_pokok ? $gaji_pokok : 0,
			'jenis_kelamin' => 'L',
			'tanggal_masuk' => date('Y-m-d'),
			'status' => 'aktif',
			'created_at' => date('Y-m-d H:i:s')
		);

		$insert = $this->m_min->insert('kos_karyawan', $data);

		if ($insert) {
			// Log aktivitas
			$tb_log = 'log';
			$log = array(
				'id_user' 	=> $this->session->userdata('isId'),
				'aksi'    	=> 'Menambah data karyawan - ' . $nama,
			);
			$this->m_min->insert($tb_log, $log);

			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Data karyawan berhasil ditambahkan</center></div></div>");
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal menambah data karyawan</center></div></div>");
		}

		redirect('karyawan');
	}

	/**
	 * Update karyawan (new version)
	 */
	public function update()
	{
		$id = $this->input->post('id_karyawan');
		$nip = $this->input->post('nip');
		$nama = $this->input->post('nama');
		$nama_lengkap = $this->input->post('nama_lengkap');
		$jabatan = $this->input->post('jabatan');
		$email = $this->input->post('email');
		$phone = $this->input->post('phone');
		$tanggal_lahir = $this->input->post('tanggal_lahir');
		$jenis_kelamin = $this->input->post('jenis_kelamin');
		$tanggal_masuk = $this->input->post('tanggal_masuk');
		$gaji_pokok = $this->input->post('gaji_pokok') ?: 0;
		$tunjangan = $this->input->post('tunjangan') ?: 0;
		$status = $this->input->post('status');
		$alamat = $this->input->post('alamat');
		$keterangan = $this->input->post('keterangan');

		$total_gaji = $gaji_pokok + $tunjangan;

		$data = array(
			'nip' => $nip,
			'nama' => $nama,
			'nama_lengkap' => $nama_lengkap,
			'jabatan' => $jabatan,
			'email' => $email,
			'phone' => $phone,
			'tanggal_lahir' => $tanggal_lahir,
			'jenis_kelamin' => $jenis_kelamin,
			'tanggal_masuk' => $tanggal_masuk,
			'gaji_pokok' => $gaji_pokok,
			'tunjangan' => $tunjangan,
			'total_gaji' => $total_gaji,
			'status' => $status,
			'alamat' => $alamat,
			'keterangan' => $keterangan,
			'updated_at' => date('Y-m-d H:i:s')
		);

		$where = array('id_karyawan' => $id);
		$update = $this->m_min->update('kos_karyawan', $data, $where);

		if ($update) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Data karyawan berhasil diupdate</center></div></div>");
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal mengupdate data karyawan</center></div></div>");
		}

		redirect('karyawan');
	}

	/**
	 * Ubah status karyawan (aktif/nonaktif)
	 */
	public function status($id)
	{
		$karyawan = $this->m_min->get_karyawan_by_id($id)->row_array();
		
		if ($karyawan) {
			$new_status = ($karyawan['status'] == 'aktif') ? 'nonaktif' : 'aktif';
			
			$data = array(
				'status' => $new_status,
				'updated_at' => date('Y-m-d H:i:s')
			);

			$where = array('id_karyawan' => $id);
			$update = $this->m_min->update('kos_karyawan', $data, $where);

			if ($update) {
				// Log aktivitas
				$tb_log = 'log';
				$log = array(
					'id_user' 	=> $this->session->userdata('isId'),
					'aksi'    	=> 'Mengubah status karyawan - ' . $karyawan['nama'] . ' menjadi ' . $new_status,
				);
				$this->m_min->insert($tb_log, $log);

				$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Status karyawan berhasil diubah</center></div></div>");
			} else {
				$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal mengubah status karyawan</center></div></div>");
			}
		}

		redirect('karyawan');
	}

	/**
	 * AJAX - Detail karyawan
	 */
	public function detail()
	{
		$id = $this->input->post('id');
		$karyawan = $this->db->get_where('kos_karyawan', ['id_karyawan' => $id])->row();
		
		if ($karyawan) {
			echo '<div class="row">';
			echo '<div class="col-md-6">';
			echo '<table class="table table-borderless">';
			echo '<tr><td width="30%"><strong>NIP</strong></td><td>: ' . $karyawan->nip . '</td></tr>';
			echo '<tr><td><strong>Nama</strong></td><td>: ' . $karyawan->nama . '</td></tr>';
			echo '<tr><td><strong>Nama Lengkap</strong></td><td>: ' . $karyawan->nama_lengkap . '</td></tr>';
			echo '<tr><td><strong>Jabatan</strong></td><td>: ' . $karyawan->jabatan . '</td></tr>';
			echo '<tr><td><strong>Email</strong></td><td>: ' . $karyawan->email . '</td></tr>';
			echo '<tr><td><strong>Phone</strong></td><td>: ' . $karyawan->phone . '</td></tr>';
			echo '</table>';
			echo '</div>';
			echo '<div class="col-md-6">';
			echo '<table class="table table-borderless">';
			echo '<tr><td width="30%"><strong>Tanggal Lahir</strong></td><td>: ' . date('d-m-Y', strtotime($karyawan->tanggal_lahir)) . '</td></tr>';
			echo '<tr><td><strong>Jenis Kelamin</strong></td><td>: ' . ($karyawan->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan') . '</td></tr>';
			echo '<tr><td><strong>Tanggal Masuk</strong></td><td>: ' . date('d-m-Y', strtotime($karyawan->tanggal_masuk)) . '</td></tr>';
			echo '<tr><td><strong>Status</strong></td><td>: ' . ucfirst($karyawan->status) . '</td></tr>';
			echo '<tr><td><strong>Gaji Pokok</strong></td><td>: Rp ' . number_format($karyawan->gaji_pokok, 0, ',', '.') . '</td></tr>';
			echo '<tr><td><strong>Tunjangan</strong></td><td>: Rp ' . number_format($karyawan->tunjangan, 0, ',', '.') . '</td></tr>';
			echo '</table>';
			echo '</div>';
			echo '</div>';
			if ($karyawan->alamat) {
				echo '<div class="row"><div class="col-12"><strong>Alamat:</strong><br>' . $karyawan->alamat . '</div></div>';
			}
			if ($karyawan->keterangan) {
				echo '<div class="row mt-2"><div class="col-12"><strong>Keterangan:</strong><br>' . $karyawan->keterangan . '</div></div>';
			}
		} else {
			echo '<div class="alert alert-warning">Data karyawan tidak ditemukan</div>';
		}
	}

	/**
	 * AJAX - Form edit karyawan
	 */
	public function form_edit()
	{
		$id = $this->input->post('id');
		$karyawan = $this->db->get_where('kos_karyawan', ['id_karyawan' => $id])->row();
		
		if ($karyawan) {
			echo '<input type="hidden" name="id_karyawan" value="' . $karyawan->id_karyawan . '">';
			echo '<div class="row">';
			echo '<div class="col-md-6">';
			echo '<div class="form-group">';
			echo '<label>NIP <span class="text-danger">*</span></label>';
			echo '<input type="text" class="form-control" name="nip" value="' . $karyawan->nip . '" required>';
			echo '</div>';
			echo '<div class="form-group">';
			echo '<label>Nama <span class="text-danger">*</span></label>';
			echo '<input type="text" class="form-control" name="nama" value="' . $karyawan->nama . '" required>';
			echo '</div>';
			echo '<div class="form-group">';
			echo '<label>Nama Lengkap</label>';
			echo '<input type="text" class="form-control" name="nama_lengkap" value="' . $karyawan->nama_lengkap . '">';
			echo '</div>';
			echo '<div class="form-group">';
			echo '<label>Jabatan <span class="text-danger">*</span></label>';
			echo '<input type="text" class="form-control" name="jabatan" value="' . $karyawan->jabatan . '" required>';
			echo '</div>';
			echo '<div class="form-group">';
			echo '<label>Email</label>';
			echo '<input type="email" class="form-control" name="email" value="' . $karyawan->email . '">';
			echo '</div>';
			echo '<div class="form-group">';
			echo '<label>Phone</label>';
			echo '<input type="text" class="form-control" name="phone" value="' . $karyawan->phone . '">';
			echo '</div>';
			echo '</div>';
			echo '<div class="col-md-6">';
			echo '<div class="form-group">';
			echo '<label>Tanggal Lahir</label>';
			echo '<input type="date" class="form-control" name="tanggal_lahir" value="' . $karyawan->tanggal_lahir . '">';
			echo '</div>';
			echo '<div class="form-group">';
			echo '<label>Jenis Kelamin</label>';
			echo '<select class="form-control" name="jenis_kelamin">';
			echo '<option value="">Pilih Gender</option>';
			echo '<option value="L"' . ($karyawan->jenis_kelamin == 'L' ? ' selected' : '') . '>Laki-laki</option>';
			echo '<option value="P"' . ($karyawan->jenis_kelamin == 'P' ? ' selected' : '') . '>Perempuan</option>';
			echo '</select>';
			echo '</div>';
			echo '<div class="form-group">';
			echo '<label>Tanggal Masuk</label>';
			echo '<input type="date" class="form-control" name="tanggal_masuk" value="' . $karyawan->tanggal_masuk . '">';
			echo '</div>';
			echo '<div class="form-group">';
			echo '<label>Gaji Pokok</label>';
			echo '<input type="number" class="form-control" name="gaji_pokok" value="' . $karyawan->gaji_pokok . '" min="0">';
			echo '</div>';
			echo '<div class="form-group">';
			echo '<label>Tunjangan</label>';
			echo '<input type="number" class="form-control" name="tunjangan" value="' . $karyawan->tunjangan . '" min="0">';
			echo '</div>';
			echo '<div class="form-group">';
			echo '<label>Status</label>';
			echo '<select class="form-control" name="status">';
			echo '<option value="aktif"' . ($karyawan->status == 'aktif' ? ' selected' : '') . '>Aktif</option>';
			echo '<option value="non_aktif"' . ($karyawan->status == 'non_aktif' ? ' selected' : '') . '>Non Aktif</option>';
			echo '<option value="resign"' . ($karyawan->status == 'resign' ? ' selected' : '') . '>Resign</option>';
			echo '</select>';
			echo '</div>';
			echo '</div>';
			echo '<div class="col-md-12">';
			echo '<div class="form-group">';
			echo '<label>Alamat</label>';
			echo '<textarea class="form-control" name="alamat" rows="3">' . $karyawan->alamat . '</textarea>';
			echo '</div>';
			echo '<div class="form-group">';
			echo '<label>Keterangan</label>';
			echo '<textarea class="form-control" name="keterangan" rows="2">' . $karyawan->keterangan . '</textarea>';
			echo '</div>';
			echo '</div>';
			echo '</div>';
		} else {
			echo '<div class="alert alert-warning">Data karyawan tidak ditemukan</div>';
		}
	}

	/**
	 * Hapus karyawan (AJAX)
	 */
	public function hapus()
	{
		$id = $this->input->post('id');
		$karyawan = $this->db->get_where('kos_karyawan', ['id_karyawan' => $id])->row();
		
		if ($karyawan) {
			$delete = $this->db->delete('kos_karyawan', ['id_karyawan' => $id]);
			
			if ($delete) {
				echo json_encode(['status' => 'success', 'message' => 'Karyawan berhasil dihapus']);
			} else {
				echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus karyawan']);
			}
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Karyawan tidak ditemukan']);
		}
	}
}
