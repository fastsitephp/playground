<?php
// This script is the main entry point for the app. All admin web servics
// exist in this file. This script and gets loaded from the file [html\index.php].

// ------------------------------------------------------------------
// Classes used in this file. Classes are not loaded unless used.
// ------------------------------------------------------------------

use FastSitePHP\FileSystem\Search;
use FastSitePHP\FileSystem\Security;
use FastSitePHP\Security\Crypto;
use FastSitePHP\Web\Request;
use FastSitePHP\Web\Response;

// --------------------------------------------------------------------------------------
// Site Configuration
// --------------------------------------------------------------------------------------

$app->template_dir = __DIR__;
$app->not_found_template = '404.htm';
$app->show_detailed_errors = true;

// The key for signing is hard-coded. The value below can be used for testing
// while the actual production server has a different value. See API docs for
// [Security\Crypto\SignedData] as new keys can be generated on the playground.
// The config key is used with [Crypto::sign()] and [Crypto::verify()].
/*
$app->get('/get-key', function() use ($app) {
    $csd = new \FastSitePHP\Security\Crypto\SignedData();
	$key = $csd->generateKey();
	$app->header('Content-Type', 'text/plain');
    return $key;
});
*/
$app->config['SIGNING_KEY'] = '85ef7bb21b3ee94b9e3e953c9aea23cf6ed03ba3252e19afe7210c788739eb87';

// Allow CORS with Headers for posting data with Auth.
// This allows the web service to run from any site.
if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] !== 'null') {
    $app->cors([
        'Access-Control-Allow-Origin' => $_SERVER['HTTP_ORIGIN'],
        'Access-Control-Allow-Headers' => 'Authorization, X-File, X-Rename',
        'Access-Control-Allow-Credentials' => 'true',
    ]);
} else {
    $app->cors('*');
}

// --------------------------------------------------------------------------------------
// Site Functions
// --------------------------------------------------------------------------------------

// More lanuages can be added here as templates are created.
// Available languages must be white-listed for security.
function getLangauage($lang) {
    if ($lang !== 'en') {
        $lang = 'en';
    }
    return $lang;
}

function getTemplateRoot($lang) {
    $lang = getLangauage($lang);
    return __DIR__ . '/../app_data/template/' . $lang . '/';
}

function getSitePath($site) {
    return __DIR__ . '/../html/sites/' . $site . '/';
}

// Return path and extension of a user file.
// [*.php] files only exist under the [app] dir.
function getFilePath($site_dir, $file) {
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    $dir = ($ext === 'php' ? $site_dir . 'app/' : $site_dir);
    return [$dir, $ext];
}


// Send an error as a 500 JSON Response and end script execution.
// The client UI handles this and shows server error messages.
// Errors are not translated because typically no errors should happen
// unless the user manually makes calls using invalid parameters.
// The client UI hides buttons that the user shouldn't have access too.
function sendError($message) {
    // Create a new Response Object passing CORS headers from the App object
    global $app;
    $res = new Response($app);

    // Send 500 response with JSON error message
    $res
        ->statusCode(500)
        ->json([
            'success' => false,
            'error' => $message,
        ])
        ->send();
    exit();
}


// Get list of files names that appear on the User's UI. This excludes
// the directory structure [app] folder along with hidden files.
function getSiteFiles($path) {
    $files = array_diff(scandir($path), ['.', '..', 'app', 'index.php', '.htaccess', 'expires.txt']);
    $app_files = array_diff(scandir($path . 'app'), ['.', '..', '.htaccess']);
    $files = array_merge($files, $app_files);
    return array_values($files);
}


// Returns an Object/Array with a list of site files and
// app code for displaying when the UI first loads.
function getSite($path) {
    return [
        'files' => getSiteFiles($path),
        'app_code' => file_get_contents($path . 'app/app.php'),
    ];
}


// Validate if a user supplied file name will be accepted.
// Only specific file types and basic ASCII letters are allowed.
// [index.php] is not allowed because users are not allowed to modify it,
// and [index.htm] because it could override the default page.
function fileNameIsValid($name) {
    $pattern = '/^[a-zA-Z0-9_\-]{1,}.(php|htm|js|css|svg)$/';
    if (!preg_match($pattern, $name)) {
        return false;
    }
    if (strtolower($name) === 'index.php' || strtolower($name) === 'index.htm') {
        return false;
    }
    return true;
}


