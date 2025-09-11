<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_pembesaran extends CI_Model
{
    private $table = 'kos_pembesaran';
    private $view = 'v_pembesaran';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get statistik pembesaran
     */
    public function get_statistik()
    {
        // Total pembesaran
        $this->db->select('COUNT(*) as total');
        $this->db->from($this->table);
        $total = $this->db->get()->row()->total;

        // Pembesaran aktif
        $this->db->select('COUNT(*) as aktif');
        $this->db->from($this->table);
        $this->db->where('status', 'aktif');
        $aktif = $this->db->get()->row()->aktif;

        // Pembesaran selesai
        $this->db->select('COUNT(*) as selesai');
        $this->db->from($this->table);
        $this->db->where_in('status', ['selesai', 'panen']);
        $selesai = $this->db->get()->row()->selesai;

        // Total populasi
        $this->db->select('SUM(jumlah_hidup) as total_populasi');
        $this->db->from($this->table);
        $this->db->where('status', 'aktif');
        $total_populasi = $this->db->get()->row()->total_populasi ?? 0;

        return (object) array(
            'total' => $total,
            'aktif' => $aktif,
            'selesai' => $selesai,
            'total_populasi' => $total_populasi
        );
    }

    /**
     * Get pembesaran aktif untuk dashboard
     */
    public function get_pembesaran_aktif()
    {
        $this->db->select('
            p.*,
            k.nama as nama_kandang,
            p.tanggal_mulai as tgl_masuk,
            p.jumlah_bibit as jml_awal,
            p.jumlah_hidup as jml_saat_ini,
            p.tanggal_selesai as target_panen
        ');
        $this->db->from($this->table . ' p');
        $this->db->join('kos_kandang k', 'p.id_kandang = k.id_kandang', 'left');
        $this->db->where('p.status', 'aktif');
        $this->db->order_by('p.tanggal_mulai', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get semua data pembesaran
     */
    public function get_all_pembesaran()
    {
        $this->db->select('
            p.*,
            k.nama as nama_kandang,
            p.tanggal_mulai as tgl_masuk,
            p.jumlah_bibit as jml_awal,
            p.jumlah_hidup as jml_saat_ini,
            p.tanggal_selesai as target_panen
        ');
        $this->db->from($this->table . ' p');
        $this->db->join('kos_kandang k', 'p.id_kandang = k.id_kandang', 'left');
        $this->db->order_by('p.tanggal_mulai', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get pembesaran berdasarkan ID
     */
    public function get_pembesaran_by_id($id)
    {
        $this->db->select('
            p.*,
            k.nama as nama_kandang,
            k.kapasitas,
            p.tanggal_mulai as tgl_masuk,
            p.jumlah_bibit as jml_awal,
            p.jumlah_hidup as jml_saat_ini,
            p.tanggal_selesai as target_panen
        ');
        $this->db->from($this->table . ' p');
        $this->db->join('kos_kandang k', 'p.id_kandang = k.id_kandang', 'left');
        $this->db->where('p.id_pembesaran', $id);
        return $this->db->get()->row();
    }

    /**
     * Get pembesaran berdasarkan periode
     */
    public function get_pembesaran_by_periode($periode)
    {
        $this->db->where('periode', $periode);
        return $this->db->get($this->table)->row();
    }

    /**
     * Insert data pembesaran baru
     */
    public function insert_pembesaran($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update data pembesaran
     */
    public function update_pembesaran($id, $data)
    {
        $this->db->where('id_pembesaran', $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Delete pembesaran
     */
    public function delete_pembesaran($id)
    {
        $this->db->where('id_pembesaran', $id);
        return $this->db->delete($this->table);
    }

    /**
     * Get total pembesaran
     */
    public function get_total_pembesaran()
    {
        return $this->db->count_all($this->table);
    }

    /**
     * Get pembesaran by kandang
     */
    public function get_pembesaran_by_kandang($id_kandang)
    {
        $this->db->where('id_kandang', $id_kandang);
        $this->db->order_by('tanggal_mulai', 'DESC');
        return $this->db->get($this->table)->result();
    }

    /**
     * Check if periode exists
     */
    public function is_periode_exists($periode, $exclude_id = null)
    {
        $this->db->where('periode', $periode);
        if ($exclude_id) {
            $this->db->where('id_pembesaran !=', $exclude_id);
        }
        $query = $this->db->get($this->table);
        return $query->num_rows() > 0;
    }

    /**
     * Get laporan pembesaran berdasarkan bulan dan tahun
     */
    public function get_laporan_pembesaran($bulan, $tahun)
    {
        $this->db->select('
            p.*,
            k.nama as nama_kandang,
            DATEDIFF(COALESCE(p.tanggal, p.tanggal_selesai), p.tanggal_mulai) as durasi_hari,
            CASE 
                WHEN p.status = "selesai" THEN "Selesai"
                WHEN p.status = "aktif" THEN "Aktif"
                ELSE "Tidak Aktif"
            END as status_text
        ');
        $this->db->from($this->table . ' p');
        $this->db->join('kos_kandang k', 'p.id_kandang = k.id_kandang', 'left');
        $this->db->where('MONTH(p.tanggal_mulai)', $bulan);
        $this->db->where('YEAR(p.tanggal_mulai)', $tahun);
        $this->db->order_by('p.tanggal_mulai', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get pembesaran dengan filter
     */
    public function get_pembesaran_filtered($filters = array())
    {
        $this->db->select('
            p.*,
            k.nama as nama_kandang
        ');
        $this->db->from($this->table . ' p');
        $this->db->join('kos_kandang k', 'p.id_kandang = k.id_kandang', 'left');

        // Apply filters
        if (!empty($filters['status'])) {
            $this->db->where('p.status', $filters['status']);
        }
        
        if (!empty($filters['id_kandang'])) {
            $this->db->where('p.id_kandang', $filters['id_kandang']);
        }
        
        if (!empty($filters['tanggal_mulai'])) {
            $this->db->where('p.tanggal_mulai >=', $filters['tanggal_mulai']);
        }
        
        if (!empty($filters['tanggal_akhir'])) {
            $this->db->where('p.tanggal_mulai <=', $filters['tanggal_akhir']);
        }

        $this->db->order_by('p.tanggal_mulai', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Update populasi saat ini
     */
    public function update_populasi($id, $jumlah_saat_ini, $keterangan = null)
    {
        // Get data existing untuk hitung jumlah mati
        $existing = $this->get_pembesaran_by_id($id);
        $jumlah_mati = $existing->jumlah_bibit - $jumlah_saat_ini;

        $data = array(
            'jumlah_hidup' => $jumlah_saat_ini,
            'jumlah_mati' => $jumlah_mati,
            'updated_at' => date('Y-m-d H:i:s')
        );
        
        if ($keterangan) {
            $data['catatan'] = $keterangan;
        }

        $this->db->where('id_pembesaran', $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Get riwayat pembesaran untuk kandang tertentu
     */
    public function get_riwayat_kandang($id_kandang, $limit = 10)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('id_kandang', $id_kandang);
        $this->db->order_by('tanggal_mulai', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * Get pembesaran yang akan panen dalam X hari
     */
    public function get_pembesaran_akan_panen($hari = 7)
    {
        $this->db->select('
            p.*,
            k.nama as nama_kandang,
            DATEDIFF(p.tanggal_selesai, CURDATE()) as hari_tersisa
        ');
        $this->db->from($this->table . ' p');
        $this->db->join('kos_kandang k', 'p.id_kandang = k.id_kandang', 'left');
        $this->db->where('p.status', 'aktif');
        $this->db->where('DATEDIFF(p.tanggal_selesai, CURDATE()) <=', $hari);
        $this->db->where('DATEDIFF(p.tanggal_selesai, CURDATE()) >=', 0);
        $this->db->order_by('p.tanggal_selesai', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Get total produksi dalam periode tertentu
     */
    public function get_total_produksi($tanggal_mulai, $tanggal_akhir)
    {
        $this->db->select('
            COUNT(*) as total_periode,
            SUM(jumlah_bibit) as total_doc_masuk,
            SUM(CASE WHEN status = "panen" THEN jumlah_hidup ELSE 0 END) as total_panen,
            AVG(CASE WHEN status = "panen" THEN 
                DATEDIFF(tgl_panen, tanggal_mulai) 
                ELSE NULL END) as rata_rata_hari
        ');
        $this->db->from($this->table);
        $this->db->where('tanggal_mulai >=', $tanggal_mulai);
        $this->db->where('tanggal_mulai <=', $tanggal_akhir);
        return $this->db->get()->row();
    }

    /**
     * Get jumlah periode aktif
     */
    public function get_periode_aktif()
    {
        $this->db->where('status', 'aktif');
        return $this->db->count_all_results($this->table);
    }

    /**
     * Get total ayam hidup
     */
    public function get_total_hidup()
    {
        $this->db->select('SUM(jumlah_hidup) as total');
        $this->db->where('status', 'aktif');
        $result = $this->db->get($this->table)->row();
        return $result ? $result->total : 0;
    }

    /**
     * Get rata-rata berat ayam
     */
    public function get_rata_berat()
    {
        $this->db->select('AVG(berat_rata) as rata');
        $this->db->where('status', 'aktif');
        $this->db->where('berat_rata >', 0);
        $result = $this->db->get($this->table)->row();
        return $result ? round($result->rata, 2) : 0;
    }

    /**
     * Get total biaya
     */
    public function get_total_biaya()
    {
        $this->db->select('SUM(biaya_pakan + biaya_obat + biaya_lain) as total');
        $this->db->where('status', 'aktif');
        $result = $this->db->get($this->table)->row();
        return $result ? $result->total : 0;
    }
}