<?php
include 'koneksi.php';

$min_support = 2;
$min_confidence = 50;

/* AMBIL DATA TRANSAKSI */
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

/* HITUNG JUMLAH KEMUNCULAN ITEM */
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

/* HITUNG ASSOCIATION RULE A => B */
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

/* FILTER RULE BERDASARKAN MIN SUPPORT DAN CONFIDENCE */
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
  <title>Hasil Prediksi Stok</title>

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
      padding:15px;
      border-radius:12px;
      margin-bottom:25px;
      font-weight:bold;
      line-height:1.6;
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
    td:nth-child(6){
      text-align:left;
    }

    .tinggi{
      color:#155724;
      font-weight:bold;
      background:#d4edda;
      padding:7px 10px;
      border-radius:8px;
      display:inline-block;
    }

    .sedang{
      color:#856404;
      font-weight:bold;
      background:#fff3cd;
      padding:7px 10px;
      border-radius:8px;
      display:inline-block;
    }

    .rendah{
      color:#721c24;
      font-weight:bold;
      background:#f8d7da;
      padding:7px 10px;
      border-radius:8px;
      display:inline-block;
    }
  </style>
</head>

<body>

<div class="container">

  <div class="header-top">

    <h1>Hasil Prediksi Stok Barang</h1>

    <div class="top-action">
      <a href="dashboard.php">
        ← Kembali ke Dashboard
      </a>
    </div>

  </div>

  <div class="info">
    Hasil prediksi stok dihitung berdasarkan pola asosiasi algoritma Apriori.
    <br>
    Minimum Support: <?= $min_support; ?>
    <br>
    Minimum Confidence: <?= $min_confidence; ?>%
    <br>
    Total Transaksi: <?= $totalTransaksi; ?>
  </div>

  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Aturan Asosiasi</th>
        <th>Jumlah Transaksi Bersama</th>
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
          <span class="<?= $class; ?>">
            <?= $kategori; ?>
          </span>
          <br><br>
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

</div>

</body>
</html>