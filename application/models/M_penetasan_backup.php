<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_penetasan extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Get semua data penetasan untuk tampilan
    public function get_all_penetasan()
    {
        $this->db->select('p.*, m.nama as nama_mesin');
        $this->db->from('kos_penetasan p');
        $this->db->join('kos_mesin m', 'm.id_mesin = p.id_mesin', 'left');
        $this->db->order_by('p.tanggal_mulai', 'DESC');
        return $this->db->get()->result_array();
    }

    // Get jumlah batch aktif
    public function get_batch_aktif()
    {
        $this->db->where('status', 'proses');
        return $this->db->count_all_results('kos_penetasan');
    }

    // Get total telur dalam proses
    public function get_telur_proses()
    {
        $this->db->select_sum('jumlah_telur');
        $this->db->where('status', 'proses');
        $result = $this->db->get('kos_penetasan')->row();
        return $result->jumlah_telur ?: 0;
    }

    // Get rata-rata persentase menetas
    public function get_rata_menetas()
    {
        $this->db->select('AVG(CASE WHEN jumlah_telur > 0 THEN (hasil_menetas / jumlah_telur * 100) ELSE 0 END) as rata_menetas');
        $this->db->where('status', 'selesai');
        $result = $this->db->get('kos_penetasan')->row();
        return round($result->rata_menetas ?: 0, 1);
    }

    // Get rata-rata suhu
    public function get_suhu_rata()
    {
        $this->db->select_avg('suhu_rata');
        $this->db->where('status !=', 'gagal');
        $result = $this->db->get('kos_penetasan')->row();
        return $result->suhu_rata ?: 0;
    }

    // Get statistik penetasan
    public function get_statistik_penetasan()
    {
        $this->db->select('
            COUNT(*) as total,
            COUNT(CASE WHEN status = "proses" THEN 1 END) as proses,
            COUNT(CASE WHEN status = "selesai" THEN 1 END) as selesai,
            COUNT(CASE WHEN status = "gagal" THEN 1 END) as gagal,
            SUM(jumlah_telur) as total_telur,
            SUM(CASE WHEN status = "selesai" THEN hasil_menetas ELSE 0 END) as total_menetas
        ');
        $this->db->from('kos_penetasan');
        return $this->db->get()->row();
    }

    // Get penetasan aktif (sedang proses)
    public function get_penetasan_aktif()
    {
        $this->db->select('p.*, m.nama as nama_mesin,
                          DATEDIFF(CURDATE(), p.tanggal_mulai) as hari_berlalu,
                          DATE_ADD(p.tanggal_mulai, INTERVAL p.lama_penetasan DAY) as tanggal_target');
        $this->db->from('kos_penetasan p');
        $this->db->join('kos_mesin m', 'm.id_mesin = p.id_mesin', 'left');
        $this->db->where('p.status', 'proses');
        $this->db->order_by('p.tanggal_mulai', 'ASC');
        return $this->db->get()->result();
    }

    // Read penetasan berdasarkan ID
    public function get_penetasan_by_id($id)
    {
        $this->db->select('p.*, m.nama as nama_mesin, m.kapasitas, m.tipe');
        $this->db->from('kos_penetasan p');
        $this->db->join('kos_mesin m', 'm.id_mesin = p.id_mesin', 'left');
        $this->db->where('p.id_penetasan', $id);
        return $this->db->get()->row();
    }

    // Read penetasan berdasarkan batch
    public function get_penetasan_by_batch($batch)
    {
        $this->db->select('p.*, m.nama as nama_mesin');
        $this->db->from('kos_penetasan p');
        $this->db->join('kos_mesin m', 'm.id_mesin = p.id_mesin', 'left');
        $this->db->where('p.batch', $batch);
        $this->db->order_by('p.tanggal_mulai', 'DESC');
        return $this->db->get()->result();
    }

    // Read penetasan berdasarkan status
    public function get_penetasan_by_status($status)
    {
        $this->db->select('p.*, m.nama as nama_mesin');
        $this->db->from('kos_penetasan p');
        $this->db->join('kos_mesin m', 'm.id_mesin = p.id_mesin', 'left');
        $this->db->where('p.status', $status);
        $this->db->order_by('p.tanggal_mulai', 'DESC');
        return $this->db->get()->result();
    }

    // Get data mesin untuk dropdown
    public function get_mesin_options()
    {
        $this->db->select('id_mesin, nama, kapasitas, status, tipe');
        $this->db->from('kos_mesin');
        $this->db->where('status', 'aktif');
        $this->db->order_by('nama', 'ASC');
        return $this->db->get()->result();
    }

    // Generate next batch code
    public function generate_next_batch()
    {
        $year = date('Y');
        $month = date('m');
        
        // Get last batch for current month
        $this->db->select('batch');
        $this->db->from('kos_penetasan');
        $this->db->like('batch', 'BATCH-' . $year . '-' . $month, 'after');
        $this->db->order_by('batch', 'DESC');
        $this->db->limit(1);
        $result = $this->db->get()->row();
        
        if ($result) {
            // Extract number from last batch
            $last_batch = $result->batch;
            $parts = explode('-', $last_batch);
            $last_number = isset($parts[3]) ? intval($parts[3]) : 0;
            $next_number = $last_number + 1;
        } else {
            $next_number = 1;
        }
        
        return 'BATCH-' . $year . '-' . $month . '-' . str_pad($next_number, 3, '0', STR_PAD_LEFT);
    }
        
        // Get last batch for current month
        $this->db->select('batch');
        $this->db->from('kos_penetasan');
        $this->db->like('batch', 'BATCH-' . $year . '-' . $month, 'after');
        $this->db->order_by('batch', 'DESC');
        $this->db->limit(1);
        $result = $this->db->get()->row();
        
        if ($result) {
            // Extract number from last batch
            $last_batch = $result->batch;
            $parts = explode('-', $last_batch);
            $last_number = isset($parts[3]) ? intval($parts[3]) : 0;
            $next_number = $last_number + 1;
        } else {
            $next_number = 1;
        }
        
        return 'BATCH-' . $year . '-' . $month . '-' . str_pad($next_number, 3, '0', STR_PAD_LEFT);
    }
        $this->db->order_by('nama', 'ASC');
        return $this->db->get()->result();
    }

    // Insert data penetasan
    public function insert_penetasan($data)
    {
        return $this->db->insert('kos_penetasan', $data);
    }

    // Update data penetasan
    public function update_penetasan($id, $data)
    {
        $this->db->where('id_penetasan', $id);
        return $this->db->update('kos_penetasan', $data);
    }

    // Delete data penetasan
    public function delete_penetasan($id)
    {
        $this->db->where('id_penetasan', $id);
        return $this->db->delete('kos_penetasan');
    }

    // Cek batch penetasan (untuk validasi)
    public function cek_batch($batch, $id = null)
    {
        $this->db->select('*');
        $this->db->from('kos_penetasan');
        $this->db->where('batch', $batch);
        if ($id) {
            $this->db->where('id_penetasan !=', $id);
        }
        return $this->db->get();
    }

    // Get progress penetasan
    public function get_progress_penetasan($batch)
    {
        $this->db->select('p.*, m.nama as nama_mesin,
                          DATEDIFF(CURDATE(), p.tanggal_mulai) as hari_berlalu,
                          CASE 
                            WHEN p.lama_penetasan > 0 THEN 
                              ROUND((DATEDIFF(CURDATE(), p.tanggal_mulai) / p.lama_penetasan) * 100, 2)
                            ELSE 0 
                          END as persentase_progress,
                          DATE_ADD(p.tanggal_mulai, INTERVAL p.lama_penetasan DAY) as tanggal_target');
        $this->db->from('kos_penetasan p');
        $this->db->join('kos_mesin m', 'm.id_mesin = p.id_mesin', 'left');
        $this->db->where('p.batch', $batch);
        $this->db->where('p.status', 'proses');
        return $this->db->get()->row();
    }

    // Update status penetasan
    public function update_status_penetasan($id, $status)
    {
        $data = array(
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        );
        $this->db->where('id_penetasan', $id);
        return $this->db->update('kos_penetasan', $data);
    }

    // Update hasil penetasan
    public function update_hasil_penetasan($id, $hasil_data)
    {
        $hasil_data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id_penetasan', $id);
        return $this->db->update('kos_penetasan', $hasil_data);
    }

    // Get laporan penetasan
    public function get_laporan_penetasan($bulan = null, $tahun = null, $mesin = null)
    {
        $this->db->select('p.*, m.nama as nama_mesin, m.kapasitas, m.tipe');
        $this->db->from('kos_penetasan p');
        $this->db->join('kos_mesin m', 'm.id_mesin = p.id_mesin', 'left');
        
        if ($bulan) {
            $this->db->where('MONTH(p.tanggal_mulai)', $bulan);
        }
        if ($tahun) {
            $this->db->where('YEAR(p.tanggal_mulai)', $tahun);
        }
        if ($mesin) {
            $this->db->where('p.id_mesin', $mesin);
        }
        
        $this->db->order_by('p.tanggal_mulai', 'DESC');
        return $this->db->get()->result();
    }

    // Generate batch code
    public function generate_batch_code()
    {
        $date = date('Ymd');
        
        // Get last batch for today
        $this->db->select('batch');
        $this->db->from('kos_penetasan');
        $this->db->like('batch', 'PEN' . $date, 'after');
        $this->db->order_by('batch', 'DESC');
        $this->db->limit(1);
        $result = $this->db->get()->row();
        
        if ($result) {
            // Extract number and increment
            $last_number = intval(substr($result->batch, -3));
            $new_number = $last_number + 1;
        } else {
            $new_number = 1;
        }
        
        return 'PEN' . $date . str_pad($new_number, 3, '0', STR_PAD_LEFT);
    }

    // Check if DOC can be moved to pembesaran
    public function can_move_to_pembesaran($batch)
    {
        $this->db->select('*');
        $this->db->from('kos_penetasan');
        $this->db->where('batch', $batch);
        $this->db->where('status', 'selesai');
        $this->db->where('hasil_menetas >', 0);
        return $this->db->get()->num_rows() > 0;
    }
}
