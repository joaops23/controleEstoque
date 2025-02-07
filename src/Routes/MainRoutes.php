<?php
namespace Routes;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

class MainRoutes {

    private App $app;
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->configure();
    }

    private function configure(): void
    {
        $this->app->group("/user", function (RouteCollectorProxy $group) {
            
            $group->get("/", function (Request $request, Response $response) {
                $response->getBody()->write("Olá");
                
                return $response;
            });
        });
    }
}