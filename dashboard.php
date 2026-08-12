<?php
include 'config/koneksi.php';

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
  $tanggalPenjualan[] = date('d M Y', strtotime($row['tanggal']));
  $totalPenjualan[] = (int)$row['total_terjual'];
}

// DATA GRAFIK PROPORSI PENJUALAN PER BARANG
$queryPerBarang = mysqli_query($koneksi, "
  SELECT 
    nama_barang, 
    SUM(jumlah) AS total_terjual
  FROM item_transaksi
  GROUP BY nama_barang
  ORDER BY total_terjual DESC
");

$labelBarang = [];
$jumlahPerBarang = [];

while($row = mysqli_fetch_assoc($queryPerBarang)){
  $labelBarang[] = $row['nama_barang'];
  $jumlahPerBarang[] = (int)$row['total_terjual'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Toko Ikhwan Grogol</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    *{
      margin:0;
      padding:0;
      box-sizing:border-box;
      font-family:'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
    }

    body{
      background:#f8fafc;
      display:flex;
      color:#0f172a;
    }

    .sidebar{
      width:260px;
      height:100vh;
      background:#ffffff;
      box-shadow:0 10px 30px rgba(0,0,0,0.03);
      padding:24px 0;
      position:fixed;
      overflow-y:auto;
      border-right:1px solid #f1f5f9;
    }

    .logo{
      text-align:center;
      margin-bottom:30px;
    }

    .logo img{
      width:65px;
      margin-bottom:10px;
      filter:drop-shadow(0 4px 6px rgba(0,0,0,0.06));
    }

    .logo h2{
      font-size:17px;
      font-weight:700;
      color:#0f172a;
      letter-spacing:-0.3px;
    }

    .menu{
      list-style:none;
    }

    .menu li{
      margin:6px 16px;
    }

    .menu a{
      display:flex;
      align-items:center;
      gap:12px;
      text-decoration:none;
      color:#64748b;
      padding:12px 16px;
      border-radius:14px;
      transition:all 0.25s ease;
      font-size:15px;
      font-weight:600;
    }

    .menu a:hover{
      background:#f1f5f9;
      color:#1f64e0;
    }

    .menu a.active{
      background:linear-gradient(135deg, #1f64e0, #3b82f6);
      color:#ffffff;
      box-shadow:0 8px 20px -4px rgba(31, 100, 224, 0.4);
    }

    .main{
      margin-left:260px;
      width:calc(100% - 260px);
      padding:30px;
    }

    .topbar{
      background:#ffffff;
      padding:18px 28px;
      border-radius:18px;
      display:flex;
      justify-content:space-between;
      align-items:center;
      margin-bottom:30px;
      box-shadow:0 4px 20px -2px rgba(0,0,0,0.03);
      border:1px solid #f1f5f9;
    }

    .topbar h1{
      font-size:26px;
      font-weight:800;
      color:#0f172a;
      letter-spacing:-0.5px;
    }

    .logout-btn{
      text-decoration:none;
      background:#ef4444;
      color:white;
      padding:10px 20px;
      border-radius:12px;
      font-weight:700;
      font-size:14px;
      transition:all 0.2s ease;
      box-shadow:0 4px 12px rgba(239, 68, 68, 0.25);
    }

    .logout-btn:hover{
      background:#dc2626;
      transform:translateY(-1px);
    }

    /* CARDS STYLING */
    .cards{
      display:grid;
      grid-template-columns:repeat(4, 1fr);
      gap:20px;
      margin-bottom:30px;
    }

    .card-stat{
      background:#ffffff;
      border-radius:20px;
      padding:22px;
      border:1px solid #f1f5f9;
      box-shadow:0 10px 25px -5px rgba(0,0,0,0.03), 0 4px 6px -2px rgba(0,0,0,0.01);
      transition:all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      display:flex;
      flex-direction:column;
      justify-content:space-between;
      position:relative;
      overflow:hidden;
    }

    .card-stat::before{
      content:'';
      position:absolute;
      top:0;
      left:0;
      width:100%;
      height:4px;
      border-radius:20px 20px 0 0;
    }

    .card-stat.blue::before{ background:linear-gradient(90deg, #3b82f6, #60a5fa); }
    .card-stat.emerald::before{ background:linear-gradient(90deg, #10b981, #34d399); }
    .card-stat.rose::before{ background:linear-gradient(90deg, #f43f5e, #fb7185); }
    .card-stat.purple::before{ background:linear-gradient(90deg, #8b5cf6, #a78bfa); }

    .card-stat:hover{
      transform:translateY(-6px);
      box-shadow:0 20px 30px -10px rgba(15, 23, 42, 0.08);
    }

    .card-header{
      display:flex;
      justify-content:space-between;
      align-items:center;
      margin-bottom:14px;
    }

    .card-title{
      font-size:13px;
      font-weight:700;
      color:#64748b;
      text-transform:uppercase;
      letter-spacing:0.5px;
    }

    .icon-badge{
      width:44px;
      height:44px;
      border-radius:14px;
      display:flex;
      align-items:center;
      justify-content:center;
      font-size:22px;
      box-shadow:0 4px 10px rgba(0,0,0,0.03);
    }

    .icon-badge.blue{ background:#eff6ff; color:#2563eb; }
    .icon-badge.emerald{ background:#ecfdf5; color:#059669; }
    .icon-badge.rose{ background:#fff1f2; color:#e11d48; }
    .icon-badge.purple{ background:#f5f3ff; color:#7c3aed; }

    .card-body{
      margin-bottom:14px;
    }

    .card-value{
      font-size:28px;
      font-weight:800;
      color:#0f172a;
      letter-spacing:-0.8px;
      line-height:1.2;
    }

    .card-value small{
      font-size:15px;
      font-weight:600;
      color:#64748b;
    }

    .card-footer{
      display:flex;
      align-items:center;
      gap:8px;
      font-size:12px;
      font-weight:600;
      color:#64748b;
    }

    .pill-tag{
      padding:4px 10px;
      border-radius:20px;
      font-size:11px;
      font-weight:700;
      display:inline-flex;
      align-items:center;
      gap:4px;
      white-space:nowrap;
    }

    .pill-tag.blue{ background:#eff6ff; color:#2563eb; }
    .pill-tag.emerald{ background:#ecfdf5; color:#059669; }
    .pill-tag.rose{ background:#fff1f2; color:#e11d48; }
    .pill-tag.purple{ background:#f5f3ff; color:#7c3aed; }

    /* CHARTS CONTAINER */
    .charts-grid{
      display:grid;
      grid-template-columns:2fr 1fr;
      gap:24px;
    }

    .chart-box{
      background:#ffffff;
      padding:24px;
      border-radius:20px;
      border:1px solid #f1f5f9;
      box-shadow:0 10px 25px -5px rgba(0,0,0,0.03);
      height:430px;
      display:flex;
      flex-direction:column;
    }

    .chart-box h2{
      margin-bottom:6px;
      color:#0f172a;
      font-size:18px;
      font-weight:700;
      display:flex;
      align-items:center;
      gap:8px;
    }

    .chart-box p.subtitle{
      color:#64748b;
      font-size:13px;
      font-weight:500;
      margin-bottom:16px;
    }

    .canvas-container{
      position:relative;
      flex:1;
      width:100%;
      height:100%;
    }

    @media(max-width:1100px){
      .charts-grid{
        grid-template-columns:1fr;
      }
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
    <li><a href="data-mining.php"><span>📈</span> <span>Forecasting SES</span></a></li>
    <li><a href="hasil-prediksi-stok.php"><span>📝</span> <span>Hasil Prediksi Stok</span></a></li>
    <li><a href="laporan.php"><span>📁</span> <span>Menu Laporan</span></a></li>
  </ul>
</aside>

<main class="main">

  <div class="topbar">
    <h1>Dashboard Analytics</h1>

    <a href="logout.php" class="logout-btn">
      Logout
    </a>
  </div>

  <div class="cards">

    <div class="card-stat blue">
      <div class="card-header">
        <span class="card-title">Jumlah Barang</span>
        <div class="icon-badge blue">📦</div>
      </div>
      <div class="card-body">
        <div class="card-value"><?= number_format($totalBarang, 0, ',', '.'); ?> <small>Unit</small></div>
      </div>
      <div class="card-footer">
        <span class="pill-tag blue">✓ Tersedia</span>
        <span>Stok barang aktif saat ini</span>
      </div>
    </div>

    <div class="card-stat emerald">
      <div class="card-header">
        <span class="card-title">Total Penghasilan</span>
        <div class="icon-badge emerald">💰</div>
      </div>
      <div class="card-body">
        <div class="card-value">Rp <?= number_format($totalPenghasilan,0,',','.'); ?></div>
      </div>
      <div class="card-footer">
        <span class="pill-tag emerald">↗ Revenue</span>
        <span>Akumulasi transaksi sukses</span>
      </div>
    </div>

    <div class="card-stat rose">
      <div class="card-header">
        <span class="card-title">Barang Terlaris</span>
        <div class="icon-badge rose">🔥</div>
      </div>
      <div class="card-body">
        <div class="card-value"><?= number_format($totalTerlaris, 0, ',', '.'); ?> <small>Terjual</small></div>
      </div>
      <div class="card-footer">
        <span class="pill-tag rose">★ Top 1</span>
        <span style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:130px;"><?= $barangTerlaris; ?></span>
      </div>
    </div>

    <div class="card-stat purple">
      <div class="card-header">
        <span class="card-title">Total Transaksi</span>
        <div class="icon-badge purple">🛒</div>
      </div>
      <div class="card-body">
        <div class="card-value"><?= number_format($totalTransaksi, 0, ',', '.'); ?> <small>Orders</small></div>
      </div>
      <div class="card-footer">
        <span class="pill-tag purple">⚡ Transaksi</span>
        <span>Jumlah transaksi tercatat</span>
      </div>
    </div>

  </div>

  <div class="charts-grid">

    <div class="chart-box">
      <h2>📈 Tren Penjualan Harian</h2>
      <p class="subtitle">Grafik dinamika kuantitas item barang yang terjual per tanggal transaksi</p>
      <div class="canvas-container">
        <canvas id="trendChart"></canvas>
      </div>
    </div>

    <div class="chart-box">
      <h2>📊 Komposisi Penjualan Barang</h2>
      <p class="subtitle">Persentase kontribusi item barang terhadap total penjualan</p>
      <div class="canvas-container">
        <canvas id="barangChart"></canvas>
      </div>
    </div>

  </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Chart 1: Tren Penjualan Harian (Smooth Curved Area Line)
const ctxTrend = document.getElementById('trendChart').getContext('2d');

const gradientTrend = ctxTrend.createLinearGradient(0, 0, 0, 300);
gradientTrend.addColorStop(0, 'rgba(31, 100, 224, 0.35)');
gradientTrend.addColorStop(1, 'rgba(31, 100, 224, 0.0)');

new Chart(ctxTrend, {
  type: 'line',
  data: {
    labels: <?= json_encode($tanggalPenjualan); ?>,
    datasets: [{
      label: 'Jumlah Barang Terjual',
      data: <?= json_encode($totalPenjualan); ?>,
      borderColor: '#1f64e0',
      borderWidth: 3,
      backgroundColor: gradientTrend,
      fill: true,
      tension: 0.4,
      pointRadius: 6,
      pointHoverRadius: 9,
      pointBackgroundColor: '#1f64e0',
      pointBorderColor: '#ffffff',
      pointBorderWidth: 3,
      pointHoverBorderWidth: 3
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        display: true,
        position: 'top',
        labels: {
          font: { family: 'Plus Jakarta Sans', size: 13, weight: '600' },
          usePointStyle: true
        }
      },
      tooltip: {
        backgroundColor: '#0f172a',
        titleFont: { family: 'Plus Jakarta Sans', size: 14, weight: 'bold' },
        bodyFont: { family: 'Plus Jakarta Sans', size: 13 },
        padding: 12,
        cornerRadius: 10,
        displayColors: false,
        callbacks: {
          label: function(context) {
            return ` Terjual: ${context.parsed.y} item`;
          }
        }
      }
    },
    scales: {
      x: {
        grid: { display: false },
        ticks: { font: { family: 'Plus Jakarta Sans', size: 12 }, color: '#64748b' }
      },
      y: {
        beginAtZero: true,
        grid: { color: '#f1f5f9', borderDash: [4, 4] },
        ticks: { font: { family: 'Plus Jakarta Sans', size: 12 }, color: '#64748b', precision: 0 }
      }
    }
  }
});

// Chart 2: Komposisi Penjualan Barang (Doughnut Chart Modern)
const ctxBarang = document.getElementById('barangChart').getContext('2d');

new Chart(ctxBarang, {
  type: 'doughnut',
  data: {
    labels: <?= json_encode($labelBarang); ?>,
    datasets: [{
      data: <?= json_encode($jumlahPerBarang); ?>,
      backgroundColor: [
        '#1f64e0',
        '#2d9cdb',
        '#8e6bf7',
        '#ff8f8f',
        '#27ae60'
      ],
      borderWidth: 3,
      borderColor: '#ffffff',
      hoverOffset: 8
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '70%',
    plugins: {
      legend: {
        position: 'bottom',
        labels: {
          font: { family: 'Plus Jakarta Sans', size: 12, weight: '600' },
          padding: 15,
          usePointStyle: true
        }
      },
      tooltip: {
        backgroundColor: '#0f172a',
        titleFont: { family: 'Plus Jakarta Sans', size: 14, weight: 'bold' },
        bodyFont: { family: 'Plus Jakarta Sans', size: 13 },
        padding: 12,
        cornerRadius: 10,
        callbacks: {
          label: function(context) {
            const total = context.dataset.data.reduce((a, b) => a + b, 0);
            const value = context.raw;
            const percentage = ((value / total) * 100).toFixed(1);
            return ` ${context.label}: ${value} unit (${percentage}%)`;
          }
        }
      }
    }
  }
});
</script>

</body>
</html>