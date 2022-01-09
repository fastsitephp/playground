<?php

// --------------------------------------------------------------------------------------
//
// Use this file to manually test some advanced PHP bugs.
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
// will stay up. If attacks are found in the future it could be taken down or switched
// to a different setup.
//
// --------------------------------------------------------------------------------------

$app->get('/', function() use ($app) {
    $html = <<<'HTML'
        <h1>Critical Error Testing</h1>
        <ul>
            <li><a href="php-bug-81705">PHP Bug 81705</a></li>
            <li><a href="mm0r1-exploits">mm0r1 Exploits</a></li>
        </ul>
HTML;
    return $html;
});

// https://bugs.php.net/bug.php?id=81705
// This returns an E_NOTICE error "Array to string conversion" the first time the
// script is loaded and occasionally when running multiple times. This does not appear
// to cause the "SEGV on address 0x123" error that was described in the original error.
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

// Automatic testing is currently not handled for any testing route and routes
// are manually handled when the server is setup. As PHP exploits are found they
// should be tested. Example:
//   https://github.com/mm0r1/exploits
// Check each PHP file one by one. Example on how to test:
//   https://github.com/mm0r1/exploits/blob/master/php-concat-bypass/exploit.php
// Copy the following to this route:
//     new Pwn('uname -a');
// Then copy [class Helper] and [class Pwn] outside of the functions and run
// Currently the exploits cause 502 Bad Gateway errors but do not take the server
// down or return the expected exploit info with the production server. On
// a test server it seemed to work.
$app->get('/mm0r1-exploits', function() {
    return 'Copy Content from: https://github.com/mm0r1/exploits';
});
