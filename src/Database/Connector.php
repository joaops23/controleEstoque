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
}

