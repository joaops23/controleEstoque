<?

namespace Middlewares;

use Controllers\Login\Login;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Factory\AppFactory;

class AuthMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        try{
            // Deixa aberta apenas a rota de login na API
            if(($request->getServerParams())['REQUEST_URI'] != '/user/login') {
    
                //Recupera o token de autenticação
                $token = explode(' ', $request->getHeader('Authorization')[0])[1];
    
                // Valida se usuário está devidamente logado
                Login::verifyToken($token);
            }
            return $handler->handle($request);
        } catch(\Exception $e) {
            return self::getReturnAccessDenied();
        }
    }

    public static function getReturnAccessDenied()
    {
        $app = AppFactory::create();

        $res = $app->getResponseFactory()->createResponse();
        $response = $res->withStatus(403)
        ->withHeader('Content-Type', 'application/json');

        $response->getBody()->write(json_encode([
            'message' => 'Acesso Negado!'
        ]));

        return $response;
    }
}