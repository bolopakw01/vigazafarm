<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Backoffice extends CI_Controller
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
		if ($this->session->userdata('isLevel') !== 'mimin') {
			redirect(base_url());
		}

		$this->load->model('m_min');

		date_default_timezone_set("Asia/Jakarta");

		function api_blast($name, $receiver, $msg, $sch)
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
				'name' => $name, //'H-7_A38_Jojon-Hutapeas',
				'type' => 'text',
				'status' => 'waiting',
				'message' => '{"text":"' . $msg . '"}', //'{"text":"test kirim"}',
				'schedule' => $sch, //'2023-04-14 17:20:00',
				'sender' => '6283843357816',
				'receiver' => $receiver, //'6281916664548',
				'statusmu' => 'pending',
			]);

			// $response = 
			curl_exec($curl);
			// $data = json_decode($response);

			curl_close($curl);
		}

		function lima_tahun($name, $receiver, $tgl, $msg_hmin14, $msg_hmin7, $msg_hmin3, $msg_hplus3, $msg_hplus7)
		{

			// $row       	= $this->m_min->get_last_id($table)->row_array();
			// $deposit   	= $row['id_mnl_siswa'];

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

				api_blast('H-14_' . $name . '_' . $bulan . '_' . $tahun, $receiver, $msg_hmin14, $hmin14);
				api_blast('H-7_' . $name . '_' . $bulan . '_' . $tahun, $receiver, $msg_hmin7, $hmin7);
				api_blast('H-3_' . $name . '_' . $bulan . '_' . $tahun, $receiver, $msg_hmin3, $hmin3);
				api_blast('H+3_' . $name . '_' . $bulan . '_' . $tahun, $receiver, $msg_hplus3, $hplus3);
				api_blast('H+7_' . $name . '_' . $bulan . '_' . $tahun, $receiver, $msg_hplus7, $hplus7);

				// echo '<u>Ini H-14 untuk bulan ' . $bulan . ' - ' . $tahun . ' :</u><br>' . $tanggal . ' - ' . $hmin14 . "<br>";
				// echo '<u>Ini H-7 untuk bulan ' . $bulan . ' - ' . $tahun . ' :</u><br>' . $tanggal . ' - ' . $hmin7 . "<br>";
				// echo '<u>Ini H-3 untuk bulan ' . $bulan . ' - ' . $tahun . ' :</u><br>' . $tanggal . ' - ' . $hmin3;
				// echo '<p></p>';
			}
		}
	}

	public function profil()
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'profil';
		$data['thisPg']		= 'profil';

		$datat['record']	= $this->m_min->log_today();

		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/profil');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
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
		$uprof = $this->m_min->update_profil($data, $where); //akses model untuk menyimpan ke database

		if ($uprof) {
			$sessionArray = array(
				'isLog' 	=> TRUE,
				'isId' 		=> $this->input->post('minid'),
				'isUname' 	=> $this->input->post('uname'),
				'isPass' 	=> get_hash($this->input->post('pass')),
				'isLevel' 	=> 'mimin'
			);

			$this->session->set_userdata($sessionArray);

			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Update data berhasil</center></div></div>");
			redirect('backoffice/profil'); //jika berhasil maka akan ditampilkan view vupload
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Update Data Gagal !!</center></div></div>");
			redirect('backoffice/profil'); //jika gagal maka akan ditampilkan form upload
		}
	}

	public function log()
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'settings';
		$data['thisPg']		= 'log';

		$datat['record']	= $this->m_min->log_today();

		$data['log']		= $this->m_min->log()->result();

		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/log');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
	}

	/* ========================================= PAGE ========================================= */

	public function index()
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'opr';
		$data['thisPg']		= 'dashboard';

		$datat['record']	= $this->m_min->log_today();

		$data['data']		= $this->m_min->rd_pembayaran_ba();
		$data['jml_siswa']	= $this->m_min->jml_penghuni();
		$data['jml_bb']		= $this->m_min->jml_bb();
		$data['jml_br']		= $this->m_min->jml_br();

		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/dashboard');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
	}

	/* ========================================= MASTER ========================================= */

	// ---------------------------------------- KANDANG ----------------------------------------
	public function kandang()
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'kandang';
		// $data['thisView']	= 'grid';

		$datat['record']	= $this->m_min->log_today();

		$data['data']		= $this->m_min->rd_kandang();
		$data['jml_siswa']	= $this->m_min->jml_penghuni();
		$data['jml_bb']		= $this->m_min->jml_bb();
		$data['jml_br']		= $this->m_min->jml_br();

		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/page/kandang');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
	}

	public function in_kandang()
	{
		$table 		= 'v_kandang';
		// $table 		= 'v_kamar';
		// $tables		= 'mnl_ortu';
		// $tgl_lahir 	= $this->input->post('tgl_lahir');
		// $password 	= preg_replace("/[^0-9]/", "", $tgl_lahir);

		// data untuk kirim ke db kandang
		$nama			= $this->input->post('nama_kandang');
		// $name			= $this->input->post('nama_pic');
		// $hp				= $this->input->post('hp_pic');
		// $tgl_berdiri 	= $this->input->post('tgl_berdiri');
		// $alamat			= $this->input->post('alamat');
		// $keterangan		= $this->input->post('keterangan');
		// $password 		= substr($hp, -4);

		// data untuk kirim ke db kandang
		// $row       	= '1000000'; //$this->m_min->get_last_id($table)->row_array();
		// $deposit   	= $row; //$row['id_mnl_siswa'];

		// get data msg
		// $row14    		= $this->m_min->msg_hmin14()->row_array();
		// $msg_hmin14   	= $row14['msg'];

		// $row7    		= $this->m_min->msg_hmin7()->row_array();
		// $msg_hmin7   	= $row7['msg'];

		// $row3    		= $this->m_min->msg_hmin3()->row_array();
		// $msg_hmin3   	= $row3['msg'];

		// $row23    		= $this->m_min->msg_hplus3()->row_array();
		// $msg_hplus3   	= $row23['msg'];

		// $row27    		= $this->m_min->msg_hplus7()->row_array();
		// $msg_hplus7    	= $row27['msg'];

		if ($this->m_min->cek_kandang($nama)->num_rows() == 1) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-warning\" id=\"alert\"><center>Data Kandang Sudah Ada</center></div></div>");
			redirect('backoffice/kandang');
		} else {
			// $path = './assets/back/images/regist/';

			// $where = array('id_mnl_siswa' => $id);

			// get foto
			// $config['upload_path'] = $path;
			// $config['allowed_types'] = 'jpg|png|jpeg|svg|ico';
			// $config['max_size'] = '2048';  //2MB max
			// $config['max_width'] = '4480'; // pixel
			// $config['max_height'] = '4480'; // pixel
			// $config['file_name'] = $_FILES['fotopost']['name'];

			// $this->upload->initialize($config);

			// if (!empty($_FILES['fotopost']['name'])) {
			// 	if ($this->upload->do_upload('fotopost')) {
			// 		$foto = $this->upload->data();
			// 		$data = array(
			// 			'ktp'      			=> $foto['file_name'],
			// 			'id_kamar'			=> $this->input->post('kamar'),
			// 			'nama'   			=> $name,
			// 			'email'   			=> $this->input->post('email'),
			// 			'hp'   				=> $hp,
			// 			'darurat'   		=> $this->input->post('darurat'),
			// 			'kendaraan'   		=> $this->input->post('kendaraan'),
			// 			'plat'   			=> $this->input->post('plat'),
			// 			'deposit'   		=> $deposit,
			// 			'ket'				=> $this->input->post('ket'),
			// 			'password'  		=> get_hash($password),
			// 			'tgl_masuk'			=> $sch,
			// 			'level'   			=> 'kandang',
			// 			'status'   			=> 'aktif',
			// 			'jabatan' 			=> 'Penghuni'
			// 		);

			// 		// hapus foto pada direktori
			// 		// @unlink($path . $this->input->post('filelama'));

			// 		// $this->m_min->update($table, $data, $where);
			// 		$this->m_min->insert($table, $data);

			// 		$tb_log = 'log';
			// 		$log   	= array(
			// 			'id_user' 	=> $this->session->userdata('isId'),
			// 			'aksi'    	=> 'Menambah data kandang - ' . $this->input->post('nama') . '',
			// 		);
			// 		$this->m_min->insert($tb_log, $log);

			// 		// update status kamar
			// 		$upd = array(
			// 			'status'   			=> 'terisi'
			// 		);

			// 		$where 		= array(
			// 			'id_kamar'	=> $this->input->post('kamar')
			// 		);

			// 		$this->m_min->update($tables, $upd, $where);

			// 		//kirim data api schedule (notifikasi wa untuk pembayaran)
			// 		lima_tahun($name, $hp, $sch, $msg_hmin14, $msg_hmin7, $msg_hmin3, $msg_hplus3, $msg_hplus7);

			// 		$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Input Data</center></div></div>");
			// 		redirect('backoffice/kandang');
			// 	} else {
			// 		$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Input Data</center></div></div>");
			// 		redirect('backoffice/kandang');
			// 	}
			// } else {
			$data = array(
				// 'id_kamar'			=> $this->input->post('kamar'),
				'nama'   			=> $nama,
				'tgl_berdiri'   	=> $this->input->post('tgl_berdiri'),
				'alamat'   			=> $this->input->post('alamat'),
				'keterangan'   		=> $this->input->post('ket'),
				// 'darurat'   		=> $this->input->post('darurat'),
				// 'kendaraan'   		=> $this->input->post('kendaraan'),
				// 'plat'   			=> $this->input->post('plat'),
				// 'deposit'   		=> $deposit,
				// 'ket'				=> $this->input->post('ket'),
				// 'password'  		=> get_hash($password),
				// 'tgl_masuk'			=> $sch,
				// 'level'   			=> 'kandang',
				'status' 	  		=> 'aktif',
				'tanggal'   		=> date('Y-m-d'),
				'waktu' 			=> date('H:i:s')
			);

			$send = $this->m_min->insert($table, $data);

			$tb_log = 'log';
			$log   	= array(
				'id_user' 	=> $this->session->userdata('isId'),
				'aksi'    	=> 'Menambah data kandang - ' . $this->input->post('nama') . '',
			);
			$this->m_min->insert($tb_log, $log);

			// update status kamar
			// $upd = array(
			// 	'status'   			=> 'terisi'
			// );

			// $where 		= array(
			// 	'id_kamar'	=> $this->input->post('kamar')
			// );

			// $this->m_min->update($tables, $upd, $where);

			//kirim data api schedule (notifikasi wa untuk pembayaran)
			// lima_tahun($name, $hp, $sch, $msg_hmin14, $msg_hmin7, $msg_hmin3, $msg_hplus3, $msg_hplus7);

			if ($send) {
				$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Input Data</center></div></div>");
				redirect('backoffice/kandang');
			} else {
				$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Input Data</center></div></div>");
				redirect('backoffice/kandang');
			}
		}
		// }
	}

	public function edit_kandang($id)
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'kandang';

		$datat['record']	= $this->m_min->log_today();

		$data['data'] 		= $this->m_min->edit_kandang($id)->row_array();
		$data['kelas']		= $this->m_min->rd_kelas();
		$data['kamar']		= $this->m_min->rd_kamar_status();
		$data['club']		= $this->m_min->rd_jkamar();

		$data['regist']		= $this->m_min->rd_regist();
		$data['spp']		= $this->m_min->rd_spp();


		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/page/edit_kandang');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
	}

	public function up_kandang()
	{
		$table 		= 'v_kandang';

		$where 		= array(
			'id_kandang'	=> $this->input->post('id')
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

		$send = $this->m_min->update($table, $data, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Mengubah data siswa dengan ID - ' . $this->input->post('id') . '',
		);
		$this->m_min->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backoffice/siswa');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Gambar</center></div></div>");
			redirect('backoffice/siswa');
		}
	}

	function hapus_kandang($id)
	{
		$table 		= 'mnl_siswa';
		$where 		= array('id_mnl_siswa' => $id);

		$this->m_min->hapus($table, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Menghapus data siswa dengan ID - ' . $id . '',
		);
		$this->m_min->insert($tb_log, $log);

		$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil hapus data</center></div></div>");
		redirect('backoffice/siswa');
	}

	public function status_kandang($id)
	{
		$table 		= 'v_kandang';
		$tables		= 'v_kamar';

		$status 	= $this->uri->segment('4');
		$id_kamar 	= $this->uri->segment('5');

		$where 		= array(
			'id_kandang'	=> $id
		);

		$wheres 		= array(
			'id_kamar'	=> $id_kamar
		);

		if ($status === 'aktif') {
			$data = array(
				'status'		=> 'tidak aktif',
			);

			$datas = array(
				'status'		=> 'belum',
			);

			$this->m_min->update($tables, $datas, $wheres);
		} else {
			$data = array(
				'status'		=> 'aktif'
			);
		}

		$send = $this->m_min->update($table, $data, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Mengubah data status kandang dengan ID - ' . $this->input->post('id') . '',
		);
		$this->m_min->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backoffice/kandang');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Update Data</center></div></div>");
			redirect('backoffice/kandang');
		}
	}
	// ------------------------------------------------------------------------------------------


	// ---------------------------------------- KARYAWAN ----------------------------------------
	public function karyawan()
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'karyawan';
		// $data['thisView']	= 'grid';

		$datat['record']	= $this->m_min->log_today();

		$data['data']		= $this->m_min->rd_pembayaran_ba();
		$data['jml_siswa']	= $this->m_min->jml_penghuni();
		$data['jml_bb']		= $this->m_min->jml_bb();
		$data['jml_br']		= $this->m_min->jml_br();

		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/page/karyawan');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
	}

	public function in_karyawan()
	{
		$table 		= 'v_kandang';
		$table 		= 'v_kamar';
		// $tables		= 'mnl_ortu';
		// $tgl_lahir 	= $this->input->post('tgl_lahir');
		// $password 	= preg_replace("/[^0-9]/", "", $tgl_lahir);

		// data untuk kirim ke db kandang
		$name			= $this->input->post('nama_kandang');
		$name			= $this->input->post('nama_pic');
		$hp				= $this->input->post('hp_pic');
		$tgl_berdiri 	= $this->input->post('tgl_berdiri') . ' 08:00:00';
		$alamat			= $this->input->post('alamat');
		$keterangan		= $this->input->post('keterangan');
		$password 		= substr($hp, -4);

		// data untuk kirim ke db kandang
		$row       	= '1000000'; //$this->m_min->get_last_id($table)->row_array();
		$deposit   	= $row; //$row['id_mnl_siswa'];

		// get data msg
		$row14    		= $this->m_min->msg_hmin14()->row_array();
		$msg_hmin14   	= $row14['msg'];

		$row7    		= $this->m_min->msg_hmin7()->row_array();
		$msg_hmin7   	= $row7['msg'];

		$row3    		= $this->m_min->msg_hmin3()->row_array();
		$msg_hmin3   	= $row3['msg'];

		$row23    		= $this->m_min->msg_hplus3()->row_array();
		$msg_hplus3   	= $row23['msg'];

		$row27    		= $this->m_min->msg_hplus7()->row_array();
		$msg_hplus7    	= $row27['msg'];

		if ($this->m_min->cek_kandang($hp)->num_rows() == 1) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-warning\" id=\"alert\"><center>Data Penghuni Sudah Ada</center></div></div>");
			redirect('backoffice/kandang');
		} else {
			$path = './assets/back/images/regist/';

			// $where = array('id_mnl_siswa' => $id);

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
						'ktp'      			=> $foto['file_name'],
						'id_kamar'			=> $this->input->post('kamar'),
						'nama'   			=> $name,
						'email'   			=> $this->input->post('email'),
						'hp'   				=> $hp,
						'darurat'   		=> $this->input->post('darurat'),
						'kendaraan'   		=> $this->input->post('kendaraan'),
						'plat'   			=> $this->input->post('plat'),
						'deposit'   		=> $deposit,
						'ket'				=> $this->input->post('ket'),
						'password'  		=> get_hash($password),
						'tgl_masuk'			=> $sch,
						'level'   			=> 'kandang',
						'status'   			=> 'aktif',
						'jabatan' 			=> 'Penghuni'
					);

					// hapus foto pada direktori
					// @unlink($path . $this->input->post('filelama'));

					// $this->m_min->update($table, $data, $where);
					$this->m_min->insert($table, $data);

					$tb_log = 'log';
					$log   	= array(
						'id_user' 	=> $this->session->userdata('isId'),
						'aksi'    	=> 'Menambah data kandang - ' . $this->input->post('nama') . '',
					);
					$this->m_min->insert($tb_log, $log);

					// update status kamar
					$upd = array(
						'status'   			=> 'terisi'
					);

					$where 		= array(
						'id_kamar'	=> $this->input->post('kamar')
					);

					$this->m_min->update($tables, $upd, $where);

					//kirim data api schedule (notifikasi wa untuk pembayaran)
					lima_tahun($name, $hp, $sch, $msg_hmin14, $msg_hmin7, $msg_hmin3, $msg_hplus3, $msg_hplus7);

					$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Input Data</center></div></div>");
					redirect('backoffice/kandang');
				} else {
					$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Input Data</center></div></div>");
					redirect('backoffice/kandang');
				}
			} else {
				$data = array(
					'id_kamar'			=> $this->input->post('kamar'),
					'nama'   			=> $name,
					'email'   			=> $this->input->post('email'),
					'hp'   				=> $hp,
					'ktp'   			=> 'ktp.jpg',
					'darurat'   		=> $this->input->post('darurat'),
					'kendaraan'   		=> $this->input->post('kendaraan'),
					'plat'   			=> $this->input->post('plat'),
					'deposit'   		=> $deposit,
					'ket'				=> $this->input->post('ket'),
					'password'  		=> get_hash($password),
					'tgl_masuk'			=> $sch,
					'level'   			=> 'kandang',
					'status'   			=> 'aktif',
					'jabatan' 			=> 'Penghuni'
				);

				$send = $this->m_min->insert($table, $data);

				$tb_log = 'log';
				$log   	= array(
					'id_user' 	=> $this->session->userdata('isId'),
					'aksi'    	=> 'Menambah data kandang - ' . $this->input->post('nama') . '',
				);
				$this->m_min->insert($tb_log, $log);

				// update status kamar
				$upd = array(
					'status'   			=> 'terisi'
				);

				$where 		= array(
					'id_kamar'	=> $this->input->post('kamar')
				);

				$this->m_min->update($tables, $upd, $where);

				//kirim data api schedule (notifikasi wa untuk pembayaran)
				lima_tahun($name, $hp, $sch, $msg_hmin14, $msg_hmin7, $msg_hmin3, $msg_hplus3, $msg_hplus7);

				if ($send) {
					$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Input Data</center></div></div>");
					redirect('backoffice/kandang');
				} else {
					$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Input Data</center></div></div>");
					redirect('backoffice/kandang');
				}
			}
		}
	}

	public function edit_karyawan($id)
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'kandang';

		$datat['record']	= $this->m_min->log_today();

		$data['data'] 		= $this->m_min->edit_kandang($id)->row_array();
		$data['kelas']		= $this->m_min->rd_kelas();
		$data['kamar']		= $this->m_min->rd_kamar_status();
		$data['club']		= $this->m_min->rd_jkamar();

		$data['regist']		= $this->m_min->rd_regist();
		$data['spp']		= $this->m_min->rd_spp();


		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/page/edit_kandang');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
	}

	public function up_karyawan()
	{
		$table 		= 'v_kandang';

		$where 		= array(
			'id_kandang'	=> $this->input->post('id')
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

		$send = $this->m_min->update($table, $data, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Mengubah data siswa dengan ID - ' . $this->input->post('id') . '',
		);
		$this->m_min->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backoffice/siswa');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Gambar</center></div></div>");
			redirect('backoffice/siswa');
		}
	}

	function hapus_karyawan($id)
	{
		$table 		= 'mnl_siswa';
		$where 		= array('id_mnl_siswa' => $id);

		$this->m_min->hapus($table, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Menghapus data siswa dengan ID - ' . $id . '',
		);
		$this->m_min->insert($tb_log, $log);

		$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil hapus data</center></div></div>");
		redirect('backoffice/siswa');
	}

	public function status_karyawan($id)
	{
		$table 		= 'v_kandang';
		$tables		= 'v_kamar';

		$status 	= $this->uri->segment('4');
		$id_kamar 	= $this->uri->segment('5');

		$where 		= array(
			'id_kandang'	=> $id
		);

		$wheres 		= array(
			'id_kamar'	=> $id_kamar
		);

		if ($status === 'aktif') {
			$data = array(
				'status'		=> 'tidak aktif',
			);

			$datas = array(
				'status'		=> 'belum',
			);

			$this->m_min->update($tables, $datas, $wheres);
		} else {
			$data = array(
				'status'		=> 'aktif'
			);
		}

		$send = $this->m_min->update($table, $data, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Mengubah data status kandang dengan ID - ' . $this->input->post('id') . '',
		);
		$this->m_min->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backoffice/kandang');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Update Data</center></div></div>");
			redirect('backoffice/kandang');
		}
	}
	// ------------------------------------------------------------------------------------------

	/* ========================================= OPERASIONAL ========================================= */

	public function pembesaran()
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'opr';
		$data['thisPg']		= 'pembesaran';
		// $data['thisView']	= 'grid';

		$datat['record']	= $this->m_min->log_today();

		$data['data']		= $this->m_min->rd_pembesaran();
		$data['kandang']	= $this->m_min->rd_kandang();
		$data['jml_bb']		= $this->m_min->jml_bb();
		$data['jml_br']		= $this->m_min->jml_br();

		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/page/pembesaran');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
	}

	public function in_pembesaran()
	{
		$table 		= 'v_pembesaran';
		// $table 		= 'v_kamar';
		// $tables		= 'mnl_ortu';
		// $tgl_lahir 	= $this->input->post('tgl_lahir');
		// $password 	= preg_replace("/[^0-9]/", "", $tgl_lahir);

		// data untuk kirim ke db pembesaran
		$periode			= $this->input->post('nama_periode');
		// $name			= $this->input->post('nama_pic');
		// $hp				= $this->input->post('hp_pic');
		// $tgl_berdiri 	= $this->input->post('tgl_berdiri');
		// $alamat			= $this->input->post('alamat');
		// $keterangan		= $this->input->post('keterangan');
		// $password 		= substr($hp, -4);

		// data untuk kirim ke db pembesaran
		// $row       	= '1000000'; //$this->m_min->get_last_id($table)->row_array();
		// $deposit   	= $row; //$row['id_mnl_siswa'];

		// get data msg
		// $row14    		= $this->m_min->msg_hmin14()->row_array();
		// $msg_hmin14   	= $row14['msg'];

		// $row7    		= $this->m_min->msg_hmin7()->row_array();
		// $msg_hmin7   	= $row7['msg'];

		// $row3    		= $this->m_min->msg_hmin3()->row_array();
		// $msg_hmin3   	= $row3['msg'];

		// $row23    		= $this->m_min->msg_hplus3()->row_array();
		// $msg_hplus3   	= $row23['msg'];

		// $row27    		= $this->m_min->msg_hplus7()->row_array();
		// $msg_hplus7    	= $row27['msg'];

		if ($this->m_min->cek_pembesaran($periode)->num_rows() == 1) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-warning\" id=\"alert\"><center>Data Pembesaran Sudah Ada</center></div></div>");
			redirect('backoffice/pembesaran');
		} else {
			// $path = './assets/back/images/regist/';

			// $where = array('id_mnl_siswa' => $id);

			// get foto
			// $config['upload_path'] = $path;
			// $config['allowed_types'] = 'jpg|png|jpeg|svg|ico';
			// $config['max_size'] = '2048';  //2MB max
			// $config['max_width'] = '4480'; // pixel
			// $config['max_height'] = '4480'; // pixel
			// $config['file_name'] = $_FILES['fotopost']['name'];

			// $this->upload->initialize($config);

			// if (!empty($_FILES['fotopost']['name'])) {
			// 	if ($this->upload->do_upload('fotopost')) {
			// 		$foto = $this->upload->data();
			// 		$data = array(
			// 			'ktp'      			=> $foto['file_name'],
			// 			'id_kamar'			=> $this->input->post('kamar'),
			// 			'nama'   			=> $name,
			// 			'email'   			=> $this->input->post('email'),
			// 			'hp'   				=> $hp,
			// 			'darurat'   		=> $this->input->post('darurat'),
			// 			'kendaraan'   		=> $this->input->post('kendaraan'),
			// 			'plat'   			=> $this->input->post('plat'),
			// 			'deposit'   		=> $deposit,
			// 			'ket'				=> $this->input->post('ket'),
			// 			'password'  		=> get_hash($password),
			// 			'tgl_masuk'			=> $sch,
			// 			'level'   			=> 'pembesaran',
			// 			'status'   			=> 'aktif',
			// 			'jabatan' 			=> 'Penghuni'
			// 		);

			// 		// hapus foto pada direktori
			// 		// @unlink($path . $this->input->post('filelama'));

			// 		// $this->m_min->update($table, $data, $where);
			// 		$this->m_min->insert($table, $data);

			// 		$tb_log = 'log';
			// 		$log   	= array(
			// 			'id_user' 	=> $this->session->userdata('isId'),
			// 			'aksi'    	=> 'Menambah data pembesaran - ' . $this->input->post('nama') . '',
			// 		);
			// 		$this->m_min->insert($tb_log, $log);

			// 		// update status kamar
			// 		$upd = array(
			// 			'status'   			=> 'terisi'
			// 		);

			// 		$where 		= array(
			// 			'id_kamar'	=> $this->input->post('kamar')
			// 		);

			// 		$this->m_min->update($tables, $upd, $where);

			// 		//kirim data api schedule (notifikasi wa untuk pembayaran)
			// 		lima_tahun($name, $hp, $sch, $msg_hmin14, $msg_hmin7, $msg_hmin3, $msg_hplus3, $msg_hplus7);

			// 		$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Input Data</center></div></div>");
			// 		redirect('backoffice/pembesaran');
			// 	} else {
			// 		$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Input Data</center></div></div>");
			// 		redirect('backoffice/pembesaran');
			// 	}
			// } else {
			$data = array(
				'id_kandang'		=> $this->input->post('kandang'),
				'periode'   		=> $periode,
				'tgl_masuk'   		=> $this->input->post('tgl_masuk'),
				'harga'   			=> $this->input->post('harga_doq'),
				'populasi' 			=> $this->input->post('populasi_awal'),
				'keterangan'   		=> $this->input->post('ket'),
				// 'darurat'   		=> $this->input->post('darurat'),
				// 'kendaraan'   		=> $this->input->post('kendaraan'),
				// 'plat'   			=> $this->input->post('plat'),
				// 'deposit'   		=> $deposit,
				// 'ket'				=> $this->input->post('ket'),
				// 'password'  		=> get_hash($password),
				// 'tgl_masuk'			=> $sch,
				// 'level'   			=> 'pembesaran',
				'status' 	  		=> 'aktif',
				'tanggal'   		=> date('Y-m-d'),
				'waktu' 			=> date('H:i:s')
			);

			$send = $this->m_min->insert($table, $data);

			$tb_log = 'log';
			$log   	= array(
				'id_user' 	=> $this->session->userdata('isId'),
				'aksi'    	=> 'Menambah data pembesaran - ' . $this->input->post('periode') . '',
			);
			$this->m_min->insert($tb_log, $log);

			// update status kamar
			// $upd = array(
			// 	'status'   			=> 'terisi'
			// );

			// $where 		= array(
			// 	'id_kamar'	=> $this->input->post('kamar')
			// );

			// $this->m_min->update($tables, $upd, $where);

			//kirim data api schedule (notifikasi wa untuk pembayaran)
			// lima_tahun($name, $hp, $sch, $msg_hmin14, $msg_hmin7, $msg_hmin3, $msg_hplus3, $msg_hplus7);

			if ($send) {
				$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Input Data</center></div></div>");
				redirect('backoffice/pembesaran');
			} else {
				$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Input Data</center></div></div>");
				redirect('backoffice/pembesaran');
			}
		}
		// }
	}

	public function edit_pembesaran($id)
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'pembesaran';

		$datat['record']	= $this->m_min->log_today();

		$data['data'] 		= $this->m_min->edit_pembesaran($id)->row_array();
		$data['kelas']		= $this->m_min->rd_kelas();
		$data['kamar']		= $this->m_min->rd_kamar_status();
		$data['club']		= $this->m_min->rd_jkamar();

		$data['regist']		= $this->m_min->rd_regist();
		$data['spp']		= $this->m_min->rd_spp();


		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/page/edit_pembesaran');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
	}

	public function up_pembesaran()
	{
		$table 		= 'v_pembesaran';

		$where 		= array(
			'id_pembesaran'	=> $this->input->post('id')
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

		$send = $this->m_min->update($table, $data, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Mengubah data siswa dengan ID - ' . $this->input->post('id') . '',
		);
		$this->m_min->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backoffice/siswa');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Gambar</center></div></div>");
			redirect('backoffice/siswa');
		}
	}

	function hapus_pembesaran($id)
	{
		$table 		= 'mnl_siswa';
		$where 		= array('id_mnl_siswa' => $id);

		$this->m_min->hapus($table, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Menghapus data siswa dengan ID - ' . $id . '',
		);
		$this->m_min->insert($tb_log, $log);

		$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil hapus data</center></div></div>");
		redirect('backoffice/siswa');
	}

	public function status_pembesaran($id)
	{
		$table 		= 'v_pembesaran';
		$tables		= 'v_kamar';

		$status 	= $this->uri->segment('4');
		$id_kamar 	= $this->uri->segment('5');

		$where 		= array(
			'id_pembesaran'	=> $id
		);

		$wheres 		= array(
			'id_kamar'	=> $id_kamar
		);

		if ($status === 'aktif') {
			$data = array(
				'status'		=> 'tidak aktif',
			);

			$datas = array(
				'status'		=> 'belum',
			);

			$this->m_min->update($tables, $datas, $wheres);
		} else {
			$data = array(
				'status'		=> 'aktif'
			);
		}

		$send = $this->m_min->update($table, $data, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Mengubah data status pembesaran dengan ID - ' . $this->input->post('id') . '',
		);
		$this->m_min->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backoffice/pembesaran');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Update Data</center></div></div>");
			redirect('backoffice/pembesaran');
		}
	}
	// ------------------------------------------------------------------------------------------

	public function detail_pembesaran()
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'opr';
		$data['thisPg']		= 'pembesaran';
		// $data['thisView']	= 'grid';

		$datat['record']	= $this->m_min->log_today();

		$data['data']		= $this->m_min->rd_pembesaran();
		$data['kandang']	= $this->m_min->rd_kandang();
		$data['jml_bb']		= $this->m_min->jml_bb();
		$data['jml_br']		= $this->m_min->jml_br();

		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/page/detail_pembesaran');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
	}

	/* ========================================= SAMPAI SINI ========================================= */






























	public function generate()
	{

		$this->m_min->copy_data_penghuni();

		$table 		= 'mnl_tmp_pembayaran';
		$tables		= 'mnl_pembayaran';

		$data = array(
			'bulan'		=> date('m'),
			'tahun'		=> date('Y'),
			'status'	=> 'belum'
		);

		$send = $this->m_min->generate($table, $data);

		$this->m_min->copy_data_pembayaran();
		$this->m_min->truncate_tmp_pembayaran();

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Generate data bulan - ' . date('m') . '',
		);
		$this->m_min->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Generate Data</center></div></div>");
			redirect('backoffice/pengaturan');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Generate Data</center></div></div>");
			redirect('backoffice/pengaturan');
		}
	}

	public function pengaturan()
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'pengaturan';

		$datat['record']	= $this->m_min->log_today();
		$datat['generate']	= $this->m_min->cek_pembayaran();

		$data['hmin14'] 	= $this->m_min->msg_hmin14()->row_array();
		$data['hmin7'] 		= $this->m_min->msg_hmin7()->row_array();
		$data['hmin3'] 		= $this->m_min->msg_hmin3()->row_array();
		$data['hplus3'] 	= $this->m_min->msg_hplus3()->row_array();
		$data['hplus7'] 	= $this->m_min->msg_hplus7()->row_array();

		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/transaksi/pengaturan');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
	}

	public function up_template()
	{
		$table 		= 'kos_msg';

		$where 		= array(
			'template'	=> $this->input->post('template')
		);

		$data = array(
			'msg'   			=> $this->input->post('msg')
		);

		$send = $this->m_min->update($table, $data, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Mengubah data template - ' . $this->input->post('template') . '',
		);
		$this->m_min->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backoffice/pengaturan');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Update Data</center></div></div>");
			redirect('backoffice/pengaturan');
		}
	}

	// ------------------------------------------------------------------------------------------

	// ------------------------------- MASTER - ADMIN -------------------------------

	public function admin()
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'admin';

		$datat['record']	= $this->m_min->log_today();

		$data['data']		= $this->m_min->rd_admin();

		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/transaksi/admin');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
	}

	public function tambah_admin()
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'admin';

		$datat['record']	= $this->m_min->log_today();

		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/transaksi/tambah_admin');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
	}

	public function in_admin()
	{
		$table 		= 'simimin';

		if ($this->m_min->cek_admin($this->input->post('uname'))->num_rows() == 1) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-warning\" id=\"alert\"><center>Username sudah ada</center></div></div>");
			redirect('backoffice/admin');
		} else {
			$data = array(
				'nama'			=> $this->input->post('nama'),
				'username'		=> $this->input->post('uname'),
				'password'  	=> get_hash($this->input->post('pass')),
				'hp'			=> $this->input->post('no_hp'),
				'level'   		=> 'operator',
				'jabatan' 		=> 'Admin'
			);

			$send = $this->m_min->insert($table, $data);
		}

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Menambah data admin - ' . $this->input->post('nama') . '',
		);
		$this->m_min->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backoffice/admin');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Gambar</center></div></div>");
			redirect('backoffice/admin');
		}
	}

	public function edit_admin($id)
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'admin';

		$datat['record']	= $this->m_min->log_today();

		$data['data'] 		= $this->m_min->edit_admin($id)->row_array();

		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/transaksi/edit_admin');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
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

		$send = $this->m_min->update($table, $data, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Mengubah data admin dengan ID - ' . $this->input->post('id') . '',
		);
		$this->m_min->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backoffice/admin');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Gambar</center></div></div>");
			redirect('backoffice/admin');
		}
	}

	function hapus_admin($id)
	{
		$table 		= 'simimin';
		$where 		= array('minid' => $id);

		$data = array(
			'hapus'   			=> 1
		);

		$this->m_min->update($table, $data, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Menghapus data admin dengan ID - ' . $id . '',
		);
		$this->m_min->insert($tb_log, $log);

		$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil hapus data</center></div></div>");
		redirect('backoffice/admin');
	}

	// ------------------------------------------------------------------------------------------

	// ------------------------------- MASTER - KELAS -------------------------------

	public function kelas()
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'kelas';

		$datat['record']	= $this->m_min->log_today();

		$data['kelas']		= $this->m_min->rd_kelas();

		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/transaksi/kelas');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
	}

	public function tambah_kelas()
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'kelas';

		$datat['record']	= $this->m_min->log_today();

		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/transaksi/tambah_kelas');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
	}

	public function in_kelas()
	{
		$table 	= 'mnl_kelas';

		$data = array(
			'kelas'   => $this->input->post('nama')
		);

		$send = $this->m_min->insert($table, $data);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Menambah data kelas - ' . $this->input->post('nama') . '',
		);
		$this->m_min->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backoffice/kelas');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Gambar</center></div></div>");
			redirect('backoffice/kelas');
		}
	}

	public function edit_kelas($id)
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'settings';
		$data['thisPg']		= 'kelas';

		$datat['record']	= $this->m_min->log_today();

		$data['data'] 		= $this->m_min->edit_kelas($id)->row_array();

		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/transaksi/edit_kelas');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
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

		$send = $this->m_min->update($table, $data, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Mengubah data kelas dengan ID - ' . $this->input->post('id') . '',
		);
		$this->m_min->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backoffice/kelas');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Gambar</center></div></div>");
			redirect('backoffice/kelas');
		}
	}

	function hapus_kelas($id)
	{
		$table 		= 'mnl_kelas';
		$where 		= array('id_mnl_kelas' => $id);

		$this->m_min->hapus($table, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Menghapus data kelas dengan ID - ' . $id . '',
		);
		$this->m_min->insert($tb_log, $log);

		$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil hapus data</center></div></div>");
		redirect('backoffice/kelas');
	}

	// ------------------------------------------------------------------------------------------

	// ------------------------------- MASTER - KAMAR -------------------------------

	public function kamar()
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'kamar';

		$datat['record']	= $this->m_min->log_today();

		$data['kamar']		= $this->m_min->rd_kamar();

		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/transaksi/kamar');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
	}

	public function tambah_kamar()
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'kamar';

		$datat['record']	= $this->m_min->log_today();

		$data['jkamar']		= $this->m_min->rd_jkamar();

		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/transaksi/tambah_kamar');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
	}

	public function in_kamar()
	{
		$table 	= 'kos_kamar';

		$data = array(
			'id_jenis_kamar'   	=> $this->input->post('jkamar'),
			'nomor'   			=> strtoupper($this->input->post('nomor')),
			'status'   			=> 'belum'
		);

		$send = $this->m_min->insert($table, $data);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Menambah data kamar - ' . $this->input->post('nama') . '',
		);
		$this->m_min->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backoffice/kamar');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Update Data</center></div></div>");
			redirect('backoffice/kamar');
		}
	}

	public function edit_kamar($id)
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'settings';
		$data['thisPg']		= 'kamar';

		$datat['record']	= $this->m_min->log_today();

		$data['data'] 		= $this->m_min->edit_kamar($id)->row_array();
		$data['jkamar']		= $this->m_min->rd_jkamar();

		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/transaksi/edit_kamar');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
	}

	public function up_kamar()
	{
		$table 		= 'kos_kamar';

		$where 		= array(
			'id_kamar'	=> $this->input->post('id')
		);

		$data = array(
			'id_jenis_kamar'   	=> $this->input->post('jkamar'),
			'nomor'   			=> strtoupper($this->input->post('nomor'))
		);

		$send = $this->m_min->update($table, $data, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Mengubah data kamar dengan ID - ' . $this->input->post('id') . '',
		);
		$this->m_min->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backoffice/kamar');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Update Data</center></div></div>");
			redirect('backoffice/kamar');
		}
	}

	function hapus_kamar($id)
	{
		$table 		= 'kos_kamar';
		$where 		= array('id_kamar' => $id);

		$this->m_min->hapus($table, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Menghapus data kamar dengan ID - ' . $id . '',
		);
		$this->m_min->insert($tb_log, $log);

		$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Hapus Data</center></div></div>");
		redirect('backoffice/kamar');
	}

	public function status_kamar($id)
	{
		$table 		= 'kos_kamar';

		$status 	= $this->uri->segment('4');

		$where 		= array(
			'id_kamar'	=> $id
		);

		if ($status === 'terisi') {
			$data = array(
				'status'		=> 'belum',
			);
		} else {
			$data = array(
				'status'		=> 'terisi'
			);
		}

		$send = $this->m_min->update($table, $data, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Mengubah data status kamar dengan ID - ' . $this->input->post('id') . '',
		);
		$this->m_min->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backoffice/kamar');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Update Data</center></div></div>");
			redirect('backoffice/kamar');
		}
	}

	// ------------------------------------------------------------------------------------------

	// ------------------------------- MASTER - JENIS KAMAR -------------------------------

	public function jkamar()
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'jenis kamar';

		$datat['record']	= $this->m_min->log_today();

		$data['jkamar']		= $this->m_min->rd_jkamar();

		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/transaksi/jkamar');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
	}

	public function tambah_jkamar()
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'jenis kamar';

		$datat['record']	= $this->m_min->log_today();

		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/transaksi/tambah_jkamar');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
	}

	public function in_jkamar()
	{
		$table 	= 'kos_jenis_kamar';

		$data = array(
			'jenis'   => $this->input->post('nama'),
			'harga'   => str_replace(".", "", $this->input->post('harga')),
			'tahun'   => $this->input->post('tahun')
		);

		$send = $this->m_min->insert($table, $data);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Menambah data jenis kamar - ' . $this->input->post('nama') . '',
		);
		$this->m_min->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backoffice/jkamar');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Gambar</center></div></div>");
			redirect('backoffice/jkamar');
		}
	}

	public function edit_jkamar($id)
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'settings';
		$data['thisPg']		= 'jenis kamar';

		$datat['record']	= $this->m_min->log_today();

		$data['data'] 		= $this->m_min->edit_jkamar($id)->row_array();

		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/transaksi/edit_jkamar');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
	}

	public function up_jkamar()
	{
		$table 		= 'kos_jenis_kamar';

		$where 		= array(
			'id_jenis_kamar'	=> $this->input->post('id')
		);

		$data = array(
			'jenis'		=> $this->input->post('nama'),
			'harga'   	=> str_replace(".", "", $this->input->post('harga')),
			'tahun'   	=> $this->input->post('tahun')
		);

		$send = $this->m_min->update($table, $data, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Mengubah data jenis kamar dengan ID - ' . $this->input->post('id') . '',
		);
		$this->m_min->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backoffice/jkamar');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Gambar</center></div></div>");
			redirect('backoffice/jkamar');
		}
	}

	function hapus_jkamar($id)
	{
		$table 		= 'kos_jenis_kamar';
		$where 		= array('id_jenis_kamar' => $id);

		$this->m_min->hapus($table, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Menghapus data jenis kamar dengan ID - ' . $id . '',
		);
		$this->m_min->insert($tb_log, $log);

		$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil hapus data</center></div></div>");
		redirect('backoffice/jkamar');
	}

	// ------------------------------------------------------------------------------------------

	// ------------------------------- MASTER - SPP -------------------------------

	public function spp()
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'spp';

		$datat['record']	= $this->m_min->log_today();

		$data['spp']		= $this->m_min->rd_spp();

		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/transaksi/spp');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
	}

	public function tambah_spp()
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'spp';

		$datat['record']	= $this->m_min->log_today();

		$data['kelas']		= $this->m_min->rd_kelas();
		$data['lokasi']		= $this->m_min->rd_kamar();
		$data['club']		= $this->m_min->rd_jkamar();

		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/transaksi/tambah_spp');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
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

		$send = $this->m_min->insert($table, $data);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Menambah data SPP - ' . $this->input->post('nama') . '',
		);
		$this->m_min->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backoffice/spp');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Gambar</center></div></div>");
			redirect('backoffice/spp');
		}
	}

	public function edit_spp($id)
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'settings';
		$data['thisPg']		= 'spp';

		$datat['record']	= $this->m_min->log_today();

		$data['data'] 		= $this->m_min->edit_spp($id)->row_array();

		$data['kelas']		= $this->m_min->rd_kelas();
		$data['lokasi']		= $this->m_min->rd_kamar();
		$data['club']		= $this->m_min->rd_jkamar();

		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/transaksi/edit_spp');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
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

		$send = $this->m_min->update($table, $data, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Mengubah data SPP dengan ID - ' . $this->input->post('id') . '',
		);
		$this->m_min->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backoffice/spp');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Gambar</center></div></div>");
			redirect('backoffice/spp');
		}
	}

	function hapus_spp($id)
	{
		$table 		= 'mnl_spp';
		$where 		= array('id_mnl_spp' => $id);

		$this->m_min->hapus($table, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Menghapus data SPP dengan ID - ' . $id . '',
		);
		$this->m_min->insert($tb_log, $log);

		$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil hapus data</center></div></div>");
		redirect('backoffice/spp');
	}

	// ------------------------------------------------------------------------------------------

	// ------------------------------- MASTER - SISWA -------------------------------

	public function penghuni()
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'penghuni';

		$datat['record']	= $this->m_min->log_today();

		$data['data']		= $this->m_min->rd_penghuni();

		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/transaksi/penghuni');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
	}

	public function tambah_penghuni()
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'penghuni';

		$datat['record']	= $this->m_min->log_today();

		// $data['kelas']		= $this->m_min->rd_kelas();
		$data['kamar']		= $this->m_min->rd_kamar_status();
		$data['club']		= $this->m_min->rd_jkamar();

		// $data['regist']		= $this->m_min->rd_regist();
		// $data['spp']		= $this->m_min->rd_spp();

		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/transaksi/tambah_penghuni');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
	}

	public function in_penghuni()
	{
		$table 		= 'kos_penghuni';
		$table 		= 'kos_kamar';
		// $tables		= 'mnl_ortu';
		// $tgl_lahir 	= $this->input->post('tgl_lahir');
		// $password 	= preg_replace("/[^0-9]/", "", $tgl_lahir);

		// data untuk kirim ke db penghuni
		$name		= $this->input->post('nama');
		$hp			= $this->input->post('hp');
		$sch		= $this->input->post('tgl_masuk') . ' 08:00:00';
		$password 	= substr($hp, -4);

		// data untuk kirim ke db penghuni
		$row       	= '1000000'; //$this->m_min->get_last_id($table)->row_array();
		$deposit   	= $row; //$row['id_mnl_siswa'];

		// get data msg
		$row14    		= $this->m_min->msg_hmin14()->row_array();
		$msg_hmin14   	= $row14['msg'];

		$row7    		= $this->m_min->msg_hmin7()->row_array();
		$msg_hmin7   	= $row7['msg'];

		$row3    		= $this->m_min->msg_hmin3()->row_array();
		$msg_hmin3   	= $row3['msg'];

		$row23    		= $this->m_min->msg_hplus3()->row_array();
		$msg_hplus3   	= $row23['msg'];

		$row27    		= $this->m_min->msg_hplus7()->row_array();
		$msg_hplus7    	= $row27['msg'];

		if ($this->m_min->cek_penghuni($hp)->num_rows() == 1) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-warning\" id=\"alert\"><center>Data Penghuni Sudah Ada</center></div></div>");
			redirect('backoffice/penghuni');
		} else {
			$path = './assets/back/images/regist/';

			// $where = array('id_mnl_siswa' => $id);

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
						'ktp'      			=> $foto['file_name'],
						'id_kamar'			=> $this->input->post('kamar'),
						'nama'   			=> $name,
						'email'   			=> $this->input->post('email'),
						'hp'   				=> $hp,
						'darurat'   		=> $this->input->post('darurat'),
						'kendaraan'   		=> $this->input->post('kendaraan'),
						'plat'   			=> $this->input->post('plat'),
						'deposit'   		=> $deposit,
						'ket'				=> $this->input->post('ket'),
						'password'  		=> get_hash($password),
						'tgl_masuk'			=> $sch,
						'level'   			=> 'penghuni',
						'status'   			=> 'aktif',
						'jabatan' 			=> 'Penghuni'
					);

					// hapus foto pada direktori
					// @unlink($path . $this->input->post('filelama'));

					// $this->m_min->update($table, $data, $where);
					$this->m_min->insert($table, $data);

					$tb_log = 'log';
					$log   	= array(
						'id_user' 	=> $this->session->userdata('isId'),
						'aksi'    	=> 'Menambah data penghuni - ' . $this->input->post('nama') . '',
					);
					$this->m_min->insert($tb_log, $log);

					// update status kamar
					$upd = array(
						'status'   			=> 'terisi'
					);

					$where 		= array(
						'id_kamar'	=> $this->input->post('kamar')
					);

					$this->m_min->update($tables, $upd, $where);

					//kirim data api schedule (notifikasi wa untuk pembayaran)
					lima_tahun($name, $hp, $sch, $msg_hmin14, $msg_hmin7, $msg_hmin3, $msg_hplus3, $msg_hplus7);

					$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Input Data</center></div></div>");
					redirect('backoffice/penghuni');
				} else {
					$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Input Data</center></div></div>");
					redirect('backoffice/penghuni');
				}
			} else {
				$data = array(
					'id_kamar'			=> $this->input->post('kamar'),
					'nama'   			=> $name,
					'email'   			=> $this->input->post('email'),
					'hp'   				=> $hp,
					'ktp'   			=> 'ktp.jpg',
					'darurat'   		=> $this->input->post('darurat'),
					'kendaraan'   		=> $this->input->post('kendaraan'),
					'plat'   			=> $this->input->post('plat'),
					'deposit'   		=> $deposit,
					'ket'				=> $this->input->post('ket'),
					'password'  		=> get_hash($password),
					'tgl_masuk'			=> $sch,
					'level'   			=> 'penghuni',
					'status'   			=> 'aktif',
					'jabatan' 			=> 'Penghuni'
				);

				$send = $this->m_min->insert($table, $data);

				$tb_log = 'log';
				$log   	= array(
					'id_user' 	=> $this->session->userdata('isId'),
					'aksi'    	=> 'Menambah data penghuni - ' . $this->input->post('nama') . '',
				);
				$this->m_min->insert($tb_log, $log);

				// update status kamar
				$upd = array(
					'status'   			=> 'terisi'
				);

				$where 		= array(
					'id_kamar'	=> $this->input->post('kamar')
				);

				$this->m_min->update($tables, $upd, $where);

				//kirim data api schedule (notifikasi wa untuk pembayaran)
				lima_tahun($name, $hp, $sch, $msg_hmin14, $msg_hmin7, $msg_hmin3, $msg_hplus3, $msg_hplus7);

				if ($send) {
					$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Input Data</center></div></div>");
					redirect('backoffice/penghuni');
				} else {
					$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Input Data</center></div></div>");
					redirect('backoffice/penghuni');
				}
			}
		}
	}

	public function edit_penghuni($id)
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'penghuni';

		$datat['record']	= $this->m_min->log_today();

		$data['data'] 		= $this->m_min->edit_penghuni($id)->row_array();
		$data['kelas']		= $this->m_min->rd_kelas();
		$data['kamar']		= $this->m_min->rd_kamar_status();
		$data['club']		= $this->m_min->rd_jkamar();

		$data['regist']		= $this->m_min->rd_regist();
		$data['spp']		= $this->m_min->rd_spp();


		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/transaksi/edit_penghuni');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
	}

	public function up_penghuni()
	{
		$table 		= 'kos_penghuni';

		$where 		= array(
			'id_penghuni'	=> $this->input->post('id')
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

		$send = $this->m_min->update($table, $data, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Mengubah data siswa dengan ID - ' . $this->input->post('id') . '',
		);
		$this->m_min->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backoffice/siswa');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Gambar</center></div></div>");
			redirect('backoffice/siswa');
		}
	}

	function hapus_siswa($id)
	{
		$table 		= 'mnl_siswa';
		$where 		= array('id_mnl_siswa' => $id);

		$this->m_min->hapus($table, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Menghapus data siswa dengan ID - ' . $id . '',
		);
		$this->m_min->insert($tb_log, $log);

		$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil hapus data</center></div></div>");
		redirect('backoffice/siswa');
	}

	public function status_penghuni($id)
	{
		$table 		= 'kos_penghuni';
		$tables		= 'kos_kamar';

		$status 	= $this->uri->segment('4');
		$id_kamar 	= $this->uri->segment('5');

		$where 		= array(
			'id_penghuni'	=> $id
		);

		$wheres 		= array(
			'id_kamar'	=> $id_kamar
		);

		if ($status === 'aktif') {
			$data = array(
				'status'		=> 'tidak aktif',
			);

			$datas = array(
				'status'		=> 'belum',
			);

			$this->m_min->update($tables, $datas, $wheres);
		} else {
			$data = array(
				'status'		=> 'aktif'
			);
		}

		$send = $this->m_min->update($table, $data, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Mengubah data status penghuni dengan ID - ' . $this->input->post('id') . '',
		);
		$this->m_min->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backoffice/penghuni');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Update Data</center></div></div>");
			redirect('backoffice/penghuni');
		}
	}

	// ------------------------------------------------------------------------------------------

	// ------------------------------- MASTER - BIAYA REGISTRASI -------------------------------

	public function regist()
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'biaya registrasi';

		$datat['record']	= $this->m_min->log_today();

		$data['data']		= $this->m_min->rd_regist();

		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/transaksi/regist');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
	}

	public function tambah_regist()
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'biaya registrasi';

		$datat['record']	= $this->m_min->log_today();

		$data['kelas']		= $this->m_min->rd_kelas();
		$data['lokasi']		= $this->m_min->rd_kamar();
		$data['club']		= $this->m_min->rd_jkamar();

		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/transaksi/tambah_regist');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
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

		$send = $this->m_min->insert($table, $data);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Menambah data biaya registrasi - ' . $this->input->post('nama') . '',
		);
		$this->m_min->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backoffice/regist');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Gambar</center></div></div>");
			redirect('backoffice/regist');
		}
	}

	public function edit_regist($id)
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'master';
		$data['thisPg']		= 'biaya registrasi';

		$datat['record']	= $this->m_min->log_today();

		$data['data'] 		= $this->m_min->edit_regist($id)->row_array();
		$data['kelas']		= $this->m_min->rd_kelas();
		$data['lokasi']		= $this->m_min->rd_kamar();
		$data['club']		= $this->m_min->rd_jkamar();

		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/transaksi/edit_regist');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
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

		$send = $this->m_min->update($table, $data, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Mengubah data biaya registrasi dengan ID - ' . $this->input->post('id') . '',
		);
		$this->m_min->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backoffice/regist');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Gambar</center></div></div>");
			redirect('backoffice/regist');
		}
	}

	function hapus_regist($id)
	{
		$table 		= 'mnl_regist';
		$where 		= array('id_mnl_regist' => $id);

		$this->m_min->hapus($table, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Menghapus data biaya registrasi dengan ID - ' . $id . '',
		);
		$this->m_min->insert($tb_log, $log);

		$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil hapus data</center></div></div>");
		redirect('backoffice/regist');
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

		$send = $this->m_min->update($table, $data, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Mengubah data registrasi siswa dengan ID - ' . $this->input->post('id') . '',
		);
		$this->m_min->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Update Data</center></div></div>");
			redirect('backoffice/siswa');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Gambar</center></div></div>");
			redirect('backoffice/siswa');
		}
	}


	// ------------------------------------------------------------------------------------------

	// ------------------------------- REGISTRASI - SEMUA -------------------------------

	public function pembayaran_regist($id)
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'registrasi';

		$datat['record']	= $this->m_min->log_today();


		if ($id == 'all') {
			$data['thisPg']		= 'semua';
			$data['data']		= $this->m_min->rd_pembayaran_regist_all();
		} else if ($id == 'ba') {
			$data['thisPg']		= 'butuh approval';
			$data['data']		= $this->m_min->rd_pembayaran_regist_ba();
		} else if ($id == 'bb') {
			$data['thisPg']		= 'belum bayar';
			$data['data']		= $this->m_min->rd_pembayaran_regist_bb();
		} else if ($id == 'sb') {
			$data['thisPg']		= 'sudah bayar';
			$data['data']		= $this->m_min->rd_pembayaran_regist_sb();
		}

		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/transaksi/pembayaran_regist');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
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

		$send = $this->m_min->update($table, $data, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Mengganti status pembayaran pada siswa dengan ID - ' . $this->input->post('id') . '',
		);
		$this->m_min->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Ganti Status</center></div></div>");
			redirect('backoffice/pembayaran_regist/ba');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Ganti Status</center></div></div>");
			redirect('backoffice/pembayaran_regist/ba');
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

				$this->m_min->insert($table, $data);
				$this->m_min->update($tables, $datas, $where);

				$tb_log = 'log';
				$log   	= array(
					'id_user' 	=> $this->session->userdata('isId'),
					'aksi'    	=> 'Mengupload bukti registrasi siswa dengan ID - ' . $id . '',
				);
				$this->m_min->insert($tb_log, $log);

				$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Upload Bukti Transfer</center></div></div>");
				redirect('backoffice/pembayaran_regist/bb');
			} else {
				$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Bukti Transfer</center></div></div>");
				redirect('backoffice/pembayaran_regist/bb');
			}
		}
	}

	// ------------------------------------------------------------------------------------------

	// ------------------------------- PEMBAYARAN - SEMUA -------------------------------

	public function pembayaran($id)
	{
		$data['profil'] 	= $this->m_min->profil($this->session->userdata('isUname'))->row_array();
		$data['thisPage']	= 'pembayaran';

		$datat['record']	= $this->m_min->log_today();


		if ($id == 'all') {
			$data['thisPg']		= 'semua';
			$data['data']		= $this->m_min->rd_pembayaran_all();
		} else if ($id == 'ba') {
			$data['thisPg']		= 'butuh approval';
			$data['data']		= $this->m_min->rd_pembayaran_ba();
		} else if ($id == 'bb') {
			$data['thisPg']		= 'belum bayar';
			$data['data']		= $this->m_min->rd_pembayaran_bb();
		} else if ($id == 'sb') {
			$data['thisPg']		= 'sudah bayar';
			$data['data']		= $this->m_min->rd_pembayaran_sb();
		}

		$this->template->kepala('tbase/kepala', 'mimin/template/header', $datat);
		$this->template->samping('tbase/samping', 'mimin/template/sidebar', $data);
		$this->template->jidat('tbase/jidat', 'mimin/template/top');
		$this->template->isi('tbase/isi', 'mimin/transaksi/pembayaran');
		$this->template->kaki('tbase/kaki', 'mimin/template/footer');
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

				$this->m_min->update($table, $data, $where);

				$tb_log = 'log';
				$log   	= array(
					'id_user' 	=> $this->session->userdata('isId'),
					'aksi'    	=> 'Mengupload bukti transfer siswa dengan ID - ' . $id . '',
				);
				$this->m_min->insert($tb_log, $log);

				$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Upload Bukti Transfer</center></div></div>");
				redirect('backoffice/pembayaran/bb');
			} else {
				$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Upload Bukti Transfer</center></div></div>");
				redirect('backoffice/pembayaran/bb');
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

		$send = $this->m_min->update($table, $data, $where);

		$tb_log = 'log';
		$log   	= array(
			'id_user' 	=> $this->session->userdata('isId'),
			'aksi'    	=> 'Mengganti status pembayaran pada siswa dengan ID - ' . $this->input->post('id') . '',
		);
		$this->m_min->insert($tb_log, $log);

		if ($send) {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Berhasil Ganti Status</center></div></div>");
			redirect('backoffice/pembayaran/ba');
		} else {
			$this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal Ganti Status</center></div></div>");
			redirect('backoffice/pembayaran/ba');
		}
	}

	// ------------------------------------------------------------------------------------------
}
