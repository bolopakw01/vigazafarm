<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Model M_penetasan
 * Menangani operasi database untuk tabel kos_penetasan
 */
class M_penetasan extends CI_Model
{
    private $table = 'kos_penetasan';
    private $table_mesin = 'kos_mesin';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get all data penetasan with join mesin
     */
    public function get_all_penetasan()
    {
        $this->db->select('p.*, m.nama as nama_mesin, m.kapasitas');
        $this->db->from($this->table . ' p');
        $this->db->join($this->table_mesin . ' m', 'p.id_mesin = m.id_mesin', 'left');
        $this->db->order_by('p.tanggal_mulai', 'DESC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get penetasan by ID with join mesin
     */
    public function get_penetasan_by_id($id)
    {
        $this->db->select('p.*, m.nama as nama_mesin, m.kapasitas');
        $this->db->from($this->table . ' p');
        $this->db->join($this->table_mesin . ' m', 'p.id_mesin = m.id_mesin', 'left');
        $this->db->where('p.id_penetasan', $id);
        
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Insert new penetasan data
     */
    public function insert_penetasan($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update penetasan data
     */
    public function update_penetasan($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id_penetasan', $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Update status otomatis berdasarkan kondisi
     */
    public function update_status_otomatis($id, $data)
    {
        // Get existing data
        $existing = $this->get_penetasan_by_id($id);
        if (!$existing) {
            return false;
        }

        // Calculate intelligent status
        $tanggal_mulai = new DateTime($data['tanggal_mulai']);
        $tanggal_sekarang = new DateTime();
        $hari_berlalu = $tanggal_sekarang->diff($tanggal_mulai)->days;
        $lama_penetasan = $existing['lama_penetasan'];

        // Auto status logic
        if (isset($data['hasil_menetas']) && $data['hasil_menetas'] > 0) {
            $persentase = ($data['hasil_menetas'] / $data['jumlah_telur']) * 100;
            
            if ($persentase >= 80) {
                $data['status'] = 'selesai';
            } elseif ($persentase < 50) {
                $data['status'] = 'gagal';
            } else {
                $data['status'] = 'proses';
            }
        } elseif ($hari_berlalu > $lama_penetasan + 2) {
            // Jika sudah lewat target + 2 hari dan belum ada hasil
            $data['status'] = 'terlambat';
        }

        return $this->update_penetasan($id, $data);
    }

    /**
     * Delete penetasan
     */
    public function delete_penetasan($id)
    {
        $this->db->where('id_penetasan', $id);
        return $this->db->delete($this->table);
    }

    /**
     * Get mesin options for dropdown
     */
    public function get_mesin_options()
    {
        $this->db->select('id_mesin, nama as nama_mesin, kapasitas');
        $this->db->from($this->table_mesin);
        $this->db->where('status', 'aktif');
        $this->db->order_by('nama', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Generate next batch code
     */
    public function generate_next_batch()
    {
        $year = date('Y');
        $month = date('m');
        $batch_prefix = "BATCH-{$year}-{$month}-";
        
        // Get the highest number for current month
        $this->db->select('batch');
        $this->db->from($this->table);
        $this->db->like('batch', $batch_prefix, 'after');
        $this->db->order_by('batch', 'DESC');
        $this->db->limit(1);
        
        $query = $this->db->get();
        $result = $query->row_array();
        
        if ($result) {
            // Extract number from last batch (e.g., BATCH-2025-09-005 -> 005)
            $last_batch = $result['batch'];
            $last_number = (int) substr($last_batch, -3);
            $next_number = $last_number + 1;
        } else {
            // First batch of the month
            $next_number = 1;
        }
        
        // Format: BATCH-YYYY-MM-XXX (e.g., BATCH-2025-09-001)
        return $batch_prefix . str_pad($next_number, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get statistik untuk dashboard
     */
    public function get_batch_aktif()
    {
        $this->db->where('status', 'proses');
        return $this->db->count_all_results($this->table);
    }

    public function get_telur_proses()
    {
        $this->db->select_sum('jumlah_telur');
        $this->db->where('status', 'proses');
        $query = $this->db->get($this->table);
        $result = $query->row_array();
        return $result['jumlah_telur'] ?: 0;
    }

    public function get_rata_menetas()
    {
        $this->db->select('AVG(persentase_menetas) as rata');
        $this->db->where('status', 'selesai');
        $this->db->where('persentase_menetas >', 0);
        $query = $this->db->get($this->table);
        $result = $query->row_array();
        return round($result['rata'] ?: 0, 1);
    }

    public function get_suhu_rata()
    {
        $this->db->select('AVG(suhu_rata) as rata');
        $this->db->where('status', 'proses');
        $query = $this->db->get($this->table);
        $result = $query->row_array();
        return round($result['rata'] ?: 37.5, 1);
    }

    /**
     * Check if batch exists
     */
    public function is_batch_exists($batch, $exclude_id = null)
    {
        $this->db->where('batch', $batch);
        if ($exclude_id) {
            $this->db->where('id_penetasan !=', $exclude_id);
        }
        return $this->db->count_all_results($this->table) > 0;
    }

    /**
     * Get penetasan by status
     */
    public function get_by_status($status)
    {
        $this->db->select('p.*, m.nama as nama_mesin');
        $this->db->from($this->table . ' p');
        $this->db->join($this->table_mesin . ' m', 'p.id_mesin = m.id_mesin', 'left');
        $this->db->where('p.status', $status);
        $this->db->order_by('p.tanggal_mulai', 'DESC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get penetasan in date range
     */
    public function get_by_date_range($start_date, $end_date)
    {
        $this->db->select('p.*, m.nama as nama_mesin');
        $this->db->from($this->table . ' p');
        $this->db->join($this->table_mesin . ' m', 'p.id_mesin = m.id_mesin', 'left');
        $this->db->where('p.tanggal_mulai >=', $start_date);
        $this->db->where('p.tanggal_mulai <=', $end_date);
        $this->db->order_by('p.tanggal_mulai', 'DESC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Update hasil penetasan
     */
    public function update_hasil($id, $hasil_menetas, $catatan = '')
    {
        // Get current data
        $current = $this->get_penetasan_by_id($id);
        if (!$current) {
            return false;
        }

        $hasil_gagal = $current['jumlah_telur'] - $hasil_menetas;
        $persentase = round(($hasil_menetas / $current['jumlah_telur']) * 100, 2);

        // Auto determine status
        $status = 'selesai';
        if ($persentase < 50) {
            $status = 'gagal';
        } elseif ($persentase >= 80) {
            $status = 'selesai';
        }

        $data = array(
            'hasil_menetas' => $hasil_menetas,
            'hasil_gagal' => $hasil_gagal,
            'persentase_menetas' => $persentase,
            'status' => $status,
            'catatan' => $catatan,
            'updated_at' => date('Y-m-d H:i:s')
        );

        return $this->update_penetasan($id, $data);
    }
}
