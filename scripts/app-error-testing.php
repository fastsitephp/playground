<?php

// Use this file to manually test the server setup and expected errors.
// Many routes will work when using a Standard Build of PHP and when
// testing locally. When following instructions to create the custom
// PHP Build on a live server most routes will fail with an expected error.
//
// To use copy the contents of this file to [app.php] after creating a custom site.
//
// If you are a security research and interested in testing attacks the first
// thing to do would be to overwrite the [.htaccess] file root of the temporary
// site. If that file can be overwritten then you will have full write access
// to the server (as allowed under Apache permissions) and that would be a good
// starting point for more serious exploits. If you do use this site to test
// security and accidentally take down the server or cause serious issues with it
// then please let the author of FastSitePHP know so that the issue can be fixed
// and so a new server can be setup (if needed) in a timely manner.
//
// For more Advanced Exploit Testing see the file [app-error-testing-2.php].

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
            <li><a href="file-upload">File Upload Test</a></li>
            <li><a href="get-error-file">Get Error File</a></li>
            <li><a href="read-dot-env-file">Read .env File</a></li>
            <li><a href="timeout">Timeout Test</a></li>
            <li><a href="error-log">Check Error Log</a></li>
            <li><a href="memory">Memory Limit</a></li>
            <li><a href="disabled-object">Disabled Object</a></li>
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
    $host = 'smtp.gmail.com';
    $port = 587;
    $timeout = 5;
    $smtp = new \FastSitePHP\Net\SmtpClient($host, $port, $timeout);
    $smtp->noop();
    $smtp->help();
    $smtp = null;
    return 'smtp-test';
});

// Error when function is disabled:
//   ini_set() has been disabled for security reasons
// Error when disabled with custom PHP build:
//   ini_set(): This function is disabled by using a custom PHP build for the FastSitePHP Playground.
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

// Confirm that files cannot be uploaded.
// This is due to the `file_uploads = Off` in the [php.ini] file
$app->route('/file-upload', function() {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $html = <<<'HTML'
        	<form method="POST">
            	<p><input type="file" name="file"></p>
                <p><button type="submit">upload</button></p>
            </form>
HTML;
	    return $html;
    }

    // This should show an empty array
    var_dump($_FILES);

    // If an actual file made it to the server then the following could be used:
    //    move_uploaded_file($filename, $destination)
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

// Make sure `error_log()` can't be used to overwrite `.htaccess`.
// This would overwrite the file so it works on the server and does not
// include settings [open_basedir, file_access_is_limited]; with those
// two settings excluded a end-user will have full-write access to any
// folder visible to PHP and Apache.
$app->get('/error-log', function() use ($app) {
    $contents = "FallbackResource index.php";
    error_log($contents, 3, __DIR__ . '/../.htaccess');
    return 'Success [.htaccess] is overwritten';
});

// When using [memory_limit = 16M]:
// Allowed memory size of 16777216 bytes exhausted (tried to allocate 10485792 bytes)
$app->get('/memory', function() {
    $str = '';
    while (1 === 1) {
        $str .= str_repeat(' ', (1024 * 1024));
    }
});

// Error when disabled from [php.ini]:
//   SplFileObject() has been disabled for security reasons
// If using the custom build of PHP but standard [php.ini] settings:
//   SplFileObject::__construct(): This function is disabled by using a custom PHP build for the FastSitePHP Playground.
$app->get('/disabled-object', function() use ($app) {
    $app->header('Content-Type', 'text/plain');
    $text = '';
    $file = new SplFileObject(__FILE__);
    foreach ($file as $line_num => $line) {
        $text .= "$line_num: $line";
    }
    return $text;
});

// This should return the standard error template
$app->get('/error-page', function() {
    throw new \Exception('Test');
});

// Use this to see what is enabled on the server
// IMPORTANT - [php.ini] settings [disable_functions] and other settings are not secure
// from advanced exploits. Because of this fact a custom build of PHP is used to fully
// block the disabled functions and classes. See [app-error-testing-2.php] for more.
// This code is making assumptions that disabled function and classes listed here
// are actually disabled in the build.
$app->get('/php-func', function() use ($app) {
    $classes = array_values(get_declared_classes());
    $disabled_classes = ini_get('disable_classes');
    $disabled_classes = explode(',', str_replace(' ', '', $disabled_classes));
    $classes = array_diff($classes, $disabled_classes);

    $functions = get_defined_functions(false);
    $functions = array_values($functions['internal']);
    if (filter_var(ini_get('file_access_is_limited'), FILTER_VALIDATE_BOOLEAN) === true) {
        // This info is hard-coded on the assumption that PHP is built with modified functions.
        // This script confirms each modified function.
        $modified_functions = ['file_put_contents', 'mkdir', 'rmdir', 'rename', 'unlink', 'copy'];
    } else {
        $modified_functions = [];
    }
    $disabled_functions = ini_get('disable_functions');
    $disabled_functions = explode(',', str_replace(' ', '', $disabled_functions));
    $unused_disabled_fn = [];
    foreach ($disabled_functions as $fn) {
        if (!in_array($fn, $functions)) {
            $unused_disabled_fn[] = $fn;
        }
    }
    $functions = array_diff($functions, $disabled_functions);
    $functions = array_diff($functions, $modified_functions);

    $obj_groups = [
        [$modified_functions, 'Modified Functions'],
        [$disabled_classes, 'Disabled Classes'],
        [$disabled_functions, 'Disabled Functions'],
        [$unused_disabled_fn, 'Disabled Functions (Not included with this PHP Build)'],
        [$classes, 'Classes'],
        [$functions, 'Functions'],
    ];

    $text = '';
    foreach ($obj_groups as $group) {
        $fn = $group[0];
        if (count($fn) === 0) {
            continue;
        }
        $label = $group[1];
        $text .= "\n" . str_repeat('-', 80) . "\n";
        $text .= count($fn) . ' ' . $label . "\n";
        $text .= str_repeat('-', 80) . "\n";
        foreach ($fn as $name) {
            $text .= $name . "\n";
        }
        $text .= "\n";
    }

    $app->header('Content-Type', 'text/plain');
    return $text;
});

// View the standard PHP Info Page
$app->get('/php-info', function() {
    phpinfo();
});
