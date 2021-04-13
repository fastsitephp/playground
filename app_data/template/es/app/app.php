<?php

// --------------------------------------------------------------------------------------
// Este archivo es el punto de entrada principal para su sitio web, por lo que no
// se puede eliminar ni renombrar. La página predeterminada y las rutas adicionales
// se ejecutan desde este archivo. Todos los archivos [*.php] en un sitio de juegos
// deben pasar por este archivo mientras que los archivos estáticos [htm, js, css y svg]
// omiten este archivo y se puede acceder a ellos directamente por URL.
//
// Después de ver la primera página, intente hacer un cambio, como reemplazar
// 'Hola Mundo' con su nombre.
// --------------------------------------------------------------------------------------

// Clases utilizadas en este archivo. Las clases no se cargan a menos que se usen.
use App\Calculator;
use FastSitePHP\Data\Validator;
use FastSitePHP\Web\Request;

// Los archivos de plantilla especificados aquí se
// incluirán cada vez que se llame a [$app->render()]
$app->header_templates = 'header.php';
$app->footer_templates = 'footer.php';

// Página de inicio
$app->get('/', function() use ($app) {
    return $app->render('home.php', [
        'page_title' => 'Hola Mundo',
    ]);
});

// Página de calculadora
$app->get('/calc', function() use ($app) {
    return $app->render('calc.php', [
        'page_title' => 'Calculadora',
        // Cree nuevos números aleatorios cada vez que se actualice la página
        'x' => rand(0, 1000000),
        'y' => rand(0, 1000000),
    ]);
});

// Servicio web llamado desde la página [calc].
// Lee una publicación JSON y devuelve JSON.
$app->post('/calculate', function() {
    // Leer la publicación de JSON
    $req = new Request();
    $data = $req->content();

    // Validar
    $v = new Validator();
    $v->addRules([
        // Campo,  Título,    Reglas
        ['x',    'Value X',   'required type="number"'],
        ['op',   'Operator',  'required list="+, -, *, /"'],
        ['y',    'Value Y',   'required type="number"'],
    ]);
    list($errors, $fields) = $v->validate($data);
    if ($errors) {
        return [
            'success' => false,
            // En PHP [implode()] es similar a [join()]
            // en otros lenguajes de programación.
            'error' => implode(' ', $errors),
        ];
    }

    // Calcular el resultado
    try {
        $calc = new Calculator();
        $x = (float)$data['x']; // Convertir a un número
        $op = $data['op'];
        $y = (float)$data['y'];
        $result = $calc->calculate($x, $op, $y);
        return [
            'success' => true,
            'result' => "${x} ${op} ${y} = ${result}",
        ];
    } catch (\Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage(),
        ];
    }
});

// Muestre la página de información PHP estándar que proporciona
// información de la versión del servidor y PHP.
$app->get('/phpinfo', function() {
    phpinfo();
});

// Route Parameters
// Based on special server config for the playground
// include `index.php` in the URL to see this URL:
//     https://playground.fastsitephp.com/{site}/index.php/hello/Name
$app->get('/hello/:name', function($name) use ($app) {
    return ['Hello' => $name];
});
