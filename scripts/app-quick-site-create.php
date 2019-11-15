<?php
// This file can be used as [Playground\app\app.php] for quick testing and
// confirming that the server/computer supports needed features. It allows
// sites to be created quickly over and over by refreshing the page, however
// it doesn't provide a UI to edit the files.

// ------------------------------------------------------------------
// Classes used in this file. Classes are not loaded unless used.
// ------------------------------------------------------------------

use FastSitePHP\FileSystem\Search;
use FastSitePHP\Security\Crypto;

// --------------------------------------------------------------------------------------
// Site Configuration
// --------------------------------------------------------------------------------------

$app->show_detailed_errors = true;

// The key for signing is hard-coded. The value below is for dev
// while the actual production server has a different value.
$app->config['SIGNING_KEY'] = 'ab2403a36467b59b20cc314bb211e1812668b3bffb00358c161f26fe003073ed';

// ----------------------------------------------------------------------------
// Routes
// ----------------------------------------------------------------------------

$app->get('/', function() {
    return 'Playground Site';
});

// Later this will require a POST
// Homepage will then be a simple single file PHP page rendered from this directory
$app->get('/create-site', function() {
    // Get list of files to copy
    $copy_from = __DIR__ . '/../app_data/template/en/';
    $search = new Search();
    $files = $search
        ->dir($copy_from)
        ->recursive(true)
        ->includeRoot(false)
        ->files();
    
    // Build a random hex string for the site
    $site = bin2hex(random_bytes(10));    
    $copy_to = __DIR__ . '/../html/sites/' . $site . '/';

    // Copy files
    mkdir($copy_to . '/app', 0777, true);
    foreach ($files as $file) {
        copy($copy_from . $file, $copy_to . $file);
    }

    // Create the [expires.txt] with a Unix Timestamp set for 1 hour from now
    $expires = time() + (60 * 60);
    file_put_contents($copy_to . 'expires.txt', $expires);

    // Show Site Info
    $token = Crypto::sign($site);
    $verified = Crypto::verify($token);
    return '<h1>Site Created: ' . $site . '</h1><a href="./sites/' . $site . '/">View Site</a><br><br><strong>Key:</strong> ' . $token . '<br><br><strong>Verified:</strong> ' . $verified;
});
