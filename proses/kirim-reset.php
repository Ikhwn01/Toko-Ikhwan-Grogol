<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

include '../config/koneksi.php';

$email = $_POST['email'];

$token = md5(rand());

mysqli_query($koneksi,
"UPDATE users 
SET reset_token='$token'
WHERE email='$email'");

$link = "http://localhost/toko/reset-password.php?token=$token";

$mail = new PHPMailer(true);

try {

    $mail->isSMTP();

    $mail->Host = 'smtp.gmail.com';

    $mail->SMTPAuth = true;

    $mail->Username = 'ikhwanmuarif71@gmail.com';

    $mail->Password = 'tkhhvdzacdifjcwc';

    $mail->SMTPSecure = 'tls';

    $mail->Port = 587;

    $mail->setFrom('emailkamu@gmail.com', 'Admin Toko');

    $mail->addAddress($email);

    $mail->isHTML(true);

    $mail->Subject = 'Reset Password';

    $mail->Body = "
    Klik link berikut untuk reset password:
    <br><br>
    <a href='$link'>$link</a>
    ";

    $mail->send();

    echo "Link reset password berhasil dikirim ke email.";

} catch (Exception $e) {

    echo "Email gagal dikirim. Error: {$mail->ErrorInfo}";

}

?>