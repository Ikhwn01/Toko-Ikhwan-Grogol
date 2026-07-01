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

$min_support = 2;
$min_confidence = 50;

// AMBIL DATA TRANSAKSI
$query = mysqli_query($koneksi, "
  SELECT kode_transaksi, nama_barang
  FROM detail_transaksi
  ORDER BY kode_transaksi
");

$transaksi = [];

while($row = mysqli_fetch_assoc($query)){
  $transaksi[$row['kode_transaksi']][] = $row['nama_barang'];
}

$totalTransaksi = count($transaksi);

// HITUNG KEMUNCULAN ITEM
$itemCount = [];

foreach($transaksi as $items){
  $items = array_unique($items);

  foreach($items as $item){
    if(!isset($itemCount[$item])){
      $itemCount[$item] = 0;
    }

    $itemCount[$item]++;
  }
}

// HITUNG ASSOCIATION RULE A => B
$rules = [];

foreach($transaksi as $items){
  $items = array_unique($items);

  foreach($items as $itemA){
    foreach($items as $itemB){

      if($itemA != $itemB){

        $key = $itemA . "=>" . $itemB;

        if(!isset($rules[$key])){
          $rules[$key] = [
            'antecedent' => $itemA,
            'consequent' => $itemB,
            'jumlah' => 0
          ];
        }

        $rules[$key]['jumlah']++;
      }
    }
  }
}

// FILTER BERDASARKAN SUPPORT DAN CONFIDENCE
$hasilRules = [];

foreach($rules as $rule){

  if($totalTransaksi > 0 && isset($itemCount[$rule['antecedent']])){

    $support = ($rule['jumlah'] / $totalTransaksi) * 100;
    $confidence = ($rule['jumlah'] / $itemCount[$rule['antecedent']]) * 100;

    if($rule['jumlah'] >= $min_support && $confidence >= $min_confidence){

      $hasilRules[] = [
        'antecedent' => $rule['antecedent'],
        'consequent' => $rule['consequent'],
        'jumlah' => $rule['jumlah'],
        'support' => $support,
        'confidence' => $confidence
      ];

    }
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Hasil Prediksi Stok</title>

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
      font-size:34px;
      margin-bottom:8px;
      letter-spacing:1px;
    }

    .judul-laporan p{
      font-size:18px;
      font-weight:bold;
    }

    .info-box{
      display:flex;
      justify-content:flex-end;
      margin-bottom:12px;
    }

    .info-box table{
      width:430px;
      border-collapse:collapse;
    }

    .info-box td{
      border:2px solid #000;
      padding:10px;
      font-weight:bold;
      font-size:17px;
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
      font-size:14px;
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
      width:25%;
      text-align:left;
    }

    .report-table th:nth-child(3),
    .report-table td:nth-child(3){
      width:12%;
    }

    .report-table th:nth-child(4),
    .report-table td:nth-child(4){
      width:12%;
    }

    .report-table th:nth-child(5),
    .report-table td:nth-child(5){
      width:12%;
    }

    .report-table th:nth-child(6),
    .report-table td:nth-child(6){
      width:34%;
      text-align:left;
    }

    .kategori{
      display:inline-block;
      font-weight:bold;
      margin-bottom:6px;
    }

    .tinggi{
      color:#155724;
    }

    .sedang{
      color:#856404;
    }

    .rendah{
      color:#721c24;
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
    <a href="laporan.php" class="btn btn-back">
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
      <img src="assets/logo.png" alt="Logo">
      <p>www.tokoikhwangrogol.com</p>
    </div>
  </div>

  <div class="judul-laporan">
    <h1>LAPORAN HASIL PREDIKSI STOK</h1>
    <p><?= $tanggalIndonesia; ?></p>
  </div>

  <div class="info-box">
    <table>
      <tr>
        <td class="info-title">Total Transaksi</td>
        <td class="info-value">
          <?= $totalTransaksi; ?>
        </td>
      </tr>

      <tr>
        <td class="info-title">Minimum Support</td>
        <td class="info-value">
          <?= $min_support; ?>
        </td>
      </tr>

      <tr>
        <td class="info-title">Minimum Confidence</td>
        <td class="info-value">
          <?= $min_confidence; ?>%
        </td>
      </tr>
    </table>
  </div>

  <table class="report-table">
    <thead>
      <tr>
        <th>No</th>
        <th>Aturan Asosiasi</th>
        <th>Jumlah</th>
        <th>Support</th>
        <th>Confidence</th>
        <th>Rekomendasi Prediksi Stok</th>
      </tr>
    </thead>

    <tbody>
      <?php
      $no = 1;

      if(count($hasilRules) > 0){

        foreach($hasilRules as $rule){

          if($rule['confidence'] >= 80){
            $kategori = "Hubungan Kuat";
            $class = "tinggi";
            $rekomendasi = "Stok " . $rule['consequent'] . " perlu diperbanyak karena sangat sering dibeli bersamaan dengan " . $rule['antecedent'] . ".";
          }elseif($rule['confidence'] >= 60){
            $kategori = "Hubungan Sedang";
            $class = "sedang";
            $rekomendasi = "Stok " . $rule['consequent'] . " perlu dipertahankan karena cukup sering dibeli bersamaan dengan " . $rule['antecedent'] . ".";
          }else{
            $kategori = "Hubungan Rendah";
            $class = "rendah";
            $rekomendasi = "Stok " . $rule['consequent'] . " tetap diperhatikan, tetapi tidak perlu ditambah terlalu banyak.";
          }
      ?>

      <tr>
        <td><?= $no++; ?></td>

        <td>
          Jika membeli <b><?= $rule['antecedent']; ?></b>,
          maka membeli <b><?= $rule['consequent']; ?></b>
        </td>

        <td><?= $rule['jumlah']; ?></td>

        <td><?= number_format($rule['support'],2); ?>%</td>

        <td><?= number_format($rule['confidence'],2); ?>%</td>

        <td>
          <span class="kategori <?= $class; ?>">
            <?= $kategori; ?>
          </span>
          <br>
          <?= $rekomendasi; ?>
        </td>
      </tr>

      <?php }}else{ ?>

      <tr>
        <td colspan="6">
          Belum ada aturan asosiasi yang memenuhi minimum support dan confidence.
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