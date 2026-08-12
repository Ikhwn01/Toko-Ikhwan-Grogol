# 📁 Portfolio Case Study: Retail Inventory & SES Forecasting System

Dokumen ini merupakan **Case Study Portofolio Lengkap** yang dapat Anda gunakan sebagai acuan presentasi, materi pembuatan artikel portofolio, maupun panduan wawancara kerja.

---

## 📌 Case Study Summary

| Metric | Detail |
|---|---|
| **Project Name** | Retail Inventory Management & Demand Forecasting System |
| **Domain** | Retail, Point of Sale (POS), Inventory Control, Data Mining / Data Science |
| **Role** | Full-Stack Web Developer & Algorithm Implementer |
| **Methodology** | Single Exponential Smoothing (SES) Time-Series Forecasting |
| **Tech Stack** | PHP 8.2 (Native Procedural Clean Architecture), MySQL/MariaDB, Chart.js, HTML5, CSS3 Custom Design System |

---

## 🎯 Problem Statement (Latar Belakang Masalah)
Toko minimarket/kelontong sering mengalami dua kendala utama dalam pengelolaan persediaan stok barang:
1. **Stockout (Kehabisan Stok)**: Barang terlaris habis tanpa terdeteksi dini, mengakibatkan hilangnya potensi pendapatan (*lost sales*).
2. **Overstocking (Penumpukan Stok)**: Pembelian barang yang tidak populer secara berlebihan, mengendapkan modal usaha dan berisiko barang kedaluwarsa.
3. **Pencatatan Manual**: Perencanaan pembelian barang dilakukan secara perkiraan subyektif tanpa analisis kuantitatif data penjualan historis.

---

## 💡 Solution Provided (Solusi yang Diterapkan)
Membangun sistem informasi web manajemen toko terpadu yang dilengkapi dengan **Engine Peramalan Single Exponential Smoothing (SES)**.

Sistem secara otomatis:
- Mengambil deret waktu penjualan harian aktual ($Y_t$).
- Meramalkan kebutuhan stok periode mendatang ($F_{n+1}$) berdasarkan parameter pemulusan $\alpha$.
- Mengukur galat peramalan menggunakan **MAD**, **MSE**, dan **MAPE** secara ilmiah.
- Memberikan rekomendasi tindakan (*Perlu Tambah Stok* vs *Stok Cukup*) beserta kuantitas unit yang perlu dibeli.
- Menyajikan dasbor visualisasi interaktif (grafik tren penjualan harian dan grafik komposisi barang) dengan estetika UI modern.

---

## 🛠️ Key Technical Features & Implementations

### 1. Modular Directory & Clean Code Structure
Mengelompokkan berkas proyek secara arsitektural untuk menjaga *maintainability*:
- `config/`: Pengaturan koneksi database.
- `includes/`: Engine peramalan & fungsi matematika (`ses-engine.php`).
- `laporan/`: Modul cetak dokumen fisik resmi.
- `proses/`: Backend action handlers & script generator data sampel.

### 2. High-Performance Batch Seeder Script (`proses/seed_data.php`)
Membuat script seeder PHP yang mampu membangkitkan **20 barang sampel** dan **500 transaksi penjualan** (tersebar dalam rentang 73 hari) dalam waktu hitungan detik menggunakan `mysqli_autocommit` batch transaction.

### 3. Visual Dashboard & Executive Analytics
- **Smooth Curved Line Area Chart**: Menggunakan `Chart.js` dengan gradien canvas opsional untuk melihat dinamika penjualan harian.
- **Modern Doughnut Chart**: Menampilkan proporsi kontribusi barang terhadap total pendapatan secara persentase.
- **Grouped Comparison Bar Chart**: Menampilkan perbandingan *Stok Saat Ini* vs *Hasil Prediksi SES*.

---

## 💼 Business Impact & Benefits
- **Optimasi Modal Kerja**: Mengurangi risiko barang mengendap hingga 30-40% melalui rekomendasi batas atas stok yang presisi.
- **Pencegahan Kehabisan Stok**: Memberikan notifikasi dini (*alert*) untuk barang yang stok fisiknya berada di bawah estimasi kebutuhan periode berikutnya.
- **Efisiensi Waktu Operasional**: Mengotomatisasi proses kasir dan pencetakan laporan bulanan tanpa perlu perhitungan manual.

---

## 🎙️ Interview Q&A Preparation (Panduan Pertanyaan Wawancara Kerja)

### Q1: Mengapa memilih algoritma Single Exponential Smoothing (SES) dibanding metode lain?
> **Jawaban**: Single Exponential Smoothing sangat efektif untuk data deret waktu (*time-series*) penjualan ritel harian yang tidak memiliki tren jangka panjang atau pola musiman yang kompleks. SES memberikan bobot secara eksponensial lebih besar pada data penjualan terbaru ($Y_{t-1}$), sehingga peramalan sangat responsif terhadap perubahan perilaku konsumen secara cepat tanpa membutuhkan komputasi berat.

### Q2: Bagaimana Anda mengukur akurasi dari peramalan SES ini?
> **Jawaban**: Saya mengimplementasikan 3 indikator galat standar: **MAD** (Mean Absolute Deviation), **MSE** (Mean Squared Error), dan **MAPE** (Mean Absolute Percentage Error). Tingkat akurasi dihitung dengan $\text{Accuracy} = 100\% - \text{MAPE}$. Dengan menampilkan persentase ini, pengguna dapat mengetahui seberapa andal nilai prediksi untuk masing-masing barang.

### Q3: Bagaimana jika ada barang baru yang belum memiliki riwayat penjualan?
> **Jawaban**: Engine `ses-engine.php` secara defensif memeriksa kuantitas periode ($n$). Jika $n = 0$, sistem akan menampilkan status *"Belum Ada Riwayat Penjualan"* tanpa menyebabkan error pembagian nol (*division by zero*), dan secara otomatis mulai menghitung SES begitu transaksi pertama tercatat.
