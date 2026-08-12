<?php
session_start();
include '../config/koneksi.php';
include '../includes/ses-engine.php';

date_default_timezone_set('Asia/Jakarta');

$hari = [
  'Sunday' => 'Minggu',
  'Monday' => 'Senin',
  'Tuesday' => 'Selasa',
  'Wednesday' => 'Rabu',
  'Thursday' => 'Kamis',
  'Friday' => 'Jumat',
  'Saturday' => 'Sabtu'
];

$bulan = [
  1 => 'Januari',
  'Februari',
  'Maret',
  'April',
  'Mei',
  'Juni',
  'Juli',
  'Agustus',
  'September',
  'Oktober',
  'November',
  'Desember'
];

$namaHari = $hari[date('l')];

$tanggalIndonesia =
$namaHari . ", " .
date('d') . " " .
$bulan[(int)date('m')] . " " .
date('Y');

$tanggalTtd =
"Jakarta, " .
$namaHari . ", " .
date('d') . " " .
$bulan[(int)date('m')] . " " .
date('Y');

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
  <title>Laporan Hasil Prediksi Stok (SES)</title>

  <style>
    *{
      margin:0;
      padding:0;
      box-sizing:border-box;
      font-family:Arial, Helvetica, sans-serif;
    }

    body{
      background:#f4f6f9;
      padding:30px;
      color:#000;
    }

    .container{
      background:white;
      padding:35px;
      border-radius:18px;
      box-shadow:0 5px 15px rgba(0,0,0,0.1);
    }

    .top-action{
      display:flex;
      justify-content:space-between;
      margin-bottom:25px;
    }

    .btn{
      text-decoration:none;
      border:none;
      padding:12px 20px;
      border-radius:10px;
      color:white;
      cursor:pointer;
      font-weight:bold;
      display:inline-block;
    }

    .btn-back{
      background:#2d9cdb;
    }

    .btn-print{
      background:#27ae60;
    }

    .kop{
      display:flex;
      justify-content:space-between;
      align-items:flex-start;
      border-bottom:4px double #000;
      padding-bottom:10px;
      margin-bottom:20px;
    }

    .kop-left h2{
      font-size:28px;
      margin-bottom:8px;
    }

    .kop-left p{
      font-size:14px;
      line-height:1.6;
    }

    .kop-right{
      text-align:right;
      font-size:14px;
    }

    .kop-right img{
      width:85px;
      height:85px;
      object-fit:contain;
      margin-bottom:5px;
    }

    .judul-laporan{
      text-align:center;
      margin:25px 0;
    }

    .judul-laporan h1{
      font-size:28px;
      margin-bottom:8px;
      letter-spacing:1px;
    }

    .judul-laporan p{
      font-size:16px;
      font-weight:bold;
    }

    .info-box{
      display:flex;
      justify-content:flex-end;
      margin-bottom:12px;
    }

    .info-box table{
      width:450px;
      border-collapse:collapse;
    }

    .info-box td{
      border:2px solid #000;
      padding:8px 12px;
      font-weight:bold;
      font-size:15px;
    }

    .info-title{
      background:#000;
      color:white;
      text-align:center;
    }

    .info-value{
      text-align:right;
      background:#f7f5ed;
    }

    .report-table{
      width:100%;
      border-collapse:collapse;
      margin-top:20px;
      table-layout:fixed;
    }

    .report-table th{
      background:#e5e5e5;
      color:#000;
      border:2px solid #000;
      padding:10px;
      font-size:14px;
    }

    .report-table td{
      border:2px solid #000;
      padding:10px;
      font-size:13px;
      text-align:center;
      vertical-align:middle;
      word-wrap:break-word;
    }

    .report-table th:nth-child(1),
    .report-table td:nth-child(1){
      width:5%;
    }

    .report-table th:nth-child(2),
    .report-table td:nth-child(2){
      width:22%;
      text-align:left;
    }

    .report-table th:nth-child(3),
    .report-table td:nth-child(3){
      width:12%;
    }

    .report-table th:nth-child(4),
    .report-table td:nth-child(4){
      width:14%;
    }

    .report-table th:nth-child(5),
    .report-table td:nth-child(5){
      width:12%;
    }

    .report-table th:nth-child(6),
    .report-table td:nth-child(6){
      width:35%;
      text-align:left;
    }

    .kategori{
      display:inline-block;
      font-weight:bold;
      margin-bottom:6px;
      padding:3px 8px;
      border-radius:4px;
    }

    .tinggi{
      color:#155724;
      background:#d4edda;
    }

    .rendah{
      color:#721c24;
      background:#f8d7da;
    }

    .ttd{
      width:280px;
      margin-top:55px;
      margin-left:auto;
      text-align:center;
      line-height:1.8;
      font-size:15px;
    }

    @media print{
      .top-action{
        display:none;
      }

      body{
        background:white;
        padding:0;
      }

      .container{
        box-shadow:none;
        border-radius:0;
      }
    }
  </style>
