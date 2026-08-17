<?php

$db_host = getenv('DB_HOST') ?: 'localhost';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
$db_name = getenv('DB_NAME') ?: 'db_toko';
$db_port = getenv('DB_PORT') ? (int)getenv('DB_PORT') : 3306;

// Turn off mysqli default exception throwing to handle error manually
mysqli_report(MYSQLI_REPORT_OFF);

$koneksi = mysqli_init();

// Enable SSL required by TiDB Cloud Serverless (port 4000 / tidbcloud.com)
if (strpos($db_host, 'tidbcloud.com') !== false || $db_port === 4000 || getenv('DB_SSL') === 'true') {
    mysqli_ssl_set($koneksi, NULL, NULL, NULL, NULL, NULL);
    $ssl_flags = MYSQLI_CLIENT_SSL;
    if (defined('MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT')) {
        $ssl_flags |= MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT;
    }
    $connected = @mysqli_real_connect($koneksi, $db_host, $db_user, $db_pass, $db_name, $db_port, NULL, $ssl_flags);
} else {
    $connected = @mysqli_real_connect($koneksi, $db_host, $db_user, $db_pass, $db_name, $db_port);
}

if (!$connected && $db_host === 'localhost' && !getenv('VERCEL')) {
    $koneksi = mysqli_init();
    $connected = @mysqli_real_connect(
        $koneksi,
        "sql312.infinityfree.com",
        "if0_42310360",
        "Ikhwanmuarif07",
        "if0_42310360_db_toko",
        3306
    );
}

if (!$connected || !$koneksi) {
    $err_msg = mysqli_connect_error();
    
    // If request comes from proses API or expects JSON response
    $is_json_request = (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'json') !== false) ||
                       (isset($_SERVER['SCRIPT_NAME']) && strpos($_SERVER['SCRIPT_NAME'], 'proses') !== false) ||
                       (basename($_SERVER['SCRIPT_NAME']) === 'proses-login.php');

    if ($is_json_request) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal koneksi ke DB: ' . ($err_msg ?: 'Unknown error') . ' (Host: ' . $db_host . ', User: ' . $db_user . ', DB: ' . $db_name . ', Port: ' . $db_port . ')'
        ]);
        exit;
    }
    
    die("Koneksi ke database gagal: " . $err_msg);
}

?>