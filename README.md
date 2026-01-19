# VigazaFarm — Integrated Quail Agribusiness DSS

![Laravel](https://img.shields.io/badge/Laravel-12.0-red?style=flat&logo=laravel)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.0-38B2AC?style=flat&logo=tailwind-css)
![Python](https://img.shields.io/badge/Python-3.x-blue?style=flat&logo=python)
![Status](https://img.shields.io/badge/Status-Active-success)

**VigazaFarm** adalah platform **Decision Support System (DSS)** terintegrasi berbasis web yang dirancang untuk mendukung operasional agribisnis peternakan burung puyuh. Sistem ini mendigitalkan pencatatan (recording), memonitor kondisi kandang secara real-time, dan memberikan rekomendasi keputusan berbasis data menggunakan pendekatan *Config-Driven Rule-Based* dan *Machine Learning*.

Project ini dikembangkan sebagai Tugas Akhir Program Diploma Universitas Amikom Yogyakarta.

---

## 🚀 Fitur Utama

### 1. Manajemen Operasional Terintegrasi
* **Penetasan (Hatching):** Pencatatan batch telur masuk, update status (incubating/hatching), dan transfer DOQ.
* **Pembesaran (Brooding):** Recording harian pakan, kematian (mortalitas), dan kondisi lingkungan untuk fase 0-42 hari.
* **Produksi (Laying):** Monitoring harian produksi telur, hitung *Hen Day Production* (HDP), dan deteksi puyuh afkir.

### 2. Decision Support System (DSS)
* **Rule-Based Logic:** Memberikan status kandang otomatis (**Normal**, **Warning**, **Critical**) berdasarkan threshold yang bisa dikonfigurasi (misal: batas kematian > 1%).
* **Smart Recommendations:** Saran tindakan teknis otomatis saat kondisi kandang memburuk.

### 3. Machine Learning Integration
* **Prediksi Produksi:** Estimasi hasil telur di masa depan menggunakan model regresi berbasis data historis.
* **Optimasi Pakan:** Rekomendasi takaran pakan ideal untuk efisiensi biaya (FCR).
* *(Script Python tersedia di folder `ml/`)*

### 4. Dashboard & Reporting
* **Visualisasi Data:** Grafik tren interaktif (ApexCharts) untuk produksi, mortalitas, dan pakan.
* **Laporan Otomatis:** Generate laporan harian/bulanan siap cetak (PDF/Excel).
* **Role Management:** Akses khusus untuk **Owner** (Full Access + Config) dan **Operator** (Input Data Harian).

---

## 🛠️ Teknologi yang Digunakan

**Backend & Database:**
* Laravel 12 (PHP ^8.2)
* MySQL Database

**Frontend:**
* Vite 7
* Tailwind CSS 3 (+ Forms Plugin)
* Alpine.js (Interaktivitas ringan)
* ApexCharts (Visualisasi Grafik)

**Data Science & ML:**
* Python 3.x
* Pandas, Scikit-learn
* Jupyter Notebooks

---

## 👥 Tim Pengembang

Project ini disusun oleh tim mahasiswa D3 Manajemen Informatika, Universitas Amikom Yogyakarta (2025):

| NIM | Nama | Peran |
| :--- | :--- | :--- |
| **23.02.1020** | **Bolopa Kakungnge Walinono** | **Fullstack Developer**, System Analyst, Backend, Frontend & Database Engineer |
| **23.02.1034** | **Muhammad Fathurrizqi** | UI/UX Designer, Frontend & Database Engineer |
| **23.02.0954** | **Laurel Nanda Bintaryan** | QA Engineer & Documentation (Laporan) |

---

## ⚙️ Instalasi & Menjalankan Project

Ikuti langkah berikut untuk menjalankan project di lokal komputer:

### Prasyarat
* PHP >= 8.2
* Composer
* Node.js & NPM
* Python 3 (untuk fitur ML)
* MySQL

### Langkah Instalasi

1.  **Clone Repositori**
    ```bash
    git clone [https://github.com/username/vigazafarm.git](https://github.com/username/vigazafarm.git)
    cd vigazafarm
    ```

2.  **Install Dependensi (Backend & Frontend)**
    ```bash
    composer install
    npm install
    ```

3.  **Setup Environment**
    Salin file `.env.example` menjadi `.env` dan sesuaikan konfigurasi database.
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4.  **Database Migration & Seeding**
    Buat database di MySQL, lalu jalankan migrasi dan seeder untuk data awal (Akun Admin & Konfigurasi Dasar).
    ```bash
    php artisan migrate --seed
    ```

5.  **Setup Python (Opsional untuk ML)**
    Masuk ke folder `ml/` dan install requirements.
    ```bash
    cd ml
    pip install -r requirements.txt
    cd ..
    ```

### Menjalankan Aplikasi

Gunakan perintah kustom yang telah disiapkan untuk menjalankan server Laravel, Queue, dan Vite secara bersamaan:

```bash
composer dev
