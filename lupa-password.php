<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lupa Password</title>

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

    .forgot-box{
      width:420px;
      background:white;
      padding:40px;
      border-radius:20px;
      box-shadow:0 5px 20px rgba(0,0,0,0.1);
      text-align:center;
    }

    .forgot-box img{
      width:90px;
      margin-bottom:15px;
    }

    .forgot-box h2{
      color:#222;
      margin-bottom:10px;
    }

    .forgot-box p{
      color:gray;
      font-size:14px;
      margin-bottom:25px;
    }

    .input-box{
      margin-bottom:20px;
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

    .back{
      margin-top:20px;
    }

    .back a{
      text-decoration:none;
      color:gray;
      font-size:14px;
    }

    .back a:hover{
      color:#2d9cdb;
    }

  </style>
</head>

<body>

  <div class="forgot-box">

    <img src="assets/logo.png" alt="Logo">

    <h2>Lupa Password</h2>

    <p>
      Masukkan email anda untuk menerima link reset password
    </p>

    <form id="forgotForm" method="POST" action="proses/kirim-reset.php">

      <div class="input-box">

        <input 
          type="email"
          name="email"
          placeholder="Masukkan Email"
          required
        >

      </div>

      <button type="submit" class="btn-reset">
        Kirim Reset Password
      </button>

    </form>

    <div class="back">
      <a href="login.php">
        Kembali ke Login
      </a>
    </div>

  </div>

  <script>

    document
    .getElementById("forgotForm")
    .addEventListener("submit", function(){

      alert("Link reset password sedang dikirim...");

    });

  </script>

</body>
</html>