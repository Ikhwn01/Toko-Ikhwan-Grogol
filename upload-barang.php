<?php

include 'koneksi.php';

$no_barang = $_POST['no_barang'];
$nama_barang = $_POST['nama_barang'];
$jumlah_barang = $_POST['jumlah_barang'];
$jenis_barang = $_POST['jenis_barang'];

mysqli_query($koneksi,
"INSERT INTO barang
(no_barang,nama_barang,jumlah_barang,jenis_barang)
VALUES
('$no_barang','$nama_barang','$jumlah_barang','$jenis_barang')");

?>