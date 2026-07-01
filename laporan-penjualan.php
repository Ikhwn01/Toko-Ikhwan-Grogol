<?php
include 'koneksi.php';

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

$dari = $_GET['dari'] ?? '';
$sampai = $_GET['sampai'] ?? '';

$whereData = "";
$whereTotal = "";

if($dari != '' && $sampai != ''){
  $whereData = "WHERE t.tanggal BETWEEN '$dari' AND '$sampai'";
  $whereTotal = "WHERE tanggal BETWEEN '$dari' AND '$sampai'";
}

$data = mysqli_query($koneksi, "
  SELECT 
    t.kode_transaksi,
    t.tanggal,
    i.nama_barang,
    i.harga,
    i.jumlah AS jumlah_beli,
    i.subtotal AS total_harga
  FROM transaksi_penjualan_multi t
  JOIN item_transaksi i 
  ON t.id_transaksi = i.id_transaksi
  $whereData
  ORDER BY t.tanggal DESC
");

$totalQuery = mysqli_query($koneksi, "
  SELECT SUM(total_transaksi) AS total
  FROM transaksi_penjualan_multi
  $whereTotal
");

$totalData = mysqli_fetch_assoc($totalQuery);
$total = $totalData['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Penjualan</title>

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

    .btn-filter{
      background:#1f64e0;
    }

    .filter{
      display:flex;
      gap:15px;
      margin-bottom:25px;
      flex-wrap:wrap;
    }

    .filter input{
      padding:12px;
      border:1px solid #ccc;
      border-radius:10px;
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

    .income-box{
      display:flex;
      justify-content:flex-end;
      margin-bottom:12px;
    }

    .income-box table{
      width:420px;
      border-collapse:collapse;
    }

    .income-box td{
      border:2px solid #000;
      padding:10px;
      font-weight:bold;
      font-size:18px;
    }

    .income-title{
      background:#000;
      color:white;
      text-align:center;
    }

    .income-value{
      text-align:right;
      background:#f7f5ed;
    }

    .report-table{
      width:100%;
      border-collapse:collapse;
      margin-top:20px;
    }

    .report-table th{
      background:#e5e5e5;
      color:#000;
      border:2px solid #000;
      padding:10px;
      font-size:15px;
    }

    .report-table td{
      border:2px solid #000;
      padding:10px;
      font-size:15px;
      text-align:center;
    }

    .report-table td:nth-child(3){
      text-align:left;
    }

    .total-row td{
      font-weight:bold;
      background:#e5e5e5;
      font-size:16px;
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
      .top-action,
      .filter{
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
    <a href="laporan.php" class="btn btn-back">
      ← Kembali ke Menu Laporan
    </a>

    <button type="button" onclick="window.print()" class="btn btn-print">
      Cetak Laporan
    </button>
  </div>

  <form method="GET" class="filter">
    <input type="date" name="dari" value="<?= $dari; ?>">
    <input type="date" name="sampai" value="<?= $sampai; ?>">

    <button type="submit" class="btn btn-filter">
      Filter
    </button>

    <a href="laporan-penjualan.php" class="btn btn-filter">
      Reset
    </a>
  </form>

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
      <img src="assets/logo.png" alt="Logo">
      <p>www.tokoikhwangrogol.com</p>
    </div>
  </div>

  <div class="judul-laporan">
    <h1>LAPORAN PENJUALAN</h1>
    <p><?= $tanggalIndonesia; ?></p>
  </div>

  <div class="income-box">
    <table>
      <tr>
        <td class="income-title">Total Pendapatan</td>
        <td class="income-value">
          Rp <?= number_format($total,0,',','.'); ?>
        </td>
      </tr>
    </table>
  </div>

  <table class="report-table">
    <thead>
      <tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Produk</th>
        <th>Harga</th>
        <th>Total Unit</th>
        <th>Total Penjualan</th>
      </tr>
    </thead>

    <tbody>
      <?php
      $no = 1;
      while($row = mysqli_fetch_assoc($data)){
      ?>
      <tr>
        <td><?= $no++; ?></td>
        <td><?= $row['tanggal']; ?></td>
        <td><?= $row['nama_barang']; ?></td>
        <td>Rp <?= number_format($row['harga'],0,',','.'); ?></td>
        <td><?= $row['jumlah_beli']; ?></td>
        <td>Rp <?= number_format($row['total_harga'],0,',','.'); ?></td>
      </tr>
      <?php } ?>

      <?php if($no == 1){ ?>
      <tr>
        <td colspan="6">Belum ada data penjualan.</td>
      </tr>
      <?php } ?>

      <tr class="total-row">
        <td colspan="5">TOTAL</td>
        <td>Rp <?= number_format($total,0,',','.'); ?></td>
      </tr>
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