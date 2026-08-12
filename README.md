# 🛒 Toko Ikhwan Grogol - Retail Inventory & Stock Forecasting System (SES)

A web-based retail inventory management and POS system integrated with **Single Exponential Smoothing (SES)** algorithm for quantitative stock demand forecasting.

![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![ChartJS](https://img.shields.io/badge/Chart.js-4.0-FF6384?style=for-the-badge&logo=chartdotjs&logoColor=white)
![Bootstrap](https://img.shields.io/badge/HTML5-CSS3-E34F26?style=for-the-badge&logo=html5&logoColor=white)

---

## 📌 Overview
**Toko Ikhwan Grogol Management System** is a full-stack PHP web application designed to optimize retail store operations. Beyond standard POS (Point of Sale) and inventory management, the system features an automated **Time-Series Forecasting Engine** using **Single Exponential Smoothing (SES)**.

The system analyzes past daily sales transaction trends ($Y_t$) to predict next-period product stock demand ($F_{t+1}$) with mathematical accuracy evaluation (MAD, MSE, MAPE), helping store owners prevent stockouts and overstocking.

---

## ⚡ Key Features

- **📊 Modern Analytics Dashboard**:
  - **Daily Sales Trend Chart**: Curved smooth area line chart displaying daily item sales volume.
  - **Product Sales Composition Chart**: Modern doughnut chart showing percentage contribution per product.
  - **Executive Stat Cards**: Modern elevated cards with icon badges for total stock, revenue, top-selling items, and total transactions.
- **📈 Single Exponential Smoothing (SES) Engine**:
  - Interactive parameter selector ($\alpha = 0.1 \dots 0.9$).
  - Step-by-step mathematical breakdown per product (Actual Sales $Y_t$, Forecast $F_t$, Error $e_t$, Absolute Error $|e_t|$, Squared Error $e_t^2$, Percentage Error $APE$).
  - Evaluates **MAD** (Mean Absolute Deviation), **MSE** (Mean Squared Error), **MAPE** (Mean Absolute Percentage Error), and **Accuracy %**.
  - Automated stock status categorization (*Perlu Tambah Stok* vs *Stok Cukup*) with recommended reorder units.
- **🛒 POS & Multi-Item Transactions**:
  - Dynamic multi-item checkout with automatic total calculation and stock deduction.
- **📄 Print-Ready Official Reports**:
  - Master Data Barang report.
  - Sales Transaction History report.
  - Top-Selling Products report.
  - SES Stock Prediction Report with official store letterhead (*Kop Surat*).

---

## 🧮 Mathematical Model (Single Exponential Smoothing)

### 1. Forecasting Recursive Formula
For period $t = 1 \dots n$:
- Initial condition: $F_1 = Y_1$
- For $t \ge 2$:
$$F_t = \alpha \cdot Y_{t-1} + (1 - \alpha) \cdot F_{t-1}$$
- Next-Period Forecast ($n+1$):
$$F_{n+1} = \alpha \cdot Y_n + (1 - \alpha) \cdot F_n$$

### 2. Error Metrics & Accuracy Evaluation
- **Mean Absolute Deviation (MAD)**:
$$\text{MAD} = \frac{1}{n} \sum_{t=1}^{n} |Y_t - F_t|$$
- **Mean Squared Error (MSE)**:
$$\text{MSE} = \frac{1}{n} \sum_{t=1}^{n} (Y_t - F_t)^2$$
- **Mean Absolute Percentage Error (MAPE)**:
$$\text{MAPE} = \frac{1}{n} \sum_{t=1}^{n} \left| \frac{Y_t - F_t}{Y_t} \right| \times 100\%$$
- **Accuracy Rate**:
$$\text{Accuracy (\%)} = \max(0, 100\% - \text{MAPE})$$

---

## 📂 Project Architecture

```
toko/
├── assets/                    # Static image & logo assets
├── config/                    # Database connection setup (koneksi.php)
├── includes/                  # Forecasting Engine (ses-engine.php)
├── database/                  # SQL database schema dump (db_toko.sql)
├── laporan/                   # Printable report documents
│   ├── laporan-data-barang.php
│   ├── laporan-penjualan.php
│   ├── laporan-barang-terlaris.php
│   └── laporan-prediksi-stok.php
├── proses/                    # Backend action handlers & seeders
│   ├── seed_data.php          # 20 items & 500 sales transaction generator
│   ├── reset-transaksi.php
│   ├── reset-apriori.php
│   └── upload-barang.php
├── portfolio-assets/          # Showcase portfolio documentation & JSON schemas
├── dashboard.php              # Main Analytics Dashboard
├── data-barang.php            # Inventory Item Management
├── transaksi-penjualan.php    # POS Sales Transaction Page
├── data-mining.php            # SES Forecasting Calculation Page
├── hasil-prediksi-stok.php    # SES Prediction Results & Visual Comparison
├── laporan.php                # Reports Menu Dashboard
├── login.php / logout.php     # Authentication Modules
└── index.php                  # Entrypoint Redirect
```

---

## 🚀 Quick Start & Installation

1. **Clone Repository**:
   Place the project inside your XAMPP `htdocs` directory:
   `C:\xampp\htdocs\toko\`

2. **Database Setup**:
   - Start MySQL server in XAMPP.
   - Create database named `db_toko`.
   - Import `database/db_toko.sql` via phpMyAdmin or CLI:
     ```bash
     mysql -u root db_toko < database/db_toko.sql
     ```

3. **(Optional) Run Seed Data**:
   To generate 20 sample items and 500 realistic sales transactions across 2+ months:
   ```bash
   php proses/seed_data.php
   ```

4. **Access System**:
   Open browser at:
   👉 **[http://localhost:8000/login.php](http://localhost:8000/login.php)** or **[http://localhost/toko/login.php](http://localhost/toko/login.php)**

   **Credentials**:
   - Username: `admin`
   - Password: (Your admin password)

---

## 📄 Portfolio Showcase
Additional case study materials, JSON schemas for portfolio websites, architecture diagrams, and CV bullet points are available in the [`portfolio-assets/`](./portfolio-assets/) directory.