</head>

<body>

<div class="container">

  <div class="top-action">
    <a href="../laporan.php" class="btn btn-back">
      ← Kembali ke Menu Laporan
    </a>

    <button onclick="window.print()" class="btn btn-print">
      Cetak Laporan
    </button>
  </div>

  <div class="kop">
    <div class="kop-left">
      <h2>TOKO IKHWAN GROGOL</h2>
      <p>
        Jl. Jembatan 17<br>
        Grogol, Depok, Jawa Barat<br>
        Telp: 0895-4147-39150
      </p>
    </div>

    <div class="kop-right">
      <img src="../assets/logo.png" alt="Logo">
      <p>toko-ikhwan-grogol.free.nf</p>
    </div>
  </div>

  <div class="judul-laporan">
    <h1>LAPORAN PREDIKSI STOK (SINGLE EXPONENTIAL SMOOTHING)</h1>
    <p><?= $tanggalIndonesia; ?></p>
  </div>

  <div class="info-box">
    <table>
      <tr>
        <td class="info-title">Metode Forecasting</td>
        <td class="info-value">Single Exponential Smoothing</td>
      </tr>
      <tr>
        <td class="info-title">Parameter Alpha (&alpha;)</td>
        <td class="info-value"><?= $alpha; ?></td>
      </tr>
      <tr>
        <td class="info-title">Total Barang</td>
        <td class="info-value"><?= $totalBarang; ?> Barang</td>
      </tr>
    </table>
  </div>

  <table class="report-table">
    <thead>
      <tr>
        <th>No</th>
        <th>Nama Barang</th>
        <th>Stok Saat Ini</th>
        <th>Prediksi Kebutuhan</th>
        <th>Akurasi</th>
        <th>Rekomendasi Prediksi Stok</th>
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
          <small>Satuan: <?= $item['jenis_barang']; ?></small>
        </td>

        <td><?= $item['stok_saat_ini']; ?> <?= $item['jenis_barang']; ?></td>

        <td>
          <b><?= number_format($item['forecast_next'], 2); ?></b>
          <br>
          <small>(~<?= $item['prediksi_stok_unit']; ?> <?= $item['jenis_barang']; ?>)</small>
        </td>

        <td><b><?= number_format($item['akurasi'], 2); ?>%</b></td>

        <td>
          <span class="kategori <?= $item['class_status']; ?>">
            <?= $item['status']; ?>
          </span>
          <br>
          <?= $item['rekomendasi']; ?>
        </td>
      </tr>

      <?php }}else{ ?>

      <tr>
        <td colspan="6">
          Belum ada data barang untuk dilakukan peramalan stok.
        </td>
      </tr>

      <?php } ?>
    </tbody>
  </table>

  <div class="ttd">
    <?= $tanggalTtd; ?>
    <br><br><br><br>
    Pemilik Toko
    <br>
    <b>Ikhwan Muarif</b>
  </div>

</div>

</body>
</html>