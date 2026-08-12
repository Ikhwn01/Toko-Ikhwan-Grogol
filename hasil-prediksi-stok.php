<?php
session_start();
include 'config/koneksi.php';
include 'includes/ses-engine.php';

$alpha = $_SESSION['alpha'] ?? 0.2;

$hasil = hitungSES($koneksi, $alpha);
$totalBarang = $hasil['totalBarang'];
$dataSES     = $hasil['dataSES'];

$chartLabels = [];
$chartStokTersedia = [];
$chartPrediksiSES = [];

foreach($dataSES as $d){
  $chartLabels[] = $d['nama_barang'];
  $chartStokTersedia[] = $d['stok_saat_ini'];
  $chartPrediksiSES[] = $d['prediksi_stok_unit'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Hasil Prediksi Stok - Single Exponential Smoothing</title>

  <style>
    *{
      margin:0;
      padding:0;
      box-sizing:border-box;
      font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body{
      background:#f4f6f9;
      padding:30px;
    }

    .container{
      background:white;
      padding:30px;
      border-radius:18px;
      box-shadow:0 5px 15px rgba(0,0,0,0.1);
    }

    .header-top{
      display:flex;
      justify-content:space-between;
      align-items:center;
      margin-bottom:30px;
    }

    h1{
      color:#1f64e0;
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
    }

    .info{
      background:#eef4ff;
      color:#1f64e0;
      padding:18px;
      border-radius:12px;
      margin-bottom:25px;
      font-weight:bold;
      line-height:1.6;
    }

    .chart-box{
      background:#f8fafc;
      padding:22px;
      border-radius:16px;
      border:1px solid #e2e8f0;
      margin-bottom:30px;
    }

    .chart-box h3{
      color:#1e293b;
      margin-bottom:15px;
      font-size:18px;
      display:flex;
      align-items:center;
      gap:8px;
    }

    table{
      width:100%;
      border-collapse:collapse;
      margin-top:20px;
    }

    th{
      background:#1f64e0;
      color:white;
      padding:13px;
      border:1px solid #ddd;
    }

    td{
      padding:13px;
      border:1px solid #ddd;
      text-align:center;
      vertical-align:middle;
    }

    td:nth-child(2),
    td:nth-child(7){
      text-align:left;
    }

    .tinggi{
      color:#155724;
      font-weight:bold;
      background:#d4edda;
      padding:7px 12px;
      border-radius:8px;
      display:inline-block;
    }

    .rendah{
      color:#721c24;
      font-weight:bold;
      background:#f8d7da;
      padding:7px 12px;
      border-radius:8px;
      display:inline-block;
    }
  </style>
</head>

<body>

<div class="container">

  <div class="header-top">

    <h1>Hasil Prediksi Stok Barang (SES)</h1>

    <div class="top-action">
      <a href="dashboard.php">
        ← Kembali ke Dashboard
      </a>
    </div>

  </div>

  <div class="info">
    Metode Forecasting: <b>Single Exponential Smoothing (SES)</b>
    <br>
    Parameter Alpha (&alpha;): <b><?= $alpha; ?></b>
    <br>
    Total Barang Diperhitungkan: <b><?= $totalBarang; ?> Barang</b>
  </div>

  <div class="chart-box">
    <h3>📊 Perbandingan Stok Saat Ini vs Perkiraan Kebutuhan (SES)</h3>
    <div style="position:relative; height:290px; width:100%;">
      <canvas id="prediksiChart"></canvas>
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Nama Barang</th>
        <th>Stok Saat Ini</th>
        <th>Hasil Prediksi (Unit)</th>
        <th>MAPE (%)</th>
        <th>Tingkat Akurasi</th>
        <th>Status & Rekomendasi Stok</th>
      </tr>
    </thead>

    <tbody>
      <?php
      $no = 1;

      if(count($dataSES) > 0){

        foreach($dataSES as $item){
      ?>

      <tr>
        <td><?= $no++; ?></td>

        <td>
          <b><?= $item['nama_barang']; ?></b>
          <br>
          <small style="color:#666;">Satuan: <?= $item['jenis_barang']; ?></small>
        </td>

        <td><?= $item['stok_saat_ini']; ?> <?= $item['jenis_barang']; ?></td>

        <td>
          <b><?= number_format($item['forecast_next'], 2); ?></b>
          <br>
          <small>(~<?= $item['prediksi_stok_unit']; ?> <?= $item['jenis_barang']; ?>)</small>
        </td>

        <td><?= number_format($item['mape'], 2); ?>%</td>

        <td><b><?= number_format($item['akurasi'], 2); ?>%</b></td>

        <td>
          <span class="<?= $item['class_status']; ?>">
            <?= $item['status']; ?>
          </span>
          <br><br>
          <?= $item['rekomendasi']; ?>
        </td>
      </tr>

      <?php }}else{ ?>

      <tr>
        <td colspan="7">
          Belum ada data barang untuk dilakukan peramalan stok.
        </td>
      </tr>

      <?php } ?>
    </tbody>
  </table>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('prediksiChart').getContext('2d');

new Chart(ctx, {
  type: 'bar',
  data: {
    labels: <?= json_encode($chartLabels); ?>,
    datasets: [
      {
        label: 'Stok Saat Ini',
        data: <?= json_encode($chartStokTersedia); ?>,
        backgroundColor: '#1f64e0',
        borderRadius: 8,
        maxBarThickness: 45
      },
      {
        label: 'Prediksi Kebutuhan SES (Next)',
        data: <?= json_encode($chartPrediksiSES); ?>,
        backgroundColor: '#8e6bf7',
        borderRadius: 8,
        maxBarThickness: 45
      }
    ]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        display: true,
        position: 'top',
        labels: {
          font: { family: 'Segoe UI', size: 13, weight: '600' },
          usePointStyle: true
        }
      },
      tooltip: {
        backgroundColor: '#1e293b',
        titleFont: { size: 14, weight: 'bold' },
        bodyFont: { size: 13 },
        padding: 12,
        cornerRadius: 10
      }
    },
    scales: {
      x: {
        grid: { display: false },
        ticks: { font: { family: 'Segoe UI', size: 13 }, color: '#475569' }
      },
      y: {
        beginAtZero: true,
        grid: { color: '#f1f5f9' },
        ticks: { font: { family: 'Segoe UI', size: 12 }, color: '#475569', precision: 0 }
      }
    }
  }
});
</script>

</body>
</html>