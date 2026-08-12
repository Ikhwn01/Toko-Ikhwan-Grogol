<?php
require_once __DIR__ . '/../config/koneksi.php';

mysqli_autocommit($koneksi, FALSE);

// Reset data lama agar clean dan konsisten
mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 0;");
mysqli_query($koneksi, "TRUNCATE TABLE barang;");
mysqli_query($koneksi, "TRUNCATE TABLE item_transaksi;");
mysqli_query($koneksi, "TRUNCATE TABLE transaksi_penjualan_multi;");
mysqli_query($koneksi, "TRUNCATE TABLE detail_transaksi;");
mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 1;");

$sampleBarang = [
  ['no' => 'BRG01', 'nama' => 'Indomie Goreng', 'stok' => 150, 'jenis' => 'PCS', 'harga' => 3500],
  ['no' => 'BRG02', 'nama' => 'Aqua Botol 600ml', 'stok' => 120, 'jenis' => 'Botol', 'harga' => 3000],
  ['no' => 'BRG03', 'nama' => 'Teh Pucuk Harum 350ml', 'stok' => 95, 'jenis' => 'Botol', 'harga' => 4000],
  ['no' => 'BRG04', 'nama' => 'Minyak Goreng Bimoli 1L', 'stok' => 45, 'jenis' => 'Pouch', 'harga' => 18500],
  ['no' => 'BRG05', 'nama' => 'Beras Pandan Wangi 5kg', 'stok' => 30, 'jenis' => 'Karung', 'harga' => 72000],
  ['no' => 'BRG06', 'nama' => 'Gula Pasir Gulaku 1kg', 'stok' => 60, 'jenis' => 'Bungkus', 'harga' => 17500],
  ['no' => 'BRG07', 'nama' => 'Kopi Kapal Api Special 165g', 'stok' => 80, 'jenis' => 'Bungkus', 'harga' => 12500],
  ['no' => 'BRG08', 'nama' => 'Susu Dancow Fortigro 800g', 'stok' => 25, 'jenis' => 'Box', 'harga' => 85000],
  ['no' => 'BRG09', 'nama' => 'Sabun Lifebuoy 110g', 'stok' => 110, 'jenis' => 'PCS', 'harga' => 4500],
  ['no' => 'BRG10', 'nama' => 'Shampoo Pantene 160ml', 'stok' => 40, 'jenis' => 'Botol', 'harga' => 22000],
  ['no' => 'BRG11', 'nama' => 'Pasta Gigi Pepsodent 190g', 'stok' => 75, 'jenis' => 'PCS', 'harga' => 14000],
  ['no' => 'BRG12', 'nama' => 'Deterjen Rinso Anti Noda 770g', 'stok' => 50, 'jenis' => 'Bungkus', 'harga' => 24500],
  ['no' => 'BRG13', 'nama' => 'Biscuit Khong Guan 1600g', 'stok' => 20, 'jenis' => 'Kaleng', 'harga' => 95000],
  ['no' => 'BRG14', 'nama' => 'Chiki Balls Keju 55g', 'stok' => 130, 'jenis' => 'Bungkus', 'harga' => 6000],
  ['no' => 'BRG15', 'nama' => 'Chitato Sapi Panggang 68g', 'stok' => 85, 'jenis' => 'Bungkus', 'harga' => 11500],
  ['no' => 'BRG16', 'nama' => 'Coca-Cola Botol 390ml', 'stok' => 90, 'jenis' => 'Botol', 'harga' => 6000],
  ['no' => 'BRG17', 'nama' => 'Pocari Sweat 500ml', 'stok' => 70, 'jenis' => 'Botol', 'harga' => 7500],
  ['no' => 'BRG18', 'nama' => 'Roti Tawar Sari Roti', 'stok' => 35, 'jenis' => 'Bungkus', 'harga' => 15000],
  ['no' => 'BRG19', 'nama' => 'Telur Ayam Negeri 1kg', 'stok' => 50, 'jenis' => 'Kg', 'harga' => 28000],
  ['no' => 'BRG20', 'nama' => 'Mentega Blue Band 200g', 'stok' => 65, 'jenis' => 'Bungkus', 'harga' => 11000]
];

$barangIds = [];
foreach($sampleBarang as $b){
    $no = $b['no'];
    $nama = mysqli_real_escape_string($koneksi, $b['nama']);
    $stok = $b['stok'];
    $jenis = $b['jenis'];
    $harga = $b['harga'];

    mysqli_query($koneksi, "INSERT INTO barang (no_barang, nama_barang, jumlah_barang, jenis_barang, harga) VALUES ('$no', '$nama', '$stok', '$jenis', '$harga')");
    $id = mysqli_insert_id($koneksi);
    $barangIds[] = [
        'id_barang' => $id,
        'nama_barang' => $nama,
        'harga' => $harga,
        'jenis' => $jenis
    ];
}

// Generate 500 Transaksi dari tanggal 2026-06-01 sampai 2026-08-12
$startDate = strtotime('2026-06-01');
$endDate = strtotime('2026-08-12');
$numDays = floor(($endDate - $startDate) / 86400) + 1;

$numTransactions = 500;

for($t = 1; $t <= $numTransactions; $t++){
    $dayOffset = rand(0, $numDays - 1);
    $trxDate = date('Y-m-d', $startDate + ($dayOffset * 86400));
    $kodeTrx = "TRX" . date('Ymd', strtotime($trxDate)) . sprintf('%04d', $t);

    mysqli_query($koneksi, "INSERT INTO transaksi_penjualan_multi (kode_transaksi, tanggal, total_transaksi) VALUES ('$kodeTrx', '$trxDate', 0)");
    $idTrx = mysqli_insert_id($koneksi);

    $numItems = rand(1, 4);
    $pickedKeys = (array)array_rand($barangIds, $numItems);
    $totalTrx = 0;

    foreach($pickedKeys as $key){
        $item = $barangIds[$key];
        $idBarang = $item['id_barang'];
        $namaBarang = mysqli_real_escape_string($koneksi, $item['nama_barang']);
        $harga = $item['harga'];
        $qty = rand(1, 5);
        $subtotal = $harga * $qty;
        $totalTrx += $subtotal;

        mysqli_query($koneksi, "INSERT INTO item_transaksi (id_transaksi, id_barang, nama_barang, harga, jumlah, subtotal) VALUES ('$idTrx', '$idBarang', '$namaBarang', '$harga', '$qty', '$subtotal')");
        mysqli_query($koneksi, "INSERT INTO detail_transaksi (kode_transaksi, nama_barang) VALUES ('$kodeTrx', '$namaBarang')");
    }

    mysqli_query($koneksi, "UPDATE transaksi_penjualan_multi SET total_transaksi = '$totalTrx' WHERE id_transaksi = '$idTrx'");
}

mysqli_commit($koneksi);

echo "BERHASIL: 20 Sampel Barang dan 500 Transaksi Penjualan berhasil ditambahkan!";
?>
