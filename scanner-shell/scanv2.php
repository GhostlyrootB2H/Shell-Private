<?php
/**
Written by Cvar1984 <Cvar1984@pm.me>, November 2022
Modified 2024: Added Delete Selected, Delete All, and File Size features.
*/

$minute = 15;
$limit = (60 * $minute); 
ini_set('memory_limit', '-1');
ini_set('max_execution_time', $limit);
set_time_limit($limit);

function recursiveScan($directory, &$entries_array = array())
{
    $handle = @opendir($directory);
    if ($handle) {
        while (($entry = readdir($handle)) !== false) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            $entry = $directory . DIRECTORY_SEPARATOR . $entry;
            if (is_dir($entry) && is_readable($directory) && !is_link($directory)) {
                $entries_array = recursiveScan($entry, $entries_array);
            } elseif (is_file($entry) && is_readable($entry)) {
                $entries_array['file_writable'][] = $entry;
            } else {
                $entries_array['file_not_writable'][] = $entry;
            }
        }
        closedir($handle);
    }
    return $entries_array;
}

function sortByLastModified($files)
{
    @array_multisort(array_map('filemtime', $files), SORT_DESC, $files);
    return $files;
}

function getSortedByTime($path)
{
    $result = recursiveScan($path);
    $fileWritable = isset($result['file_writable']) ? $result['file_writable'] : array();
    $fileNotWritable = isset($result['file_not_writable']) ? $result['file_not_writable'] : false;
    $fileWritable = sortByLastModified($fileWritable);

    return array(
        'file_writable' => $fileWritable,
        'file_not_writable' => $fileNotWritable
    );
}

function getSortedByExtension($path, $ext)
{
    $result = getSortedByTime($path);
    $fileWritable = $result['file_writable'];
    $sortedWritableFile = array();
    $sortedNotWritableFile = array();

    foreach ($fileWritable as $entry) {
        $pathinfo = pathinfo($entry, PATHINFO_EXTENSION);
        $pathinfo = strtolower($pathinfo);
        if (in_array($pathinfo, $ext)) {
            $sortedWritableFile[] = $entry;
        }
    }
    return array(
        'file_writable' => $sortedWritableFile,
        'file_not_writable' => false
    );
}

function getFileTokens($filename)
{
    $fileContent = file_get_contents($filename);
    $fileContent = preg_replace('/<\?([^p=\w])/m', '<?php ', $fileContent);
    $token = token_get_all($fileContent);
    $output = array();
    $tokenCount = count($token);

    if ($tokenCount > 0) {
        for ($i = 0; $i < $tokenCount; $i++) {
            if (isset($token[$i][1])) {
                $output[] .= strtolower($token[$i][1]);
            }
        }
    }
    $output = array_values(array_unique(array_filter(array_map("trim", $output))));
    return $output;
}

function compareTokens($tokenNeedles, $tokenHaystack)
{
    $output = array();
    foreach ($tokenNeedles as $tokenNeedle) {
        if (in_array($tokenNeedle, $tokenHaystack)) {
            $output[] = $tokenNeedle;
        }
    }
    return $output;
}

// Format Size Helper
function formatSize($bytes) {
    if ($bytes >= 1073741824) { $bytes = number_format($bytes / 1073741824, 2) . ' GB'; }
    elseif ($bytes >= 1048576) { $bytes = number_format($bytes / 1048576, 2) . ' MB'; }
    elseif ($bytes >= 1024) { $bytes = number_format($bytes / 1024, 2) . ' KB'; }
    elseif ($bytes > 1) { $bytes = $bytes . ' bytes'; }
    elseif ($bytes == 1) { $bytes = $bytes . ' byte'; }
    else { $bytes = '0 bytes'; }
    return $bytes;
}

// Logic Penghapusan
if(isset($_POST['delete_selected']) && !empty($_POST['files_to_delete'])){
    foreach($_POST['files_to_delete'] as $delFile){
        @unlink($delFile);
    }
    echo "<script>alert('Terpilih berhasil dihapus');</script>";
}

