<?php
include 'koneksi.php';

$min_support = $_POST['support'] ?? 2;
$min_confidence = $_POST['confidence'] ?? 50;

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

/* HITUNG SUPPORT ITEM */
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

/* HITUNG RULE A => B */
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

$hasilRules = [];

foreach($rules as $rule){
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
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Mining Apriori</title>

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Arial;}
body{background:#f4f6f9;padding:30px;}
.container{background:white;padding:30px;border-radius:15px;box-shadow:0 5px 15px rgba(0,0,0,0.1);}
.header-top{display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;}
h1{color:#1f64e0;}
.top-action a{background:#2d9cdb;color:white;text-decoration:none;padding:13px 22px;border-radius:12px;font-weight:bold;}
.form-apriori{display:flex;gap:15px;align-items:end;background:#f8fbff;padding:20px;border-radius:12px;margin-bottom:25px;}
.form-group label{display:block;font-weight:bold;margin-bottom:8px;}
.form-group input{padding:12px;border:1px solid #ccc;border-radius:10px;width:180px;}
.btn-proses{background:#1f64e0;color:white;border:none;padding:12px 22px;border-radius:10px;font-weight:bold;cursor:pointer;}
.info{background:#eef4ff;color:#1f64e0;padding:15px;border-radius:10px;margin-bottom:20px;font-weight:bold;}
table{width:100%;border-collapse:collapse;margin-top:20px;}
th{background:#1f64e0;color:white;padding:12px;border:1px solid #ddd;}
td{padding:12px;border:1px solid #ddd;text-align:center;}
.reset-box{margin-top:25px;}
.reset-box a{background:#e74c3c;color:white;text-decoration:none;padding:12px 22px;border-radius:10px;font-weight:bold;}
</style>
</head>

<body>

<div class="container">

<div class="header-top">
  <h1>Data Mining Apriori</h1>
  <div class="top-action">
    <a href="dashboard.php">← Kembali ke Dashboard</a>
  </div>
</div>

<form method="POST" class="form-apriori">
  <div class="form-group">
    <label>Minimum Support</label>
    <input type="number" name="support" value="<?= $min_support; ?>" min="1">
  </div>

  <div class="form-group">
    <label>Minimum Confidence (%)</label>
    <input type="number" name="confidence" value="<?= $min_confidence; ?>" min="1" max="100">
  </div>

  <button type="submit" class="btn-proses">Proses Apriori</button>
</form>

<div class="info">
  Minimum Support: <?= $min_support; ?><br><br>
  Minimum Confidence: <?= $min_confidence; ?>%<br><br>
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
  <th>Rekomendasi</th>
</tr>
</thead>

<tbody>
<?php
$no = 1;

if(count($hasilRules) > 0){
  foreach($hasilRules as $rule){
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
    Stok <?= $rule['consequent']; ?> perlu diperhatikan karena sering dibeli bersama <?= $rule['antecedent']; ?>.
  </td>
</tr>
<?php }}else{ ?>
<tr>
  <td colspan="6">Belum ada aturan asosiasi yang memenuhi minimum support dan confidence.</td>
</tr>
<?php } ?>
</tbody>
</table>

<div class="reset-box">
  <a href="reset-apriori.php" onclick="return confirm('Yakin ingin mereset data Apriori?')">
    Reset Data Apriori
  </a>
</div>

</div>

</body>
</html>