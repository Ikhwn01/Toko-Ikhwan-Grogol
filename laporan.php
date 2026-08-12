<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laporan</title>

  <style>

    *{
      margin:0;
      padding:0;
      box-sizing:border-box;
      font-family:Arial, Helvetica, sans-serif;
    }

   body{
      background:#f4f6f9;
      padding:20px;
      overflow:hidden;
    }

    .container{
      background:white;
      padding:25px;
      border-radius:20px;
      box-shadow:0 5px 15px rgba(0,0,0,0.1);
      min-height:calc(100vh - 40px);
    }

    h1{
      color:#1f64e0;
      margin-bottom:30px;
    }

    .laporan-grid{
      display:grid;
      grid-template-columns:repeat(2, 1fr);
      gap:20px;
    }

    .laporan-card{
      background:white;
      border-radius:18px;
      padding:25px;
      text-align:center;
      box-shadow:0 5px 15px rgba(0,0,0,0.08);
      transition:0.3s;
      border:2px solid #f1f1f1;
      min-height:260px;
    }

    .laporan-icon{
      font-size:45px;
      margin-bottom:12px;
    }

    .laporan-card h2{
      color:#333;
      margin-bottom:10px;
      font-size:24px;
    }

    .laporan-card p{
      color:gray;
      font-size:14px;
      line-height:1.5;
      margin-bottom:18px;
    }

    .btn-laporan{
      display:inline-block;
      text-decoration:none;
      background:#1f64e0;
      color:white;
      padding:12px 24px;
      border-radius:12px;
      font-weight:bold;
      transition:0.3s;
    }
    .btn-laporan:hover{
      background:#174fb3;
    }
    .header-top{
      display:flex;
      justify-content:space-between;
      align-items:center;
      margin-bottom:30px;
    }

    .header-top h1{
      margin:0;
    }

    .top-action a{
      text-decoration:none;
      background:#2d9cdb;
      color:white;
      padding:13px 22px;
      border-radius:12px;
      font-weight:bold;
      transition:0.3s;
      box-shadow:0 4px 12px rgba(45,156,219,0.3);
    }

    .top-action a:hover{
      background:#1d87c5;
      transform:translateY(-2px);
    }
    .back{
      margin-top:35px;
    }

    .back a{
      text-decoration:none;
      background:#2d9cdb;
      color:white;
      padding:13px 22px;
      border-radius:12px;
      font-weight:bold;
      transition:0.3s;
      display:inline-block;
    }

    .back a:hover{
      background:#1d87c5;
    }

  </style>
</head>

<body>

<div class="container">

  <div class="header-top">

  <h1>Menu Laporan</h1>

  <div class="top-action">
    <a href="dashboard.php">
      ← Kembali ke Dashboard
    </a>
  </div>

</div>

<div class="laporan-grid">

  <div class="laporan-card">
    <div class="laporan-icon">📦</div>
    <h2>Laporan Data Barang</h2>
    <p>Menampilkan seluruh data barang, stok, harga, dan jenis satuan.</p>
    <a href="laporan/laporan-data-barang.php" class="btn-laporan">Buka Laporan</a>
  </div>

  <div class="laporan-card">
    <div class="laporan-icon">📁</div>
    <h2>Laporan Penjualan</h2>
    <p>Menampilkan seluruh transaksi penjualan barang.</p>
    <a href="laporan/laporan-penjualan.php" class="btn-laporan">Buka Laporan</a>
  </div>

  <div class="laporan-card">
    <div class="laporan-icon">🔥</div>
    <h2>Laporan Barang Terlaris</h2>
    <p>Menampilkan barang yang paling banyak terjual.</p>
    <a href="laporan/laporan-barang-terlaris.php" class="btn-laporan">Buka Laporan</a>
  </div>

  <div class="laporan-card">
    <div class="laporan-icon">📊</div>
    <h2>Laporan Hasil Prediksi Stok</h2>
    <p>Menampilkan hasil prediksi stok berdasarkan algoritma Single Exponential Smoothing (SES).</p>
    <a href="laporan/laporan-prediksi-stok.php" class="btn-laporan">Buka Laporan</a>
  </div>

</div>

</div>

</body>
</html>