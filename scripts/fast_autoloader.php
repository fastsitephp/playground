<?php

// Minimal Autoloader for the FastSitePHP Playground Site
spl_autoload_register(function($class) {
    if (strpos($class, 'FastSitePHP\\') === 0) {
        $file_path = __DIR__ . '/fastsitephp/src/' . str_replace('\\', '/', substr($class, 12)) . '.php';
    } elseif (strpos($class, 'Psr\\') === 0) {
        $file_path = __DIR__ . '/psr/log/Psr/' . str_replace('\\', '/', substr($class, 4)) . '.php';
    }
    if (isset($file_path) && is_file($file_path)) {
        require $file_path;
    }
});
