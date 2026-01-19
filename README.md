# VigazaFarm — Integrated Quail Agribusiness DSS

![Laravel](https://img.shields.io/badge/Laravel-12.0-FF2D20?style=flat&logo=laravel&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-7.0.4-646CFF?style=flat&logo=vite&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3.0-7952B3?style=flat&logo=bootstrap&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.1.0-38B2AC?style=flat&logo=tailwind-css&logoColor=white)
![Alpine.js](https://img.shields.io/badge/Alpine.js-3.4.2-8BC0D0?style=flat&logo=alpinedotjs&logoColor=white)
![Python](https://img.shields.io/badge/Python-3.x-3776AB?style=flat&logo=python&logoColor=white)

**VigazaFarm** adalah platform **Decision Support System (DSS)** terintegrasi berbasis web yang dirancang untuk mendukung operasional agribisnis peternakan burung puyuh. Sistem ini mendigitalkan pencatatan (recording), memonitor kondisi kandang secara real-time, dan memberikan rekomendasi keputusan berbasis data menggunakan pendekatan *Config-Driven Rule-Based* dan *Machine Learning*.

Project ini dikembangkan sebagai Tugas Akhir Program Diploma Universitas Amikom Yogyakarta.

---

## 🚀 Fitur Utama

### 1. Manajemen Operasional Terintegrasi
* **Penetasan (Hatching):** Pencatatan batch telur masuk, update status (incubating/hatching), dan transfer DOQ.
* **Pembesaran (Brooding):** Recording harian pakan, kematian (mortalitas), dan kondisi lingkungan untuk fase 0-42 hari.
* **Produksi (Laying):** Monitoring harian produksi telur, hitung *Hen Day Production* (HDP), dan deteksi puyuh afkir.

### 2. Decision Support System (DSS)
* **Rule-Based Logic:** Memberikan status kandang otomatis (**Normal**, **Warning**, **Critical**) berdasarkan threshold yang bisa dikonfigurasi.
* **Smart Recommendations:** Saran tindakan teknis otomatis saat kondisi kandang memburuk.

### 3. Machine Learning Integration
* **Prediksi Produksi:** Estimasi hasil telur di masa depan menggunakan model regresi berbasis data historis.
* **Optimasi Pakan:** Rekomendasi takaran pakan ideal untuk efisiensi biaya (FCR).
* *(Script Python tersedia di folder `ml/`)*

### 4. Dashboard & Reporting
* **Visualisasi Data:** Grafik tren interaktif untuk produksi, mortalitas, dan pakan.
* **Laporan Otomatis:** Generate laporan harian/bulanan siap cetak.

---

## 🛠️ Teknologi yang Digunakan

### Backend
* **Laravel 12** (PHP ^8.2) - Framework utama aplikasi.
* **MySQL Database** - Penyimpanan data relasional.

### Frontend
* **Vite 7.0.4** - Build tool modern untuk bundling aset.
* **Bootstrap 5.3.0** - Framework CSS untuk struktur layout (Grid System: Container, Row, Col).
* **Tailwind CSS 3.1.0** - Utility-first CSS framework untuk styling komponen.
* **Alpine.js 3.4.2** - Framework JavaScript ringan untuk interaktivitas UI.
* **Axios 1.11.0** - HTTP Client untuk request API asinkron.

### Libraries & Plugins
* **ApexCharts** - Visualisasi data grafik interaktif.
* **SweetAlert2** - Notifikasi popup dan alert yang modern.
* **Cropper.js** - Fitur pemotong (crop) gambar sebelum upload.
* **FontAwesome** - Ikon antarmuka pengguna.
* **@tailwindcss/forms** - Plugin untuk styling elemen form yang lebih baik.

### Data Science & ML
* **Python 3.x**
* **Pandas, Scikit-learn**
* **Jupyter Notebooks**

---

## 👥 Tim Pengembang

Project ini disusun oleh tim mahasiswa D3 Manajemen Informatika, Universitas Amikom Yogyakarta (2025):

| NIM | Nama | Peran |
| :--- | :--- | :--- |
| **23.02.1020** | **Bolopa Kakungnge Walinono** | **Fullstack Developer**, System Analyst & Database Engineer |
| **23.02.1034** | **Muhammad Fathurrizqi** | UI/UX Designer, Frontend & Database Support |
| **23.02.0954** | **Laurel Nanda Bintaryan** | QA Engineer, Technical Writer & Documentation |

---

## ⚙️ Instalasi & Menjalankan Project

Ikuti langkah berikut untuk menjalankan project di lokal komputer:

1.  **Clone Repositori**
    ```bash
    git clone [https://github.com/username/vigazafarm.git](https://github.com/username/vigazafarm.git)
    cd vigazafarm
    ```

2.  **Install Dependensi**
    ```bash
    composer install
    npm install
    ```

3.  **Setup Environment**
    ```bash
    cp .env.example .env
    php artisan key:generate
    # Konfigurasi database di file .env
    ```

4.  **Database Migration & Seeding**
    ```bash
    php artisan migrate --seed
    ```

5.  **Menjalankan Aplikasi**
    Gunakan perintah berikut untuk menjalankan Laravel Server, Vite, dan Queue Worker secara bersamaan:
    ```bash
    composer dev
    ```
    Akses aplikasi di: `http://localhost:8000`

---

## 📄 Lisensi

Project ini bersifat *open-source* di bawah lisensi [MIT](https://opensource.org/licenses/MIT).
