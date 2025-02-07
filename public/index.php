<?php

use Resources\InitDatabase;
use Routes\MainRoutes;
use Slim\Factory\AppFactory;

require_once("../src/Config/Config.php");

$app = AppFactory::create();

$app->addRoutingMiddleware();

$database = new InitDatabase();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// controla as rotas de usuário
new MainRoutes($app);
// Run app
$app->run();