// Route Filter Function to get and validate the submitted site.
// This is the core security function that prevents users from modifying
// content on a site that they do not have the key for.
$require_auth = function () use ($app) {
    // Read Key from Auth Header
    // OPTIONS requests will not contain the header
    $req = new Request();
    if ($req->method() === 'OPTIONS') {
        return;
    } else {
        $token = $req->header('Authorization');
        if ($token === null) {
            sendError('Missing Request Header [Authorization]');
        }
    }

    // Validate Token
    $token = str_replace('Bearer ', '', $token);
    $site = Crypto::verify($token);
    if ($site === null) {
        sendError('The site has already expired or has been deleted.');
    }

    // Make sure the site exists
    $path = getSitePath($site);
    if (!is_dir($path)) {
        sendError('The site has been deleted.');
    }

    // Assign to App
    $app->locals['site'] = $site;
};


// Get file name from Request Header
$require_file_name = function() use ($app) {
    $req = new Request();
    if ($req->method() === 'OPTIONS') {
        return;
    }
    $file = $req->header('X-File');
    if ($file === null) {
        sendError('Missing Request Header [X-File]');
    }
    $app->locals['file'] = $file;
};


// Get file name and new name from Request Header
$require_file_rename = function() use ($app) {
    $req = new Request();
    if ($req->method() === 'OPTIONS') {
        return;
    }
    $file = $req->header('X-File');
    $rename = $req->header('X-Rename');
    if ($file === null) {
        sendError('Missing Request Header [X-File]');
    }
    if ($rename === null) {
        sendError('Missing Request Header [X-Rename]');
    }
    $app->locals['file'] = $file;
    $app->locals['rename'] = $rename;
};

// ----------------------------------------------------------------------------
// Routes
// ----------------------------------------------------------------------------

/**
 * Home Page - Return a simple HTML page
 */
$app->get('/', function() {
    return file_get_contents(__DIR__ . '/index.htm');
});


/**
 * Return the template code and files as a JSON object
 */
$app->post('/:lang/site-template', function($lang) {
    $path = getTemplateRoot($lang);
    return getSite($path);
});


/**
 * Create a site for the user by copying the template site
 */
$app->post('/:lang/create-site', function($lang) {
    // Get list of files to copy
    $copy_from = getTemplateRoot($lang);
    $search = new Search();
    $files = $search
        ->dir($copy_from)
        ->recursive(true)
        ->includeRoot(false)
        ->files();

    // Build a random hex string for the site.
    // It's unlikely that a site hex string would be duplicated because
    // the format used allows for 18,446,744,073,709,551,616 possible sites,
    // however just in case check to make sure the site doesn't exist.
    $n = 0;
    do {
        $site = bin2hex(random_bytes(10));
        $copy_to = getSitePath($site);
        $n++;
        if ($n > 2) {
            sendError('Unexpected error. Unable to create site.');
        }
    } while (is_dir($site));

    // Copy files
    mkdir($copy_to . '/app', 0777, true);
    foreach ($files as $file) {
        copy($copy_from . $file, $copy_to . $file);
    }

    // Create the [expires.txt] with a Unix Timestamp set for 1 hour from now.
    // This file is used by a CLI script to delete expired sites.
    $expires = time() + (60 * 60);
    file_put_contents($copy_to . 'expires.txt', $expires);

    // Return site info (site string and expires time) as signed data.
    // Signed data is similar to JWT but uses a different format.
    // By default [Crypto::sign()] uses a 1 hour timeout.
    return [
        'site' => Crypto::sign($site),
    ];
});


/**
 * Return a user site. File list and Code for [app.php] which is
 * the page that appears when the UI is first loaded.
 */
$app->post('/download-site', function() use ($app) {
    $path = getSitePath($app->locals['site']);
    return getSite($path);
})
->filter($require_auth);


/**
 * Download a file from a user site
 */
$app->post('/get-file', function() use ($app) {
    // Get site directory and file
    $dir = getSitePath($app->locals['site']);
    $file = $app->locals['file'];
    list($dir, $ext) = getFilePath($dir, $file);

    // Validate that the file exists only in the user's directory.
    // If the user manually submits a request for a hidden file
    // [index.php, .htaccess, expires.txt] it will be returned
    // however the standard UI does not show it.
    if (!Security::dirContainsFile($dir, $file)) {
        return $app->pageNotFound();
    }

    // Return as an Object for a JSON Response
    return [
        'file' => $file,
        'type' => $ext,
        'content' => file_get_contents($dir . $file),
    ];
})
->filter($require_auth)
->filter($require_file_name);


