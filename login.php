<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Toko Ikhwan Grogol</title>
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

    .login-box{
      width:420px;
      background:white;
      padding:40px;
      border-radius:24px;
      box-shadow:0 15px 35px -5px rgba(0,0,0,0.05);
      border:1px solid #f1f5f9;
    }

    .login-container{
      width:100%;
      text-align:center;
    }

    .login-container img{
      width:80px;
      margin-bottom:12px;
      filter:drop-shadow(0 4px 6px rgba(0,0,0,0.05));
    }

    .login-container h2{
      font-size:22px;
      font-weight:800;
      margin-bottom:25px;
      color:#0f172a;
      letter-spacing:-0.5px;
    }

    .input-box{
      margin-bottom:18px;
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
      background:white;
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
      color:#64748b;
      font-size:13px;
      font-weight:700;
      user-select:none;
    }

    .show-password:hover{
      color:#1f64e0;
    }

    .forgot{
      margin-top:-4px;
      margin-bottom:24px;
      text-align:right;
    }

    .forgot a{
      text-decoration:none;
      color:#1f64e0;
      font-size:13px;
      font-weight:600;
    }

    .forgot a:hover{
      text-decoration:underline;
    }

    .btn-login{
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

    .btn-login:hover{
      transform:translateY(-1px);
      box-shadow:0 12px 25px -4px rgba(31,100,224,0.4);
    }

    .error-message{
      background:#fef2f2;
      color:#ef4444;
      border:1px solid #fecaca;
      padding:12px;
      border-radius:12px;
      margin-bottom:20px;
      display:none;
      font-size:14px;
      font-weight:600;
    }
  </style>
</head>

<body>

  <div class="login-box">

    <div class="login-container">

      <img src="assets/logo.png" alt="Logo">

      <h2>TOKO IKHWAN GROGOL</h2>

      <div class="error-message" id="errorMessage">
        Username atau Password salah!
      </div>

      <form id="loginForm">

        <div class="input-box">
          <input 
            type="text"
            id="username"
            name="username"
            placeholder="Username"
            required
          >
        </div>

        <div class="input-box">
          <input 
            type="password"
            id="password"
            name="password"
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
            Lupa Password?
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
    function showPassword(){
      const password = document.getElementById("password");
      const text = document.querySelector(".show-password");

      if(password.type === "password"){
        password.type = "text";
        text.innerHTML = "Hide";
      }else{
        password.type = "password";
        text.innerHTML = "Show";
      }
    }

    document.getElementById("loginForm").addEventListener("submit", function(e){
      e.preventDefault();

      const btn = document.getElementById("btnLogin");
      const error = document.getElementById("errorMessage");
      const formData = new FormData(this);

      btn.disabled = true;
      btn.innerText = "Memproses...";
      error.style.display = "none";

      fetch('proses-login.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        btn.disabled = false;
        btn.innerText = "Login";

        if(data.status === 'success'){
          window.location.href = data.redirect;
        } else {
          error.innerText = data.message || "Login gagal!";
          error.style.display = "block";
        }
      })
      .catch(err => {
        btn.disabled = false;
        btn.innerText = "Login";
        error.innerText = "Terjadi kesalahan jaringan/server!";
        error.style.display = "block";
      });
    });
  </script>

</body>
</html>