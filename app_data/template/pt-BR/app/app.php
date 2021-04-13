<?php

// --------------------------------------------------------------------------------------
// Este arquivo é o ponto de entrada principal para seu site então não pode apagá-lo
// ou renomeá-lo. A página padrão e rotas adicionais rodam deste arquivo.
// Todos os arquivo [*.php] em um site do Code Playground devem passar por este arquivo
// enquanto arquivos estáticos [htm, js, css, and svg] ignoram este arquivo e podem ser
// acessados diretamente pela URL.
//
// Depois que você vir a primeira página tente fazer uma modificação como
// substituir 'Olá Mundo' com o seu nome.
// --------------------------------------------------------------------------------------

// Classes utilizadas neste arquivo. Classes não são carregadas a não ser se
// forem utilizadas.
use App\Calculator;
use FastSitePHP\Data\Validator;
use FastSitePHP\Web\Request;

// Arquivos modelo especificados aqui serão incluídos sempre que [$app->render()]
// for chamada.
$app->header_templates = 'header.php';
$app->footer_templates = 'footer.php';

// Página Inicial
$app->get('/', function() use ($app) {
    return $app->render('home.php', [
        'page_title' => 'Olá Mundo',
    ]);
});

// Página Calculadora
$app->get('/calc', function() use ($app) {
    return $app->render('calc.php', [
        'page_title' => 'Calculator',
        // Crie novos números aleatórios cada vez que a página for atualizada
        'x' => rand(0, 1000000),
        'y' => rand(0, 1000000),
    ]);
});

// Web Service chamado à partir da página [calc].
// Isto lê um post JSON e retorna JSON.
$app->post('/calculate', function() {
    // Lê o Post JSON
    $req = new Request();
    $data = $req->content();

    // Validar
    $v = new Validator();
    $v->addRules([
        // Campo,  Título,    Regras
        ['x',    'Value X',   'required type="number"'],
        ['op',   'Operator',  'required list="+, -, *, /"'],
        ['y',    'Value Y',   'required type="number"'],
    ]);
    list($errors, $fields) = $v->validate($data);
    if ($errors) {
        return [
            'success' => false,
            // Em PHP [implode()] é similar à [join()]
            // em outras linguagens de programação.
            'error' => implode(' ', $errors),
        ];
    }

    // Calcular o resultado
    try {
        $calc = new Calculator();
        $x = (float)$data['x']; // Converter para um número
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

// Mostrar a página de informações padrão do PHP que fornece informações do Servidor e do PHP.
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
