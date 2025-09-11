<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Penetasan Controller
 * Menangani manajemen proses penetasan telur
 */
class Penetasan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['session', 'template']);
        $this->load->helper(['url', 'form']);
        $this->load->model(['M_penetasan', 'M_kandang', 'm_min']);
        
        // Enhanced authentication check
        $isLoggedIn = $this->session->userdata('isLog');
        $username = $this->session->userdata('isUname');
        
        if ($isLoggedIn !== TRUE || empty($username)) {
            $this->session->sess_destroy();
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
        $data['thisPg'] = 'penetasan';
        
        // Get profile safely
        try {
            $profil_result = $this->m_min->profil($this->session->userdata('isUname'));
            if ($profil_result && !empty($profil_result)) {
                $data['profil'] = $profil_result;
            } else {
                $data['profil'] = array(
                    'username' => $this->session->userdata('isUname'),
                    'nama_lengkap' => $this->session->userdata('isUname')
                );
            }
        } catch (Exception $e) {
            $data['profil'] = array(
                'username' => $this->session->userdata('isUname'),
                'nama_lengkap' => $this->session->userdata('isUname')
            );
        }
        
        // Load views
        $this->load->view('admin/template/header', $data);
        $this->load->view('admin/template/top', $data);  
        $this->load->view('admin/template/sidebar', $data);
        $this->load->view('admin/' . $view, $data);
        $this->load->view('admin/template/footer', $data);
    }

    /**
     * Halaman utama penetasan
     */
    public function index()
    {
        try {
            // Get statistik penetasan
            $data['batch_aktif'] = $this->M_penetasan->get_batch_aktif();
            $data['telur_proses'] = $this->M_penetasan->get_telur_proses();
            $data['rata_menetas'] = $this->M_penetasan->get_rata_menetas();
            $data['suhu_rata'] = $this->M_penetasan->get_suhu_rata();
            
            // Get data penetasan untuk tabel
            $data['penetasan'] = $this->M_penetasan->get_all_penetasan();
            
            // Load template
            $this->load_template('penetasan/index_penetasan', $data);
            
        } catch (Exception $e) {
            log_message('error', 'Penetasan index error: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Terjadi kesalahan saat memuat data.');
            $data['penetasan'] = array();
            $this->load_template('penetasan/index_penetasan', $data);
        }
    }

    /**
     * Form tambah penetasan
     */
    public function tambah()
    {
        $this->load->library('form_validation');
        
        if ($this->input->method() == 'post') {
            // Set validation rules
            $this->form_validation->set_rules('batch', 'Batch', 'required|trim');
            $this->form_validation->set_rules('tanggal_mulai', 'Tanggal Mulai', 'required');
            $this->form_validation->set_rules('lama_penetasan', 'Lama Penetasan', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('jumlah_telur', 'Jumlah Telur', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('suhu_rata', 'Suhu Rata-rata', 'required|numeric');
            $this->form_validation->set_rules('kelembaban_rata', 'Kelembaban Rata-rata', 'required|numeric');
            
            if ($this->form_validation->run() == TRUE) {
                // Get form data
                $batch = $this->input->post('batch');
                $id_mesin = $this->input->post('id_mesin');
                $tanggal_mulai = $this->input->post('tanggal_mulai');
                $lama_penetasan = intval($this->input->post('lama_penetasan'));
                $jumlah_telur = $this->input->post('jumlah_telur');
                $suhu_rata = $this->input->post('suhu_rata');
                $kelembaban_rata = $this->input->post('kelembaban_rata');
                $catatan = $this->input->post('catatan');
                
                // Calculate tanggal_selesai berdasarkan lama_penetasan
                $tanggal_selesai = date('Y-m-d', strtotime($tanggal_mulai . ' + ' . $lama_penetasan . ' days'));
                
                // Check if batch already exists
                $existing = $this->db->get_where('kos_penetasan', ['batch' => $batch])->num_rows();
                if ($existing > 0) {
                    $this->session->set_flashdata('error', 'Batch sudah ada! Gunakan nama batch yang berbeda.');
                } else {
                    $data_penetasan = array(
                        'batch' => $batch,
                        'id_mesin' => $id_mesin,
                        'tanggal_mulai' => $tanggal_mulai,
                        'tanggal_selesai' => $tanggal_selesai,
                        'lama_penetasan' => $lama_penetasan,
                        'jumlah_telur' => $jumlah_telur,
                        'hasil_menetas' => 0,
                        'hasil_gagal' => 0,
                        'persentase_menetas' => 0,
                        'suhu_rata' => $suhu_rata,
                        'kelembaban_rata' => $kelembaban_rata,
                        'status' => 'proses',
                        'catatan' => $catatan,
                        'tanggal' => date('Y-m-d')
                    );
                    
                    $insert = $this->db->insert('kos_penetasan', $data_penetasan);
                    
                    if ($insert) {
                        $this->session->set_flashdata('success', 'Data penetasan berhasil ditambahkan!');
                        redirect('penetasan');
                    } else {
                        $this->session->set_flashdata('error', 'Gagal menambahkan data penetasan!');
                    }
                }
            } else {
                // Validation failed
                $this->session->set_flashdata('error', validation_errors());
            }
        }
        
        // Get data untuk form
        $data['next_batch'] = $this->M_penetasan->generate_next_batch();
        $data['mesin_options'] = $this->M_penetasan->get_mesin_options();
        
        // Load form tambah
        $this->load_template('penetasan/tambah_penetasan', $data);
    }

    /**
     * Edit penetasan
     */
    public function edit($id)
    {
        if (!$id) {
            $this->session->set_flashdata('error', 'ID tidak valid!');
            redirect('penetasan');
        }
        
        $this->load->library('form_validation');
        
        if ($this->input->method() == 'post') {
            // Set validation rules
            $this->form_validation->set_rules('batch', 'Batch', 'required|trim');
            $this->form_validation->set_rules('tanggal_mulai', 'Tanggal Mulai', 'required');
            $this->form_validation->set_rules('jumlah_telur', 'Jumlah Telur', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('hasil_menetas', 'Hasil Menetas', 'numeric');
            $this->form_validation->set_rules('suhu_rata', 'Suhu Rata-rata', 'required|numeric');
            $this->form_validation->set_rules('kelembaban_rata', 'Kelembaban Rata-rata', 'required|numeric');
            
            if ($this->form_validation->run() == TRUE) {
                // Get existing data
                $existing_data = $this->db->get_where('kos_penetasan', ['id_penetasan' => $id])->row_array();
                $lama_penetasan = $existing_data['lama_penetasan'] ?? 21;
                
                $data_update = array(
                    'batch' => $this->input->post('batch'),
                    'id_mesin' => $this->input->post('id_mesin'),
                    'tanggal_mulai' => $this->input->post('tanggal_mulai'),
                    'tanggal_selesai' => date('Y-m-d', strtotime($this->input->post('tanggal_mulai') . ' + ' . $lama_penetasan . ' days')),
                    'jumlah_telur' => $this->input->post('jumlah_telur'),
                    'hasil_menetas' => $this->input->post('hasil_menetas') ?: 0,
                    'suhu_rata' => $this->input->post('suhu_rata'),
                    'kelembaban_rata' => $this->input->post('kelembaban_rata'),
                    'catatan' => $this->input->post('catatan'),
                    'status' => $this->input->post('status') ?: $existing_data['status'],
                    'updated_at' => date('Y-m-d H:i:s')
                );
                
                // Calculate hasil_gagal and persentase
                if ($data_update['hasil_menetas'] > 0) {
                    $data_update['hasil_gagal'] = $data_update['jumlah_telur'] - $data_update['hasil_menetas'];
                    $data_update['persentase_menetas'] = round(($data_update['hasil_menetas'] / $data_update['jumlah_telur']) * 100, 2);
                }
                
                // Use status-aware update method
                $update = $this->M_penetasan->update_status_otomatis($id, $data_update);
                
                if ($update) {
                    // Check if AJAX request
                    if ($this->input->is_ajax_request()) {
                        $response = array(
                            'status' => 'success',
                            'message' => 'Data penetasan berhasil diupdate!',
                            'data' => $data_update
                        );
                        header('Content-Type: application/json');
                        echo json_encode($response);
                        return;
                    } else {
                        $this->session->set_flashdata('success', 'Data penetasan berhasil diupdate!');
                        redirect('penetasan');
                    }
                } else {
                    if ($this->input->is_ajax_request()) {
                        $response = array(
                            'status' => 'error',
                            'message' => 'Gagal mengupdate data penetasan!'
                        );
                        header('Content-Type: application/json');
                        echo json_encode($response);
                        return;
                    } else {
                        $this->session->set_flashdata('error', 'Gagal mengupdate data penetasan!');
                    }
                }
            } else {
                if ($this->input->is_ajax_request()) {
                    $response = array(
                        'status' => 'error',
                        'message' => validation_errors()
                    );
                    header('Content-Type: application/json');
                    echo json_encode($response);
                    return;
                } else {
                    $this->session->set_flashdata('error', validation_errors());
                }
            }
        }
        
        // Get data for form
        $data['penetasan'] = $this->M_penetasan->get_penetasan_by_id($id);
        $data['mesin_options'] = $this->M_penetasan->get_mesin_options();
        $data['mesin'] = $this->M_penetasan->get_mesin_options(); // For edit form compatibility
        
        if (!$data['penetasan']) {
            $this->session->set_flashdata('error', 'Data penetasan tidak ditemukan!');
            redirect('penetasan');
        }
        
        // Load edit form
        $this->load_template('penetasan/edit_penetasan', $data);
    }

    /**
     * Get detail penetasan for AJAX
     */
    public function get_detail($id)
    {
        try {
            $detail = $this->M_penetasan->get_penetasan_by_id($id);
            
            if ($detail) {
                $response = array(
                    'status' => 'success',
                    'data' => $detail
                );
            } else {
                $response = array(
                    'status' => 'error',
                    'message' => 'Data penetasan tidak ditemukan'
                );
            }
        } catch (Exception $e) {
            $response = array(
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            );
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    /**
     * Generate batch code via AJAX
     */
    public function generate_batch()
    {
        try {
            $next_batch = $this->M_penetasan->generate_next_batch();
            $response = array(
                'status' => 'success',
                'batch' => $next_batch
            );
        } catch (Exception $e) {
            $response = array(
                'status' => 'error',
                'message' => 'Gagal generate batch: ' . $e->getMessage()
            );
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    /**
     * Update status penetasan via AJAX
     */
    public function update_status()
    {
        try {
            if (!$this->input->is_ajax_request()) {
                show_error('Direct access not allowed');
                return;
            }
            
            $id = $this->input->post('id');
            $status = $this->input->post('status');
            
            if (!$id || !$status) {
                $response = array(
                    'status' => 'error',
                    'message' => 'ID dan status harus diisi'
                );
            } else {
                $valid_status = ['persiapan', 'proses', 'selesai', 'gagal'];
                if (!in_array($status, $valid_status)) {
                    $response = array(
                        'status' => 'error',
                        'message' => 'Status tidak valid'
                    );
                } else {
                    $data_update = array(
                        'status' => $status,
                        'updated_at' => date('Y-m-d H:i:s')
                    );
                    
                    $update = $this->M_penetasan->update_penetasan($id, $data_update);
                    
                    if ($update) {
                        $response = array(
                            'status' => 'success',
                            'message' => 'Status berhasil diubah'
                        );
                    } else {
                        $response = array(
                            'status' => 'error',
                            'message' => 'Gagal mengubah status'
                        );
                    }
                }
            }
        } catch (Exception $e) {
            $response = array(
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            );
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    /**
     * Delete penetasan
     */
    public function delete($id)
    {
        try {
            // Check if request is AJAX
            if ($this->input->is_ajax_request()) {
                $delete = $this->M_penetasan->delete_penetasan($id);
                
                if ($delete) {
                    $response = array(
                        'status' => 'success',
                        'message' => 'Data penetasan berhasil dihapus!'
                    );
                } else {
                    $response = array(
                        'status' => 'error',
                        'message' => 'Gagal menghapus data penetasan!'
                    );
                }
                
                header('Content-Type: application/json');
                echo json_encode($response);
                return;
            } else {
                // Traditional form submission
                $delete = $this->M_penetasan->delete_penetasan($id);
                
                if ($delete) {
                    $this->session->set_flashdata('success', 'Data penetasan berhasil dihapus!');
                } else {
                    $this->session->set_flashdata('error', 'Gagal menghapus data penetasan!');
                }
                
                redirect('penetasan');
            }
        } catch (Exception $e) {
            if ($this->input->is_ajax_request()) {
                $response = array(
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                );
                header('Content-Type: application/json');
                echo json_encode($response);
            } else {
                $this->session->set_flashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
                redirect('penetasan');
            }
        }
    }
}
