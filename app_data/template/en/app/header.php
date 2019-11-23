<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="site.css">
        <link rel="shortcut icon" href="../../favicon.ico" />
        <title><?= $app->escape($page_title) ?></title>
    </head>
    <body>
        <header>
            <nav>
                <a href="./">Home</a>
                <a href="calc">Calculator</a>
                <a href="page.htm">HTML Page</a>
                <a href="phpinfo" target="_blank">PHP Info</a>
            </nav>
        </header>
        <main>
<?php
    // This file, [footer.php], [home.php], and [calc.php] are PHP Templates.
    // They mix standard web content (HTML, CSS, JS) with PHP and are rendered
    // on the server when [$app->render()] is called.
?>