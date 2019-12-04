<?php
// -----------------------------------------------------------
// Configurar un cargador automático de PHP y FastSitePHP
// -----------------------------------------------------------

// Compruebe si la cadena de consulta [?_Debug=stats] está definida y,
// en caso afirmativo, realice un seguimiento del tiempo de inicio
// y el uso de la memoria.
$show_debug_info = isset($_GET['_debug']) && $_GET['_debug'] === 'stats';
if (isset($show_debug_info) && $show_debug_info) {
    require __DIR__ . '/../../../vendor/fastsitephp/src/Utilities/debug.php';    
}

// Configurar un autocargador para clases FastSitePHP
require __DIR__ . '/../../../vendor/autoload.php';

// Configurar un autocargador de clases propias [App] del usuario
spl_autoload_register(function($class) {
    if (strpos($class, 'App\\') === 0) {
        $file_path = __DIR__ . '/app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
        if (is_file($file_path)) {
            require $file_path;
        }
    }
});

// Crear y configurar el objeto de aplicación FastSitePHP
$app = new \FastSitePHP\Application();
$app->setup('UTC');

// Establecer configuración para el sitio de juegos del usuario
$app->show_detailed_errors = true;
$app->template_dir = __DIR__ . '/app/';
$app->controller_root = 'App';
$app->middleware_root = 'App';

// Desactiva la mayoría de las transmisiones, [file] y [php] están permitidos,
// mientras que [http / https] se desactivará mediante la configuración
// [php.ini] [allow_url_fopen=Off].
// http://docs.php.net/manual/es/wrappers.php
foreach (stream_get_wrappers() as $protocal) {
    if ($protocal !== 'file' && $protocal !== 'php' && $protocal !== 'http' && $protocal !== 'https') {
        stream_wrapper_unregister($protocal);
    }
}

// Incluya el archivo de la aplicación para el sitio.
require __DIR__ . '/app/app.php';

// -----------------------------------------------------------
// Ejecuta la aplicación
// -----------------------------------------------------------

// Ejecute la aplicación para determinar y mostrar la URL especificada
$app->run();

// En caso de depuración, agregue el tiempo del script y
// la información de la memoria al final de la página
if (isset($show_debug_info) && $show_debug_info) {
    $showDebugInfo();
}
