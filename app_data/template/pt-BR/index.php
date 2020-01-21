<?php
// ------------------------------------------------------------------
// Configurar um autoloader PHP e o FastSitePHP
// ------------------------------------------------------------------

// Verificar se a string de consulta [?_debug=stats] está definida e
// se estiver então observe do horário de início e uso de memória.
$show_debug_info = isset($_GET['_debug']) && $_GET['_debug'] === 'stats';
if (isset($show_debug_info) && $show_debug_info) {
    require __DIR__ . '/../../../vendor/fastsitephp/src/Utilities/debug.php';    
}

// Configurar um Autoloader para classes do FastSitePHP
require __DIR__ . '/../../../vendor/autoload.php';

// Configurar um Autoloader para as classes [App] do próprio usuário
spl_autoload_register(function($class) {
    if (strpos($class, 'App\\') === 0) {
        $file_path = __DIR__ . '/app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
        if (is_file($file_path)) {
            require $file_path;
        }
    }
});

// Criar e Configurar o Objeto da Aplicação FastSitePHP
$app = new \FastSitePHP\Application();
$app->setup('UTC');

// Definir a Configuração para o Site Playground do Usuário
$app->show_detailed_errors = true;
$app->template_dir = __DIR__ . '/app/';
$app->controller_root = 'App';
$app->middleware_root = 'App';

//Desabilitar a maioria dos fluxos, [file] e [php] são permitidos enquanto [http/https]
// serão desativados pela definição [allow_url_fopen = Off] do [php.ini].
// http://docs.php.net/manual/en/wrappers.php
foreach (stream_get_wrappers() as $protocal) {
    if ($protocal !== 'file' && $protocal !== 'php' && $protocal !== 'http' && $protocal !== 'https') {
        stream_wrapper_unregister($protocal);
    }
}

// Incluir o Arquivo da App para o Site.
require __DIR__ . '/app/app.php';

// -----------------------------------------------------------
// Rodar a aplicação
// -----------------------------------------------------------

// Rodar a aplicação para determinar e mostrar a URL especificada
$app->run();

// Se depurar, adicione tempo do script e informações de memória no final da página
if (isset($show_debug_info) && $show_debug_info) {
    $showDebugInfo();
}
