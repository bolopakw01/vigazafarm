<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_produksi extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Read semua data produksi
    public function get_all_produksi()
    {
        $this->db->select('p.*, k.nama as nama_kandang, p.jumlah as jml_telur, p.berat as berat_telur, p.catatan as keterangan');
        $this->db->from('kos_produksi p');
        $this->db->join('kos_kandang k', 'p.id_kandang = k.id_kandang', 'left');
        $this->db->where('p.status !=', 'deleted');
        $this->db->order_by('p.tanggal', 'DESC');
        return $this->db->get()->result();
    }

    // Read produksi berdasarkan ID
    public function get_produksi_by_id($id)
    {
        $this->db->select('p.*, k.nama as nama_kandang');
        $this->db->from('kos_produksi p');
        $this->db->join('kos_kandang k', 'p.id_kandang = k.id_kandang', 'left');
        $this->db->where('p.id_produksi', $id);
        return $this->db->get()->row();
    }

    // Read produksi berdasarkan jenis
    public function get_produksi_by_jenis($jenis)
    {
        $this->db->select('*');
        $this->db->from('v_produksi');
        $this->db->where('jenis_produksi', $jenis);
        $this->db->order_by('tanggal', 'DESC');
        return $this->db->get()->result();
    }

    // Read produksi berdasarkan kandang
    public function get_produksi_by_kandang($id_kandang)
    {
        $this->db->select('v_produksi.*, v_kandang.nama as nama_kandang');
        $this->db->from('v_produksi');
        $this->db->join('v_kandang', 'v_kandang.id_kandang = v_produksi.id_kandang');
        $this->db->where('v_produksi.id_kandang', $id_kandang);
        $this->db->order_by('v_produksi.tanggal', 'DESC');
        return $this->db->get()->result();
    }

    // Read produksi berdasarkan periode
    public function get_produksi_by_periode($tanggal_mulai, $tanggal_selesai)
    {
        $this->db->select('v_produksi.*, v_kandang.nama as nama_kandang');
        $this->db->from('v_produksi');
        $this->db->join('v_kandang', 'v_kandang.id_kandang = v_produksi.id_kandang');
        $this->db->where('v_produksi.tanggal >=', $tanggal_mulai);
        $this->db->where('v_produksi.tanggal <=', $tanggal_selesai);
        $this->db->order_by('v_produksi.tanggal', 'DESC');
        return $this->db->get()->result();
    }

    // Insert data produksi
    public function insert_produksi($data)
    {
        return $this->db->insert('kos_produksi', $data);
    }

    // Update data produksi
    public function update_produksi($id, $data)
    {
        $this->db->where('id_produksi', $id);
        return $this->db->update('kos_produksi', $data);
    }

    // Delete data produksi
    public function delete_produksi($id)
    {
        $this->db->where('id_produksi', $id);
        return $this->db->delete('kos_produksi');
    }

    // Standard CRUD methods for controller compatibility
    public function get_by_id($id)
    {
        return $this->get_produksi_by_id($id);
    }

    public function insert($data)
    {
        return $this->insert_produksi($data);
    }

    public function update($id, $data)
    {
        return $this->update_produksi($id, $data);
    }

    public function delete($id)
    {
        return $this->delete_produksi($id);
    }

    // Get produksi dengan filter
    public function get_produksi_filtered($filters = array())
    {
        $this->db->select('
            p.*,
            k.nama as kandang
        ');
        $this->db->from('kos_produksi p');
        $this->db->join('kos_kandang k', 'p.id_kandang = k.id_kandang', 'left');

        // Apply filters
        if (!empty($filters['jenis'])) {
            $this->db->where('p.jenis_produksi', $filters['jenis']);
        }
        
        if (!empty($filters['status'])) {
            $this->db->where('p.status', $filters['status']);
        }
        
        if (!empty($filters['dari'])) {
            $this->db->where('p.tanggal >=', $filters['dari']);
        }
        
        if (!empty($filters['sampai'])) {
            $this->db->where('p.tanggal <=', $filters['sampai']);
        }

        $this->db->order_by('p.tanggal', 'DESC');
        return $this->db->get()->result();
    }

    // Get detail produksi dengan kandang
    public function get_detail_produksi($id)
    {
        $this->db->select('v_produksi.*, v_kandang.nama as nama_kandang, v_kandang.kapasitas');
        $this->db->from('v_produksi');
        $this->db->join('v_kandang', 'v_kandang.id_kandang = v_produksi.id_kandang');
        $this->db->where('v_produksi.id_produksi', $id);
        return $this->db->get()->row();
    }

    // Get laporan produksi harian
    public function get_laporan_harian($tanggal)
    {
        $this->db->select('v_produksi.*, v_kandang.nama as nama_kandang');
        $this->db->from('v_produksi');
        $this->db->join('v_kandang', 'v_kandang.id_kandang = v_produksi.id_kandang');
        $this->db->where('DATE(v_produksi.tanggal)', $tanggal);
        $this->db->order_by('v_produksi.tanggal', 'DESC');
        return $this->db->get()->result();
    }

    // Get laporan produksi bulanan
    public function get_laporan_bulanan($bulan, $tahun)
    {
        $this->db->select('v_produksi.*, v_kandang.nama as nama_kandang');
        $this->db->from('v_produksi');
        $this->db->join('v_kandang', 'v_kandang.id_kandang = v_produksi.id_kandang');
        $this->db->where('MONTH(v_produksi.tanggal)', $bulan);
        $this->db->where('YEAR(v_produksi.tanggal)', $tahun);
        $this->db->order_by('v_produksi.tanggal', 'DESC');
        return $this->db->get()->result();
    }

    // Get statistik produksi
    public function get_statistik_produksi($periode = null)
    {
        $this->db->select('
            COUNT(*) as total_record,
            SUM(CASE WHEN jenis_produksi = "telur" THEN jumlah ELSE 0 END) as total_telur,
            SUM(CASE WHEN jenis_produksi = "daging" THEN jumlah ELSE 0 END) as total_daging,
            SUM(CASE WHEN jenis_produksi = "ayam_hidup" THEN jumlah ELSE 0 END) as total_ayam_hidup,
            AVG(CASE WHEN jenis_produksi = "telur" THEN jumlah ELSE NULL END) as rata_telur_harian
        ');
        $this->db->from('v_produksi');
        
        if ($periode) {
            $this->db->where('DATE(tanggal) >=', $periode['mulai']);
            $this->db->where('DATE(tanggal) <=', $periode['selesai']);
        }
        
        return $this->db->get()->row();
    }

    // Get produksi per kandang
    public function get_produksi_per_kandang($periode = null)
    {
        $this->db->select('v_kandang.nama as nama_kandang, v_kandang.id_kandang,
                          SUM(CASE WHEN jenis_produksi = "telur" THEN jumlah ELSE 0 END) as total_telur,
                          SUM(CASE WHEN jenis_produksi = "daging" THEN jumlah ELSE 0 END) as total_daging,
                          SUM(CASE WHEN jenis_produksi = "ayam_hidup" THEN jumlah ELSE 0 END) as total_ayam_hidup,
                          COUNT(*) as total_record');
        $this->db->from('v_produksi');
        $this->db->join('v_kandang', 'v_kandang.id_kandang = v_produksi.id_kandang');
        
        if ($periode) {
            $this->db->where('DATE(v_produksi.tanggal) >=', $periode['mulai']);
            $this->db->where('DATE(v_produksi.tanggal) <=', $periode['selesai']);
        }
        
        $this->db->group_by('v_kandang.id_kandang');
        $this->db->order_by('total_telur', 'DESC');
        return $this->db->get()->result();
    }

    // Get trend produksi (untuk grafik)
    public function get_trend_produksi($jenis, $periode = null)
    {
        $this->db->select('DATE(tanggal) as tanggal, SUM(jumlah) as total');
        $this->db->from('v_produksi');
        $this->db->where('jenis_produksi', $jenis);
        
        if ($periode) {
            $this->db->where('DATE(tanggal) >=', $periode['mulai']);
            $this->db->where('DATE(tanggal) <=', $periode['selesai']);
        }
        
        $this->db->group_by('DATE(tanggal)');
        $this->db->order_by('tanggal', 'ASC');
        return $this->db->get()->result();
    }

    // Get produksi terbaik per kandang
    public function get_produksi_terbaik($jenis, $limit = 5)
    {
        $this->db->select('v_kandang.nama as nama_kandang, SUM(jumlah) as total');
        $this->db->from('v_produksi');
        $this->db->join('v_kandang', 'v_kandang.id_kandang = v_produksi.id_kandang');
        $this->db->where('jenis_produksi', $jenis);
        $this->db->group_by('v_produksi.id_kandang');
        $this->db->order_by('total', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    // Update kualitas produksi
    public function update_kualitas($id, $kualitas, $catatan = null)
    {
        $data = array(
            'kualitas' => $kualitas,
            'catatan' => $catatan
        );
        $this->db->where('id_produksi', $id);
        return $this->db->update('kos_produksi', $data);
    }

    // ===== ENHANCED WORKFLOW INTEGRATION =====
    
    /**
     * Create produksi from pembesaran workflow
     */
    public function create_from_pembesaran($batch_penetasan, $batch_pembesaran, $data)
    {
        $this->db->trans_start();
        
        // Set workflow data
        $produksi_data = array_merge($data, [
            'batch_penetasan' => $batch_penetasan,
            'batch_pembesaran' => $batch_pembesaran,
            'status' => 'persiapan',
            'fase_produksi' => 'awal',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $this->session->userdata('user_id') ?? 0
        ]);
        
        // Insert produksi
        $this->db->insert('kos_produksi', $produksi_data);
        $produksi_id = $this->db->insert_id();
        
        // Create workflow log
        $this->create_workflow_log($produksi_id, 'produksi', 'persiapan', 'Produksi disiapkan dari pembesaran batch ' . $batch_penetasan);
        
        $this->db->trans_complete();
        return $this->db->trans_status() ? $produksi_id : false;
    }

    /**
     * Start produksi process
     */
    public function start_produksi($id, $start_data = [])
    {
        $this->db->trans_start();
        
        $update_data = array_merge($start_data, [
            'status' => 'aktif',
            'tanggal_mulai_produksi' => date('Y-m-d'),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $this->session->userdata('user_id') ?? 0
        ]);
        
        $this->db->where('id_produksi', $id);
        $this->db->update('kos_produksi', $update_data);
        
        // Create workflow log
        $this->create_workflow_log($id, 'produksi', 'mulai', 'Produksi dimulai');
        
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * Add daily production record
     */
    public function add_daily_record($produksi_id, $data)
    {
        $daily_data = array_merge($data, [
            'id_produksi' => $produksi_id,
            'tanggal' => $data['tanggal'] ?? date('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $this->session->userdata('user_id') ?? 0
        ]);
        
        // Check if record for this date already exists
        $existing = $this->get_daily_record($produksi_id, $daily_data['tanggal']);
        
        if ($existing) {
            // Update existing record
            $this->db->where('id_produksi_harian', $existing->id_produksi_harian);
            return $this->db->update('produksi_harian', $daily_data);
        } else {
            // Insert new record
            return $this->db->insert('produksi_harian', $daily_data);
        }
    }

    /**
     * Get daily production record
     */
    public function get_daily_record($produksi_id, $tanggal = null)
    {
        if (!$this->db->table_exists('produksi_harian')) {
            return null;
        }
        
        $this->db->select('*');
        $this->db->from('produksi_harian');
        $this->db->where('id_produksi', $produksi_id);
        
        if ($tanggal) {
            $this->db->where('tanggal', $tanggal);
            return $this->db->get()->row();
        } else {
            $this->db->order_by('tanggal', 'DESC');
            return $this->db->get()->result();
        }
    }

    /**
     * Get daily records summary for produksi
     */
    public function get_daily_summary($produksi_id)
    {
        if (!$this->db->table_exists('produksi_harian')) {
            return null;
        }
        
        $this->db->select('
            COUNT(*) as total_hari_produksi,
            SUM(jumlah_telur) as total_telur,
            SUM(konsumsi_pakan) as total_pakan,
            SUM(jumlah_mati) as total_kematian,
            AVG(jumlah_telur) as rata_telur_harian,
            AVG(konsumsi_pakan) as rata_pakan_harian,
            MAX(tanggal) as produksi_terakhir
        ');
        $this->db->from('produksi_harian');
        $this->db->where('id_produksi', $produksi_id);
        return $this->db->get()->row();
    }

    /**
     * Get active produksi with daily status
     */
    public function get_active_produksi_with_daily()
    {
        $this->db->select('
            p.*,
            k.nama as nama_kandang,
            DATEDIFF(CURDATE(), p.tanggal_mulai_produksi) as hari_produksi,
            (SELECT COUNT(*) FROM produksi_harian ph WHERE ph.id_produksi = p.id_produksi) as total_record_harian,
            (SELECT MAX(tanggal) FROM produksi_harian ph WHERE ph.id_produksi = p.id_produksi) as record_terakhir,
            (SELECT SUM(jumlah_telur) FROM produksi_harian ph WHERE ph.id_produksi = p.id_produksi) as total_telur,
            (SELECT SUM(jumlah_mati) FROM produksi_harian ph WHERE ph.id_produksi = p.id_produksi) as total_kematian
        ');
        $this->db->from('kos_produksi p');
        $this->db->join('kos_kandang k', 'k.id_kandang = p.id_kandang', 'left');
        $this->db->where('p.status', 'aktif');
        $this->db->order_by('p.tanggal_mulai_produksi', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Complete produksi
     */
    public function complete_produksi($id, $result_data)
    {
        $this->db->trans_start();
        
        // Get summary data
        $summary = $this->get_daily_summary($id);
        
        // Update produksi
        $update_data = array_merge($result_data, [
            'status' => 'selesai',
            'tanggal_selesai' => date('Y-m-d'),
            'total_telur_produksi' => $summary->total_telur ?? 0,
            'total_pakan_konsumsi' => $summary->total_pakan ?? 0,
            'total_kematian' => $summary->total_kematian ?? 0,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $this->session->userdata('user_id') ?? 0
        ]);
        
        $this->db->where('id_produksi', $id);
        $this->db->update('kos_produksi', $update_data);
        
        // Create workflow log
        $this->create_workflow_log($id, 'produksi', 'selesai', 'Produksi selesai dengan total ' . ($summary->total_telur ?? 0) . ' telur');
        
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * Get dashboard statistics for produksi
     */
    public function get_dashboard_stats()
    {
        $result = [];
        
        // Basic statistics
        $this->db->select('
            COUNT(*) as total_produksi,
            COUNT(CASE WHEN status = "persiapan" THEN 1 END) as persiapan,
            COUNT(CASE WHEN status = "aktif" THEN 1 END) as aktif,
            COUNT(CASE WHEN status = "selesai" THEN 1 END) as selesai,
            SUM(jumlah_ayam_awal) as total_ayam_produksi,
            SUM(total_telur_produksi) as total_telur_dihasilkan
        ');
        $this->db->from('kos_produksi');
        $this->db->where('YEAR(tanggal_mulai)', date('Y'));
        $result['yearly'] = $this->db->get()->row();
        
        // Daily production statistics (last 30 days)
        if ($this->db->table_exists('produksi_harian')) {
            $this->db->select('
                DATE(tanggal) as tanggal,
                SUM(jumlah_telur) as total_telur,
                SUM(konsumsi_pakan) as total_pakan,
                SUM(jumlah_mati) as total_kematian,
                COUNT(DISTINCT id_produksi) as aktif_kandang
            ');
            $this->db->from('produksi_harian');
            $this->db->where('tanggal >=', date('Y-m-d', strtotime('-30 days')));
            $this->db->group_by('DATE(tanggal)');
            $this->db->order_by('tanggal', 'DESC');
            $result['daily_trend'] = $this->db->get()->result();
        }
        
        // Performance by kandang
        $this->db->select('
            k.nama as nama_kandang,
            COUNT(p.id_produksi) as total_periode_produksi,
            AVG(p.total_telur_produksi) as rata_telur_per_periode,
            SUM(p.total_telur_produksi) as total_telur,
            AVG(CASE WHEN p.jumlah_ayam_awal > 0 THEN p.total_telur_produksi/p.jumlah_ayam_awal ELSE 0 END) as rata_telur_per_ayam
        ');
        $this->db->from('kos_kandang k');
        $this->db->join('kos_produksi p', 'p.id_kandang = k.id_kandang', 'left');
        $this->db->where('p.tanggal_mulai >=', date('Y-01-01'));
        $this->db->group_by('k.id_kandang');
        $this->db->order_by('total_telur', 'DESC');
        $result['kandang_performance'] = $this->db->get()->result();
        
        return $result;
    }

    /**
     * Get complete workflow history for a batch
     */
    public function get_complete_workflow($batch_penetasan)
    {
        $workflow = [];
        
        // Get penetasan data
        $this->db->select('*');
        $this->db->from('kos_penetasan');
        $this->db->where('batch', $batch_penetasan);
        $workflow['penetasan'] = $this->db->get()->row();
        
        // Get pembesaran data
        $this->db->select('*');
        $this->db->from('kos_pembesaran');
        $this->db->where('batch_penetasan', $batch_penetasan);
        $workflow['pembesaran'] = $this->db->get()->row();
        
        // Get produksi data
        $this->db->select('*');
        $this->db->from('kos_produksi');
        $this->db->where('batch_penetasan', $batch_penetasan);
        $workflow['produksi'] = $this->db->get()->row();
        
        // Get workflow logs
        if ($this->db->table_exists('workflow_logs')) {
            $this->db->select('wl.*, u.nama_lengkap as user_name');
            $this->db->from('workflow_logs wl');
            $this->db->join('users u', 'u.id = wl.user_id', 'left');
            $this->db->join('kos_penetasan p', 'p.id_penetasan = wl.entity_id AND wl.stage = "penetasan"', 'left');
            $this->db->join('kos_pembesaran pb', 'pb.id_pembesaran = wl.entity_id AND wl.stage = "pembesaran"', 'left');
            $this->db->join('kos_produksi pr', 'pr.id_produksi = wl.entity_id AND wl.stage = "produksi"', 'left');
            $this->db->where('(p.batch = "' . $batch_penetasan . '" OR pb.batch_penetasan = "' . $batch_penetasan . '" OR pr.batch_penetasan = "' . $batch_penetasan . '")');
            $this->db->order_by('wl.created_at', 'ASC');
            $workflow['timeline'] = $this->db->get()->result();
        }
        
        return $workflow;
    }

    /**
     * Create workflow log entry
     */
    private function create_workflow_log($entity_id, $stage, $action, $description)
    {
        if (!$this->db->table_exists('workflow_logs')) {
            return false;
        }
        
        $log_data = [
            'entity_id' => $entity_id,
            'stage' => $stage,
            'action' => $action,
            'description' => $description,
            'user_id' => $this->session->userdata('user_id') ?? 0,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert('workflow_logs', $log_data);
    }

    /**
     * Get production efficiency metrics
     */
    public function get_efficiency_metrics($produksi_id)
    {
        $produksi = $this->get_produksi_by_id($produksi_id);
        $summary = $this->get_daily_summary($produksi_id);
        
        if (!$produksi || !$summary) {
            return null;
        }
        
        $metrics = [
            'hen_day_production' => $produksi->jumlah_ayam_awal > 0 ? 
                ($summary->rata_telur_harian / $produksi->jumlah_ayam_awal) * 100 : 0,
            'feed_conversion_ratio' => $summary->total_telur > 0 ? 
                $summary->total_pakan / $summary->total_telur : 0,
            'mortality_rate' => $produksi->jumlah_ayam_awal > 0 ? 
                ($summary->total_kematian / $produksi->jumlah_ayam_awal) * 100 : 0,
            'production_days' => $summary->total_hari_produksi,
            'eggs_per_hen' => $produksi->jumlah_ayam_awal > 0 ? 
                $summary->total_telur / $produksi->jumlah_ayam_awal : 0
        ];
        
        return $metrics;
    }

    // Get statistics untuk dashboard
    public function get_statistik()
    {
        // Total telur hari ini
        $total_telur_today = $this->db->select('SUM(jumlah) as total')
            ->from('kos_produksi')
            ->where('DATE(tanggal)', date('Y-m-d'))
            ->where('jenis_produksi', 'telur')
            ->get()->row();

        // Total berat hari ini
        $total_berat_today = $this->db->select('SUM(berat) as total')
            ->from('kos_produksi')
            ->where('DATE(tanggal)', date('Y-m-d'))
            ->get()->row();

        // Total ayam hidup
        $total_ayam_hidup = $this->db->select('SUM(jumlah_ayam_saat_ini) as total')
            ->from('kos_produksi')
            ->where('status', 'aktif')
            ->get()->row();

        // Total kandang aktif
        $total_kandang = $this->db->select('COUNT(*) as total')
            ->from('kos_kandang')
            ->where('status', 'aktif')
            ->where('tipe', 'produksi')
            ->get()->row();

        // Rata-rata produksi telur per hari (30 hari terakhir)
        $rata_telur = $this->db->select('AVG(jumlah) as rata')
            ->from('kos_produksi')
            ->where('tanggal >=', date('Y-m-d', strtotime('-30 days')))
            ->where('jenis_produksi', 'telur')
            ->get()->row();

        return (object)[
            'total_telur_hari_ini' => $total_telur_today->total ?? 0,
            'total_berat_hari_ini' => $total_berat_today->total ?? 0,
            'total_ayam_hidup' => $total_ayam_hidup->total ?? 0,
            'total_kandang_aktif' => $total_kandang->total ?? 0,
            'rata_telur_harian' => round($rata_telur->rata ?? 0, 1)
        ];
    }

    /**
     * Get total telur hari ini
     */
    public function get_telur_hari_ini()
    {
        $this->db->select('SUM(jumlah) as total');
        $this->db->from('kos_produksi');
        $this->db->where('jenis_produksi', 'telur');
        $this->db->where('DATE(tanggal)', date('Y-m-d'));
        $result = $this->db->get()->row();
        return $result ? $result->total : 0;
    }

    /**
     * Get total berat produksi
     */
    public function get_total_berat()
    {
        $this->db->select('SUM(berat) as total');
        $this->db->from('kos_produksi');
        $this->db->where('status !=', 'deleted');
        $result = $this->db->get()->row();
        return $result ? $result->total : 0;
    }

    /**
     * Get total produksi
     */
    public function get_total_produksi()
    {
        $this->db->select('COUNT(*) as total');
        $this->db->from('kos_produksi');
        $this->db->where('status !=', 'deleted');
        $result = $this->db->get()->row();
        return $result ? $result->total : 0;
    }

    /**
     * Get data produksi by id dengan join kandang
     */
    public function get_by_id_with_kandang($id)
    {
        $this->db->select('p.*, k.nama as nama_kandang');
        $this->db->from('kos_produksi p');
        $this->db->join('kos_kandang k', 'p.id_kandang = k.id_kandang', 'left');
        $this->db->where('p.id_produksi', $id);
        return $this->db->get()->row();
    }
}