if(isset($_POST['delete_all']) && !empty($_POST['all_files_list'])){
    $allFiles = explode(",", $_POST['all_files_list']);
    foreach($allFiles as $delFile){
        @unlink($delFile);
    }
    echo "<script>alert('Semua file ditemukan berhasil dihapus');</script>";
}

$ext = array('php','phps','pht','phpt','phtml','phar','php3','php4','php5','php7','php8','suspected');
$tokenNeedles = array('base64_decode','eval','exec','system','shell_exec','passthru','assert','str_rot13','gzinflate','include','require','move_uploaded_file','$_files','curl_init'); // (Disingkat untuk contoh, silakan gunakan list lengkap Anda)

?>
<!DOCTYPE html>
<html lang="en-us">
<head>
    <title>Pussy Finder Pro</title>
    <style type="text/css">
        @import url('https://fonts.googleapis.com/css?family=Ubuntu+Mono&display=swap');
        body { font-family: 'Ubuntu Mono', monospace; color: #8a8a8a; background: #f4f4f4; }
        table { border-spacing: 0; padding: 10px; border-radius: 7px; border: 3px solid #d6d6d6; background: #fff; margin-bottom: 20px; }
        tr, td { padding: 7px; border-bottom: 1px solid #eee; }
        th { color: #8a8a8a; padding: 7px; font-size: 20px; }
        input[type=submit], button, .btn { border: 2px solid #d6d6d6; padding: 7px; border-radius: 7px; cursor: pointer; font-family: 'Ubuntu Mono'; background: white; }
        input[type=submit]:hover, .btn:hover { background: #ff9999; color: #fff; border-color: #ff9999; }
        .del-btn { color: red; border-color: red; }
        .del-btn:hover { background: red; color: white; }
        input[type=text] { border: 3px solid #d6d6d6; padding: 7px; width: 100%; border-radius: 7px; }
    </style>
</head>
<body>
    <form method="post" id="mainForm">
        <table align="center" width="50%">
            <tr><th>Pussy Finder</th></tr>
            <tr><td><input type="text" name="dir" value="<?= isset($_POST['dir']) ? $_POST['dir'] : getcwd() ?>"></td></tr>
            <tr><td><input type="submit" name="submit" value="SEARCH"></td></tr>
        </table>

        <?php if (isset($_POST['submit'])) { 
            $path = $_POST['dir'];
            $result = getSortedByExtension($path, $ext);
            $fileWritable = sortByLastModified($result['file_writable']);
            $foundFiles = [];
        ?>
            <table align="center" width="90%">
                <tr>
                    <td colspan="4">
                        <span style="font-weight:bold;font-size:25px;">RESULT</span>
                        <input type="submit" name="delete_selected" value="Hapus Terpilih" class="del-btn" onclick="return confirm('Hapus file yang dipilih?')">
                        <input type="submit" name="delete_all" value="Hapus Semua" class="del-btn" onclick="return confirm('YAKIN HAPUS SEMUA FILE DITEMUKAN?')">
                    </td>
                </tr>
                <tr style="background: #eee;">
                    <th><input type="checkbox" onclick="for(c in document.getElementsByName('files_to_delete[]')) document.getElementsByName('files_to_delete[]')[c].checked = this.checked"></th>
                    <th>File Path & Tokens</th>
                    <th>Size</th>
                </tr>
                <?php
                foreach ($fileWritable as $file) {
                    $filePath = str_replace('\\', '/', $file);
                    $tokens = getFileTokens($filePath);
                    $cmp = compareTokens($tokenNeedles, $tokens);
                    
                    if (!empty($cmp)) {
                        $foundFiles[] = $filePath;
                        $cmpString = implode(', ', $cmp);
                        $fileSize = formatSize(filesize($filePath));
                        echo "<tr>
                            <td align='center'><input type='checkbox' name='files_to_delete[]' value='$filePath'></td>
                            <td><span style='color:red;'>$filePath</span><br/><small style='color:#555;'>Tokens: $cmpString</small></td>
                            <td nowrap>$fileSize</td>
                        </tr>";
                    }
                }
                ?>
            </table>
            <input type="hidden" name="all_files_list" value="<?= implode(',', $foundFiles) ?>">
        <?php } ?>
    </form>
</body>
</html>