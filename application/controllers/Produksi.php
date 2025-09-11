<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produksi extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_produksi');
        $this->load->model('M_kandang');
        $this->load->model('m_min');
        $this->load->library('session');
        $this->load->helper('url');
        
        // Authentication check - uncomment untuk enable
        if ($this->session->userdata('isLog') != TRUE) {
            redirect('mimin');
        }
        if ($this->session->userdata('isUname') == "") {
            redirect('mimin');
        }
        
        date_default_timezone_set("Asia/Jakarta");
    }

    public function index()
    {
        $data['title'] = 'Data Produksi';
        $data['produksi'] = $this->M_produksi->get_all_produksi();
        $data['kandang'] = $this->M_kandang->get_all();
        $data['profil'] = $this->m_min->profil($this->session->userdata('isUname'))->row_array();
        
        // Set sidebar variables seperti controller lain
        $data['thisPage'] = 'opr';
        $data['thisPg'] = 'produksi';
        
        // Get statistics data untuk dashboard
        $data['jml_telur_hari_ini'] = $this->M_produksi->get_telur_hari_ini();
        $data['total_berat'] = $this->M_produksi->get_total_berat();
        $data['total_produksi'] = $this->M_produksi->get_total_produksi();
        $data['kandang_aktif'] = $this->M_kandang->get_kandang_aktif();
        
        // Load view dengan template yang sama seperti menu lain
        $this->load->view('admin/template/header', $data);
        $this->load->view('admin/template/top', $data);
        $this->load->view('admin/template/sidebar', $data);
        $this->load->view('admin/produksi/index_produksi', $data);
        $this->load->view('admin/template/footer');
    }

    public function tambah()
    {
        $tanggal = $this->input->post('tanggal');
        $id_kandang = $this->input->post('id_kandang');
        $batch_penetasan = $this->input->post('batch_penetasan');
        $batch_pembesaran = $this->input->post('batch_pembesaran');
        $jenis_produksi = $this->input->post('jenis_produksi');
        $jumlah = $this->input->post('jumlah');
        $berat = $this->input->post('berat') ?: 0;
        $harga_satuan = $this->input->post('harga_satuan') ?: 0;
        $kualitas = $this->input->post('kualitas') ?: 'A';
        $status = $this->input->post('status') ?: 'persiapan';
        $catatan = $this->input->post('catatan');

        $total_nilai = $jumlah * $harga_satuan;

        $data = array(
            'tanggal' => $tanggal,
            'id_kandang' => $id_kandang,
            'batch_penetasan' => $batch_penetasan,
            'batch_pembesaran' => $batch_pembesaran,
            'jenis_produksi' => $jenis_produksi,
            'jumlah' => $jumlah,
            'berat' => $berat,
            'harga_satuan' => $harga_satuan,
            'total_nilai' => $total_nilai,
            'kualitas' => $kualitas,
            'status' => $status,
            'catatan' => $catatan,
            'created_at' => date('Y-m-d H:i:s')
        );

        $insert = $this->M_produksi->insert($data);

        if ($insert) {
            $this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Data produksi berhasil ditambahkan</center></div></div>");
        } else {
            $this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal menambahkan data produksi</center></div></div>");
        }

        redirect('produksi');
    }

    public function proses_tambah()
    {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('id_kandang', 'Kandang', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        $this->form_validation->set_rules('jml_telur', 'Jumlah Telur', 'required|numeric');
        $this->form_validation->set_rules('berat_telur', 'Berat Telur', 'required|numeric');
        
        if ($this->form_validation->run() == FALSE) {
            $this->tambah();
        } else {
            $data = array(
                'id_kandang' => $this->input->post('id_kandang'),
                'tanggal' => $this->input->post('tanggal'),
                'jenis_produksi' => 'telur',
                'jumlah' => $this->input->post('jml_telur'), // Map to correct field
                'berat' => $this->input->post('berat_telur'), // Map to correct field
                'catatan' => $this->input->post('keterangan'), // Map to correct field
                'status' => 'aktif',
                'created_at' => date('Y-m-d H:i:s')
            );
            
            $result = $this->M_produksi->insert($data);
            
            if ($result) {
                $this->session->set_flashdata('success', 'Data produksi berhasil ditambahkan');
            } else {
                $this->session->set_flashdata('error', 'Gagal menambahkan data produksi');
            }
            
            redirect('produksi');
        }
    }

    public function detail($id)
    {
        $data['title'] = 'Detail Produksi';
        $data['produksi'] = $this->M_produksi->get_by_id($id);
        
        if (!$data['produksi']) {
            show_404();
        }
        
        $this->load->view('admin/template/header', $data);
        $this->load->view('admin/template/sidebar');
        $this->load->view('admin/produksi/detail_produksi', $data);
        $this->load->view('admin/template/footer');
    }

    public function edit($id)
    {
        $data['title'] = 'Edit Data Produksi';
        $data['produksi'] = $this->M_produksi->get_by_id($id);
        $data['kandang'] = $this->M_kandang->get_all();
        
        if (!$data['produksi']) {
            show_404();
        }
        
        $this->load->view('admin/template/header', $data);
        $this->load->view('admin/template/sidebar');
        $this->load->view('admin/produksi/edit_produksi', $data);
        $this->load->view('admin/template/footer');
    }

    public function proses_edit()
    {
        $this->load->library('form_validation');
        
        $id = $this->input->post('id');
        $this->form_validation->set_rules('id_kandang', 'Kandang', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        $this->form_validation->set_rules('jml_telur', 'Jumlah Telur', 'required|numeric');
        $this->form_validation->set_rules('berat_telur', 'Berat Telur', 'required|numeric');
        
        if ($this->form_validation->run() == FALSE) {
            $this->edit($id);
        } else {
            $data = array(
                'id_kandang' => $this->input->post('id_kandang'),
                'tanggal' => $this->input->post('tanggal'),
                'jml_telur' => $this->input->post('jml_telur'),
                'berat_telur' => $this->input->post('berat_telur'),
                'keterangan' => $this->input->post('keterangan')
            );
            
            $result = $this->M_produksi->update($id, $data);
            
            if ($result) {
                $this->session->set_flashdata('success', 'Data produksi berhasil diperbarui');
            } else {
                $this->session->set_flashdata('error', 'Gagal memperbarui data produksi');
            }
            
            redirect('produksi');
        }
    }

    public function hapus($id)
    {
        $result = $this->M_produksi->delete($id);
        
        if ($result) {
            $this->session->set_flashdata('success', 'Data produksi berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data produksi');
        }
        
        redirect('produksi');
    }

    public function export()
    {
        $this->load->library('PHPExcel');
        
        $produksi = $this->M_produksi->get_all_produksi();
        
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();
        
        // Set headers
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kandang');
        $sheet->setCellValue('C1', 'Tanggal');
        $sheet->setCellValue('D1', 'Jumlah Telur');
        $sheet->setCellValue('E1', 'Berat Telur');
        $sheet->setCellValue('F1', 'Keterangan');
        
        // Add data
        $row = 2;
        $no = 1;
        foreach ($produksi as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item->nama_kandang);
            $sheet->setCellValue('C' . $row, date('d/m/Y', strtotime($item->tanggal)));
            $sheet->setCellValue('D' . $row, $item->jml_telur);
            $sheet->setCellValue('E' . $row, $item->berat_telur . ' kg');
            $sheet->setCellValue('F' . $row, $item->keterangan);
            $row++;
        }
        
        $filename = 'Data_Produksi_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

    public function get_filtered_data()
    {
        $draw = $this->input->post('draw');
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $search = $this->input->post('search')['value'];
        $kandang_filter = $this->input->post('kandang_filter');
        $tanggal_dari = $this->input->post('tanggal_dari');
        $tanggal_sampai = $this->input->post('tanggal_sampai');
        
        $data = $this->M_produksi->get_produksi_filtered($start, $length, $search, $kandang_filter, $tanggal_dari, $tanggal_sampai);
        $total = $this->M_produksi->count_all_produksi();
        $filtered = $this->M_produksi->count_filtered_produksi($search, $kandang_filter, $tanggal_dari, $tanggal_sampai);
        
        $result = array(
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data
        );
        
        echo json_encode($result);
    }

    /**
     * AJAX - Detail produksi
     */
    public function detail_ajax()
    {
        $id = $this->input->post('id');
        $produksi = $this->M_produksi->get_by_id_with_kandang($id);
        
        if ($produksi) {
            echo '<div class="row">';
            echo '<div class="col-md-6">';
            echo '<table class="table table-borderless">';
            echo '<tr><td width="30%"><strong>Tanggal</strong></td><td>: ' . date('d-m-Y', strtotime($produksi->tanggal)) . '</td></tr>';
            echo '<tr><td><strong>Kandang</strong></td><td>: ' . ($produksi->nama_kandang ?? 'N/A') . '</td></tr>';
            echo '<tr><td><strong>Batch Penetasan</strong></td><td>: ' . ($produksi->batch_penetasan ?? '-') . '</td></tr>';
            echo '<tr><td><strong>Batch Pembesaran</strong></td><td>: ' . ($produksi->batch_pembesaran ?? '-') . '</td></tr>';
            echo '<tr><td><strong>Jenis Produksi</strong></td><td>: ' . ucfirst($produksi->jenis_produksi) . '</td></tr>';
            echo '</table>';
            echo '</div>';
            echo '<div class="col-md-6">';
            echo '<table class="table table-borderless">';
            echo '<tr><td width="30%"><strong>Jumlah</strong></td><td>: ' . number_format($produksi->jumlah) . '</td></tr>';
            echo '<tr><td><strong>Berat</strong></td><td>: ' . number_format($produksi->berat, 2) . ' kg</td></tr>';
            echo '<tr><td><strong>Harga Satuan</strong></td><td>: Rp ' . number_format($produksi->harga_satuan, 0, ',', '.') . '</td></tr>';
            echo '<tr><td><strong>Total Nilai</strong></td><td>: Rp ' . number_format($produksi->total_nilai, 0, ',', '.') . '</td></tr>';
            echo '<tr><td><strong>Kualitas</strong></td><td>: Grade ' . $produksi->kualitas . '</td></tr>';
            echo '<tr><td><strong>Status</strong></td><td>: ' . ucfirst($produksi->status) . '</td></tr>';
            echo '</table>';
            echo '</div>';
            echo '</div>';
            if ($produksi->catatan) {
                echo '<div class="row"><div class="col-12"><strong>Catatan:</strong><br>' . $produksi->catatan . '</div></div>';
            }
        } else {
            echo '<div class="alert alert-warning">Data produksi tidak ditemukan</div>';
        }
    }

    /**
     * AJAX - Form edit produksi
     */
    public function form_edit()
    {
        $id = $this->input->post('id');
        $produksi = $this->M_produksi->get_by_id($id);
        $kandang = $this->M_kandang->get_all();
        
        if ($produksi) {
            echo '<input type="hidden" name="id_produksi" value="' . $produksi->id_produksi . '">';
            echo '<div class="row">';
            echo '<div class="col-md-6">';
            echo '<div class="form-group">';
            echo '<label>Tanggal <span class="text-danger">*</span></label>';
            echo '<input type="date" class="form-control" name="tanggal" value="' . $produksi->tanggal . '" required>';
            echo '</div>';
            echo '<div class="form-group">';
            echo '<label>Kandang <span class="text-danger">*</span></label>';
            echo '<select class="form-control" name="id_kandang" required>';
            echo '<option value="">Pilih Kandang</option>';
            if (!empty($kandang)) {
                foreach ($kandang as $k) {
                    $selected = ($k->id_kandang == $produksi->id_kandang) ? 'selected' : '';
                    echo '<option value="' . $k->id_kandang . '" ' . $selected . '>' . $k->nama . ' - ' . $k->tipe . '</option>';
                }
            }
            echo '</select>';
            echo '</div>';
            echo '<div class="form-group">';
            echo '<label>Batch Penetasan</label>';
            echo '<input type="text" class="form-control" name="batch_penetasan" value="' . $produksi->batch_penetasan . '">';
            echo '</div>';
            echo '<div class="form-group">';
            echo '<label>Batch Pembesaran</label>';
            echo '<input type="text" class="form-control" name="batch_pembesaran" value="' . $produksi->batch_pembesaran . '">';
            echo '</div>';
            echo '<div class="form-group">';
            echo '<label>Jenis Produksi <span class="text-danger">*</span></label>';
            echo '<select class="form-control" name="jenis_produksi" required>';
            echo '<option value="telur"' . ($produksi->jenis_produksi == 'telur' ? ' selected' : '') . '>Telur</option>';
            echo '<option value="daging"' . ($produksi->jenis_produksi == 'daging' ? ' selected' : '') . '>Daging</option>';
            echo '<option value="ayam_hidup"' . ($produksi->jenis_produksi == 'ayam_hidup' ? ' selected' : '') . '>Ayam Hidup</option>';
            echo '</select>';
            echo '</div>';
            echo '</div>';
            echo '<div class="col-md-6">';
            echo '<div class="form-group">';
            echo '<label>Jumlah <span class="text-danger">*</span></label>';
            echo '<input type="number" class="form-control" name="jumlah" value="' . $produksi->jumlah . '" min="0" required>';
            echo '</div>';
            echo '<div class="form-group">';
            echo '<label>Berat (kg)</label>';
            echo '<input type="number" class="form-control" name="berat" value="' . $produksi->berat . '" step="0.01" min="0">';
            echo '</div>';
            echo '<div class="form-group">';
            echo '<label>Harga Satuan</label>';
            echo '<input type="number" class="form-control" name="harga_satuan" value="' . $produksi->harga_satuan . '" step="0.01" min="0">';
            echo '</div>';
            echo '<div class="form-group">';
            echo '<label>Kualitas</label>';
            echo '<select class="form-control" name="kualitas">';
            echo '<option value="A"' . ($produksi->kualitas == 'A' ? ' selected' : '') . '>Grade A</option>';
            echo '<option value="B"' . ($produksi->kualitas == 'B' ? ' selected' : '') . '>Grade B</option>';
            echo '<option value="C"' . ($produksi->kualitas == 'C' ? ' selected' : '') . '>Grade C</option>';
            echo '<option value="Reject"' . ($produksi->kualitas == 'Reject' ? ' selected' : '') . '>Reject</option>';
            echo '</select>';
            echo '</div>';
            echo '<div class="form-group">';
            echo '<label>Status</label>';
            echo '<select class="form-control" name="status">';
            echo '<option value="persiapan"' . ($produksi->status == 'persiapan' ? ' selected' : '') . '>Persiapan</option>';
            echo '<option value="aktif"' . ($produksi->status == 'aktif' ? ' selected' : '') . '>Aktif</option>';
            echo '<option value="selesai"' . ($produksi->status == 'selesai' ? ' selected' : '') . '>Selesai</option>';
            echo '<option value="gagal"' . ($produksi->status == 'gagal' ? ' selected' : '') . '>Gagal</option>';
            echo '</select>';
            echo '</div>';
            echo '</div>';
            echo '<div class="col-md-12">';
            echo '<div class="form-group">';
            echo '<label>Catatan</label>';
            echo '<textarea class="form-control" name="catatan" rows="3">' . $produksi->catatan . '</textarea>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        } else {
            echo '<div class="alert alert-warning">Data produksi tidak ditemukan</div>';
        }
    }

    /**
     * Update produksi
     */
    public function update()
    {
        $id = $this->input->post('id_produksi');
        $tanggal = $this->input->post('tanggal');
        $id_kandang = $this->input->post('id_kandang');
        $batch_penetasan = $this->input->post('batch_penetasan');
        $batch_pembesaran = $this->input->post('batch_pembesaran');
        $jenis_produksi = $this->input->post('jenis_produksi');
        $jumlah = $this->input->post('jumlah');
        $berat = $this->input->post('berat') ?: 0;
        $harga_satuan = $this->input->post('harga_satuan') ?: 0;
        $kualitas = $this->input->post('kualitas');
        $status = $this->input->post('status');
        $catatan = $this->input->post('catatan');

        $total_nilai = $jumlah * $harga_satuan;

        $data = array(
            'tanggal' => $tanggal,
            'id_kandang' => $id_kandang,
            'batch_penetasan' => $batch_penetasan,
            'batch_pembesaran' => $batch_pembesaran,
            'jenis_produksi' => $jenis_produksi,
            'jumlah' => $jumlah,
            'berat' => $berat,
            'harga_satuan' => $harga_satuan,
            'total_nilai' => $total_nilai,
            'kualitas' => $kualitas,
            'status' => $status,
            'catatan' => $catatan,
            'updated_at' => date('Y-m-d H:i:s')
        );

        $update = $this->M_produksi->update($id, $data);

        if ($update) {
            $this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-success\" id=\"alert\"><center>Data produksi berhasil diupdate</center></div></div>");
        } else {
            $this->session->set_flashdata("pesan", "<div class=\"form-group\" id=\"gone\"><div class=\"alert alert-danger\" id=\"alert\"><center>Gagal mengupdate data produksi</center></div></div>");
        }

        redirect('produksi');
    }

    /**
     * Hapus produksi (AJAX)
     */
    public function hapus_ajax()
    {
        $id = $this->input->post('id');
        $produksi = $this->M_produksi->get_by_id($id);
        
        if ($produksi) {
            $delete = $this->M_produksi->delete($id);
            
            if ($delete) {
                echo json_encode(['status' => 'success', 'message' => 'Data produksi berhasil dihapus']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data produksi']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Data produksi tidak ditemukan']);
        }
    }
}
