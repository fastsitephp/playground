<?php

// --------------------------------------------------------------------------------------
//
// As of 2022-01-02 the FastSitePHP playground no longer allows references
// due to the following bugs (or design decisions) with PHP.
//
// To test without blocking copy and comment out code block
//     `if (strpos($contents, '&$') !== false) {`
// in the main server file [app/app.php] then run these functions.
//
// ** IMPORTANT when testing manual routes this file will not be saved until
//    all instances of the text "&$" are removed from this file
//    (including the text in this comments).
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
// script is loaded and occasionally when running multiple times. Even if references
// are allowed this does not appear to cause the "SEGV on address 0x123" error that
// was described in the original error. Regardless with references blocked this attach
// is also blocked.
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

    // To allow this file to run keep this line and remove all others.
    return 'Hello World';
});

// ** IMPORTANT **
//      To try and these scripts all references characters
//      in above code must to be removed.
//
// Automatic testing is currently not handled for any testing route and routes
// are manually handled when the server is setup. As PHP exploits are found they
// should be tested. Example:
//   https://github.com/mm0r1/exploits
// Check each PHP file one by one. Example on how to test:
//   https://github.com/mm0r1/exploits/blob/master/php-concat-bypass/exploit.php
// Copy the following to this route:
//     new Pwn('uname -a');
// Then copy [class Helper] and [class Pwn] outside of the functions and run
//
// (*) Try this for each php file in the project (no file should be allowed).
// (*) As of 2022-01-08 all examples in the repository depend on references
//     so they are all blocked from saving on the FastSitePHP Playground.
$app->get('/mm0r1-exploits', function() {
    return 'Copy Content from: https://github.com/mm0r1/exploits';
});
