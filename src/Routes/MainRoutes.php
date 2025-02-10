<?php
namespace Routes;

use Controllers\Login\Login;
use Middlewares\AuthMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

class MainRoutes extends RoutesDefault{

    private App $app;
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->configure();
        $this->app->add(new AuthMiddleware());
    }

    private function configure(): void
    {
        $this->app->group("/user", function (RouteCollectorProxy $group) {
            $group->post('/login', function(Request $req, Response $res) {

                try{

                    $body = self::getBody($req);

                    /** @var Login */
                    $login = new Login($body->login, $body->password);

                    $token = $login->login();
    
                    $response = $res->withStatus(201)
                        ->withHeader('Content-Type', 'application/json');
                    $response->getBody()->write(json_encode(
                        [
                            "token" => $token,
                            "timeout" => "60"
                        ]
                    ));
                   
                    return $response;
                } catch(\Exception $e) {
                    $response = $res->withStatus(403)
                        ->withHeader('Content-Type', 'application/json');
                    $response->getBody()->write(json_encode(['message' => $e->getMessage()]));
                   
                    return $response;
                }
            });
        });
    }
}