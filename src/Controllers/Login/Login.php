<?
namespace Controllers\Login;

use Controllers\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Controllers\Login\Interfaces\LoginInterface;
use Models\Usuario;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;

class Login extends Controller implements LoginInterface{

    private String $login;
    private String $password;

    public function __construct(String $login = '', String $password = '') 
    {
        $this->setLogin($login);
        $this->setPassword($password);
    }

    public static function index(Request $req, Response $res): Response {

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
    }
    
    public function login(): String|bool
    {
        // Busca as credenciais na base de dados
        $user = (new Usuario)->login($this->login, $this->password);
        
        if(!!$user == false) {
            throw new \Exception("Usuário ou senha inválido!");
        }

        return $this->generateHash($user);
        
    }


    public function setLogin(String $login = ''): void
    {
        if($login == '' || empty($login)) {
            throw new \Exception("Login do usuário não informado!");
        }

        $this->login = $login;
    }

    
    public function setPassword(String $password = ''): void
    {
        if($password == '' || empty($password)) {
            throw new \Exception("Senha do usuário não informada!");
        }

        $this->password = $password;
    }

    private function generateHash($user): String
    {
        $hash = JWT::encode(self::getUserDataHash($user), 
            getenv('SECRET_HASH'), 
            getenv('ALGO_HASH')
        );

        return $hash;
    }

    private static function getUserDataHash($user)
    {
        return [
            'usu_id' => $user['usu_id'], 
            'usu_cpf' => $user['usu_cpf'],
            'logIn' => (new \Datetime())->getTimestamp()
        ];
    }

    /**
     * Método que verifica se o token é válido e se sessão ainda está ativa (tempo máximo: 1h)
     */
    public static function verifyToken($token)
    {
        try {
            $decoded = JWT::decode($token, new Key(getenv('SECRET_HASH'), getenv('ALGO_HASH')));
            
            if(self::validateLoginTime($decoded->logIn)) {
                throw new \Exception('Token expirado!');
            }
        } catch (SignatureInvalidException $e) {
            throw new \Exception('Token Expirado!');
        }
    }

    private static function validateLoginTime($logIn): bool {
        $login = (new \DateTime())->setTimestamp($logIn);
        $now = new \DateTime();

        $diff = date_diff($login, $now);

        # Verifica se a diferença de tempo supera 1 hora
        return $diff->h > 0;
    }
}