<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/../config/koneksi.php';

$email = mysqli_real_escape_string($koneksi, $_POST['email'] ?? '');

$queryCheck = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email'");
if (!$queryCheck) {
    die("Query error: " . mysqli_error($koneksi) . " (Pastikan tabel 'users' sudah dibuat di database TiDB Cloud)");
}

if(mysqli_num_rows($queryCheck) < 1){
    echo "<script>
        alert('Email tidak terdaftar dalam sistem!');
        window.location='../lupa-password.php';
    </script>";
    exit;
}

$token = md5(uniqid(rand(), true));

mysqli_query($koneksi, "UPDATE users SET reset_token='$token' WHERE email='$email'");

$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$scriptDir = dirname(dirname($_SERVER['SCRIPT_NAME']));
$basePath = ($scriptDir === '/' || $scriptDir === '\\') ? '' : rtrim(str_replace('\\', '/', $scriptDir), '/');
$link = "$protocol://$host$basePath/reset-password.php?token=$token";

$mailSent = false;
$errorMsg = '';

if(class_exists('PHPMailer\PHPMailer\PHPMailer')){
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ikhwanmuarif71@gmail.com';
        $mail->Password = 'tkhhvdzacdifjcwc';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->setFrom('ikhwanmuarif71@gmail.com', 'Admin Toko Ikhwan');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Reset Password - Toko Ikhwan Grogol';
        $mail->Body = "
            <h2>Reset Password Anda</h2>
            <p>Silakan klik link di bawah ini untuk mereset password akun Anda:</p>
            <p><a href='$link' style='padding:10px 20px;background:#1f64e0;color:#fff;text-decoration:none;border-radius:8px;'>Reset Password</a></p>
            <br>
            <p>Atau buka link: <br> <a href='$link'>$link</a></p>
        ";
        $mail->send();
        $mailSent = true;
    } catch (Exception $e) {
        $errorMsg = $mail->ErrorInfo;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Link Reset Password - Toko Ikhwan</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    *{margin:0;padding:0;box-sizing:border-box;font-family:'Plus Jakarta Sans', sans-serif;}
    body{height:100vh;display:flex;justify-content:center;align-items:center;background:#f8fafc;color:#0f172a;}
    .card-box{width:460px;background:white;padding:40px;border-radius:24px;box-shadow:0 15px 35px -5px rgba(0,0,0,0.05);border:1px solid #f1f5f9;text-align:center;}
    .icon{width:65px;height:65px;border-radius:20px;display:flex;align-items:center;justify-content:center;font-size:32px;margin:0 auto 20px;background:#ecfdf5;color:#10b981;}
    h2{color:#0f172a;font-size:22px;font-weight:800;margin-bottom:10px;}
    p{color:#64748b;font-size:14px;line-height:1.6;margin-bottom:20px;}
    .link-box{background:#f1f5f9;padding:15px;border-radius:14px;word-break:break-all;font-size:13px;font-weight:600;color:#1f64e0;margin-bottom:20px;}
    .btn{display:inline-block;width:100%;padding:14px;border-radius:14px;background:#1f64e0;color:white;text-decoration:none;font-weight:700;margin-bottom:12px;box-shadow:0 8px 20px -4px rgba(31,100,224,0.3);}
    .btn:hover{background:#174fb3;}
    .link-back{color:#64748b;font-size:14px;text-decoration:none;font-weight:600;}
    .link-back:hover{color:#0f172a;}
  </style>
</head>
<body>

<div class="card-box">
  <div class="icon">✉️</div>
  <h2>Link Reset Berhasil Dibuat</h2>
  <p>
    <?= $mailSent ? 'Link reset password telah dikirim ke email <b>'.htmlspecialchars($email).'</b>.' : 'Berikut adalah link reset password untuk akun <b>'.htmlspecialchars($email).'</b>:'; ?>
  </p>

  <div class="link-box">
    <a href="<?= $link; ?>" style="color:#1f64e0;text-decoration:none;"><?= $link; ?></a>
  </div>

  <a href="<?= $link; ?>" class="btn">👉 Buka Halaman Reset Password</a>
  <a href="../login.php" class="link-back">Kembali ke Login</a>
</div>

</body>
</html>