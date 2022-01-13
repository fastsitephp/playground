<?php
/**
 * This script is used to automate updating C Source files for the custom build
 * of PHP so that functions can be disabled on the Playground Server. This script
 * runs directly on the production server during the initial setup and has zero
 * dependencies other than requiring the PHP source to be downloaded at a location
 * specified near the top of this file.
 *
 * This script was created because [disable_functions] in [php.ini] can
 * be bypassed from user code. For all disabled functions a custom C Macro
 * `DISABLED_FOR_PLAYGROUND;` is added to the start of each function so that
 * users cannot execute the functions in a playground site. Many files must
 * be updated (and likely more functions in future builds of PHP) so it makes
 * sense to have an automated script for this.
 *
 * See related docs at:
 *     docs\PHP Custom Build Instructions.txt
 *     docs\docs\PHP INI Settings.txt
 *     docs\playground-server-setup.sh
 */

error_reporting(-1);
ini_set('display_errors', 'on');
date_default_timezone_set('UTC');

$root_dir = '/home/ubuntu/php-7.4.27';
// $root_dir = 'C:\Users\csollitt\Downloads\php-7.4.27';
// $root_dir = '/Users/conrad/Downloads/php-7.4.27';

$update_files = [
    'ext/standard/basic_functions.c' => 'ini_set,ini_restore,sleep,usleep,set_include_path,error_log,move_uploaded_file',
    'ext/standard/exec.c' => 'exec,system,passthru,shell_exec,proc_nice',
    'ext/curl/interface.c' => 'curl_exec',
    'ext/curl/multi.c' => 'curl_multi_exec',
    'ext/standard/dl.c' => 'dl',
    'main/main.c' => 'set_time_limit',
    // Functions with `PHP_NAMED_FUNCTION` are prefixed with 'php_if_' for the name
    'ext/standard/file.c' => 'tempnam,PHP_NAMED_FUNCTION(php_if_tmpfile),PHP_NAMED_FUNCTION(php_if_fopen),'
        . 'fwrite,PHP_NAMED_FUNCTION(php_if_ftruncate),fputcsv,umask,popen',
    'ext/standard/link.c' => 'link,symlink',
    'ext/standard/filestat.c' => 'touch,chown,lchown,chmod,chgrp,lchgrp',
    'ext/standard/dir.c' => 'glob',
    'ext/standard/streamsfuncs.c' => 'stream_socket_client,stream_socket_server,stream_context_create,stream_socket_pair',
    'ext/standard/dns.c' => 'dns_get_record,dns_check_record,dns_get_mx',
    'ext/standard/fsock.c' => 'fsockopen,pfsockopen',
    'ext/standard/head.c' => 'setcookie,setrawcookie',
    'ext/standard/syslog.c' => 'syslog,openlog',
    'main/streams/userspace.c' => 'stream_wrapper_restore',
    'ext/fileinfo/fileinfo.c' => 'finfo_set_flags',
    'ext/standard/mail.c' => 'mail',
    'ext/session/session.c' => 'session_start,session_create_id',
    // Classes
    'ext/spl/spl_directory.c' => 'SPL_METHOD(SplFileObject, __construct)|'
        . 'SPL_METHOD(SplTempFileObject, __construct)|SPL_METHOD(FilesystemIterator, __construct)|'
        . 'SPL_METHOD(DirectoryIterator, __construct)|SPL_METHOD(GlobIterator, __construct)',
];
$update_text = 'DISABLED_FOR_PLAYGROUND;';
$file_updates = 0;
$fn_updates = 0;
$fn_skipped = 0;
$errors = 0;

echo 'Updating C Files from [php-src] to disable functions:';
if (!is_dir($root_dir)) {
    echo "\nERROR - PHP Source code was not found in the folder: " . $root_dir;
    exit();
}

foreach ($update_files as $file => $functions) {
    $path = $root_dir . '/' . $file;
    if (!is_file($path)) {
        echo "\nERROR - Missing file: " . $path;
        $errors++;
        continue;
    }
    $contents = file_get_contents($path);
    $orig_content = $contents;
    $functions = (strpos($functions, '|') === false ? explode(',', $functions) : explode('|', $functions));
    $updated = false;
    foreach ($functions as $fn_name) {
        // Build Search text - either `PHP_FUNCTION(name)` or hard-coded the correct version above
        // such as functions using `PHP_NAMED_FUNCTION()` or `SPL_METHOD()`.
        $search = (strpos($fn_name, '(') === false ? 'PHP_FUNCTION(' . $fn_name . ')' : $fn_name);
        $pos = strpos($contents, $search);
        if ($pos === false) {
            echo "\nERROR - " . $search . ' was not found in file: ' . $path;
            $errors++;
            continue;
        }
        $start = $pos + strlen($search);
        $next_50_char = substr($contents, $start, 50);
        if (strpos($next_50_char, $update_text) === false) {
            $len = strlen($contents);
            $found = false;
            while ($start < $len) {
                if ($contents[$start] === '{') {
                    $contents = substr($contents, 0, $start+1) . "\n\tDISABLED_FOR_PLAYGROUND;" . substr($contents, $start+1);
                    $found = true;
                    break;
                }
                $start++;
            }
            if ($found) {
                $fn_updates++;
                $updated = true;
            } else {
                echo "\nERROR - Missing file: " . $path;
                $errors++;
            }
        } else {
            $fn_skipped++;
        }
    }
    if ($updated) {
        file_put_contents($path, $contents);
        $file_updates++;
    }
}

echo "\nFiles Checked: " . count(array_keys($update_files));
echo "\nFiles Updated: " . $file_updates;
echo "\nFunctions Updated: " . $fn_updates;
echo "\nFunctions Skipped: " . $fn_skipped;
echo "\nErrors: " . $errors;
echo "\n";
