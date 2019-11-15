<?php

// --------------------------------------------------------------------------------------
// This file is the main entry point for your website so it cannot be deleted
// or renamed. The default page and additional routes run from this file.
// All [*.php] files on a playground site must go through this file while static files
// [htm, js, css, and svg] bypass this file and can be accessed by URL directly.
//
// After you view the first page try making a change such as replacing
// 'Hello World' with your name.
// --------------------------------------------------------------------------------------

// Classes used in this file. Classes are not loaded unless used.
use App\Calculator;
use FastSitePHP\Data\Validator;
use FastSitePHP\Web\Request;

// Template files specified here will be included whenever [$app->render()] is called
$app->header_templates = 'header.php';
$app->footer_templates = 'footer.php';

// Home Page
$app->get('/', function() use ($app) {
    return $app->render('home.php', [
        'page_title' => 'Hello World',
    ]);
});

// Calculator Page
$app->get('/calc', function() use ($app) {
    return $app->render('calc.php', [
        'page_title' => 'Calculator',
        // Create new random numbers each time the page is refreshed
        'x' => rand(0, 1000000),
        'y' => rand(0, 1000000),
    ]);
});

// Web Service called from the [calc] page.
// It reads a JSON post and returns JSON.
$app->post('/calculate', function() {
    // Read the JSON Post
    $req = new Request();
    $data = $req->content();

    // Validate
    $v = new Validator();
    $v->addRules([
        // Field,  Title,    Rules
        ['x',    'Value X',   'required type="number"'],
        ['op',   'Operator',  'required list="+, -, *, /"'],
        ['y',    'Value Y',   'required type="number"'],
    ]);
    list($errors, $fields) = $v->validate($data);
    if ($errors) {
        return [
            'success' => false,
            // In PHP [implode()] is similar to [join()] 
            // in other programming languages.
            'error' => implode(' ', $errors),
        ];
    }

    // Calculate the result
    try {
        $calc = new Calculator();
        $x = (float)$data['x']; // Convert to a number
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

// Show the standard PHP info page which provides Server and PHP version info.
$app->get('/phpinfo', function() {
    phpinfo();
});
