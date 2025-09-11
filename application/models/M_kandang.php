<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_kandang extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Cek kandang berdasarkan nama
    public function cek_kandang($nama)
    {
        $this->db->select('*');
        $this->db->from('kos_kandang');
        $this->db->where('nama', $nama);
        return $this->db->get();
    }

    // Read semua data kandang
    public function get_all_kandang()
    {
        $this->db->select('*');
        $this->db->from('kos_kandang');
        $this->db->where('status !=', 'deleted');
        $this->db->order_by('id_kandang', 'DESC');
        return $this->db->get()->result();
    }

    // Alias method for compatibility
    public function get_all()
    {
        return $this->get_all_kandang();
    }

    // Read kandang berdasarkan ID
    public function get_kandang_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('kos_kandang');
        $this->db->where('id_kandang', $id);
        return $this->db->get()->row();
    }

    // Read kandang berdasarkan status
    public function get_kandang_by_status($status)
    {
        $this->db->select('*');
        $this->db->from('kos_kandang');
        $this->db->where('status', $status);
        $this->db->order_by('tanggal', 'DESC');
        return $this->db->get()->result();
    }

    // Insert data kandang
    public function insert_kandang($data)
    {
        return $this->db->insert('kos_kandang', $data);
    }

    // Update data kandang
    public function update_kandang($id, $data)
    {
        $this->db->where('id_kandang', $id);
        return $this->db->update('kos_kandang', $data);
    }

    // Delete data kandang
    public function delete_kandang($id)
    {
        $this->db->where('id_kandang', $id);
        return $this->db->delete('kos_kandang');
    }

    // Get kandang untuk dropdown
    public function get_kandang_dropdown()
    {
        $this->db->select('id_kandang, nama');
        $this->db->from('v_kandang');
        $this->db->where('status', 'aktif');
        $this->db->order_by('nama', 'ASC');
        return $this->db->get()->result();
    }

    // Get kandang dengan kapasitas
    public function get_kandang_with_capacity()
    {
        $this->db->select('v_kandang.*, COUNT(kos_penghuni.id_penghuni) as terisi');
        $this->db->from('v_kandang');
        $this->db->join('kos_penghuni', 'kos_penghuni.id_kandang = v_kandang.id_kandang', 'left');
        $this->db->where('v_kandang.status', 'aktif');
        $this->db->group_by('v_kandang.id_kandang');
        return $this->db->get()->result();
    }

    // Get laporan kandang
    public function get_laporan_kandang($bulan = null, $tahun = null)
    {
        $this->db->select('v_kandang.*, COUNT(kos_penghuni.id_penghuni) as terisi');
        $this->db->from('v_kandang');
        $this->db->join('kos_penghuni', 'kos_penghuni.id_kandang = v_kandang.id_kandang', 'left');
        
        if ($bulan) {
            $this->db->where('MONTH(v_kandang.tanggal)', $bulan);
        }
        if ($tahun) {
            $this->db->where('YEAR(v_kandang.tanggal)', $tahun);
        }
        
        $this->db->group_by('v_kandang.id_kandang');
        $this->db->order_by('v_kandang.tanggal', 'DESC');
        return $this->db->get()->result();
    }

    // Update status kandang
    public function update_status_kandang($id, $status)
    {
        $data = array('status' => $status);
        $this->db->where('id_kandang', $id);
        return $this->db->update('kos_kandang', $data);
    }

    /**
     * Get jumlah kandang aktif
     */
    public function get_kandang_aktif()
    {
        $this->db->select('COUNT(*) as total');
        $this->db->from('kos_kandang');
        $this->db->where('status', 'aktif');
        $result = $this->db->get()->row();
        return $result ? $result->total : 0;
    }
}
