<?php

use Resources\InitDatabase;
use Routes\MainRoutes;
use Slim\Factory\AppFactory;

require_once("../src/Config/Config.php");

$app = AppFactory::create();

// Configuração Básica de CORS
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});


$app->addRoutingMiddleware();

$database = new InitDatabase();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// controla as rotas de usuário
new MainRoutes($app);
// Run app
$app->run();