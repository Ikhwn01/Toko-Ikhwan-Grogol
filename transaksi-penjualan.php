<?php
include 'koneksi.php';

if(isset($_POST['simpan'])){

  $tanggal = $_POST['tanggal'];
  $id_barang = $_POST['id_barang'];
  $jumlah = $_POST['jumlah'];

  $kode_transaksi = "TRX" . date("YmdHis");
  $total_transaksi = 0;

  mysqli_query($koneksi, "INSERT INTO transaksi_penjualan_multi 
  (kode_transaksi, tanggal, total_transaksi)
  VALUES 
  ('$kode_transaksi', '$tanggal', '0')");

  $id_transaksi = mysqli_insert_id($koneksi);

  for($i = 0; $i < count($id_barang); $i++){

    $barang_id = $id_barang[$i];
    $qty = $jumlah[$i];

    if($barang_id == "" || $qty == ""){
      continue;
    }

    $queryBarang = mysqli_query($koneksi, "SELECT * FROM barang WHERE id_barang='$barang_id'");
    $barang = mysqli_fetch_assoc($queryBarang);

    $nama_barang = $barang['nama_barang'];
    $harga = $barang['harga'];
    $stok = $barang['jumlah_barang'];

    if($qty > $stok){
      echo "<script>
        alert('Stok $nama_barang tidak cukup');
        window.location='transaksi-penjualan.php';
      </script>";
      exit;
    }

    $subtotal = $harga * $qty;
    $total_transaksi += $subtotal;
    $stok_baru = $stok - $qty;

    mysqli_query($koneksi, "INSERT INTO item_transaksi
    (id_transaksi, id_barang, nama_barang, harga, jumlah, subtotal)
    VALUES
    ('$id_transaksi', '$barang_id', '$nama_barang', '$harga', '$qty', '$subtotal')");

    mysqli_query($koneksi, "UPDATE barang 
    SET jumlah_barang='$stok_baru'
    WHERE id_barang='$barang_id'");

    mysqli_query($koneksi, "INSERT INTO detail_transaksi
    (kode_transaksi, nama_barang)
    VALUES
    ('$kode_transaksi', '$nama_barang')");
  }

  mysqli_query($koneksi, "UPDATE transaksi_penjualan_multi 
  SET total_transaksi='$total_transaksi'
  WHERE id_transaksi='$id_transaksi'");

  echo "<script>
    alert('Transaksi berhasil disimpan');
    window.location='transaksi-penjualan.php';
  </script>";
}

if(isset($_GET['hapus'])){
  $id = $_GET['hapus'];

  mysqli_query($koneksi, "DELETE FROM item_transaksi WHERE id_transaksi='$id'");
  mysqli_query($koneksi, "DELETE FROM transaksi_penjualan_multi WHERE id_transaksi='$id'");

  echo "<script>
    alert('Transaksi berhasil dihapus');
    window.location='transaksi-penjualan.php';
  </script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Transaksi Penjualan</title>

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
      margin-bottom:25px;
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
    }

    .form-group{
      margin-bottom:20px;
    }

    label{
      display:block;
      font-weight:bold;
      margin-bottom:8px;
    }

    input, select{
      width:100%;
      padding:12px;
      border:1px solid #ccc;
      border-radius:10px;
      font-size:15px;
    }

    .item-row{
      display:grid;
      grid-template-columns:2fr 1fr 1fr 1fr auto;
      gap:15px;
      margin-bottom:15px;
      align-items:end;
    }

    .btn{
      border:none;
      padding:12px 20px;
      border-radius:10px;
      color:white;
      cursor:pointer;
      text-decoration:none;
      display:inline-block;
      font-weight:bold;
    }

    .btn-add{
      background:#2d9cdb;
      margin-bottom:20px;
    }

    .btn-save{
      background:#1f64e0;
    }

    .btn-remove{
      background:#e74c3c;
    }

    .btn-hapus{
      background:#e74c3c;
      padding:8px 14px;
    }

    .total-box{
      background:#eef4ff;
      color:#1f64e0;
      padding:15px;
      border-radius:12px;
      font-weight:bold;
      margin:20px 0;
    }

    table{
      width:100%;
      border-collapse:collapse;
      margin-top:30px;
    }

    th{
      background:#1f64e0;
      color:white;
      padding:12px;
      border:1px solid #ddd;
    }

    td{
      padding:12px;
      border:1px solid #ddd;
      text-align:center;
    }

    .action-button{
      margin-top:20px;
      display:flex;
      gap:10px;
    }

    .btn-reset{
        text-decoration:none;
        background:#e74c3c;
        color:white;
        padding:12px 20px;
        border-radius:10px;
        font-weight:bold;
    }

    .btn-reset:hover{
        background:#c0392b;
    }
  </style>
</head>

<body>

<div class="container">

  <div class="header-top">
    <h1>Transaksi Penjualan</h1>

    <div class="top-action">
      <a href="dashboard.php">← Kembali ke Dashboard</a>
    </div>
  </div>

  <form method="POST" id="transaksiForm">

    <div class="form-group">
      <label>Tanggal</label>
      <input type="date" name="tanggal" value="<?= date('Y-m-d'); ?>" required>
    </div>

    <div id="itemContainer">

      <div class="item-row">

        <div>
          <label>Nama Barang</label>
          <select name="id_barang[]" class="barang" required>
            <option value="">-- Pilih Barang --</option>

            <?php
            $barang = mysqli_query($koneksi, "SELECT * FROM barang ORDER BY nama_barang ASC");
            while($b = mysqli_fetch_assoc($barang)){
            ?>
              <option 
                value="<?= $b['id_barang']; ?>"
                data-harga="<?= $b['harga']; ?>"
                data-stok="<?= $b['jumlah_barang']; ?>"
              >
                <?= $b['nama_barang']; ?> - Stok: <?= $b['jumlah_barang']; ?>
              </option>
            <?php } ?>

          </select>
        </div>

        <div>
          <label>Harga</label>
          <input type="number" class="harga" readonly>
        </div>

        <div>
          <label>Jumlah</label>
          <input type="number" name="jumlah[]" class="jumlah" min="1" required>
        </div>

        <div>
          <label>Subtotal</label>
          <input type="number" class="subtotal" readonly>
        </div>

        <button type="button" class="btn btn-remove" onclick="hapusItem(this)">
          Hapus
        </button>

      </div>

    </div>

    <button type="button" class="btn btn-add" onclick="tambahItem()">
      + Tambah Barang
    </button>

    <div class="total-box">
      Total Transaksi: Rp <span id="totalTransaksi">0</span>
    </div>

    <button type="submit" name="simpan" class="btn btn-save">
      Simpan Transaksi
    </button>
    <a href="reset-transaksi.php"
      class="btn btn-hapus"
      onclick="return confirm('Yakin ingin mereset semua transaksi? Barang terlaris juga akan ikut kosong.')">
      Reset Transaksi
    </a>
  </form>

  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Kode Transaksi</th>
        <th>Tanggal</th>
        <th>Total Transaksi</th>
        <th>Detail Barang</th>
        <th>Aksi</th>
      </tr>
    </thead>

    <tbody>
      <?php
      $no = 1;
      $transaksi = mysqli_query($koneksi, "SELECT * FROM transaksi_penjualan_multi ORDER BY id_transaksi DESC");

      while($t = mysqli_fetch_assoc($transaksi)){
      ?>
      <tr>
        <td><?= $no++; ?></td>
        <td><?= $t['kode_transaksi']; ?></td>
        <td><?= $t['tanggal']; ?></td>
        <td>Rp <?= number_format($t['total_transaksi'],0,',','.'); ?></td>
        <td>
          <?php
          $items = mysqli_query($koneksi, "SELECT * FROM item_transaksi WHERE id_transaksi='".$t['id_transaksi']."'");

          while($i = mysqli_fetch_assoc($items)){
            echo $i['nama_barang'] . " (" . $i['jumlah'] . ")<br>";
          }
          ?>
        </td>
        <td>
          <a href="transaksi-penjualan.php?hapus=<?= $t['id_transaksi']; ?>" 
             class="btn btn-hapus"
             onclick="return confirm('Yakin ingin menghapus transaksi ini?')">
            Hapus
          </a>
        </td>
      </tr>
      <?php } ?>
    </tbody>
  </table>

</div>

<script>
function hitungTotal(){
  let total = 0;

  document.querySelectorAll(".item-row").forEach(row => {
    const harga = parseInt(row.querySelector(".harga").value) || 0;
    const jumlah = parseInt(row.querySelector(".jumlah").value) || 0;
    const subtotal = harga * jumlah;

    row.querySelector(".subtotal").value = subtotal;
    total += subtotal;
  });

  document.getElementById("totalTransaksi").innerText =
  total.toLocaleString("id-ID");
}

function pasangEvent(row){
  const barang = row.querySelector(".barang");
  const harga = row.querySelector(".harga");
  const jumlah = row.querySelector(".jumlah");

  barang.addEventListener("change", function(){
    const selected = this.options[this.selectedIndex];
    const hargaBarang = selected.getAttribute("data-harga") || 0;

    harga.value = hargaBarang;
    hitungTotal();
  });

  jumlah.addEventListener("input", hitungTotal);
}

document.querySelectorAll(".item-row").forEach(row => {
  pasangEvent(row);
});

function tambahItem(){
  const container = document.getElementById("itemContainer");
  const itemPertama = document.querySelector(".item-row");

  const itemBaru = itemPertama.cloneNode(true);

  itemBaru.querySelector(".barang").value = "";
  itemBaru.querySelector(".harga").value = "";
  itemBaru.querySelector(".jumlah").value = "";
  itemBaru.querySelector(".subtotal").value = "";

  container.appendChild(itemBaru);
  pasangEvent(itemBaru);
}

function hapusItem(button){
  const rows = document.querySelectorAll(".item-row");

  if(rows.length > 1){
    button.parentElement.remove();
    hitungTotal();
  }else{
    alert("Minimal harus ada 1 barang dalam transaksi");
  }
}
</script>

</body>
</html>