/**
 * Save a file on a user site. This service handles both existing and new files.
 */
$app->post('/save-file', function() use ($app) {
    // Get site directory, file list, and file
    $dir = getSitePath($app->locals['site']);
    $files = getSiteFiles($dir);
    $file = $app->locals['file'];
    list($dir, $ext) = getFilePath($dir, $file);

    // Validate file name
    if (!fileNameIsValid($file)) {
        sendError('File name is not allowed');
    }

    // Limit the number of files that a user can create
    if (count($files) >= 30 && !is_file($dir . $file)) {
        sendError('You have reached the limit of 30 files on a single site.');
    }

    // Get file contents from the POST body content.
    // [Content-Type] used is 'text/plain' for all files.
    $contents = file_get_contents('php://input');

    // Save file and return success
    file_put_contents($dir . $file, $contents);
    return [ 'success' => true ];
})
->filter($require_auth)
->filter($require_file_name);


/**
 * Rename a file on a user site
 */
$app->post('/rename-file', function() use ($app) {
    // Get site directory and files
    $dir = getSitePath($app->locals['site']);
    $file = $app->locals['file'];
    $rename = $app->locals['rename'];
    list($dir, $ext) = getFilePath($dir, $file);

    // Make sure file type is the same
    $new_ext = pathinfo($rename, PATHINFO_EXTENSION);
    if ($ext !== $new_ext) {
        sendError('Renaming to a new file type is not allowed');
    }

    // Validate file names
    if (!fileNameIsValid($file)) {
        sendError('File name to rename from is not allowed');
    } elseif (!fileNameIsValid($rename)) {
        sendError('New File name is not allowed');
    } elseif (strtolower($file) === 'app.php') {
        sendError('Cannot rename [app.php]');
    }

    // Make sure file to rename exists and new file does not
    if (!Security::dirContainsFile($dir, $file)) {
        sendError('File to rename was not found or has already been deleted');
    } elseif (is_file($dir . $rename)) {
        sendError('A file with new name already exists. Please refresh the page and try again.');
    }

    // Rename file
    $source = $dir . $file;
    $dest = $dir . $rename;
    rename($source, $dest);

    // Update file contents as this route handles both file name and contents.
    // The user sees a [Rename] or [Rename and Save] depending on what changed,
    // however this service simply overwrites the file each time.
    $contents = file_get_contents('php://input');
    file_put_contents($dest, $contents);

    // Return Success
    return [ 'success' => true ];
})
->filter($require_auth)
->filter($require_file_rename);


/**
 * Delete a file on a user site
 */
$app->post('/delete-file', function() use ($app) {
    // Get site directory and file
    $dir = getSitePath($app->locals['site']);
    $file = $app->locals['file'];
    list($dir, $ext) = getFilePath($dir, $file);

    // Validate file name and that the user is not deleting [app.php]
    if (!fileNameIsValid($file)) {
        sendError('File name is not allowed');
    } elseif (strtolower($file) === 'app.php') {
        sendError('File [app.php] cannot be deleted');
    }

    // Validate and delete the file
    if (Security::dirContainsFile($dir, $file)) {
        unlink($dir . $file);
    } else {
        sendError('File was not found or has already been deleted');
    }
    return [ 'success' => true ];
})
->filter($require_auth)
->filter($require_file_name);


/**
 * Delete a user site
 */
$app->post('/delete-site', function() use ($app) {
    // Get directory to look for file in.
    $dir = getSitePath($app->locals['site']);

    // Delete all files and directories.
    // If there is an error (file locked, etc) then the
    // site will later be deleted by the CLI script.
    $app_files = array_diff(scandir($dir . 'app'), ['.', '..']);
    $files = array_diff(scandir($dir), ['.', '..', 'app']);
    foreach ($app_files as $file) {
        unlink($dir . 'app/' . $file);
    }
    foreach ($files as $file) {
        unlink($dir . $file);
    }
    rmdir($dir . 'app');
    rmdir($dir);

    // Result
    return [ 'success' => true ];
})
->filter($require_auth);
