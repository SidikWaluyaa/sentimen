# Sistem Analisis Sentimen ShopeeFood — Laravel 12 & Pure PHP Naive Bayes

Aplikasi web analitik tingkat enterprise untuk **Analisis Sentimen Keluhan Pengguna Terhadap Fitur Pesan Antar Makanan (ShopeeFood) pada Aplikasi Shopee Menggunakan Algoritma Multinomial Naive Bayes dan Pembobotan TF-IDF**.

Dikembangkan berdasarkan penelitian skripsi:
* **Peneliti**: Ariq Fauzan Adiwiria (NIM: 22110297)
* **Institusi**: Program Studi Teknik Informatika — STMIK Mardira Indonesia
* **Tahun**: 2026

---

## 🌟 Fitur Utama Sistem

1. **Pure PHP Machine Learning Inference (Zero Python Dependency in Production)**:
   - Pembobotan **TF-IDF Vectorizer** (1.522 kosakata) dihitung menggunakan komputasi matriks native PHP.
   - Algoritma **Multinomial Naive Bayes** dengan Laplace Smoothing ($\alpha = 1.0$) berjalan murni di PHP dengan Softmax probability scoring.
   - Paritas hasil inferensi **100% identik** dengan Scikit-Learn Python.
2. **Text Preprocessing Bahasa Indonesia Otomatis**:
   - Case folding, regex cleansing (URL, mention, hashtag, tanda baca, angka).
   - Normalisasi bahasa gaul / singkatan lokal (*slang dictionary*).
   - Stopword removal bahasa Indonesia resmi via Sastrawi PHP.
   - Stemming algoritma Nazief-Adriani resmi via Sastrawi PHP.
3. **Multi-Role Authentication & Access Control (Laravel Breeze)**:
   - **Administrator**: Akses penuh ke dashboard, kelola dataset, ekspor PDF/CSV, dan manajemen pengguna (*User Management*).
   - **Data Analyst**: Akses dashboard, upload dataset ulasan baru, uji kalimat tunggal, dan ekspor laporan.
4. **Modern Executive Dark Glassmorphism UI**:
   - Tata letak **Sidebar Vertikal Kiri (Fixed Desktop & Slide-Over Mobile Drawer)**.
   - Logo vektor khusus **AI-ShopeeFood** dengan palet gradien khas Shopee (`#ee4d2d` ke `#ff7337`).
   - Kartu Metrik KPI bercahaya (Total Ulasan, Akurasi, Presisi, Recall, F1-Score).
   - Visualisasi interaktif Chart.js (Doughnut Chart, Bar Chart, Voice of Customer Word Cloud tags).
5. **Manajemen Dataset Fleksibel**:
   - Unggah berkas Excel (`.xlsx`) dan `.csv` baru secara mandiri.
   - Otomatis mengeliminasi rating bintang 3 (netral) dan melabeli biner (rating 1-2 = Negatif, 4-5 = Positif).
6. **Ekspor Laporan Resmi**:
   - **Unduh PDF**: Laporan formal A4 dengan tata letak rapi, rekapitulasi dataset, tabel metrik evaluasi, dan rating bintang emas bebas bug.
   - **Unduh CSV**: Dataset hasil prediksi lengkap dengan encoding UTF-8 BOM untuk kompatibilitas sempurna Microsoft Excel.
7. **Simulasi Prediksi Ulasan Real-Time**:
   - Pengujian kalimat keluhan atau ulasan pelanggan secara langsung dengan indikator skor keyakinan (*confidence level*).
8. **Zona Waktu WIB (Asia/Jakarta, UTC+7)**:
   - Jam digital real-time berdetik aktif di navbar dan seluruh timestamp berstandar WIB.

---

## 📊 Hasil Evaluasi Model Naive Bayes

Berdasarkan dataset utama sebanyak **1.313 ulasan valid ShopeeFood**:
* **Akurasi (Accuracy)**: `82,71%` (Skripsi: `82,5%`)
* **Presisi (Precision)**: `82,74%` (Skripsi: `82,6%`)
* **Recall (Sensitivitas)**: `84,78%` (Skripsi: `84,5%`)
* **F1-Score**: `83,75%` (Skripsi: `83,5%`)
* **Distribusi Sentimen**: `53,8% Positif` (706 ulasan) dan `46,2% Negatif` (607 ulasan)

---

## 🚀 Panduan Instalasi & Menjalankan Sistem

