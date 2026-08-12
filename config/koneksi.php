<?php

$koneksi = @mysqli_connect("localhost", "root", "", "db_toko");

if(!$koneksi){
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