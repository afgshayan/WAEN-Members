<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// When accessed via root .htaccess rewrite (no /public in REQUEST_URI),
// fix SCRIPT_NAME so Laravel's route detection works correctly.
if (
    strpos($_SERVER['REQUEST_URI'] ?? '', '/public/') === false &&
    strpos($_SERVER['SCRIPT_NAME'] ?? '', '/public/') !== false
) {
    $_SERVER['SCRIPT_NAME'] = str_replace('/public/index.php', '/index.php', $_SERVER['SCRIPT_NAME']);
    $_SERVER['PHP_SELF']    = str_replace('/public/index.php', '/index.php', $_SERVER['PHP_SELF'] ?? '');
}

// Redirect to installer if not yet installed
if (!file_exists(__DIR__ . '/../storage/installed.lock')) {
    // Build installer URL: strip /public from SCRIPT_NAME then re-add /public/install/
    $scriptDir    = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    $appBase      = preg_replace('#/public$#i', '', $scriptDir);
    $installerUrl = $appBase . '/public/install/';
    if (strpos($_SERVER['REQUEST_URI'] ?? '', '/install') === false) {
        header('Location: ' . $installerUrl);
        exit;
    }
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
