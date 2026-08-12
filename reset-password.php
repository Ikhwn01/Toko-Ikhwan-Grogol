<?php
include 'config/koneksi.php';

$token = $_GET['token'] ?? '';
$isValid = false;
$userEmail = '';

if(!empty($token)){
    $token_clean = mysqli_real_escape_string($koneksi, $token);
    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE reset_token='$token_clean' AND reset_token != ''");
    if($query && mysqli_num_rows($query) > 0){
        $isValid = true;
        $user = mysqli_fetch_assoc($query);
        $userEmail = $user['email'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password - Toko Ikhwan Grogol</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    *{
      margin:0;
      padding:0;
      box-sizing:border-box;
      font-family:'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
    }

    body{
      height:100vh;
      display:flex;
      justify-content:center;
      align-items:center;
      background:#f8fafc;
      color:#0f172a;
    }

    .reset-box{
      width:420px;
      background:white;
      padding:40px;
      border-radius:24px;
      box-shadow:0 15px 35px -5px rgba(0,0,0,0.05);
      border:1px solid #f1f5f9;
      text-align:center;
    }

    .reset-box img{
      width:80px;
      margin-bottom:15px;
      filter:drop-shadow(0 4px 6px rgba(0,0,0,0.05));
    }

    .icon-status{
      width:65px;
      height:65px;
      border-radius:20px;
      display:flex;
      align-items:center;
      justify-content:center;
      font-size:30px;
      margin:0 auto 20px;
    }

    .icon-status.error{
      background:#fef2f2;
      color:#ef4444;
    }

    .icon-status.success{
      background:#ecfdf5;
      color:#10b981;
    }

    .reset-box h2{
      color:#0f172a;
      font-size:22px;
      font-weight:800;
      margin-bottom:10px;
      letter-spacing:-0.5px;
    }

    .reset-box p{
      color:#64748b;
      font-size:14px;
      line-height:1.5;
      margin-bottom:25px;
    }

    .input-box{
      margin-bottom:20px;
      position:relative;
    }

    .input-box input{
      width:100%;
      padding:14px 20px;
      border:2px solid #e2e8f0;
      border-radius:14px;
      outline:none;
      font-size:15px;
      font-weight:600;
      transition:0.2s;
    }

    .input-box input:focus{
      border-color:#1f64e0;
      box-shadow:0 0 0 4px rgba(31,100,224,0.1);
    }

    .show-password{
      position:absolute;
      right:18px;
      top:15px;
      cursor:pointer;
      font-size:13px;
      font-weight:700;
      color:#64748b;
      user-select:none;
    }

    .btn-reset{
      width:100%;
      padding:14px;
      border:none;
      border-radius:14px;
      background:linear-gradient(135deg, #1f64e0, #3b82f6);
      color:white;
      font-size:16px;
      font-weight:700;
      cursor:pointer;
      transition:0.2s;
      box-shadow:0 8px 20px -4px rgba(31,100,224,0.3);
    }

    .btn-reset:hover{
      transform:translateY(-1px);
      box-shadow:0 12px 25px -4px rgba(31,100,224,0.4);
    }

    .btn-secondary{
      display:inline-block;
      width:100%;
      padding:14px;
      border-radius:14px;
      background:#f1f5f9;
      color:#475569;
      text-decoration:none;
      font-size:15px;
      font-weight:700;
      margin-top:10px;
      transition:0.2s;
    }

    .btn-secondary:hover{
      background:#e2e8f0;
    }
  </style>
</head>

<body>

  <div class="reset-box">

    <img src="assets/logo.png" alt="Logo">

    <?php if(!$isValid){ ?>

      <div class="icon-status error">⚠️</div>
      <h2>Token Tidak Valid</h2>
      <p>Link reset password yang Anda gunakan tidak valid, tidak ditemukan, atau telah kadaluwarsa.</p>

      <a href="lupa-password.php" class="btn-reset" style="text-decoration:none; display:block;">
        Minta Reset Password Baru
      </a>

      <a href="login.php" class="btn-secondary">
        Kembali ke Login
      </a>

    <?php } else { ?>

      <h2>Reset Password</h2>

      <p>
        Silakan masukkan password baru untuk akun <b><?= htmlspecialchars($userEmail); ?></b>
      </p>

      <form id="resetForm" method="POST" action="update-password.php">

        <input 
          type="hidden"
          name="token"
          value="<?= htmlspecialchars($token); ?>"
        >

        <div class="input-box">

          <input 
            type="password"
            name="password_baru"
            id="password"
            placeholder="Password Baru"
            required
            minlength="6"
          >

          <span 
            class="show-password"
            onclick="togglePassword()"
          >
            Show
          </span>

        </div>

        <button type="submit" class="btn-reset">
          Simpan Password Baru
        </button>

      </form>

    <?php } ?>

  </div>

  <script>
    function togglePassword(){
      const password = document.getElementById("password");
      const btn = document.querySelector(".show-password");

      if(password.type === "password"){
        password.type = "text";
        btn.textContent = "Hide";
      } else {
        password.type = "password";
        btn.textContent = "Show";
      }
    }
  </script>

</body>
</html>