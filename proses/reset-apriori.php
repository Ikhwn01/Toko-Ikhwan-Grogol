<?php
include '../config/koneksi.php';

// reset isi detail transaksi
mysqli_query($koneksi, "DELETE FROM detail_transaksi");

// kembali ke halaman forecasting
header("Location: ../data-mining.php");
exit;
?>