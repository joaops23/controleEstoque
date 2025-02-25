<?php
namespace Database;

class Connector{
    protected \PDO $conn;

    protected string $driver;
    protected string $host;
    protected string $user;
    protected string $pwd;
    protected string $database;

    
    public function __construct()
    {
        // construir conexão com banco de dados
        $this->getParams();

        
        $this->conn = new \PDO("$this->driver:host=$this->host;dbname=$this->database", $this->user, $this->pwd);
    }

    private function getParams(): void
    {
        $this->driver = getenv("DB_DRIVER");
        $this->host = getenv("DB_HOST");
        $this->database = getenv("DB_NAME");
        $this->user = getenv("DB_USER");
        $this->pwd = getenv ("DB_PWD");
    }

    public function getConnection(): \PDO
    {
        return $this->conn;
    }

    /**
     * Neste método foi adicionado um ponteiro '&' para que as alterações do Statement sejam diretamente alteradas na variável raiz declarada no método que o chamou
     */
    protected function bindParams($params = array(), $fetch = true)
    {
        $parameters = "";
        if(count($params)) {
            foreach($params as $method => $param) {
                $parameters .= ' AND ';

                $param = $this::formatParam($param);
                
                $parameters .= " $method " . implode(" ", $param);
                
            }
        }

        return $parameters;
    }

    protected static function formatParam($param)
    {
        $paramReturn = $param;
        // Ao encontrar um |sl, substitui para aspas simples
        $paramReturn = str_replace("|sl", '"', $paramReturn);
        return $paramReturn;
    }

    protected static function formatValueParams($params = array())
    {
        $paramReturn = array();
        foreach($params as $param) {
            $return = $param;
            // Ao encontrar um |sl, substitui para aspas simples
            $return = str_replace("|sl", '"', $return);
            $paramReturn[] = $return;
        }

        return $paramReturn;
    }
}

