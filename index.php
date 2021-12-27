<?php

// This file runs from a development environment and simply
// redirects to the [html] directory which is used as the
// public root directory on the production web server.

// On the main site the public web location is [/var/www/public]
// so the [.htaccess] files for user sites are configured for the
// specific path, because this site uses specific [.htaccess] files
// running in development is recommended for the PHP built-in Web Server
// using the below instructions and not a local install of Apache.

// Or to test on an actual server use a cloud server and follow instructions
// from the file [docs\Playground Server Setup.txt].

// To run from a command line or terminal program you can use the following:
//     cd {root-directory}
//     php -S localhost:3000
//
// Then open your web browser to:
//     http://localhost:3000/playground/public/
//
// This assume the following folder structure:
//   - fastsitephp "Root Directory"
//     - fastsitephp Repository
//     - playground Repository (this project)

header('Location: public/');
