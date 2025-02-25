<?php
namespace Routes;

use Middlewares\AuthMiddleware;
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

            $group->post('/login', 'Controllers\Login\Login::index');
            $group->post('/', '\Controllers\Usuario\UsuarioController::getUsers');
            $group->get("/{id}", '\Controllers\Usuario\UsuarioController::getUsersById');
            $group->post('/store', '\Controllers\Usuario\UsuarioController::storeUser');
            $group->post('/update/{id}', '\Controllers\Usuario\UsuarioController::updateUser');
        });

        $this->app->group('/product', function (RouteCollectorProxy $group) {
            $group->get('', '');
        });
    }
}