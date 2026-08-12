<?php
session_start();
include 'config/koneksi.php';

header('Content-Type: application/json');

$username = mysqli_real_escape_string($koneksi, $_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if(empty($username) || empty($password)){
    echo json_encode(['status' => 'error', 'message' => 'Username dan Password wajib diisi!']);
    exit;
}

$query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
$data = mysqli_fetch_assoc($query);

if($data){
    if(password_verify($password, $data['password'])){
        $_SESSION['username'] = $data['username'];
        echo json_encode(['status' => 'success', 'redirect' => 'dashboard.php']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Password yang Anda masukkan salah!']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Username tidak ditemukan!']);
}
?>