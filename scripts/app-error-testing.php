<?php

// Use this file to manually test the server setup and expected errors.
// Many routes will work when using a Standard Build of PHP and when
// testing locally. When following instructions to create the custom
// PHP Build on a live server most routes will fail with an expected error.
//
// To use copy the contents of this file to [app.php] after creating a custom site.

$app->get('/', function() use ($app) {
    $html = <<<'HTML'
        <h1>Error Testing</h1>
        <ul>
            <li><a href="file-test">Create File</a></li>
            <li><a href="unlink-test">Delete File</a></li>
            <li><a href="copy-test">Copy File</a></li>
            <li><a href="mkdir-test">Create Dir</a></li>
            <li><a href="rmdir-test">Delete Dir</a></li>
            <li><a href="rename-test">Rename File</a></li>
            <li><a href="http-test">HTTP Test</a></li>
            <li><a href="https-test">HTTPS Test</a></li>
            <li><a href="smtp-test">SMTP Test</a></li>
            <li><a href="ini-set">INI Set</a></li>
            <li><a href="shell-exec">Shell Exec</a></li>
            <li><a href="get-valid-file-1">Get Valid File 1</a></li>
            <li><a href="get-valid-file-2">Get Valid File 2</a></li>
            <li><a href="get-error-file">Get Error File</a></li>
            <li><a href="read-dot-env-file">Read .env File</a></li>
            <li><a href="timeout">Timeout Test</a></li>
            <li><a href="memory">Memory Limit</a></li>
            <li><a href="error-page">Error Page</a></li>
            <li><a href="php-func">PHP Classes and Functions</a></li>
            <li><a href="php-info">PHP Info</a></li>
        </ul>
HTML;
    return $html;
});

// Error when using Custom PHP Build:
// file_put_contents(): You cannot write files using this build of PHP.
$app->get('/file-test', function() {
    $path = __DIR__ . '/test.txt';
    file_put_contents($path, 'This is a test');
    return 'File Created: ' . json_encode(is_file($path));
});

// Error when using Custom PHP Build:
// unlink(): You cannot delete files using this build of PHP.
$app->get('/unlink-test', function() {
    $path = __DIR__ . '/test.txt';
    if (!is_file($path)) {
        $path = __FILE__;
    }
    unlink($path);
    return 'File Exits: ' . json_encode(is_file($path));
});

// Error when using Custom PHP Build:
// copy(): You cannot copy files using this build of PHP.
$app->get('/copy-test', function() {
    $source = __DIR__ . '/app.php';
    $dest = __DIR__ . '/copy.php';
    copy($source, $dest);
    return 'File Copied: ' . json_encode(is_file($dest));
});

// Error when using Custom PHP Build:
// mkdir(): You cannot create directories using this build of PHP.
$app->get('/mkdir-test', function() {
    $path =  __DIR__ . '/test';
    mkdir($path);
    return 'Directory Created: ' . json_encode(is_dir($path));
});

// Error when using Custom PHP Build:
// mkdir(): You cannot create directories using this build of PHP.
$app->get('/rmdir-test', function() {
    $path =  __DIR__ . '/app';
    rmdir($path);
    return 'Directory Removed: ' . json_encode(is_dir($path));
});

// Error when using Custom PHP Build:
// mkdir(): You cannot rename files using this build of PHP.
$app->get('/rename-test', function() {
    $source = __DIR__ . '/app.php';
    $dest = __DIR__ . '/renamed.php';
    rename($source, $dest);
    return 'File Renamed: ' . json_encode(is_file($dest));
});

// Error when [allow_url_fopen=0]:
// file_get_contents(): http:// wrapper is disabled in the server configuration by allow_url_fopen=0
$app->get('/http-test', function() {
    return file_get_contents('http://www.example.com/');
});

// [https] will also error however the custom PHP build does not include [openssl] for [https]
// support so this will give a different error message than the http test.
$app->get('/https-test', function() {
    return file_get_contents('https://www.example.com/');
});

