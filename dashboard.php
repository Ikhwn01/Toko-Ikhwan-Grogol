<?php
include 'koneksi.php';

// TOTAL BARANG
$queryBarang = mysqli_query($koneksi, "
  SELECT SUM(jumlah_barang) AS total_barang 
  FROM barang
");
$dataBarang = mysqli_fetch_assoc($queryBarang);
$totalBarang = $dataBarang['total_barang'] ?? 0;

// TOTAL PENGHASILAN
$queryPenghasilan = mysqli_query($koneksi, "
  SELECT SUM(total_transaksi) AS total_penghasilan 
  FROM transaksi_penjualan_multi
");
$dataPenghasilan = mysqli_fetch_assoc($queryPenghasilan);
$totalPenghasilan = $dataPenghasilan['total_penghasilan'] ?? 0;

// BARANG TERLARIS
$queryTerlaris = mysqli_query($koneksi, "
  SELECT 
    nama_barang, 
    SUM(jumlah) AS total_beli
  FROM item_transaksi
  GROUP BY nama_barang
  ORDER BY total_beli DESC
  LIMIT 1
");

$dataTerlaris = mysqli_fetch_assoc($queryTerlaris);
$barangTerlaris = $dataTerlaris['nama_barang'] ?? '-';
$totalTerlaris = $dataTerlaris['total_beli'] ?? 0;

// TOTAL TRANSAKSI
$queryTransaksi = mysqli_query($koneksi, "
  SELECT COUNT(*) AS total_transaksi
  FROM transaksi_penjualan_multi
");
$dataTransaksi = mysqli_fetch_assoc($queryTransaksi);
$totalTransaksi = $dataTransaksi['total_transaksi'] ?? 0;

// DATA GRAFIK PENJUALAN PER HARI
$queryGrafik = mysqli_query($koneksi, "
  SELECT 
    t.tanggal, 
    SUM(i.jumlah) AS total_terjual
  FROM transaksi_penjualan_multi t
  JOIN item_transaksi i 
  ON t.id_transaksi = i.id_transaksi
  GROUP BY t.tanggal
  ORDER BY t.tanggal ASC
");

$tanggalPenjualan = [];
$totalPenjualan = [];

while($row = mysqli_fetch_assoc($queryGrafik)){
  $tanggalPenjualan[] = $row['tanggal'];
  $totalPenjualan[] = $row['total_terjual'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>

  <style>
    *{
      margin:0;
      padding:0;
      box-sizing:border-box;
      font-family:Arial, Helvetica, sans-serif;
    }

    body{
      background:#f3f4f8;
      display:flex;
    }

    .sidebar{
      width:260px;
      height:100vh;
      background:white;
      box-shadow:2px 0 10px rgba(0,0,0,0.05);
      padding:20px 0;
      position:fixed;
      overflow-y:auto;
    }

    .logo{
      text-align:center;
      margin-bottom:30px;
    }

    .logo img{
      width:70px;
      margin-bottom:10px;
    }

    .logo h2{
      font-size:18px;
      color:#333;
    }

    .menu{
      list-style:none;
    }

    .menu li{
      margin:8px 15px;
    }

    .menu a{
      display:flex;
      align-items:center;
      gap:10px;
      text-decoration:none;
      color:#333;
      padding:14px 15px;
      border-radius:12px;
      transition:0.3s;
      font-size:16px;
    }

    .menu a:hover,
    .menu a.active{
      background:#1f64e0;
      color:white;
    }

    .main{
      margin-left:260px;
      width:calc(100% - 260px);
      padding:25px;
    }

    .topbar{
      background:white;
      padding:15px 25px;
      border-radius:15px;
      display:flex;
      justify-content:space-between;
      align-items:center;
      margin-bottom:25px;
      box-shadow:0 3px 10px rgba(0,0,0,0.05);
    }

    .topbar h1{
      font-size:30px;
      color:#222;
    }

    .logout-btn{
      text-decoration:none;
      background:#e53935;
      color:white;
      padding:12px 22px;
      border-radius:10px;
      font-weight:bold;
      transition:0.3s;
    }

    .logout-btn:hover{
      background:#c62828;
    }

    .cards{
      display:grid;
      grid-template-columns:repeat(4, 1fr);
      gap:20px;
      margin-bottom:25px;
    }

    .card{
      padding:25px;
      border-radius:18px;
      color:white;
      box-shadow:0 5px 15px rgba(0,0,0,0.08);
      min-height:170px;
    }

    .card h2{
      font-size:18px;
      margin-bottom:15px;
    }

    .card .value{
      font-size:38px;
      font-weight:bold;
      margin-bottom:10px;
    }

    .card p{
      font-size:14px;
      opacity:0.9;
    }

    .purple{
      background:linear-gradient(135deg, #8e6bf7, #6a73d1);
    }

    .blue{
      background:linear-gradient(135deg, #53b3ff, #5187d8);
    }

    .red{
      background:linear-gradient(135deg, #ff8f8f, #dd6f6f);
    }

    .pink{
      background:linear-gradient(135deg, #b26af3, #9a67d8);
    }

    .chart-box{
      background:white;
      padding:25px;
      border-radius:18px;
      box-shadow:0 5px 15px rgba(0,0,0,0.05);
      height:430px;
    }

    .chart-box h2{
      margin-bottom:20px;
      color:#333;
    }

    canvas{
      width:100% !important;
      height:340px !important;
    }

    @media(max-width:1000px){
      .cards{
        grid-template-columns:repeat(2,1fr);
      }
    }

    @media(max-width:700px){
      .sidebar{
        width:80px;
      }

      .logo h2,
      .menu a span:last-child{
        display:none;
      }

      .main{
        margin-left:80px;
        width:calc(100% - 80px);
      }

      .cards{
        grid-template-columns:1fr;
      }
    }
  </style>
</head>

<body>

<aside class="sidebar">
  <div class="logo">
    <img src="assets/logo.png" alt="logo">
    <h2>Toko Ikhwan Grogol</h2>
  </div>

  <ul class="menu">
    <li><a href="dashboard.php" class="active"><span>🏠</span> <span>Dashboard</span></a></li>
    <li><a href="data-barang.php"><span>📦</span> <span>Data Barang</span></a></li>
    <li><a href="transaksi-penjualan.php"><span>🛒</span> <span>Transaksi Penjualan</span></a></li>
    <li><a href="data-mining.php"><span>💰</span> <span>Data Mining Apriori</span></a></li>
    <li><a href="hasil-prediksi-stok.php"><span>📝</span> <span>Hasil Prediksi Stok</span></a></li>
    <li><a href="laporan.php"><span>📁</span> <span>Menu Laporan</span></a></li>
  </ul>
</aside>

<main class="main">

  <div class="topbar">
    <h1>Dashboard</h1>

    <a href="logout.php" class="logout-btn">
      Logout
    </a>
  </div>

  <div class="cards">

    <div class="card purple">
      <h2>Jumlah Barang</h2>
      <div class="value">
        <?= $totalBarang; ?>
      </div>
      <p>Total stok barang tersedia</p>
    </div>

    <div class="card blue">
      <h2>Penghasilan</h2>
      <div class="value">
        Rp <?= number_format($totalPenghasilan,0,',','.'); ?>
      </div>
      <p>Total penghasilan dari transaksi penjualan</p>
    </div>

    <div class="card red">
      <h2>Barang Terlaris</h2>
      <div class="value">
        <?= $totalTerlaris; ?>
      </div>
      <p><?= $barangTerlaris; ?></p>
    </div>

    <div class="card pink">
      <h2>Total Transaksi</h2>
      <div class="value">
        <?= $totalTransaksi; ?>
      </div>
      <p>Jumlah seluruh transaksi penjualan</p>
    </div>

  </div>

  <div class="chart-box">
    <h2>Grafik Penjualan Per Hari</h2>
    <canvas id="studyChart"></canvas>
  </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('studyChart');

new Chart(ctx, {
  type: 'bar',
  data: {
    labels: <?= json_encode($tanggalPenjualan); ?>,
    datasets: [{
      label: 'Jumlah Penjualan',
      data: <?= json_encode($totalPenjualan); ?>,
      borderWidth: 2,
      backgroundColor: '#53b3ff'
    }]
  },
  options: {
    responsive:true,
    maintainAspectRatio:false,
    plugins:{
      legend:{
        display:true
      }
    },
    scales:{
      y:{
        beginAtZero:true
      }
    }
  }
});
</script>

</body>
</html>