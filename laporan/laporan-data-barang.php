<?php
include '../config/koneksi.php';

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
date('d') . " " .
$bulan[(int)date('m')] . " " .
date('Y');

$data = mysqli_query($koneksi, "
  SELECT * FROM barang
  ORDER BY nama_barang ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Data Barang</title>

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
      font-weight:bold;
      cursor:pointer;
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
      font-size:34px;
      margin-bottom:8px;
      letter-spacing:1px;
    }

    .judul-laporan p{
      font-size:18px;
      font-weight:bold;
    }

    table{
      width:100%;
      border-collapse:collapse;
      margin-top:20px;
    }

    th{
      background:#e5e5e5;
      border:2px solid #000;
      padding:10px;
      font-size:15px;
    }

    td{
      border:2px solid #000;
      padding:10px;
      text-align:center;
      font-size:15px;
    }

    td:nth-child(3){
      text-align:left;
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
      <p>www.tokoikhwangrogol.com</p>
    </div>
  </div>

  <div class="judul-laporan">
    <h1>LAPORAN DATA BARANG</h1>
    <p><?= $tanggalIndonesia; ?></p>
  </div>

  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>No Barang</th>
        <th>Nama Barang</th>
        <th>Jumlah Stok</th>
        <th>Harga</th>
        <th>Jenis Satuan</th>
      </tr>
    </thead>

    <tbody>
      <?php
      $no = 1;
      while($row = mysqli_fetch_assoc($data)){
      ?>
      <tr>
        <td><?= $no++; ?></td>
        <td><?= $row['no_barang']; ?></td>
        <td><?= $row['nama_barang']; ?></td>
        <td><?= $row['jumlah_barang']; ?></td>
        <td>Rp <?= number_format($row['harga'],0,',','.'); ?></td>
        <td><?= $row['jenis_barang']; ?></td>
      </tr>
      <?php } ?>

      <?php if($no == 1){ ?>
      <tr>
        <td colspan="6">Belum ada data barang.</td>
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