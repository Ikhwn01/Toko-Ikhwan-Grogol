<?php
include 'koneksi.php';

/*
Reset transaksi penjualan
Sekaligus mereset laporan barang terlaris
dan data untuk Apriori
*/

mysqli_query($koneksi, "DELETE FROM item_transaksi");
mysqli_query($koneksi, "DELETE FROM transaksi_penjualan_multi");
mysqli_query($koneksi, "DELETE FROM detail_transaksi");

echo "<script>
  alert('Data transaksi, barang terlaris, dan data Apriori berhasil direset');
  window.location='transaksi-penjualan.php';
</script>";
?>