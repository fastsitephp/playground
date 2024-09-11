<!doctype html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title><?= $app->escape($page_title) ?></title>
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link rel="stylesheet" href="site.css">
        <script nomodule>
            var link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'site-ie.css';
            document.head.appendChild(link);
        </script>
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
