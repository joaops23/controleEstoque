<?
namespace Models;

use Database\Connector;

class Usuario extends Connector 
{
    #campos 
    public function __construct()
    {
        parent::__construct();
    }
    
    public function login(String $login, String $password)
    {
        $stmt = $this->conn->prepare('Select * from usuario where usu_email = :login and usu_senha = :senha');

        $stmt->execute(['login' => $login, 'senha' => $password]);

        $usuario = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return count($usuario) != 0 ? $usuario[0] : false ;
    }
}