<?
namespace Models;

use Database\Connector;
use PDOStatement;

class UsuarioAbstract
{
    public $usu_id;
    public $usu_nome;
    public $usu_cpf;
    public $usu_email;
    public $usu_senha;
    public $usu_data_inclusao;
    public $usu_data_alteracao;

}

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

    /**
     * Array $params composto por 
     * ["método" => ['campo' => 'valor']]
     * Ex:
     * ['AND' => ['usu_id', "=", '|slvalor|sl']]
     * ['OR' => ['usu_id', "=", 'valor|sl']]
     * ['AND' => ['usu_id', 'BETWEEN', 'valor|sl', 'AND', 'valor2|sl']]
     * ['AND' => ['usu_id', 'LIKE', "%VALOR%|sl"]]
     * 
     * Argumento "|sl"(slaches) no valor serve para encapsular o valor em aspas, prevenção XSS
     */
    public function getAllUsers($params = array(), \stdClass $order = null)
    {
        $query = 'SELECT usu_id, usu_nome, usu_cpf, usu_email, usu_data_inclusao FROM usuario WHERE 1 :filters order by :ordenate :direction';

        $parameters = $this->bindParams($params);

        $query = str_replace(":filters", $parameters, $query);
        $query = str_replace(':ordenate', !empty($order->column) ? $order->column : 'usu_id', $query);
        $query = str_replace(':direction', !empty($order->direction) ? $order->direction : "desc", $query);

        $stmt = $this->conn->query($query);
        $stmt->execute();
        $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $res;
    }
    
    public function setInsertUser($params)
    {
        $this->conn->beginTransaction();
        try{
            $query = "INSERT INTO usuario (:COLUMNS) VALUES (:VAL);";
    
            $columns = implode(",", array_keys($params));
            $values = implode(",", $this->formatValueParams(array_values($params)));
    
            $query = str_replace(":COLUMNS", $columns, $query);
            $query = str_replace(":VAL", $values, $query);
    
            $this->conn->exec($query);

            $this->conn->commit();
            return $this->conn->lastInsertId();
        }catch(\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Não foi possível cadastrar o usuário, entre em contato com o administrador!\n {$e->getMessage()}");
        }
    }

    public function setUpdateUser($id, $params)
    {
        $this->conn->beginTransaction();
        try{
            $query = "UPDATE usuario SET :params WHERE usu_id = ':usu_id'";
    
            $queryParams = "";

            foreach($params as $column => $value) {
                $queryParams .= ", $column = $value";
            }
    
            $queryParams = substr(str_replace("|sl", '"', $queryParams), 1);

            $query = str_replace(":params", $queryParams, $query);
            $query = str_replace(":usu_id", $id, $query);
    
            $this->conn->exec($query);

            $this->conn->commit();
            return true;
        }catch(\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Não foi possível atualizar o usuário, entre em contato com o administrador!\n {$e->getMessage()}");
        }
    }

    public function getUserById($id): UsuarioAbstract
    {
        $stmt = $this->conn->prepare("SELECT * FROM usuario where usu_id = :id");

        $stmt->bindParam('id', $id);

        $stmt->execute();

        return $this->mapUser($stmt->fetch(\PDO::FETCH_ASSOC));
    }


    public function mapUser($map): UsuarioAbstract
    {
        $user = new UsuarioAbstract();
        foreach($map as $index => $value) {
            if(property_exists(UsuarioAbstract::class, $index)) {
                eval('$user->' . $index ."='" . $value . "';");
            }
        }

        return $user;
    }
    
}