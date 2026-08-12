<?php
session_start();
include 'config/koneksi.php';
include 'includes/ses-engine.php';

if(isset($_POST['alpha'])){
    $_SESSION['alpha'] = floatval($_POST['alpha']);
}

$alpha = $_SESSION['alpha'] ?? 0.2;

$hasil = hitungSES($koneksi, $alpha);
$totalBarang = $hasil['totalBarang'];
$dataSES     = $hasil['dataSES'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Forecasting Single Exponential Smoothing (SES)</title>

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Arial, Helvetica, sans-serif;}
body{background:#f4f6f9;padding:30px;}
.container{background:white;padding:30px;border-radius:18px;box-shadow:0 5px 15px rgba(0,0,0,0.1);}
.header-top{display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;}
h1{color:#1f64e0;}
.top-action a{background:#2d9cdb;color:white;text-decoration:none;padding:13px 22px;border-radius:12px;font-weight:bold;}
.form-ses{display:flex;gap:15px;align-items:end;background:#f8fbff;padding:20px;border-radius:12px;margin-bottom:25px;}
.form-group label{display:block;font-weight:bold;margin-bottom:8px;}
.form-group select{padding:12px;border:1px solid #ccc;border-radius:10px;width:220px;font-size:15px;}
.btn-proses{background:#1f64e0;color:white;border:none;padding:12px 22px;border-radius:10px;font-weight:bold;cursor:pointer;transition:0.3s;}
.btn-proses:hover{background:#174fb3;}
.info{background:#eef4ff;color:#1f64e0;padding:18px;border-radius:12px;margin-bottom:25px;font-weight:bold;line-height:1.6;}
.card-barang{background:#ffffff;border:1px solid #e1e8ed;border-radius:14px;padding:20px;margin-bottom:30px;box-shadow:0 3px 10px rgba(0,0,0,0.03);}
.card-barang h2{color:#333;margin-bottom:12px;font-size:20px;display:flex;justify-content:space-between;align-items:center;}
.badge{padding:6px 12px;border-radius:8px;font-size:14px;font-weight:bold;}
.tinggi{color:#155724;background:#d4edda;}
.rendah{color:#721c24;background:#f8d7da;}
table{width:100%;border-collapse:collapse;margin-top:15px;margin-bottom:15px;}
th{background:#1f64e0;color:white;padding:12px;border:1px solid #ddd;font-size:14px;}
td{padding:12px;border:1px solid #ddd;text-align:center;font-size:14px;}
.summary-grid{display:grid;grid-template-columns:repeat(4, 1fr);gap:15px;margin-top:15px;background:#f8f9fa;padding:15px;border-radius:10px;}
.summary-item{text-align:center;}
.summary-item .title{font-size:13px;color:#666;}
.summary-item .val{font-size:18px;font-weight:bold;color:#1f64e0;margin-top:4px;}
.rekom-box{background:#fff8e1;border-left:4px solid #ffc107;padding:12px 15px;margin-top:15px;border-radius:4px;font-weight:bold;color:#555;}
</style>
</head>

<body>

<div class="container">

<div class="header-top">
  <h1>Forecasting Single Exponential Smoothing (SES)</h1>
  <div class="top-action">
    <a href="dashboard.php">← Kembali ke Dashboard</a>
  </div>
</div>

<form method="POST" class="form-ses">
  <div class="form-group">
    <label>Nilai Alpha (&alpha;)</label>
    <select name="alpha">
      <option value="0.1" <?= ($alpha == 0.1) ? 'selected' : ''; ?>>0.1</option>
      <option value="0.2" <?= ($alpha == 0.2) ? 'selected' : ''; ?>>0.2 (Default)</option>
      <option value="0.3" <?= ($alpha == 0.3) ? 'selected' : ''; ?>>0.3</option>
      <option value="0.4" <?= ($alpha == 0.4) ? 'selected' : ''; ?>>0.4</option>
      <option value="0.5" <?= ($alpha == 0.5) ? 'selected' : ''; ?>>0.5</option>
      <option value="0.6" <?= ($alpha == 0.6) ? 'selected' : ''; ?>>0.6</option>
      <option value="0.7" <?= ($alpha == 0.7) ? 'selected' : ''; ?>>0.7</option>
      <option value="0.8" <?= ($alpha == 0.8) ? 'selected' : ''; ?>>0.8</option>
      <option value="0.9" <?= ($alpha == 0.9) ? 'selected' : ''; ?>>0.9</option>
    </select>
  </div>

  <button type="submit" class="btn-proses">Hitung Forecasting SES</button>
</form>

<div class="info">
  Parameter Alpha (&alpha;): <b><?= $alpha; ?></b><br>
  Rumus Perhitungan: <i>F<sub>t</sub> = &alpha; &middot; Y<sub>t-1</sub> + (1 - &alpha;) &middot; F<sub>t-1</sub></i><br>
  Total Barang Diperhitungkan: <b><?= $totalBarang; ?> Barang</b>
</div>

<?php if(count($dataSES) > 0){
  foreach($dataSES as $item){
?>
<div class="card-barang">
  <h2>
    <span>📦 <?= $item['nama_barang']; ?> <small style="font-weight:normal;color:#666;">(Stok Saat Ini: <?= $item['stok_saat_ini']; ?> <?= $item['jenis_barang']; ?>)</small></span>
    <span class="badge <?= $item['class_status']; ?>"><?= $item['status']; ?></span>
  </h2>

  <?php if($item['n_periode'] > 0){ ?>
  <table>
    <thead>
      <tr>
        <th>Periode (t)</th>
        <th>Tanggal</th>
        <th>Penjualan Aktual (Y<sub>t</sub>)</th>
        <th>Forecast (F<sub>t</sub>)</th>
        <th>Error (e<sub>t</sub>)</th>
        <th>|Error| (|e<sub>t</sub>|)</th>
        <th>Squared Error (e<sub>t</sub><sup>2</sup>)</th>
        <th>APE (%)</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($item['detail'] as $d){ ?>
      <tr>
        <td><?= $d['periode']; ?></td>
        <td><?= date('d-m-Y', strtotime($d['tanggal'])); ?></td>
        <td><b><?= $d['aktual']; ?></b></td>
        <td><?= number_format($d['forecast'], 2); ?></td>
        <td><?= number_format($d['error'], 2); ?></td>
        <td><?= number_format($d['abs_error'], 2); ?></td>
        <td><?= number_format($d['sq_error'], 2); ?></td>
        <td><?= number_format($d['pct_error'], 2); ?>%</td>
      </tr>
      <?php } ?>
    </tbody>
  </table>

  <div class="summary-grid">
    <div class="summary-item">
      <div class="title">Forecast Periode Berikutnya (F<sub>n+1</sub>)</div>
      <div class="val"><?= number_format($item['forecast_next'], 2); ?> (~<?= $item['prediksi_stok_unit']; ?> <?= $item['jenis_barang']; ?>)</div>
    </div>
    <div class="summary-item">
      <div class="title">MAD (Mean Abs Error)</div>
      <div class="val"><?= number_format($item['mad'], 2); ?></div>
    </div>
    <div class="summary-item">
      <div class="title">MSE (Mean Sq Error)</div>
      <div class="val"><?= number_format($item['mse'], 2); ?></div>
    </div>
    <div class="summary-item">
      <div class="title">MAPE / Akurasi</div>
      <div class="val"><?= number_format($item['mape'], 2); ?>% / <b><?= number_format($item['akurasi'], 2); ?>%</b></div>
    </div>
  </div>

  <div class="rekom-box">
    📌 Rekomendasi: <?= $item['rekomendasi']; ?>
  </div>

  <?php } else { ?>
    <p style="color:gray; font-style:italic; margin-top:10px;">Belum ada riwayat transaksi penjualan untuk barang ini.</p>
  <?php } ?>
</div>
<?php } } else { ?>
  <div class="info" style="background:#fff3cd;color:#856404;">
    Belum ada data barang tersedia untuk dihitung forecasting SES.
  </div>
<?php } ?>

</div>

</body>
</html>