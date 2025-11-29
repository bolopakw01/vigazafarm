-- ERD Schema untuk Decision Support System Monitoring Agribisnis Burung Puyuh
-- VigazaFarm Database Schema
-- Date: 2025-10-01
-- Version: 2.0

-- ===========================================
-- TABEL MASTER
-- ===========================================

-- Tabel Pengguna
CREATE TABLE pengguna (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(255) NOT NULL,
    nama_pengguna VARCHAR(255) UNIQUE NOT NULL,
    surel VARCHAR(255) UNIQUE NOT NULL,
    kata_sandi VARCHAR(255) NOT NULL,
    peran ENUM('owner', 'operator') DEFAULT 'operator',
    foto_profil VARCHAR(255),
    surel_terverifikasi_pada TIMESTAMP NULL,
    token_ingat VARCHAR(100),
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    diperbarui_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nama_pengguna (nama_pengguna),
    INDEX idx_surel (surel)
);

-- Tabel Kandang
CREATE TABLE kandang (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    kode_kandang VARCHAR(50) UNIQUE NOT NULL,
    nama_kandang VARCHAR(100) NOT NULL,
    kapasitas_maksimal INT NOT NULL,
    tipe_kandang ENUM('penetasan', 'pembesaran', 'produksi', 'karantina') NOT NULL,
    status ENUM('aktif', 'maintenance', 'kosong') DEFAULT 'aktif',
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    INDEX idx_kode_kandang (kode_kandang),
    INDEX idx_tipe_kandang (tipe_kandang),
    INDEX idx_status (status)
);

-- Tabel Batch Produksi
CREATE TABLE batch_produksi (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    kode_batch VARCHAR(50) UNIQUE NOT NULL,
    kandang_id BIGINT NOT NULL,
    tanggal_mulai DATE NOT NULL,
    tanggal_akhir DATE,
    jumlah_awal INT NOT NULL,
    jumlah_saat_ini INT,
    fase ENUM('DOQ', 'grower', 'layer', 'afkir') DEFAULT 'DOQ',
    status ENUM('aktif', 'selesai', 'dibatalkan') DEFAULT 'aktif',
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (kandang_id) REFERENCES kandang(id) ON DELETE RESTRICT,
    INDEX idx_kode_batch (kode_batch),
    INDEX idx_kandang_id (kandang_id),
    INDEX idx_fase (fase),
    INDEX idx_status (status),
    INDEX idx_tanggal_mulai (tanggal_mulai)
);

-- Tabel Stok Pakan
CREATE TABLE stok_pakan (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    kode_pakan VARCHAR(50) UNIQUE NOT NULL,
    nama_pakan VARCHAR(100) NOT NULL,
    jenis_pakan VARCHAR(50) NOT NULL,
    merek VARCHAR(100),
    harga_per_kg DECIMAL(10,2) NOT NULL,
    stok_kg DECIMAL(10,2) DEFAULT 0,
    stok_karung INT DEFAULT 0,
    berat_per_karung DECIMAL(8,2) DEFAULT 50,
    tanggal_kadaluarsa DATE,
    supplier VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    INDEX idx_kode_pakan (kode_pakan),
    INDEX idx_jenis_pakan (jenis_pakan),
    INDEX idx_tanggal_kadaluarsa (tanggal_kadaluarsa)
);

-- ===========================================
-- TABEL TRANSAKSI OPERASIONAL
-- ===========================================

