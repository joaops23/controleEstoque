<?php

use Resources\InitDatabase;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require_once("../src/Config/Config.php");

$app = AppFactory::create();

$app->addRoutingMiddleware();

$database = new InitDatabase();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
// Add rotas


// Run app
$app->run();