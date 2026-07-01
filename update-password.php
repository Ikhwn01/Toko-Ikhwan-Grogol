<?php

include 'koneksi.php';

$token = $_POST['token'];

$password_baru = password_hash(
$_POST['password_baru'],
PASSWORD_DEFAULT
);

$query = mysqli_query($koneksi,
"UPDATE users 
SET password='$password_baru',
reset_token=''
WHERE reset_token='$token'");

if($query){

    echo "
    Password berhasil diubah.
    <br><br>

    <a href='login.php'>
    Login Sekarang
    </a>
    ";

}else{

    echo "Password gagal diubah";

}

?>