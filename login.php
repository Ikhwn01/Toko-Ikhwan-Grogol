<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>

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

    .login-box{
      width:420px;
      background:white;
      padding:40px;
      border-radius:20px;
      box-shadow:0 5px 20px rgba(0,0,0,0.1);
    }

    .login-container{
      width:100%;
      text-align:center;
    }

    .login-container img{
      width:90px;
      margin-bottom:10px;
    }

    .login-container h2{
      font-size:22px;
      margin-bottom:25px;
      color:#222;
    }

    .input-box{
      margin-bottom:18px;
      position:relative;
    }

    .input-box input{
      width:100%;
      padding:15px 20px;
      border:3px solid #555;
      border-radius:40px;
      outline:none;
      font-size:16px;
      background:white;
      transition:0.3s;
    }

    .input-box input:focus{
      border-color:#2d9cdb;
      box-shadow:0 0 10px rgba(45,156,219,0.3);
    }

    .show-password{
      position:absolute;
      right:20px;
      top:16px;
      cursor:pointer;
      color:gray;
      font-size:13px;
      user-select:none;
    }

    .show-password:hover{
      color:#2d9cdb;
    }

    .forgot{
      margin-top:-5px;
      margin-bottom:25px;
      text-align:center;
    }

    .forgot a{
      text-decoration:none;
      color:gray;
      font-size:13px;
    }

    .forgot a:hover{
      color:#2d9cdb;
    }

    .btn-login{
      width:160px;
      padding:14px;
      border:none;
      border-radius:40px;
      background:#2d9cdb;
      color:white;
      font-size:28px;
      font-weight:bold;
      cursor:pointer;
      transition:0.3s;
    }

    .btn-login:hover{
      background:#1d87c5;
      transform:scale(1.03);
    }

    .error-message{
      background:#ffdddd;
      color:#d8000c;
      padding:12px;
      border-radius:10px;
      margin-bottom:20px;
      display:none;
      font-size:14px;
    }

  </style>
</head>

<body>

  <div class="login-box">

    <div class="login-container">

      <img src="assets/logo.png" alt="Logo">

      <h2>TOKO IKHWAN GROGOL</h2>

      <!-- PESAN ERROR -->
      <div class="error-message" id="errorMessage">
        Username atau Password salah!
      </div>

      <form id="loginForm">

        <div class="input-box">

          <input 
            type="text"
            id="username"
            placeholder="Username"
            required
          >

        </div>

        <div class="input-box">

          <input 
            type="password"
            id="password"
            placeholder="Password"
            required
          >

          <span 
            class="show-password"
            onclick="showPassword()"
          >
            Show
          </span>

        </div>

        <div class="forgot">
          <a href="lupa-password.php">
            Forgot Password?
          </a>
        </div>

        <button 
          type="submit"
          class="btn-login"
          id="btnLogin"
        >
          Login
        </button>

      </form>

    </div>

  </div>

  <script>

    // SHOW PASSWORD
    function showPassword(){

      const password =
      document.getElementById("password");

      const text =
      document.querySelector(".show-password");

      if(password.type === "password"){

        password.type = "text";
        text.innerHTML = "Hide";

      }else{

        password.type = "password";
        text.innerHTML = "Show";

      }

    }

    // LOGIN VALIDASI
    document
    .getElementById("loginForm")
    .addEventListener("submit", function(e){

      e.preventDefault();

      const username =
      document.getElementById("username").value;

      const password =
      document.getElementById("password").value;

      const error =
      document.getElementById("errorMessage");

      // USERNAME & PASSWORD CONTOH
      if(
        username === "admin" &&
        password === "123456"
      ){

        window.location.href =
        "dashboard.php";

      }else{

        error.style.display = "block";

      }

    });

  </script>

</body>
</html>