// With correct [php.ini] settings this will generate:
// stream_socket_client() has been disabled for security reasons
$app->get('/smtp-test', function() {
    $reply_lines = [];
    $debug_callback = function($message) use (&$reply_lines) {
        $reply_lines[] = '[' . date('H:i:s') . '] ' . trim($message);
    };
    $host = 'smtp.gmail.com';
    $port = 587;
    $timeout = 5;
    $smtp = new \FastSitePHP\Net\SmtpClient($host, $port, $timeout, $debug_callback);
    $smtp->noop();
    $smtp->help();
    $smtp = null;
    return $reply_lines;
});

// Error when function is disabled:
// ini_set() has been disabled for security reasons
$app->get('/ini-set', function() {
    ini_set('display_errors', 'off');
    return ini_get('display_errors');
});

// Error when function is disabled:
// shell_exec() has been disabled for security reasons
$app->get('/shell-exec', function() {
    return shell_exec('whoami');
});

// Valid because user will have access to this directory
// based on the [.] in [.htaccess] -> [php_value open_basedir /var/www/vendor:.].
// [:] is the path separator in Linux/Unix.
$app->get('/get-valid-file-1', function() use ($app) {
    $app->header('Content-Type', 'text/plain');
    return file_get_contents(__FILE__);
});

// Valid because user will have access to this directory
// based on the [/var/www/vendor] in [.htaccess] -> [php_value open_basedir]
$app->get('/get-valid-file-2', function() use ($app) {
    $path = __DIR__ . '/../../../../vendor/fastsitephp/src/Application.php';
    $app->header('Content-Type', 'text/plain');
    return file_get_contents($path);
});

// This works when testing local without the [open_basedir] setting.
// In production it gives this error:
// file_get_contents(): open_basedir restriction in effect. ...
$app->get('/get-error-file', function() use ($app) {
    $path = __DIR__ . '/../../../../app/app.php';
    $app->header('Content-Type', 'text/plain');
    return file_get_contents($path);
});

// View the [.env] file, if a user can view this file, then they could save or view other sites.
// Same result as the above route - works locally, error in production.
$app->get('/read-dot-env-file', function() use ($app) {
    $path = __DIR__ . '/../../../../app_data/.env';
    $app->header('Content-Type', 'text/plain');
    return file_get_contents($path);
});

// [php.ini] should be set to [max_execution_time = 1] so requests with
// this should timeout quickly. The browser will try for many seconds
// and the give up and show an error such as "ERR_EMPTY_RESPONSE".
// This function uses a lot of resources as well so the CPU will spike
// but the site should still function ok when it runs.
$app->get('/timeout', function() {
    while (1 === 1) {
        \password_hash('password', PASSWORD_BCRYPT, ['cost' => 20]);
    }
});

// When using [memory_limit = 16M]:
// Allowed memory size of 16777216 bytes exhausted (tried to allocate 10485792 bytes)
$app->get('/memory', function() {
    $str = '';
    while (1 === 1) {
        $str .= str_repeat(' ', (1024 * 1024));
    }
});

// This should return the standard error template
$app->get('/error-page', function() {
    throw new \Exception('Test');
});

// Use this to see what is enabled on the server
$app->get('/php-func', function() use ($app) {
    $classes = array_values(get_declared_classes());
    $disabled_classes = ini_get('disable_classes');
    $disabled_classes = explode(',', str_replace(' ', '', $disabled_classes));
    $classes = array_diff($classes, $disabled_classes);
    $text = str_repeat('-', 80) . "\n";
    $text .= count($classes) . " Classes\n";
    $text .= str_repeat('-', 80) . "\n";
    foreach ($classes as $name) {
        $text .= $name . "\n";
    }

    $functions = get_defined_functions(true);
    $functions = array_values($functions['internal']);
    $text .= "\n\n" . str_repeat('-', 80) . "\n";
    $text .= count($functions) . ' Functions' . "\n";
    $text .= str_repeat('-', 80) . "\n";
    foreach ($functions as $name) {
        $text .= $name . "\n";
    }

    $app->header('Content-Type', 'text/plain');
    return $text;
});

// View the standard PHP Info Page
$app->get('/php-info', function() {
    phpinfo();
});