-- Tabel Penetasan
CREATE TABLE penetasan (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    batch VARCHAR(50),
    kandang_id BIGINT,
    tanggal_simpan_telur DATE NOT NULL,
    estimasi_tanggal_menetas DATE,
    tanggal_masuk_hatcher DATE,
    jumlah_telur INT NOT NULL,
    tanggal_menetas DATE,
    jumlah_menetas INT,
    jumlah_doc INT,
    suhu_penetasan DECIMAL(5,2),
    kelembaban_penetasan DECIMAL(5,2),
    telur_tidak_fertil INT,
    persentase_tetas DECIMAL(5,2),
    catatan TEXT,
    status ENUM('proses', 'selesai', 'gagal') DEFAULT 'proses',
    fase_penetasan ENUM('setter', 'hatcher') DEFAULT 'setter',
    doc_ditransfer INT DEFAULT 0,
    telur_infertil_ditransfer INT DEFAULT 0,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    diperbarui_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kandang_id) REFERENCES kandang(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES pengguna(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES pengguna(id) ON DELETE SET NULL,
    INDEX idx_batch_penetasan (batch),
    INDEX idx_tanggal_simpan (tanggal_simpan_telur),
    INDEX idx_kandang_id (kandang_id),
    INDEX idx_status_penetasan (status),
    INDEX idx_fase_penetasan (fase_penetasan)
);

-- Tabel Pembesaran
CREATE TABLE pembesaran (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    kandang_id BIGINT,
    batch_produksi_id BIGINT,
    tanggal_masuk DATE NOT NULL,
    jumlah_anak_ayam INT NOT NULL,
    jenis_kelamin ENUM('betina', 'jantan'),
    tanggal_siap DATE,
    jumlah_siap INT,
    umur_hari INT,
    berat_rata_rata DECIMAL(8,2),
    catatan TEXT,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    diperbarui_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kandang_id) REFERENCES kandang(id) ON DELETE SET NULL,
    FOREIGN KEY (batch_produksi_id) REFERENCES batch_produksi(id) ON DELETE SET NULL,
    INDEX idx_tanggal_masuk (tanggal_masuk),
    INDEX idx_batch_id (batch_produksi_id)
);

-- Tabel Produksi
CREATE TABLE produksi (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    kandang_id BIGINT,
    batch_produksi_id BIGINT,
    tanggal_mulai DATE NOT NULL,
    jumlah_indukan INT NOT NULL,
    umur_mulai_produksi INT,
    tanggal_akhir DATE,
    status ENUM('aktif', 'selesai') DEFAULT 'aktif',
    catatan TEXT,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    diperbarui_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kandang_id) REFERENCES kandang(id) ON DELETE SET NULL,
    FOREIGN KEY (batch_produksi_id) REFERENCES batch_produksi(id) ON DELETE SET NULL,
    INDEX idx_tanggal_mulai (tanggal_mulai),
    INDEX idx_batch_id (batch_produksi_id),
    INDEX idx_status (status)
);

-- Tabel Telur
CREATE TABLE telur (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    produksi_id BIGINT NOT NULL,
    batch_produksi_id BIGINT,
    tanggal DATE NOT NULL,
    jumlah INT NOT NULL,
    telur_grade_a INT,
    telur_grade_b INT,
    telur_grade_c INT,
    telur_retak INT,
    berat_rata_rata DECIMAL(5,2),
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    diperbarui_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (produksi_id) REFERENCES produksi(id) ON DELETE CASCADE,
    FOREIGN KEY (batch_produksi_id) REFERENCES batch_produksi(id) ON DELETE SET NULL,
    INDEX idx_tanggal (tanggal),
    INDEX idx_produksi_id (produksi_id),
    INDEX idx_batch_id (batch_produksi_id)
);

-- Tabel Pakan
CREATE TABLE pakan (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    produksi_id BIGINT NOT NULL,
    stok_pakan_id BIGINT,
    batch_produksi_id BIGINT,
    tanggal DATE NOT NULL,
    jumlah_kg DECIMAL(8,2),
    jumlah_karung INT,
    harga_per_kg DECIMAL(10,2),
    total_biaya DECIMAL(12,2),
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    diperbarui_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (produksi_id) REFERENCES produksi(id) ON DELETE CASCADE,
    FOREIGN KEY (stok_pakan_id) REFERENCES stok_pakan(id) ON DELETE SET NULL,
    FOREIGN KEY (batch_produksi_id) REFERENCES batch_produksi(id) ON DELETE SET NULL,
    INDEX idx_tanggal (tanggal),
    INDEX idx_produksi_id (produksi_id),
    INDEX idx_batch_id (batch_produksi_id)
);

