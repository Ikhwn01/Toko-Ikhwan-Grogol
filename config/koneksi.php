<?php

$db_host = getenv('DB_HOST') ?: 'localhost';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
$db_name = getenv('DB_NAME') ?: 'db_toko';

$koneksi = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if(!$koneksi && $db_host !== 'sql312.infinityfree.com'){
    $koneksi = @mysqli_connect(
        "sql312.infinityfree.com",
        "if0_42310360",
        "Ikhwanmuarif07",
        "if0_42310360_db_toko"
    );
}

if(!$koneksi){
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

?>