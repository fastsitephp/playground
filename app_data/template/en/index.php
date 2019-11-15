<?php
// -----------------------------------------------------------
// Setup a PHP Autoloader and FastSitePHP
// -----------------------------------------------------------

// Check if the query string [?_debug=stats] is defined and
// if so then keep track of starting time and memory usage.
$show_debug_info = isset($_GET['_debug']) && $_GET['_debug'] === 'stats';
if (isset($show_debug_info) && $show_debug_info) {
    require __DIR__ . '/../../../vendor/fastsitephp/src/Utilities/debug.php';    
}

// Setup a Autoloader for FastSitePHP classes
require __DIR__ . '/../../../vendor/autoload.php';

// Setup a Autoloader the user's own [App] classes
spl_autoload_register(function($class) {
    if (strpos($class, 'App\\') === 0) {
        $file_path = __DIR__ . '/app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
        if (is_file($file_path)) {
            require $file_path;
        }
    }
});

// Create and Setup the FastSitePHP Application Object
$app = new \FastSitePHP\Application();
$app->setup('UTC');

// Set Config for the User's Playground Site
$app->show_detailed_errors = true;
$app->template_dir = __DIR__ . '/app/';
$app->controller_root = 'App';
$app->middleware_root = 'App';

// Disable most streams, [file] and [php] are allowed while [http/https]
// will be disabled by the [php.ini] setting [allow_url_fopen = Off].
// http://docs.php.net/manual/en/wrappers.php
foreach (stream_get_wrappers() as $protocal) {
    if ($protocal !== 'file' && $protocal !== 'php' && $protocal !== 'http' && $protocal !== 'https') {
        stream_wrapper_unregister($protocal);
    }
}

// Include the App File for the Site.
require __DIR__ . '/app/app.php';

// -----------------------------------------------------------
// Run the application
// -----------------------------------------------------------

// Run the app to determine and show the specified URL
$app->run();

// If debug then add script time and memory info to the end of the page
if (isset($show_debug_info) && $show_debug_info) {
    $showDebugInfo();
}
