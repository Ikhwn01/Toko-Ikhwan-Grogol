<?php

$db_host = getenv('DB_HOST') ?: 'localhost';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
$db_name = getenv('DB_NAME') ?: 'db_toko';
$db_port = getenv('DB_PORT') ?: 3306;

// Turn off mysqli default exception throwing to handle error manually
mysqli_report(MYSQLI_REPORT_OFF);

$koneksi = @mysqli_connect($db_host, $db_user, $db_pass, $db_name, (int)$db_port);

if(!$koneksi && $db_host !== 'sql312.infinityfree.com'){
    $koneksi = @mysqli_connect(
        "sql312.infinityfree.com",
        "if0_42310360",
        "Ikhwanmuarif07",
        "if0_42310360_db_toko"
    );
}

if(!$koneksi){
    // If request comes from proses API or expects JSON response
    $is_json_request = (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'json') !== false) ||
                       (isset($_SERVER['SCRIPT_NAME']) && strpos($_SERVER['SCRIPT_NAME'], 'proses') !== false) ||
                       (basename($_SERVER['SCRIPT_NAME']) === 'proses-login.php');

    if ($is_json_request) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => 'Koneksi ke database MySQL gagal! (InfinityFree melarang koneksi MySQL remote dari server cloud Vercel). Silakan masukkan DB_HOST cloud di Vercel Environment Variables.'
        ]);
        exit;
    }
    
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

?>