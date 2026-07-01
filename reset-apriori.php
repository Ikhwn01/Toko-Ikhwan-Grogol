<?php
include 'koneksi.php';

// reset isi detail transaksi
mysqli_query($koneksi, "DELETE FROM detail_transaksi");

// kembali ke halaman apriori
header("Location: data-mining.php");
exit;
?>