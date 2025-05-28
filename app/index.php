<?php

namespace App;

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__DIR__) . '/logs/php-error.log');

// Define the project root path
define('BASE_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);


if (!defined('APP_PATH')) {
    define('APP_PATH', BASE_PATH . "app" . DIRECTORY_SEPARATOR);
}

if (!defined('MODULE_PATH')) {
    define('MODULE_PATH', BASE_PATH . "modules" . DIRECTORY_SEPARATOR);
}

if (!defined('ROUTE_BASE')) {
    define("ROUTE_BASE", str_replace('\\', '/', APP_PATH));
}

if (!defined('VIEW_PATH')) {
    define('VIEW_PATH', APP_PATH . "views" . DIRECTORY_SEPARATOR);
}

if (!defined('TEMPLATE_PATH')) {
    define('TEMPLATE_PATH', APP_PATH . "templates" . DIRECTORY_SEPARATOR);
}


// Require the autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';


use System\Format;
use System\AppServer;
use System\HTTP\Method;
use System\Core\Logger;
use App\Config\Routes;

// Initialize the AppServer using the singleton pattern
$AppServer = AppServer::Start(true);
$AppServer->loadEnv(__DIR__ . '/.env');
$AppServer->loadDB();

$routes = new Routes();
$AppServer->AddRoutes($routes->getRoutes());

$AppServer->Process();
//qfpx7cARRJP7
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="./assets/images/favicon.ico">
    <title><?php echo $title ? $title : 'Local Web Directory for Navi Mumbai'; ?></title>
    <meta name="description" content="Discover local businesses and services in your area. Find properties, jobs, doctors, dentist, restaurants, schools, classes, service providers, and more in our comprehensive directory listing website.">
</head>

<body>
<h1>Hello from App Index.php</h1>
</body>

</html>