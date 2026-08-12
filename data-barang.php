<?php
include 'config/koneksi.php';

$edit = null;
$search = $_GET['search'] ?? '';

if(isset($_GET['edit'])){
  $id = $_GET['edit'];
  $query = mysqli_query($koneksi, "SELECT * FROM barang WHERE id_barang='$id'");
  $edit = mysqli_fetch_assoc($query);
}

if(isset($_POST['simpan'])){
  $no_barang = $_POST['no_barang'];
  $nama_barang = $_POST['nama_barang'];
  $jumlah_barang = $_POST['jumlah_barang'];
  $harga = $_POST['harga'];
  $jenis_barang = $_POST['jenis_barang'];

  mysqli_query($koneksi, "INSERT INTO barang 
  (no_barang, nama_barang, jumlah_barang, harga, jenis_barang)
  VALUES 
  ('$no_barang', '$nama_barang', '$jumlah_barang', '$harga', '$jenis_barang')");

  header("Location: data-barang.php");
}

if(isset($_POST['update'])){
  $id_barang = $_POST['id_barang'];
  $no_barang = $_POST['no_barang'];
  $nama_barang = $_POST['nama_barang'];
  $jumlah_barang = $_POST['jumlah_barang'];
  $harga = $_POST['harga'];
  $jenis_barang = $_POST['jenis_barang'];

  mysqli_query($koneksi, "UPDATE barang SET
  no_barang='$no_barang',
  nama_barang='$nama_barang',
  jumlah_barang='$jumlah_barang',
  harga='$harga',
  jenis_barang='$jenis_barang'
  WHERE id_barang='$id_barang'");

  header("Location: data-barang.php");
}

if(isset($_GET['hapus'])){
  $id = $_GET['hapus'];
  mysqli_query($koneksi, "DELETE FROM barang WHERE id_barang='$id'");
  header("Location: data-barang.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Barang</title>

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
      border-radius:15px;
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

    .form-grid{
      display:grid;
      grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
      gap:20px;
    }

    .form-group label{
      display:block;
      margin-bottom:8px;
      font-weight:bold;
    }

    .form-group input{
      width:100%;
      padding:12px;
      border:1px solid #ccc;
      border-radius:10px;
    }

    button, .btn{
      display:inline-block;
      text-decoration:none;
      margin-top:20px;
      padding:12px 20px;
      color:white;
      border:none;
      border-radius:10px;
      cursor:pointer;
      font-size:15px;
    }

    .btn-simpan{
      background:#1f64e0;
    }

    .btn-edit{
      background:#f39c12;
      padding:8px 14px;
      margin:2px;
    }

    .btn-hapus{
      background:#e74c3c;
      padding:8px 14px;
      margin:2px;
    }

    .btn-batal{
      background:gray;
    }

    .search-box{
  margin:35px 0 25px 0;
}

.search-box form{
  display:flex;
  align-items:center;
  gap:15px;
  flex-wrap:wrap;
}

.search-box input{
  width:340px;
  height:52px;
  padding:0 18px;
  border:2px solid #ddd;
  border-radius:12px;
  font-size:16px;
  outline:none;
  transition:0.3s;
}

.search-box input:focus{
  border-color:#2d9cdb;
  box-shadow:0 0 8px rgba(45,156,219,0.2);
}

.search-box .btn-search{
  background:#2d9cdb;
  color:white;
  border:none;

  height:52px;
  padding:0 30px;

  border-radius:12px;

  font-size:16px;
  font-weight:bold;

  cursor:pointer;

  display:flex;
  align-items:center;
  justify-content:center;

  margin-top:0 !important;

  transition:0.3s;
}

.search-box .btn-search:hover{
  background:#1d87c5;
}

.search-box .btn-reset{
  background:#e74c3c;
  color:white;
  text-decoration:none;

  height:52px;
  padding:0 30px;

  border-radius:12px;

  font-size:16px;
  font-weight:bold;

  display:flex;
  align-items:center;
  justify-content:center;

  margin-top:0 !important;

  transition:0.3s;
}

.search-box .btn-reset:hover{
  background:#c0392b;
}

    .btn-search{
      background:#2d9cdb;
    }

    .btn-reset{
      background:#e74c3c;
      margin-top:20px;
    }

    .upload-box{
      margin-top:35px;
    }

    .upload-box input{
      margin-top:15px;
    }

    table{
      width:100%;
      border-collapse:collapse;
      margin-top:30px;
    }

    table th{
      background:#1f64e0;
      color:white;
      padding:12px;
      border:1px solid #ddd;
    }

    table td{
      padding:12px;
      border:1px solid #ddd;
      text-align:center;
    }
  </style>
</head>

<body>

<div class="container">

  <div class="header-top">
    <h1>Manajemen Data Barang</h1>

    <div class="top-action">
      <a href="dashboard.php">← Kembali ke Dashboard</a>
    </div>
  </div>

  <form method="POST">

    <?php if($edit){ ?>
      <input type="hidden" name="id_barang" value="<?= $edit['id_barang']; ?>">
    <?php } ?>

    <div class="form-grid">

      <div class="form-group">
        <label>No Barang</label>
        <input type="text" name="no_barang" required
        value="<?= $edit ? $edit['no_barang'] : ''; ?>">
      </div>

      <div class="form-group">
        <label>Nama Barang</label>
        <input type="text" name="nama_barang" required
        value="<?= $edit ? $edit['nama_barang'] : ''; ?>">
      </div>

      <div class="form-group">
        <label>Jumlah Barang</label>
        <input type="number" name="jumlah_barang" required
        value="<?= $edit ? $edit['jumlah_barang'] : ''; ?>">
      </div>

      <div class="form-group">
        <label>Harga Barang</label>
        <input type="number" name="harga" required
        value="<?= $edit ? $edit['harga'] : ''; ?>">
      </div>

      <div class="form-group">
        <label>Jenis Satuan</label>
        <input type="text" name="jenis_barang" required
        value="<?= $edit ? $edit['jenis_barang'] : ''; ?>">
      </div>

    </div>

    <?php if($edit){ ?>
      <button type="submit" name="update" class="btn-simpan">Update</button>
      <a href="data-barang.php" class="btn btn-batal">Batal</a>
    <?php }else{ ?>
      <button type="submit" name="simpan" class="btn-simpan">Simpan</button>
    <?php } ?>

  </form>

  <div class="search-box">
  <form method="GET">

    <input 
      type="text"
      name="search"
      placeholder="Cari no barang atau nama barang..."
      value="<?= $search; ?>"
    >

    <button type="submit" class="btn-search">
      Search
    </button>

    <a href="data-barang.php" class="btn-reset">
      Reset
    </a>

  </form>
</div>

  <div class="upload-box">
    <h2>Upload Excel / CSV</h2>

    <input
      type="file"
      id="fileBarang"
      accept=".xlsx,.xls,.csv"
    >

    <p style="margin-top:10px;color:gray;">
      Format: No Barang | Nama Barang | Jumlah Barang | Harga | Jenis
    </p>
  </div>

  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>No Barang</th>
        <th>Nama Barang</th>
        <th>Jumlah Barang</th>
        <th>Harga</th>
        <th>Jenis Satuan</th>
        <th>Aksi</th>
      </tr>
    </thead>

    <tbody>
      <?php
      $no = 1;

      $data = mysqli_query($koneksi, "
        SELECT * FROM barang
        WHERE 
        no_barang LIKE '%$search%'
        OR nama_barang LIKE '%$search%'
        OR jenis_barang LIKE '%$search%'
        ORDER BY id_barang DESC
      ");

      while($row = mysqli_fetch_assoc($data)){
      ?>
      <tr>
        <td><?= $no++; ?></td>
        <td><?= $row['no_barang']; ?></td>
        <td><?= $row['nama_barang']; ?></td>
        <td><?= $row['jumlah_barang']; ?></td>
        <td>Rp <?= number_format($row['harga'],0,',','.'); ?></td>
        <td><?= $row['jenis_barang']; ?></td>
        <td>
          <a href="data-barang.php?edit=<?= $row['id_barang']; ?>" class="btn btn-edit">
            Edit
          </a>

          <a href="data-barang.php?hapus=<?= $row['id_barang']; ?>" 
             class="btn btn-hapus"
             onclick="return confirm('Yakin ingin menghapus data ini?')">
            Hapus
          </a>
        </td>
      </tr>
      <?php } ?>

      <?php if($no == 1){ ?>
      <tr>
        <td colspan="7">Data barang tidak ditemukan.</td>
      </tr>
      <?php } ?>
    </tbody>
  </table>

</div>

<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>

<script>
const fileBarang = document.getElementById('fileBarang');

fileBarang.addEventListener('change', function(e){
  const file = e.target.files[0];

  if(!file){
    alert('Pilih file terlebih dahulu');
    return;
  }

  const fileName = file.name.toLowerCase();

  if(fileName.endsWith('.xlsx') || fileName.endsWith('.xls')){
    const reader = new FileReader();

    reader.onload = function(event){
      const data = new Uint8Array(event.target.result);

      const workbook = XLSX.read(data,{
        type:'array'
      });

      const firstSheet = workbook.SheetNames[0];
      const worksheet = workbook.Sheets[firstSheet];

      const excelRows = XLSX.utils.sheet_to_json(
        worksheet,
        {header:1}
      );

      uploadData(excelRows);
    };

    reader.readAsArrayBuffer(file);
  }

  else if(fileName.endsWith('.csv')){
    const reader = new FileReader();

    reader.onload = function(event){
      const csv = event.target.result;
      const lines = csv.split(/\r?\n/);
      let rows = [];

      lines.forEach(line => {
        rows.push(line.split(','));
      });

      uploadData(rows);
    };

    reader.readAsText(file);
  }

  else{
    alert('Format file tidak didukung');
  }
});

function uploadData(rows){
  for(let i = 1; i < rows.length; i++){
    const row = rows[i];

    if(row.length >= 5){
      fetch('proses/upload-barang.php', {
        method:'POST',
        headers:{
          'Content-Type':'application/x-www-form-urlencoded'
        },
        body:
        `no_barang=${row[0]}&nama_barang=${row[1]}&jumlah_barang=${row[2]}&harga=${row[3]}&jenis_barang=${row[4]}`
      });
    }
  }

  alert('Data berhasil diupload');

  setTimeout(() => {
    location.reload();
  },1000);
}
</script>

</body>
</html>