# 📐 Architecture & Data Flow Diagrams

Berikut adalah diagram alur arsitektur sistem dan algoritma Single Exponential Smoothing (SES) menggunakan format visual **Mermaid**.

---

## 1. System High-Level Architecture

```mermaid
graph TD
    User(["Pengguna / Admin"]) --> Dashboard["dashboard.php Analytics Dashboard"]
    User --> Barang["data-barang.php Master Barang"]
    User --> Transaksi["transaksi-penjualan.php POS Kasir"]
    User --> Mining["data-mining.php Perhitungan SES"]
    User --> Hasil["hasil-prediksi-stok.php Hasil Prediksi & Rekomendasi"]
    User --> Laporan["laporan.php Menu Cetak Laporan"]

    subgraph Backend["Backend Core Architecture"]
        Config[("config/koneksi.php Database Connection")]
        Engine["includes/ses-engine.php SES Calculation Engine"]
        Proses["proses/ Script Handlers & Seeder"]
    end

    subgraph Database["Database MySQL"]
        DB[("db_toko Database")]
        tbl_barang[("Tabel barang")]
        tbl_trx[("Tabel transaksi_penjualan_multi")]
        tbl_item[("Tabel item_transaksi")]
    end

    Dashboard --> DB
    Transaksi --> Proses
    Proses --> DB
    Mining --> Engine
    Hasil --> Engine
    Engine --> DB
    Laporan --> DB
```

---

## 2. Single Exponential Smoothing (SES) Data Flow

```mermaid
sequenceDiagram
    autonumber
    actor Admin as Admin
    participant UI as Page (data-mining.php)
    participant Engine as ses-engine.php
    participant DB as MySQL Database (db_toko)

    Admin->>UI: Input nilai Alpha (misal: 0.2)
    UI->>Engine: Call hitungSES($koneksi, 0.2)
    Engine->>DB: SELECT * FROM barang
    DB-->>Engine: Return List Barang

    loop Untuk Setiap Barang
        Engine->>DB: Query Penjualan Harian
        DB-->>Engine: Return Time-Series Sales Data
        
        Note over Engine: 1. Inisialisasi F1 = Y1<br/>2. Hitung Ft = alpha*Y(t-1) + (1-alpha)*F(t-1)<br/>3. Hitung Error, |e_t|, e_t^2, APE %<br/>4. Hitung Forecast Next F(n+1)<br/>5. Evaluasi MAD, MSE, MAPE, Akurasi %
        
        Engine->>Engine: Evaluasi Kebutuhan Stok vs Stok Fisik Saat Ini
    end

    Engine-->>UI: Return Structured Data ($dataSES)
    UI-->>Admin: Render Tabel Detail Perhitungan & Grafik Komparasi
```

---

## 3. Database Entity Relationship Overview

```mermaid
erDiagram
    barang ||--o{ item_transaksi : "dibeli dalam"
    transaksi_penjualan_multi ||--|{ item_transaksi : "berisi"
    transaksi_penjualan_multi ||--|{ detail_transaksi : "mencatat item"

    barang {
        int id_barang PK
        string no_barang
        string nama_barang
        int jumlah_barang
        string jenis_barang
        int harga
    }

    transaksi_penjualan_multi {
        int id_transaksi PK
        string kode_transaksi
        date tanggal
        int total_transaksi
    }

    item_transaksi {
        int id_item PK
        int id_transaksi FK
        int id_barang FK
        string nama_barang
        int harga
        int jumlah
        int subtotal
    }
```
