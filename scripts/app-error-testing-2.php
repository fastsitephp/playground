<?php

// --------------------------------------------------------------------------------------
//
// Advanced Exploit Testing
//
// When this file was first created PHP references were blocked from saving
// however that turned out to be unreliable so now end users can save PHP code
// with references.
//
// See: https://github.com/mm0r1/exploits/issues/10#issuecomment-1008248348
//     "Relying on php.ini settings doesn't provide any additional security due
//      to the abundance of memory corruption vulnerabilities in PHP."
//
// Based on the above issue comment its likely there is a way to bypass playground
// security with memory corruption. If methods are found in the future the custom
// build of PHP may include more changes rather than relying on [php.ini]. In the
// meantime no security-sensitive info exists on the server so the playground site
// will stay up. If attacks are found in the future it could be taken down or
// switched to a different setup.
//
// --------------------------------------------------------------------------------------

$app->get('/', function() use ($app) {
    $routes = $app->routes();

    $html = '<h1>Critical Error Testing</h1><ul>';
    foreach ($routes as $route) {
        $pattern = substr($route->pattern, 1);
        if ($pattern) {
            $html .= '<li><a href="' . $app->escape($pattern) . '">' . $app->escape($pattern) . '</a></li>';
        }
    }
    $html .= '</ul>';

    return $html;
});

// https://bugs.php.net/bug.php?id=81705
// This returns an E_NOTICE error "Array to string conversion" the first time the
// page is loaded and often when refreshing the page (even without viewing this route).
// Because of this issue the code is commented out by default.
//
// Running this does not cause the "SEGV on address 0x123" error that
// was described in the original error.
/*
$app->get('/php-bug-81705', function() {
    $my_var = str_repeat("a", 1);
    set_error_handler(
        function() use(&$my_var) {
            echo("error\n");
            $my_var = 0x123;
        }
    );
    $my_var .= [0];
    return $my_var;
});
*/

// If security is in place this will generate an error
// otherwise if security is bypassed the file will show.
$app->get('/get-error-file', function() use ($app) {
    $path = __DIR__ . '/../../../../app/app.php';
    $app->header('Content-Type', 'text/plain');
    return file_get_contents($path);
});

// https://github.com/mm0r1/exploits
//
// Prior to a new custom PHP build using changes in [exec.c] the first route
// below for [php-concat-bypass] successfully worked to bypass security.
//
// Automatic testing is currently not handled for any testing route and routes
// are manually handled when the server is setup. As PHP exploits are found they
// should be tested.
//
// Check each PHP file one by one. For each of these routes the actual class
// and function code must be copied from the [exploit.php] file. It can go at
// the below the routes.

// https://github.com/mm0r1/exploits/blob/master/php-concat-bypass/exploit.php
// Result:
//  502 Bad Gateway from nginx using default code (some tested servers)
//      It causes a "Segmentation fault" for Apache but doesn't stop
//      the site from working so these errors are acceptable for now.
//      To view related apache log on server:
//      tail /var/log/apache2/error.log
//  BEFORE UPDATE in [exec.c]:
//      It runs and bypasses security when using modified code from (not always required):
//      https://github.com/mm0r1/exploits/commit/e287753cadd23836c35c8b5cb39a135e174b13db
//      Uncomment `$addr += 0x10;` and comment out `$addr -= 0x10;`
//  AFTER UPDATE:
//      Pwn::{closure}(): This function is disabled by using a custom PHP build for the FastSitePHP Playground.
$app->get('/mm0r1-exploits-php-concat-bypass', function() {
    // Default code to show current system info
    new Pwn("uname -a");

    // Successful attack on the system. After this try route [/get-error-file]
    // to confirm security was bypassed.
    //
    // new Pwn("echo 'FallbackResource index.php' > " . __DIR__ . '/../.htaccess');
    // return 'Updated [.htaccess], refresh page and try again';
});

// https://github.com/mm0r1/exploits/blob/master/php-filter-bypass/exploit.php
// Result: fopen() has been disabled for security reasons
$app->get('/mm0r1-exploits-php-filter-bypass', function() {
    pwn('uname -a');
});

// https://github.com/mm0r1/exploits/blob/master/php-json-bypass/exploit.php
// Result: UAF failed.
$app->get('/mm0r1-exploits-php-json-bypass', function() {
    global $cmd, $n_alloc, $y;
    $cmd = "id";
    $n_alloc = 10; # increase this value if you get segfaults
    $y = [new Z()];
    json_encode([&$y]);
});

// https://github.com/mm0r1/exploits/blob/master/php7-backtrace-bypass/exploit.php
// Result: Couldn't determine binary base address
$app->get('/mm0r1-exploits-php-backtrace-bypass', function() {
    error_reporting(E_ERROR);
    pwn("uname -a");
});

// https://github.com/mm0r1/exploits/blob/master/php7-gc-bypass/exploit.php
// Result: UAF failed
$app->get('/mm0r1-exploits-php-gc-bypass', function() {
    error_reporting(E_ERROR);
    pwn("uname -a");
});

// IMPORTANT - for [mm0r1/exploits] the classes and functions
// need to be manually copied here when testing, example:
/*
class Helper { public $a, $b, $c; }
class Pwn {
*/
