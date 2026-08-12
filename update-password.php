<?php
include 'config/koneksi.php';

$token = mysqli_real_escape_string($koneksi, $_POST['token'] ?? '');
$password_baru = $_POST['password_baru'] ?? '';

if(empty($token) || empty($password_baru)){
    header("Location: reset-password.php");
    exit;
}

$hash_password = password_hash($password_baru, PASSWORD_DEFAULT);

$query = mysqli_query($koneksi, "UPDATE users SET password='$hash_password', reset_token='' WHERE reset_token='$token'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Password Berhasil Diubah - Toko Ikhwan</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    *{margin:0;padding:0;box-sizing:border-box;font-family:'Plus Jakarta Sans', sans-serif;}
    body{height:100vh;display:flex;justify-content:center;align-items:center;background:#f8fafc;color:#0f172a;}
    .card-box{width:420px;background:white;padding:40px;border-radius:24px;box-shadow:0 15px 35px -5px rgba(0,0,0,0.05);border:1px solid #f1f5f9;text-align:center;}
    .icon{width:65px;height:65px;border-radius:20px;display:flex;align-items:center;justify-content:center;font-size:32px;margin:0 auto 20px;}
    .icon.success{background:#ecfdf5;color:#10b981;}
    .icon.error{background:#fef2f2;color:#ef4444;}
    h2{color:#0f172a;font-size:22px;font-weight:800;margin-bottom:10px;}
    p{color:#64748b;font-size:14px;line-height:1.5;margin-bottom:25px;}
    .btn{display:inline-block;width:100%;padding:14px;border-radius:14px;background:#1f64e0;color:white;text-decoration:none;font-weight:700;box-shadow:0 8px 20px -4px rgba(31,100,224,0.3);transition:0.2s;}
    .btn:hover{background:#174fb3;}
  </style>
</head>
<body>

<div class="card-box">
  <?php if($query && mysqli_affected_rows($koneksi) > 0){ ?>
    <div class="icon success">✅</div>
    <h2>Password Berhasil Diubah!</h2>
    <p>Password akun Anda telah berhasil diperbarui. Silakan login kembali dengan password baru Anda.</p>
    <a href="login.php" class="btn">Login Sekarang</a>
  <?php } else { ?>
    <div class="icon error">❌</div>
    <h2>Gagal Mengubah Password</h2>
    <p>Terjadi kesalahan atau token reset password sudah tidak berlaku lagi.</p>
    <a href="lupa-password.php" class="btn" style="background:#ef4444;">Minta Reset Password Baru</a>
  <?php } ?>
</div>

</body>
</html>