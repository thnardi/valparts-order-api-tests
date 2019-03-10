<?php
declare(strict_types=1);

// conf systemroot
$rootPath = __DIR__ . '/..';
$appPath = $rootPath . '/app';

// composer
require $rootPath . '/vendor/autoload.php';

// config db, email, etc.
$dotenv = new \Dotenv\Dotenv($rootPath);
$dotenv->load();

// session
session_start();

// Instantiate the app
$settings = require $appPath . '/settings.php';
$app = new \Slim\App(['settings' => $settings]);

// Set up dependencies
require $appPath . '/dependencies.php';

// Register middleware
require $appPath . '/middleware.php';

// Register routes
require $appPath . '/routes.php';

// Run app
$app->run();