-- Tabel Kematian
CREATE TABLE kematian (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    produksi_id BIGINT NOT NULL,
    batch_produksi_id BIGINT,
    tanggal DATE NOT NULL,
    jumlah INT NOT NULL,
    penyebab ENUM('penyakit', 'stress', 'kecelakaan', 'usia', 'tidak_diketahui'),
    keterangan TEXT,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    diperbarui_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (produksi_id) REFERENCES produksi(id) ON DELETE CASCADE,
    FOREIGN KEY (batch_produksi_id) REFERENCES batch_produksi(id) ON DELETE SET NULL,
    INDEX idx_tanggal (tanggal),
    INDEX idx_produksi_id (produksi_id),
    INDEX idx_penyebab (penyebab)
);

-- Tabel Transaksi Pakan
CREATE TABLE transaksi_pakan (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    stok_pakan_id BIGINT NOT NULL,
    batch_produksi_id BIGINT,
    tipe_transaksi ENUM('pembelian', 'penggunaan', 'penyesuaian', 'pengembalian') NOT NULL,
    tanggal DATE NOT NULL,
    jumlah_kg DECIMAL(10,2) NOT NULL,
    jumlah_karung INT,
    harga_total DECIMAL(12,2),
    keterangan TEXT,
    pengguna_id BIGINT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (stok_pakan_id) REFERENCES stok_pakan(id) ON DELETE RESTRICT,
    FOREIGN KEY (batch_produksi_id) REFERENCES batch_produksi(id) ON DELETE SET NULL,
    FOREIGN KEY (pengguna_id) REFERENCES pengguna(id) ON DELETE RESTRICT,
    INDEX idx_tanggal (tanggal),
    INDEX idx_tipe_transaksi (tipe_transaksi),
    INDEX idx_stok_pakan_id (stok_pakan_id)
);

-- Tabel Kesehatan
CREATE TABLE kesehatan (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    batch_produksi_id BIGINT NOT NULL,
    tanggal DATE NOT NULL,
    tipe_kegiatan ENUM('vaksinasi', 'pengobatan', 'pemeriksaan_rutin', 'karantina') NOT NULL,
    nama_vaksin_obat VARCHAR(100),
    jumlah_burung INT,
    catatan TEXT,
    biaya DECIMAL(10,2),
    petugas VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (batch_produksi_id) REFERENCES batch_produksi(id) ON DELETE CASCADE,
    INDEX idx_tanggal (tanggal),
    INDEX idx_tipe_kegiatan (tipe_kegiatan),
    INDEX idx_batch_id (batch_produksi_id)
);

-- ===========================================
-- TABEL KEUANGAN & PENJUALAN
-- ===========================================

-- Tabel Keuangan
CREATE TABLE keuangan (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tanggal DATE NOT NULL,
    kategori ENUM('pemasukan', 'pengeluaran') NOT NULL,
    jenis ENUM('penjualan_telur', 'penjualan_burung', 'pembelian_pakan', 'pembelian_bibit', 
               'pembelian_obat', 'pembelian_peralatan', 'gaji_karyawan', 'listrik_air', 
               'maintenance', 'lainnya') NOT NULL,
    jumlah DECIMAL(12,2) NOT NULL,
    batch_produksi_id BIGINT,
    keterangan TEXT,
    nomor_bukti VARCHAR(50),
    pengguna_id BIGINT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (batch_produksi_id) REFERENCES batch_produksi(id) ON DELETE SET NULL,
    FOREIGN KEY (pengguna_id) REFERENCES pengguna(id) ON DELETE RESTRICT,
    INDEX idx_tanggal (tanggal),
    INDEX idx_kategori (kategori),
    INDEX idx_jenis (jenis)
);

-- Tabel Penjualan Telur
CREATE TABLE penjualan_telur (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    kode_transaksi VARCHAR(50) UNIQUE NOT NULL,
    tanggal DATE NOT NULL,
    batch_produksi_id BIGINT,
    jumlah_butir INT NOT NULL,
    harga_per_butir DECIMAL(8,2) NOT NULL,
    total_harga DECIMAL(12,2) NOT NULL,
    pembeli VARCHAR(100),
    kontak_pembeli VARCHAR(50),
    status_pembayaran ENUM('lunas', 'belum_lunas', 'cicilan') DEFAULT 'lunas',
    catatan TEXT,
    pengguna_id BIGINT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (batch_produksi_id) REFERENCES batch_produksi(id) ON DELETE SET NULL,
    FOREIGN KEY (pengguna_id) REFERENCES pengguna(id) ON DELETE RESTRICT,
    INDEX idx_kode_transaksi (kode_transaksi),
    INDEX idx_tanggal (tanggal),
    INDEX idx_status_pembayaran (status_pembayaran)
);

