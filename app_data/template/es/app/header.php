<!doctype html>
<html lang="es">
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
                <a href="./">Casa</a>
                <a href="calc">Calculadora</a>
                <a href="page.htm">Página HTML</a>
                <a href="phpinfo" target="_blank">Información PHP</a>
            </nav>
        </header>
        <main>
<?php
    // Este archivo, [footer.php], [home.php] y [calc.php] son plantillas PHP.
    // Combinan contenido web estándar (HTML, CSS, JS) con PHP y se procesan
    // en el servidor cuando se llama a [$app->render()].
?>