### 1. Kebutuhan Sistem (Prerequisites)
* PHP 8.2 atau lebih baru (ekstensi: `pdo_mysql`, `mbstring`, `zip`, `xml`, `bcmath`)
* MySQL / MariaDB (via XAMPP, Laragon, atau MySQL Server standalone)
* Composer 2.x
* Node.js 18+ & NPM (opsional, karena file build frontend sudah tersedia)

### 2. Kloning Repositori
```bash
git clone https://github.com/SidikWaluyaa/sentimen.git
cd sentimen
```

### 3. Konfigurasi Lingkungan (`.env`)
File `.env` sudah disertakan langsung di repositori dengan konfigurasi siap pakai:
```env
APP_NAME="Shopee Sentiment Analysis"
APP_ENV=local
APP_KEY=base64:LZoYLRmsKyY2k0vKw4Cp6qi1VU0iTZi0kocZYE4YGlg=
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_sentimen_shopee
DB_USERNAME=root
DB_PASSWORD=
```
> *Catatan: Jika password MySQL Anda tidak kosong, sesuaikan nilai `DB_PASSWORD` pada file `.env`.*

### 4. Install Dependensi PHP
```bash
composer install
```

### 5. Setup Database & Seeding Data Awal
Pastikan service MySQL Anda sudah aktif, lalu jalankan:
```bash
# Buat database jika belum ada
mysql -u root -e "CREATE DATABASE IF NOT EXISTS db_sentimen_shopee;"

# Jalankan migrasi dan seeder awal (1.313 ulasan ShopeeFood)
php artisan migrate:fresh --seed
```

### 6. Build Aset Frontend (Opsional)
Aset CSS & JS hasil kompilasi sudah tersedia di folder `public/build/`. Namun jika Anda mengubah styling, Anda dapat mengompilasi ulang:
```bash
npm install
npm run build
```

### 7. Jalankan Server Aplikasi
```bash
php artisan serve
```
Akses aplikasi melalui browser di: **`http://127.0.0.1:8000`**

---

## 🔐 Kredensial Akun Bawaan

| Role | Email | Password | Hak Akses |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@sentiment.id` | `password123` | Akses penuh: Dashboard, Upload, Simulasi, Kelola Pengguna, Ekspor PDF/CSV, Hapus Data |
| **Data Analyst** | `analyst@sentiment.id` | `password123` | Dashboard, Upload Dataset, Simulasi Ulasan, Ekspor PDF/CSV |

---

## 🧪 Menjalankan Pengujian Otomatis (TDD)

Sistem dilengkapi **36 unit & feature test suite** dengan 152 assertions untuk menjamin integritas kalkulasi, paritas prediksi Naive Bayes, proteksi role, dan zona waktu WIB:
```bash
php artisan test
```

---

## 📁 Struktur Direktori Penting

```
shopee_sentiment/
├── app/
│   ├── Http/Controllers/       # Controller Dashboard, Dataset, Simulasi, Report, Admin
│   ├── Models/                 # User, Evaluation, PredictionResult
│   └── Services/
│       ├── Dataset/            # ExcelReaderService (Pure PHP ZipArchive & SimpleXML)
│       └── NLP/                # IndonesianTextPreprocessor, TfidfVectorizer,
│                               # NaiveBayesClassifier, SentimentPipeline
├── config/                     # Konfigurasi Laravel & Timezone (Asia/Jakarta)
├── database/
│   ├── data/                   # File dataset awal KANDIDAT_VALIDASI_SHOPEEFOOD_FINAL.xlsx
│   ├── migrations/             # Schema tabel users, evaluations, prediction_results
│   └── seeders/                # DatabaseSeeder otomatis seeding 1.313 ulasan
├── legacy_streamlit/           # Arsip prototipe lama Python Streamlit
├── resources/
│   ├── css/                    # Tailwind CSS custom styling & dark aurora glassmorphism
│   └── views/                  # Blade templates (Dashboard, Upload, Simulasi, PDF, Auth)
├── storage/app/ml/             # ml_model.json (Bobot TF-IDF & Naive Bayes)
└── tests/                      # Rangkaian pengujian TDD PHPUnit/Pest
```

---

## 📄 Lisensi
Dikembangkan untuk keperluan akademis Program Sarjana Teknik Informatika STMIK Mardira Indonesia. Lisensi di bawah [MIT License](LICENSE).
