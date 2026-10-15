<?php
$minute = 15;
$limit = (60 * $minute);
ini_set('memory_limit', '-1');
ini_set('max_execution_time', $limit);
set_time_limit($limit);

$successMessage = '';
$errorMessage = '';

function recursiveScan($directory, &$entriesArray = array())
{
    $handle = @opendir($directory);
    if ($handle) {
        while (($entry = readdir($handle)) !== false) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            $entry = $directory . DIRECTORY_SEPARATOR . $entry;
            if (is_dir($entry) && is_readable($directory) && !is_link($directory)) {
                $entriesArray = recursiveScan($entry, $entriesArray);
            } elseif (is_file($entry) && is_readable($entry)) {
                $entriesArray['file_writable'][] = $entry;
            } else {
                $entriesArray['file_not_writable'][] = $entry;
            }
        }
        closedir($handle);
    }
    return $entriesArray;
}

function sortByLastModified($files)
{
    @array_multisort(array_map('filemtime', $files), SORT_DESC, $files);
    return $files;
}

function getSortedByExtension($path, $ext)
{
    $result = recursiveScan($path);
    $fileWritable = $result['file_writable'];
    $fileNotWritable = isset($result['file_not_writable']) ? $result['file_not_writable'] : [];

    $sortedWritableFile = [];
    foreach ($fileWritable as $entry) {
        $extension = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
        if (in_array($extension, $ext)) {
            $sortedWritableFile[] = $entry;
        }
    }

    return [
        'file_writable' => $sortedWritableFile,
        'file_not_writable' => $sortedNotWritableFile
    ];
}

function getFileTokens($filename)
{
    $fileContent = file_get_contents($filename);
    $fileContent = preg_replace('/<\?([^p=\w])/m', '<?php ', $fileContent);
    $token = token_get_all($fileContent);
    $output = [];

    foreach ($token as $t) {
        if (isset($t[1])) {
            $output[] = strtolower($t[1]);
        }
    }
    return array_values(array_unique(array_filter(array_map("trim", $output))));
}

function compareTokens($tokenNeedles, $tokenHaystack)
{
    return array_values(array_intersect($tokenNeedles, $tokenHaystack));
}

$ext = ['php', 'phps', 'pht', 'phpt', 'phtml', 'phar', 'php3', 'php4', 'php5', 'php7', 'suspected'];
$tokenNeedles = [
    'base64_decode', 'rawurldecode', 'urldecode', 'gzinflate', 'gzuncompress', 'eval', 'exec',
    'shell_exec', 'system', 'passthru', 'proc_open', 'popen', 'assert', 'mysql_connect',
    'include', 'include_once', 'require', 'require_once', '__file__', 'file_put_contents'
];

$scanResults = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit']) && !empty($_POST['dir'])) {
    $path = $_POST['dir'];
    $scanResults = getSortedByExtension($path, $ext);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $fileToDelete = $_POST['fileToDelete'];
    if (is_file($fileToDelete) && is_writable($fileToDelete)) {
        if (unlink($fileToDelete)) {
            $successMessage = "Successfully deleted: " . htmlspecialchars($fileToDelete);
        } else {
            $errorMessage = "Failed to delete: " . htmlspecialchars($fileToDelete);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Backdoor Scanner</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Ubuntu Mono', monospace;
            background-color: #1e1e1e;
            color: #cfcfcf;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            width: 90%;
            max-width: 800px;
            margin: 20px;
            padding: 20px;
            background-color: #2a2a2a;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
        }

        th, td {
            padding: 10px;
            text-align: left;
            background-color: #323232;
            border-bottom: 1px solid #444;
            color: #dcdcdc;
            overflow: hidden;
            text-overflow: ellipsis;
            word-wrap: break-word;
        }

        th {
            background-color: #404040;
            color: #f9f9f9;
        }

        input[type=text] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #282828;
            color: #ddd;
            border: 1px solid #444;
            border-radius: 5px;
        }

        input[type=submit] {
            padding: 10px 20px;
            border-radius: 5px;
            border: 1px solid #444;
            background-color: #4a4a4a;
            color: #eee;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type=submit]:hover {
            background-color: #646464;
        }

        .code-container {
            max-height: 200px;
            overflow: auto;
            background-color: #222;
            border: 1px solid #444;
            padding: 10px;
            border-radius: 4px;
        }

        .matched-tokens {
            color: #ff9999;
        }

        .meme-container {
            display: none;
            text-align: center;
        }

        .meme-container img {
            max-width: 100%;
            height: auto;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <form method="post">
            <table>
                <thead>
                    <tr>
                        <th colspan="2">Backdoor Scanner</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="2">
                            <input type="text" name="dir" placeholder="Enter Directory Path" value="<?= htmlspecialchars(getcwd()) ?>">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center;">
                            <input type="submit" name="submit" value="SEARCH">
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>

        <?php if (!empty($scanResults)): ?>
        <table>
            <thead>
                <tr>
                    <th colspan="2">Results</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($scanResults['file_writable'] as $file): ?>
                    <?php
                        if (isset($_POST['deletedFiles']) && in_array($file, $_POST['deletedFiles'])) continue;

                        $filePath = str_replace('\\', '/', $file);
                        $tokens = getFileTokens($filePath);
                        $matchedTokens = compareTokens($tokenNeedles, $tokens);

                        if (!empty($matchedTokens)):
                            $matchedTokensString = implode(', ', $matchedTokens);
                    ?>
                    <tr>
                        <td colspan="2" style="color:red;"><?= htmlspecialchars($filePath) ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="matched-tokens">Contains: <?= htmlspecialchars($matchedTokensString) ?></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="code-container">
                                <pre><?= htmlspecialchars(file_get_contents($filePath)) ?></pre></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center;">
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="fileToDelete" value="<?= htmlspecialchars($filePath) ?>">
                                <input type="submit" name="delete" value="Delete File">
                                <input type="hidden" name="dir" value="<?= htmlspecialchars($_POST['dir']) ?>">
                                <input type="hidden" name="submit" value="submit">
                            </form>
                        </td>
                    </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <script>
        // Display alert for successful or failed file deletion
        document.addEventListener("DOMContentLoaded", function() {
            <?php if (!empty($successMessage)) : ?>
                alert("<?= $successMessage ?>");
                const memeContainer = document.getElementById("memeContainer");
                if (memeContainer) {
                    memeContainer.style.display = "block";
                }
            <?php elseif (!empty($errorMessage)) : ?>
                alert("<?= $errorMessage ?>");
            <?php endif; ?>
        });
    </script>
</body>

</html>