<?php
// This script deletes expired sites and runs every minute as a cron job on the production server.
// If the server were using Windows it would be set to run as a Scheduled Task.
// Each time this script runs it logs the results, overwriting the last file.

set_time_limit(0);
error_reporting(-1);
ini_set('display_errors', 'on');
date_default_timezone_set('UTC');

// Modify [SITES_DIR] as needed for testing locally or using on a different server.
// const SITES_DIR = 'C:\Users\Administrator\Documents\Temp\FastSitePHP\Playground\html\sites\\';
const SITES_DIR = '/var/www/html/sites/';
define('LOG_FILE', SITES_DIR . '../../app_data/delete-sites-last-result.txt');

if (php_sapi_name() === 'cli') {
    main();
} else {
    echo 'This script only runs from Command Line';
}

function main() {
    $start_time = time();
    $log = [
        'Starting Script at ' . date(DATE_RFC2822),
    ];
    $expired_count = 0;
    $active_count = 0;
    $list = array_diff(scandir(SITES_DIR), ['.', '..']);

    foreach ($list as $site) {
        $full_path = SITES_DIR . $site;
        if (is_dir($full_path)) {
            $is_expired = siteHasExpired($log, $full_path, $site);
            if ($is_expired) {
                deleteSite($full_path);
                $expired_count++;
            } else {
                $active_count++;
            }
        }
    }

    $log[] = 'Expired / Deleted Sites: ' . $expired_count;
    $log[] = 'Active Sites: ' . $active_count;
    $log[] = 'Script Complete at ' . date(DATE_RFC2822);
    $log[] = 'Script Time in Seconds: ' . (time() - $start_time);
    file_put_contents(LOG_FILE, implode("\n", $log));
    foreach ($log as $item) {
        echo $item . "\n";
    }
}

function siteHasExpired(&$log, $dir, $site) {
    // Check if site has expired based on timestamp in [expires.txt]
    $expires_txt = $dir . '/expires.txt';
    if (is_file($expires_txt)) {
        $expires_time = (int)file_get_contents($expires_txt);
        if ($expires_time < time()) {
            return true;
        }
    } else {
        // This is unexpected so log the error as the site will need to be
        // manually deleted. It's possible that the site was being deleted
        // and failed before all files were deleted. If this happens run
        // [rm -rf {dir}] on the server for each directory after reviewing it.
        $one_hour_ago = time() - (60 * 60);
        if (filemtime($dir) < $one_hour_ago) {
            $log[] = "Site ${site} is missing [expires.txt]";
        }
    }
    return false;
}

function deleteSite($dir) {
    $dir = $dir . '/';
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
}
