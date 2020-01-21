<!doctype html>
<html lang="pt-br">
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
                <a href="./">Início</a>
                <a href="calc">Calculadora</a>
                <a href="page.htm">Página HTML</a>
                <a href="phpinfo" target="_blank">PHP Info</a>
            </nav>
        </header>
        <main>
<?php
    // Este arquivo, [footer.php], [home.php] e [calc.php] são Modelos PHP.
    // Eles misturam conteúdo web padrão (HTML, CSS, JS) com PHP e são
    // renderizados no servidor quando [$app->render()] é chamada.
?>
