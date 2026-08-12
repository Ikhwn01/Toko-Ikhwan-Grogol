<?php
/**
 * Engine Single Exponential Smoothing (SES)
 * Untuk melakukan forecasting kebutuhan stok barang berdasarkan riwayat penjualan.
 */

function hitungSES($koneksi, $alpha = 0.2){
    $alpha = floatval($alpha);
    if($alpha <= 0 || $alpha >= 1){
        $alpha = 0.2;
    }

    $queryBarang = mysqli_query($koneksi, "SELECT * FROM barang ORDER BY id_barang ASC");
    $dataSES = [];
    $totalBarang = 0;

    while($b = mysqli_fetch_assoc($queryBarang)){
        $totalBarang++;
        $id_barang = $b['id_barang'];
        $nama_barang = $b['nama_barang'];
        $stok_saat_ini = (int)$b['jumlah_barang'];
        $jenis_barang = $b['jenis_barang'];

        // Ambil riwayat penjualan per tanggal
        $queryPenjualan = mysqli_query($koneksi, "
            SELECT t.tanggal, SUM(i.jumlah) AS total_terjual
            FROM item_transaksi i
            JOIN transaksi_penjualan_multi t ON i.id_transaksi = t.id_transaksi
            WHERE i.id_barang = '$id_barang'
            GROUP BY t.tanggal
            ORDER BY t.tanggal ASC
        ");

        $riwayat = [];
        while($r = mysqli_fetch_assoc($queryPenjualan)){
            $riwayat[] = [
                'tanggal' => $r['tanggal'],
                'aktual'  => (int)$r['total_terjual']
            ];
        }

        $n = count($riwayat);

        $detailPerhitungan = [];
        $forecastNext = 0;
        $mad = 0;
        $mse = 0;
        $mape = 0;
        $akurasi = 0;

        if($n > 0){
            $totalAbsError = 0;
            $totalSqError = 0;
            $totalPctError = 0;

            $prevForecast = $riwayat[0]['aktual'];

            for($i = 0; $i < $n; $i++){
                $t = $i + 1;
                $aktual = $riwayat[$i]['aktual'];
                $tanggal = $riwayat[$i]['tanggal'];

                if($i == 0){
                    $forecast = $aktual;
                } else {
                    $prevAktual = $riwayat[$i-1]['aktual'];
                    $forecast = ($alpha * $prevAktual) + ((1 - $alpha) * $prevForecast);
                }

                $error = $aktual - $forecast;
                $absError = abs($error);
                $sqError = pow($error, 2);
                $pctError = ($aktual > 0) ? ($absError / $aktual) * 100 : 0;

                $totalAbsError += $absError;
                $totalSqError += $sqError;
                $totalPctError += $pctError;

                $detailPerhitungan[] = [
                    'periode' => $t,
                    'tanggal' => $tanggal,
                    'aktual' => $aktual,
                    'forecast' => $forecast,
                    'error' => $error,
                    'abs_error' => $absError,
                    'sq_error' => $sqError,
                    'pct_error' => $pctError
                ];

                $prevForecast = $forecast;
            }

            // Forecast untuk periode berikutnya (n+1)
            $lastAktual = $riwayat[$n-1]['aktual'];
            $forecastNext = ($alpha * $lastAktual) + ((1 - $alpha) * $prevForecast);

            $mad = $totalAbsError / $n;
            $mse = $totalSqError / $n;
            $mape = $totalPctError / $n;
            $akurasi = max(0, 100 - $mape);
        }

        $prediksiStokUnit = (int)ceil($forecastNext);

        if($stok_saat_ini < $prediksiStokUnit){
            $status = "Perlu Tambah Stok";
            $classStatus = "rendah";
            $tambahQty = $prediksiStokUnit - $stok_saat_ini;
            $rekomendasi = "Stok saat ini ($stok_saat_ini $jenis_barang) kurang dari perkiraan kebutuhan ($prediksiStokUnit $jenis_barang). Disarankan menambah stok minimal $tambahQty $jenis_barang.";
        } else {
            $status = "Stok Cukup";
            $classStatus = "tinggi";
            $rekomendasi = "Stok saat ini ($stok_saat_ini $jenis_barang) sudah mencukupi perkiraan kebutuhan ($prediksiStokUnit $jenis_barang) untuk periode berikutnya.";
        }

        $dataSES[] = [
            'id_barang' => $id_barang,
            'nama_barang' => $nama_barang,
            'jenis_barang' => $jenis_barang,
            'stok_saat_ini' => $stok_saat_ini,
            'n_periode' => $n,
            'riwayat' => $riwayat,
            'detail' => $detailPerhitungan,
            'forecast_next' => $forecastNext,
            'prediksi_stok_unit' => $prediksiStokUnit,
            'mad' => $mad,
            'mse' => $mse,
            'mape' => $mape,
            'akurasi' => $akurasi,
            'status' => $status,
            'class_status' => $classStatus,
            'rekomendasi' => $rekomendasi
        ];
    }

    return [
        'alpha' => $alpha,
        'totalBarang' => $totalBarang,
        'dataSES' => $dataSES
    ];
}
?>
