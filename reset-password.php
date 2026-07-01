<?php

include 'koneksi.php';

$token = $_GET['token'];

$query = mysqli_query($koneksi,
"SELECT * FROM users
WHERE reset_token='$token'");

$data = mysqli_num_rows($query);

if($data < 1){
    die("Token tidak valid");
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password</title>

  <style>

    *{
      margin:0;
      padding:0;
      box-sizing:border-box;
      font-family:Arial, Helvetica, sans-serif;
    }

    body{
      height:100vh;
      display:flex;
      justify-content:center;
      align-items:center;
      background:#f4f4f4;
    }

    .reset-box{
      width:420px;
      background:white;
      padding:40px;
      border-radius:20px;
      box-shadow:0 5px 20px rgba(0,0,0,0.1);
      text-align:center;
    }

    .reset-box img{
      width:90px;
      margin-bottom:15px;
    }

    .reset-box h2{
      color:#222;
      margin-bottom:10px;
    }

    .reset-box p{
      color:gray;
      font-size:14px;
      margin-bottom:25px;
    }

    .input-box{
      margin-bottom:20px;
      position:relative;
    }

    .input-box input{
      width:100%;
      padding:15px 20px;
      border:3px solid #555;
      border-radius:40px;
      outline:none;
      font-size:15px;
      transition:0.3s;
    }

    .input-box input:focus{
      border-color:#2d9cdb;
    }

    .show-password{
      position:absolute;
      right:20px;
      top:16px;
      cursor:pointer;
      font-size:13px;
      color:gray;
    }

    .btn-reset{
      width:100%;
      padding:14px;
      border:none;
      border-radius:40px;
      background:#2d9cdb;
      color:white;
      font-size:18px;
      font-weight:bold;
      cursor:pointer;
      transition:0.3s;
    }

    .btn-reset:hover{
      background:#1d87c5;
    }

  </style>
</head>

<body>

  <div class="reset-box">

    <img src="assets/logo.png" alt="Logo">

    <h2>Reset Password</h2>

    <p>
      Silahkan masukkan password baru anda
    </p>

    <form id="resetForm" method="POST" action="update-password.php">

      <input 
        type="hidden"
        name="token"
        value="<?php echo $token; ?>"
      >

      <div class="input-box">

        <input 
          type="password"
          name="password_baru"
          id="password"
          placeholder="Password Baru"
          required
        >

        <span 
          class="show-password"
          onclick="showPassword()"
        >
          Show
        </span>

      </div>

      <button type="submit" class="btn-reset">
        Simpan Password
      </button>

    </form>

  </div>

  <script>

    function showPassword(){

      const password =
      document.getElementById("password");

      if(password.type === "password"){

        password.type = "text";

      }else{

        password.type = "password";

      }

    }

    document
    .getElementById("resetForm")
    .addEventListener("submit", function(){

      alert("Password berhasil diperbarui");

    });

  </script>

</body>
</html>