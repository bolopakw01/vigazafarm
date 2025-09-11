<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_karyawan extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Read semua data karyawan
    public function get_all_karyawan()
    {
        $this->db->select('*');
        $this->db->from('v_karyawan');
        $this->db->order_by('nama', 'ASC');
        return $this->db->get()->result();
    }

    // Read karyawan berdasarkan ID
    public function get_karyawan_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('v_karyawan');
        $this->db->where('id_karyawan', $id);
        return $this->db->get()->row();
    }

    // Read karyawan berdasarkan status
    public function get_karyawan_by_status($status)
    {
        $this->db->select('*');
        $this->db->from('v_karyawan');
        $this->db->where('status', $status);
        $this->db->order_by('nama', 'ASC');
        return $this->db->get()->result();
    }

    // Read karyawan berdasarkan jabatan
    public function get_karyawan_by_jabatan($jabatan)
    {
        $this->db->select('*');
        $this->db->from('v_karyawan');
        $this->db->where('jabatan', $jabatan);
        $this->db->order_by('nama', 'ASC');
        return $this->db->get()->result();
    }

    // Insert data karyawan
    public function insert_karyawan($data)
    {
        return $this->db->insert('kos_karyawan', $data);
    }

    // Update data karyawan
    public function update_karyawan($id, $data)
    {
        $this->db->where('id_karyawan', $id);
        return $this->db->update('kos_karyawan', $data);
    }

    // Delete data karyawan
    public function delete_karyawan($id)
    {
        $this->db->where('id_karyawan', $id);
        return $this->db->delete('kos_karyawan');
    }

    // Cek email karyawan (untuk validasi)
    public function cek_email($email, $id = null)
    {
        $this->db->select('*');
        $this->db->from('v_karyawan');
        $this->db->where('email', $email);
        if ($id) {
            $this->db->where('id_karyawan !=', $id);
        }
        return $this->db->get();
    }

    // Cek NIK karyawan (untuk validasi)
    public function cek_nik($nik, $id = null)
    {
        $this->db->select('*');
        $this->db->from('v_karyawan');
        $this->db->where('nik', $nik);
        if ($id) {
            $this->db->where('id_karyawan !=', $id);
        }
        return $this->db->get();
    }

    // Get karyawan untuk dropdown
    public function get_karyawan_dropdown()
    {
        $this->db->select('id_karyawan, nama');
        $this->db->from('v_karyawan');
        $this->db->where('status', 'aktif');
        $this->db->order_by('nama', 'ASC');
        return $this->db->get()->result();
    }

    // Get laporan karyawan
    public function get_laporan_karyawan($bulan = null, $tahun = null, $jabatan = null)
    {
        $this->db->select('*');
        $this->db->from('v_karyawan');
        
        if ($bulan) {
            $this->db->where('MONTH(tanggal_masuk)', $bulan);
        }
        if ($tahun) {
            $this->db->where('YEAR(tanggal_masuk)', $tahun);
        }
        if ($jabatan) {
            $this->db->where('jabatan', $jabatan);
        }
        
        $this->db->order_by('tanggal_masuk', 'DESC');
        return $this->db->get()->result();
    }

    // Update status karyawan
    public function update_status_karyawan($id, $status)
    {
        $data = array('status' => $status);
        $this->db->where('id_karyawan', $id);
        return $this->db->update('kos_karyawan', $data);
    }

    // Get statistik karyawan
    public function get_statistik_karyawan()
    {
        $this->db->select('
            COUNT(*) as total,
            COUNT(CASE WHEN status = "aktif" THEN 1 END) as aktif,
            COUNT(CASE WHEN status = "nonaktif" THEN 1 END) as nonaktif,
            COUNT(CASE WHEN jabatan = "manager" THEN 1 END) as manager,
            COUNT(CASE WHEN jabatan = "supervisor" THEN 1 END) as supervisor,
            COUNT(CASE WHEN jabatan = "pekerja" THEN 1 END) as pekerja
        ');
        $this->db->from('v_karyawan');
        return $this->db->get()->row();
    }

    // Get karyawan dengan tugas
    public function get_karyawan_with_tugas()
    {
        $this->db->select('v_karyawan.*, COUNT(kos_tugas.id_tugas) as jumlah_tugas');
        $this->db->from('v_karyawan');
        $this->db->join('kos_tugas', 'kos_tugas.id_karyawan = v_karyawan.id_karyawan', 'left');
        $this->db->where('v_karyawan.status', 'aktif');
        $this->db->group_by('v_karyawan.id_karyawan');
        $this->db->order_by('v_karyawan.nama', 'ASC');
        return $this->db->get()->result();
    }

    // Upload foto karyawan
    public function update_foto($id, $foto)
    {
        $data = array('foto' => $foto);
        $this->db->where('id_karyawan', $id);
        return $this->db->update('kos_karyawan', $data);
    }
}