-- Tabel Penjualan Burung
CREATE TABLE penjualan_burung (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    kode_transaksi VARCHAR(50) UNIQUE NOT NULL,
    tanggal DATE NOT NULL,
    batch_produksi_id BIGINT,
    kategori ENUM('DOQ', 'grower', 'layer', 'afkir', 'jantan') NOT NULL,
    jumlah_ekor INT NOT NULL,
    berat_rata_rata DECIMAL(8,2),
    harga_per_ekor DECIMAL(10,2) NOT NULL,
    total_harga DECIMAL(12,2) NOT NULL,
    pembeli VARCHAR(100),
    kontak_pembeli VARCHAR(50),
    status_pembayaran ENUM('lunas', 'belum_lunas', 'cicilan') DEFAULT 'lunas',
    catatan TEXT,
    pengguna_id BIGINT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (batch_produksi_id) REFERENCES batch_produksi(id) ON DELETE SET NULL,
    FOREIGN KEY (pengguna_id) REFERENCES pengguna(id) ON DELETE RESTRICT,
    INDEX idx_kode_transaksi (kode_transaksi),
    INDEX idx_tanggal (tanggal),
    INDEX idx_kategori (kategori)
);

-- ===========================================
-- TABEL MONITORING & DSS
-- ===========================================

-- Tabel Monitoring Lingkungan
CREATE TABLE monitoring_lingkungan (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    kandang_id BIGINT NOT NULL,
    batch_produksi_id BIGINT,
    waktu_pencatatan DATETIME NOT NULL,
    suhu DECIMAL(5,2),
    kelembaban DECIMAL(5,2),
    intensitas_cahaya DECIMAL(8,2),
    kondisi_ventilasi VARCHAR(50),
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kandang_id) REFERENCES kandang(id) ON DELETE CASCADE,
    FOREIGN KEY (batch_produksi_id) REFERENCES batch_produksi(id) ON DELETE SET NULL,
    INDEX idx_waktu_pencatatan (waktu_pencatatan),
    INDEX idx_kandang_id (kandang_id)
);

-- Tabel Parameter Standar
CREATE TABLE parameter_standar (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    fase ENUM('DOQ', 'grower', 'layer') NOT NULL,
    parameter VARCHAR(100) NOT NULL,
    nilai_minimal DECIMAL(10,2),
    nilai_optimal DECIMAL(10,2),
    nilai_maksimal DECIMAL(10,2),
    satuan VARCHAR(20),
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_fase (fase),
    INDEX idx_parameter (parameter)
);

-- Tabel Analisis Rekomendasi
CREATE TABLE analisis_rekomendasi (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    batch_produksi_id BIGINT NOT NULL,
    tanggal_analisis DATE NOT NULL,
    jenis_analisis VARCHAR(100) NOT NULL,
    nilai_aktual DECIMAL(10,2),
    nilai_standar DECIMAL(10,2),
    status ENUM('baik', 'perhatian', 'kritis') DEFAULT 'baik',
    analisis TEXT,
    rekomendasi TEXT,
    prioritas ENUM('rendah', 'sedang', 'tinggi', 'urgent') DEFAULT 'sedang',
    target_tindakan DATE,
    status_tindakan ENUM('pending', 'dalam_proses', 'selesai', 'diabaikan') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (batch_produksi_id) REFERENCES batch_produksi(id) ON DELETE CASCADE,
    INDEX idx_batch_id (batch_produksi_id),
    INDEX idx_tanggal_analisis (tanggal_analisis),
    INDEX idx_status (status),
    INDEX idx_prioritas (prioritas)
);

