-- Database: db_toko (TiDB Cloud Compatible)
CREATE DATABASE IF NOT EXISTS `db_toko`;
USE `db_toko`;

DROP TABLE IF EXISTS `item_transaksi`;
DROP TABLE IF EXISTS `detail_transaksi`;
DROP TABLE IF EXISTS `transaksi_penjualan_multi`;
DROP TABLE IF EXISTS `transaksi_penjualan`;
DROP TABLE IF EXISTS `barang`;
DROP TABLE IF EXISTS `users`;

-- Table structure for table `barang`
CREATE TABLE `barang` (
  `id_barang` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `no_barang` varchar(50) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `jumlah_barang` int(11) NOT NULL,
  `jenis_barang` varchar(50) NOT NULL,
  `harga` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `barang` (`id_barang`, `no_barang`, `nama_barang`, `jumlah_barang`, `jenis_barang`, `harga`) VALUES
(34, 'BRG01', 'Indomie Goreng', 16, 'PCS', 3500),
(35, 'BRG02', 'Aqua Botol', 31, 'Botol', 3000);

-- Table structure for table `detail_transaksi`
CREATE TABLE `detail_transaksi` (
  `id_detail` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `kode_transaksi` varchar(50) DEFAULT NULL,
  `nama_barang` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `detail_transaksi` (`id_detail`, `kode_transaksi`, `nama_barang`) VALUES
(29, 'TRX20260530151743', 'Indomie Goreng'),
(30, 'TRX20260530151743', 'Aqua Botol'),
(31, 'TRX20260530151756', 'Aqua Botol'),
(32, 'TRX20260530151756', 'Indomie Goreng'),
(33, 'TRX20260530151811', 'Aqua Botol'),
(34, 'TRX20260530151811', 'Indomie Goreng'),
(35, 'TRX20260530151824', 'Aqua Botol'),
(36, 'TRX20260530151835', 'Indomie Goreng'),
(37, 'TRX20260530151928', 'Indomie Goreng'),
(38, 'TRX20260530151928', 'Aqua Botol'),
(39, 'TRX20260530151946', 'Aqua Botol'),
(40, 'TRX20260530151946', 'Indomie Goreng'),
(41, 'TRX20260530152017', 'Indomie Goreng'),
(42, 'TRX20260530152017', 'Aqua Botol'),
(43, 'TRX20260530152027', 'Aqua Botol'),
(44, 'TRX20260530152044', 'Indomie Goreng'),
(45, 'TRX20260530152044', 'Aqua Botol'),
(46, 'TRX20260530152135', 'Indomie Goreng'),
(47, 'TRX20260530152353', 'Aqua Botol'),
(48, 'TRX20260530152353', 'Indomie Goreng');

-- Table structure for table `item_transaksi`
CREATE TABLE `item_transaksi` (
  `id_item` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_transaksi` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `harga` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `subtotal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `item_transaksi` (`id_item`, `id_transaksi`, `id_barang`, `nama_barang`, `harga`, `jumlah`, `subtotal`) VALUES
(19, 15, 34, 'Indomie Goreng', 3500, 2, 7000),
(20, 15, 35, 'Aqua Botol', 3000, 2, 6000),
(21, 16, 35, 'Aqua Botol', 3000, 1, 3000),
(22, 16, 34, 'Indomie Goreng', 3500, 2, 7000),
(23, 17, 35, 'Aqua Botol', 3000, 2, 6000),
(24, 17, 34, 'Indomie Goreng', 3500, 1, 3500),
(25, 18, 35, 'Aqua Botol', 3000, 2, 6000),
(26, 19, 34, 'Indomie Goreng', 3500, 2, 7000),
(27, 20, 34, 'Indomie Goreng', 3500, 5, 17500),
(28, 20, 35, 'Aqua Botol', 3000, 2, 6000),
(29, 21, 35, 'Aqua Botol', 3000, 3, 9000),
(30, 21, 34, 'Indomie Goreng', 3500, 6, 21000),
(31, 22, 34, 'Indomie Goreng', 3500, 1, 3500),
(32, 22, 35, 'Aqua Botol', 3000, 1, 3000),
(33, 23, 35, 'Aqua Botol', 3000, 3, 9000),
(34, 24, 34, 'Indomie Goreng', 3500, 4, 14000),
(35, 24, 35, 'Aqua Botol', 3000, 1, 3000),
(36, 25, 34, 'Indomie Goreng', 3500, 10, 35000),
(37, 26, 35, 'Aqua Botol', 3000, 2, 6000),
(38, 26, 34, 'Indomie Goreng', 3500, 1, 3500);

-- Table structure for table `transaksi_penjualan`
CREATE TABLE `transaksi_penjualan` (
  `id_transaksi` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `tanggal` date NOT NULL,
  `id_barang` int(11) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `harga` int(11) NOT NULL,
  `jumlah_beli` int(11) NOT NULL,
  `total_harga` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `transaksi_penjualan_multi`
CREATE TABLE `transaksi_penjualan_multi` (
  `id_transaksi` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `kode_transaksi` varchar(50) NOT NULL,
  `tanggal` date NOT NULL,
  `total_transaksi` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `transaksi_penjualan_multi` (`id_transaksi`, `kode_transaksi`, `tanggal`, `total_transaksi`) VALUES
(15, 'TRX20260530151743', '2026-05-30', 13000),
(16, 'TRX20260530151756', '2026-05-30', 10000),
(17, 'TRX20260530151811', '2026-05-30', 9500),
(18, 'TRX20260530151824', '2026-05-30', 6000),
(19, 'TRX20260530151835', '2026-05-30', 7000),
(20, 'TRX20260530151928', '2026-05-30', 23500),
(21, 'TRX20260530151946', '2026-05-30', 30000),
(22, 'TRX20260530152017', '2026-05-30', 6500),
(23, 'TRX20260530152027', '2026-05-30', 9000),
(24, 'TRX20260530152044', '2026-05-30', 17000),
(25, 'TRX20260530152135', '2026-05-30', 35000),
(26, 'TRX20260530152353', '2026-05-30', 9500);

-- Table structure for table `users`
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `level` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` (`id`, `username`, `password`, `level`, `email`, `reset_token`) VALUES
(1, 'admin', '$2y$10$l7mhZlz3vupfgCjcTNhY3.P7Bl6YDFxpAJBv94doUNP7D8iEUlzZ6', 'admin', 'ikhwanmuarif71@gmail.com', '');