-- Tabel Laporan Harian
CREATE TABLE laporan_harian (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    batch_produksi_id BIGINT NOT NULL,
    tanggal DATE NOT NULL,
    jumlah_burung INT NOT NULL,
    produksi_telur INT DEFAULT 0,
    jumlah_kematian INT DEFAULT 0,
    konsumsi_pakan_kg DECIMAL(10,2) DEFAULT 0,
    fcr DECIMAL(5,2),
    hen_day_production DECIMAL(5,2),
    mortalitas_kumulatif DECIMAL(5,2),
    catatan_kejadian TEXT,
    pengguna_id BIGINT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (batch_produksi_id) REFERENCES batch_produksi(id) ON DELETE CASCADE,
    FOREIGN KEY (pengguna_id) REFERENCES pengguna(id) ON DELETE RESTRICT,
    INDEX idx_batch_id (batch_produksi_id),
    INDEX idx_tanggal (tanggal),
    UNIQUE KEY unique_batch_tanggal (batch_produksi_id, tanggal)
);

-- Tabel Alert
CREATE TABLE alert (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tipe_alert ENUM('stok_pakan', 'kesehatan', 'produktivitas', 'keuangan', 'lingkungan', 'lainnya') NOT NULL,
    tingkat_urgency ENUM('info', 'warning', 'critical') NOT NULL,
    judul VARCHAR(200) NOT NULL,
    pesan TEXT NOT NULL,
    batch_produksi_id BIGINT,
    kandang_id BIGINT,
    sudah_dibaca BOOLEAN DEFAULT FALSE,
    waktu_dibaca DATETIME,
    pengguna_id BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (batch_produksi_id) REFERENCES batch_produksi(id) ON DELETE CASCADE,
    FOREIGN KEY (kandang_id) REFERENCES kandang(id) ON DELETE CASCADE,
    FOREIGN KEY (pengguna_id) REFERENCES pengguna(id) ON DELETE SET NULL,
    INDEX idx_tipe_alert (tipe_alert),
    INDEX idx_tingkat_urgency (tingkat_urgency),
    INDEX idx_sudah_dibaca (sudah_dibaca),
    INDEX idx_created_at (created_at)
);

-- ===========================================
-- VIEWS UNTUK REPORTING & ANALYTICS
-- ===========================================

-- View: Dashboard Summary per Batch
CREATE VIEW v_dashboard_batch AS
SELECT 
    bp.id,
    bp.kode_batch,
    bp.fase,
    bp.status,
    k.nama_kandang,
    bp.jumlah_awal,
    bp.jumlah_saat_ini,
    DATEDIFF(CURRENT_DATE, bp.tanggal_mulai) as umur_hari,
    COALESCE(SUM(lh.produksi_telur), 0) as total_produksi_telur,
    COALESCE(SUM(lh.jumlah_kematian), 0) as total_kematian,
    COALESCE(AVG(lh.fcr), 0) as fcr_rata_rata,
    COALESCE(AVG(lh.hen_day_production), 0) as hdp_rata_rata
FROM batch_produksi bp
LEFT JOIN kandang k ON bp.kandang_id = k.id
LEFT JOIN laporan_harian lh ON bp.id = lh.batch_produksi_id
GROUP BY bp.id;

-- View: Stok Pakan Summary
CREATE VIEW v_stok_pakan_summary AS
SELECT 
    sp.*,
    CASE 
        WHEN sp.stok_kg < 100 THEN 'Kritis'
        WHEN sp.stok_kg < 500 THEN 'Rendah'
        ELSE 'Aman'
    END as status_stok,
    DATEDIFF(sp.tanggal_kadaluarsa, CURRENT_DATE) as hari_ke_kadaluarsa
FROM stok_pakan sp
WHERE sp.deleted_at IS NULL;

-- View: Analisis Keuangan Bulanan
CREATE VIEW v_keuangan_bulanan AS
SELECT 
    DATE_FORMAT(tanggal, '%Y-%m') as bulan,
    SUM(CASE WHEN kategori = 'pemasukan' THEN jumlah ELSE 0 END) as total_pemasukan,
    SUM(CASE WHEN kategori = 'pengeluaran' THEN jumlah ELSE 0 END) as total_pengeluaran,
    SUM(CASE WHEN kategori = 'pemasukan' THEN jumlah ELSE -jumlah END) as laba_rugi
FROM keuangan
WHERE deleted_at IS NULL
GROUP BY DATE_FORMAT(tanggal, '%Y-%m')
ORDER BY bulan DESC;
